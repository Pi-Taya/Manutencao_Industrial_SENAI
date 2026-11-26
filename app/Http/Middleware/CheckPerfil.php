<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\Response;

class CheckPerfil
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     * @param  string  ...$perfis
     */
    public function handle(Request $request, Closure $next, ...$perfis): Response
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        $user = Auth::user();

        // Verifica se o utilizador está ativo
        if (!$user->status) {
            Auth::logout();
            return redirect()->route('login')->withErrors([
                'email' => 'A sua conta está inativa. Contacte o administrador.'
            ]);
        }

        // Obtém e normaliza o nome do perfil (remove acentos e coloca em minúsculas)
        $tipoPerfil = $user->perfil->tipo_perfil ?? null;

        if (!$tipoPerfil) {
            abort(403, 'Utilizador sem perfil atribuído.');
        }

        $perfilNormalizado = Str::ascii(mb_strtolower(trim($tipoPerfil)));
        $permitidos = array_map(fn($p) => Str::ascii(mb_strtolower(trim($p))), $perfis);

        if (!in_array($perfilNormalizado, $permitidos, true)) {
            abort(403, 'Acesso não autorizado para o seu perfil de utilizador.');
        }

        return $next($request);
    }
}