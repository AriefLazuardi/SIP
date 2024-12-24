<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     */
    public function create(): View
    {
        return view('auth.login');
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        $request->authenticate();

        $request->session()->regenerate();

        return $this->redirectBasedOnRole();

    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/');
    }

    private function redirectBasedOnRole(): RedirectResponse
    {
        if (Auth::user()->hasRole('administrator')) {
            return redirect()->route('admin.dashboard');
        } elseif (Auth::user()->hasRole('wakakurikulum')) {
            return redirect()->route('wakilkurikulum.dashboard');
        } elseif (Auth::user()->hasRole('guru')) {
            return redirect()->route('guru.dashboard');
        }

        // Default redirect if no specific role matches
        return redirect()->route('/');
    }
}
