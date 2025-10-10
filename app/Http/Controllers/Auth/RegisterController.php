<?php
// app/Http/Controllers/Auth/RegisterController.php
namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class RegisterController extends Controller
{
    public function register(Request $request)
    {
        $validated = $request->validate([
            'name'     => ['required','string','max:255'],
            'email'    => ['required','email','regex:/^\d{10}@student\.its\.ac\.id$/i','unique:users,email'],
            'password' => ['required','min:6'],
        ], [
            'email.regex' => 'Email harus 10 digit + @student.its.ac.id (contoh: 5024241033@student.its.ac.id).',
        ]);

        // Ambil NRP dari email (10 digit di depannya)
        preg_match('/^(\d{10})@student\.its\.ac\.id$/i', strtolower($validated['email']), $m);
        $nrp = $m[1] ?? null;

        // Pastikan NRP unik juga
        if (User::where('nrp', $nrp)->exists()) {
            return back()->withErrors(['email' => 'NRP sudah terdaftar.'])->withInput();
        }

        $user = User::create([
            'name'     => $validated['name'],
            'email'    => strtolower($validated['email']), // mutator juga akan set nrp
            'nrp'      => $nrp,                            // eksplisit supaya pasti
            'password' => bcrypt($validated['password']),
        ]);

        Auth::login($user);
        return back()->with('ok','Register sukses!');
    }
}
