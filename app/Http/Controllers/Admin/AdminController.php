<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Content;
use App\Models\Menu;
use App\Models\Idioma;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AdminController extends Controller
{
    /**
     * Dashboard principal del admin (método index)
     */
    public function index()
    {
        return $this->dashboard();
    }

    /**
     * Dashboard principal del admin
     */
    public function dashboard()
    {
        $stats = [
            'contenidos' => Content::count(),
            'noticias' => Content::where('tipo_contenido', 'noticia')->count(),
            'paginas' => Content::where('tipo_contenido', 'pagina')->count(),
            'menus' => Menu::count(),
            'idiomas' => Idioma::where('activo', true)->count(),
            'usuarios' => User::count(),
        ];

        $ultimasNoticias = Content::where('tipo_contenido', 'noticia')
                                 ->orderBy('created_at', 'desc')
                                 ->limit(5)
                                 ->get();

        return view('admin.dashboard', compact('stats', 'ultimasNoticias'));
    }

    /**
     * Mostrar formulario de login
     */
    public function showLogin()
    {
        if (Auth::check()) {
            return redirect()->route('admin.dashboard');
        }
        
        return view('admin.login');
    }

    /**
     * Procesar login
     */
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|min:6',
        ]);

        $remember = $request->has('remember');

        // Buscar usuario por email
        $user = User::where('email', $request->input('email'))->first();

        if ($user) {
            $valid = false;

            try {
                $valid = Hash::check($request->input('password'), $user->password);
            } catch (\RuntimeException $e) {
                // Manejar hashes legados: comprobar MD5 o texto plano
                $plain = $request->input('password');
                if ($user->password === $plain || $user->password === md5($plain)) {
                    $valid = true;
                }
            }

            if ($valid) {
                // Rehashear si el password no parece estar en un algoritmo moderno
                if (!str_starts_with($user->password, '$2y$') && !str_starts_with($user->password, '$2b$') && !str_starts_with($user->password, '$argon')) {
                    $user->password = Hash::make($request->input('password'));
                    $user->save();
                }

                Auth::login($user, $remember);
                $request->session()->regenerate();

                return redirect()->intended(route('admin.dashboard'))
                               ->with('success', '¡Bienvenido al CMS Eunomia!');
            }
        }

        return back()->withErrors([
            'email' => 'Las credenciales no coinciden con nuestros registros.',
        ])->withInput($request->except('password'));
    }

    /**
     * Cerrar sesión
     */
    public function logout(Request $request)
    {
        Auth::logout();
        
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        
        return redirect()->route('admin.login')
                       ->with('success', 'Sesión cerrada correctamente.');
    }

    /**
     * Crear usuario administrador de prueba
     */
    public function createTestAdmin()
    {
        $admin = User::firstOrCreate(
            ['email' => 'admin@nuntristeatro.com'],
            [
                'name' => 'Administrador',
                'password' => Hash::make('admin123'),
                'email_verified_at' => now(),
            ]
        );

        Auth::login($admin);

        return redirect()->route('admin.dashboard')
                       ->with('success', 'Usuario administrador creado y autenticado. Email: admin@nuntristeatro.com, Password: admin123');
    }
}
