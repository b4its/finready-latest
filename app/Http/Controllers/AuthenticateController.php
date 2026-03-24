<?php

namespace App\Http\Controllers;

use App\Models\Profile;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class AuthenticateController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
        return view('authentication.login');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create_view()
    {
        //
        return view('authentication.register');
    }

    public function post_login(Request $request)
    {
        // 1. Validasi input (Hapus 'unique:users' karena ini form login)
        $credentials = $request->validate([
            'email'    => ['required', 'string', 'email'],
            'password' => ['required', 'string'],
            'role'     => ['required', 'string', 'in:umkm,investor'],
        ]);

        // 2. Coba melakukan autentikasi
        // Catatan: Pastikan tabel users kamu memiliki kolom 'role' untuk membedakan umkm/investor
        if (Auth::attempt([
            'email' => $credentials['email'], 
            'password' => $credentials['password'], 
            'role' => $credentials['role']
        ], $request->boolean('remember'))) {
            
            // 3. Regenerasi session untuk keamanan (mencegah session fixation)
            $request->session()->regenerate();
            
            // 4. Redirect sesuai role
            $routePrefix = $credentials['role'] === 'umkm' ? 'umkm' : 'investor';
            
            return redirect()
                ->route('filament.' . $routePrefix . '.pages.dashboard')
                ->with('success', 'Login berhasil!');
        }

        // 5. Jika gagal login, kembalikan ke form dengan pesan error
        return back()->withErrors([
            'email' => 'Email, password, atau role yang dipilih tidak sesuai.',
        ])->onlyInput('email');
    }

    /**
     * Store a newly created resource in storage.
     */


    public function store(Request $request)
    {
        // 1. Validasi menggunakan array syntax (lebih aman) dan aturan 'confirmed'
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255', 'unique:users'],
            'email'    => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'role'     => ['required', 'string', 'in:umkm,investor'],
        ]);

        // 2. Gunakan DB Transaction untuk konsistensi data
        $user = DB::transaction(function () use ($validated) {
            $newUser = User::create([
                'name'     => $validated['name'],
                'email'    => $validated['email'],
                'password' => $validated['password'], 
                'role'     => $validated['role'], 
            ]);

            Profile::create([
                'idUsers' => $newUser->id,
                'email'   => $newUser->email, // Salin email dari user
            ]);

            return $newUser;
        });

        // 3. Login user
        Auth::login($user);

        // 4. Optimasi logika Redirect (lebih dinamis dan bersih)
        $routePrefix = $validated['role'] === 'umkm' ? 'umkm' : 'investor';
        
        return redirect()
            ->route('filament.' . $routePrefix . '.pages.dashboard')
            ->with('success', 'Registrasi berhasil!');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
