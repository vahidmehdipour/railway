<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Validation\Rules\Password;
use Illuminate\View\View;

class ProfileController extends Controller
{
    /**
     * Display the user's profile form.
     */
    public function edit(Request $request): View
    {
        return view('profile.edit', [
            'user' => $request->user(),
        ]);
    }

    /**
     * Update the user's profile information.
     */
    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        $passwordData = null;

        if ($request->filled('current_password') || $request->filled('password') || $request->filled('password_confirmation')) {
            $passwordData = $request->validateWithBag('updatePassword', [
                'current_password' => ['required', 'current_password'],
                'password' => ['required', Password::defaults(), 'confirmed'],
            ]);
        }

        $request->user()->fill($request->validated());

        $profileChanged = $request->user()->isDirty('name') || $request->user()->isDirty('email');

        if ($request->user()->isDirty('email')) {
            $request->user()->email_verified_at = null;
        }

        $request->user()->save();

        if ($passwordData !== null) {
            $request->user()->update([
                'password' => Hash::make($passwordData['password']),
            ]);
        }

        $status = match (true) {
            $passwordData !== null && $profileChanged => 'profile-and-password-updated',
            $passwordData !== null => 'password-updated',
            default => 'profile-updated',
        };

        return Redirect::route('profile.edit')->with('status', $status);
    }

    /**
     * Delete the user's account.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $request->validateWithBag('userDeletion', [
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        Auth::logout();

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }
}
