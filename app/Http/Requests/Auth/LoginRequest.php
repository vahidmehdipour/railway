<?php

namespace App\Http\Requests\Auth;

use Illuminate\Auth\Events\Lockout;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class LoginRequest extends FormRequest
{
    private const CAPTCHA_THRESHOLD = 3;

    private const ATTEMPTS_KEY = 'login_failed_attempts';

    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $rules = [
            'email' => ['required', 'string', 'email'],
            'password' => ['required', 'string'],
        ];

        if ($this->captchaRequired()) {
            $rules['captcha'] = ['required', 'numeric'];
        }

        return $rules;
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'captcha.required' => 'لطفاً کد امنیتی را وارد کنید.',
            'captcha.numeric' => 'کد امنیتی باید عدد باشد.',
        ];
    }

    /**
     * @throws ValidationException
     */
    public function authenticate(): void
    {
        $this->ensureIsNotRateLimited();

        if (! Auth::attempt($this->only('email', 'password'), $this->boolean('remember'))) {
            RateLimiter::hit($this->throttleKey());

            $attempts = (int) $this->session()->get(self::ATTEMPTS_KEY, 0) + 1;
            $this->session()->put(self::ATTEMPTS_KEY, $attempts);

            if ($attempts >= self::CAPTCHA_THRESHOLD) {
                $this->regenerateCaptcha();
                $this->session()->put('captcha_required', true);
            }

            throw ValidationException::withMessages([
                'email' => trans('auth.failed'),
            ]);
        }

        if (! Auth::user()->is_active) {
            Auth::logout();
            $this->session()->invalidate();
            $this->session()->regenerateToken();

            throw ValidationException::withMessages([
                'email' => trans('auth.inactive'),
            ]);
        }

        RateLimiter::clear($this->throttleKey());
        $this->clearCaptchaState();
    }

    public function ensureIsNotRateLimited(): void
    {
        if (! RateLimiter::tooManyAttempts($this->throttleKey(), 5)) {
            return;
        }

        event(new Lockout($this));

        $seconds = RateLimiter::availableIn($this->throttleKey());

        throw ValidationException::withMessages([
            'email' => trans('auth.throttle', [
                'seconds' => $seconds,
                'minutes' => ceil($seconds / 60),
            ]),
        ]);
    }

    public function throttleKey(): string
    {
        return Str::transliterate(Str::lower($this->string('email')).'|'.$this->ip());
    }

    public function captchaRequired(): bool
    {
        return (bool) $this->session()->get('captcha_required', false);
    }

    /**
     * @throws ValidationException
     */
    protected function prepareForValidation(): void
    {
        if (! $this->captchaRequired()) {
            return;
        }

        $answer = (int) $this->session()->get('captcha_answer');
        $provided = $this->input('captcha');

        if ($answer === 0 || $provided === null || (int) $provided !== $answer) {
            $attempts = (int) $this->session()->get(self::ATTEMPTS_KEY, 0) + 1;
            $this->session()->put(self::ATTEMPTS_KEY, $attempts);
            $this->regenerateCaptcha();
            $this->session()->put('captcha_required', true);

            throw ValidationException::withMessages([
                'captcha' => 'پاسخ کد امنیتی اشتباه است. لطفاً دوباره تلاش کنید.',
            ]);
        }
    }

    protected function regenerateCaptcha(): void
    {
        $a = random_int(1, 9);
        $b = random_int(1, 9);

        $this->session()->put([
            'captcha_a' => $a,
            'captcha_b' => $b,
            'captcha_answer' => $a + $b,
        ]);
    }

    protected function clearCaptchaState(): void
    {
        $this->session()->forget([
            self::ATTEMPTS_KEY,
            'captcha_required',
            'captcha_a',
            'captcha_b',
            'captcha_answer',
        ]);
    }
}
