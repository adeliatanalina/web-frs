<?php
// app/Http/Controllers/AuthController.php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function authenticate(Request $request)
    {
        $credentials = $request->validate([
            'loginname'     => ['required','regex:/^\d{10}@student\.its\.ac\.id$/i'],
            'loginpassword' => ['required'],
        ], [
            'loginname.regex' => 'Gunakan email NRP: 10digit@student.its.ac.id',
        ]);

        $ok = Auth::attempt([
            'email'    => strtolower($credentials['loginname']),
            'password' => $credentials['loginpassword'],
        ]);

        if ($ok) {
            $request->session()->regenerate();
            return redirect()->intended('/')->with('ok','Login sukses');
        }
        return back()->withErrors(['loginname' => 'Email/password salah.'])->withInput();
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/')->with('ok','Logout sukses');
    }
}
