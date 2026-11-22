<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use App\Support\Appearance;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class AppearanceController extends Controller
{
    /**
     * Show the appearance management form.
     */
    public function edit(): View
    {
        $appearance = collect(Appearance::DEFAULTS)
            ->map(fn (string $default, string $key): mixed => get_setting($key, $default))
            ->all();

        return view('appearance.edit', [
            'appearance' => $appearance,
            'backgrounds' => Appearance::BACKGROUNDS,
        ]);
    }

    /**
     * Persist the appearance settings.
     */
    public function update(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            Appearance::APP_NAME => ['required', 'string', 'max:120'],
            Appearance::FOOTER_TEXT => ['required', 'string', 'max:255'],
            Appearance::COPYRIGHT_TEXT => ['required', 'string', 'max:255'],
            Appearance::LOGIN_BACKGROUND => ['required', Rule::in(array_keys(Appearance::BACKGROUNDS))],
        ]);

        foreach ($validated as $key => $value) {
            Setting::updateOrCreate(
                ['key' => $key],
                ['value' => (string) $value],
            );
        }

        return back()->with('success', 'ظاهر سامانه با موفقیت بهروزرسانی شد.');
    }
}
