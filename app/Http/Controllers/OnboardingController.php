<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;

class OnboardingController extends Controller
{
    public function complete(): RedirectResponse
    {
        $user = auth()->user();

        if ($user) {
            $user->update(['onboarding_completed_at' => now()]);
        }

        return back();
    }
}
