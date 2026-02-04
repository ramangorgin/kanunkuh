<?php

/**
 * User settings endpoints.
 */

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

/**
 * Handles account settings changes such as password updates.
 */
class SettingsController extends Controller
{
    /**
     * Update the authenticated user's password.
     */
    public function updatePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required',
            'new_password' => 'required|string|min:6|confirmed',
        ]);

        $user = auth()->user();

        if (!Hash::check($request->current_password, $user->password)) {
            return back()->withErrors(['current_password' => 'رمز فعلی نادرست است.']);
        }

        $user->update([
            'password' => bcrypt($request->new_password)
        ]);

        return back()->with('success', 'رمز عبور با موفقیت به‌روزرسانی شد.');
    }
}
