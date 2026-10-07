<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;

class EnsurePortalAccess
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     * @param  string  ...$portals
     */
    public function handle(Request $request, Closure $next, string ...$portals): Response
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        $user = Auth::user();

        // Akun nonaktif tidak boleh mengakses portal manapun
        if (!$user->is_active) {
            Auth::logout();
            return redirect()->route('login')->withErrors([
                'username' => 'Akun Anda telah dinonaktifkan oleh Administrator.',
            ]);
        }

        // Administrator memiliki hak akses penuh ke seluruh portal
        if ($user->role === 'admin') {
            return $next($request);
        }

        // Parse jika portal dioper sebagai comma-separated string (mis. 'portal:admin,guru_mengajar')
        $portalList = [];
        foreach ($portals as $portal) {
            foreach (explode(',', $portal) as $p) {
                $trimmed = trim($p);
                if ($trimmed !== '') {
                    $portalList[] = $trimmed;
                }
            }
        }

        // Periksa apakah user memiliki salah satu dari portal yang diizinkan
        if (!empty($portalList) && $user->hasPortal(...$portalList)) {
            return $next($request);
        }

        // Jika request mengharapkan respon JSON (API / AJAX)
        if ($request->expectsJson()) {
            return response()->json([
                'message'           => 'Akses Ditolak. Anda tidak memiliki izin untuk mengakses portal ini.',
                'required_portals'  => $portalList,
                'available_portals' => $user->availablePortalKeys(),
            ], 403);
        }

        // Tampilkan halaman 403 ramah dengan opsi portal yang dimiliki
        $availablePortals = $user->availablePortals();

        return response()->view('errors.403_portal', [
            'user'             => $user,
            'requiredPortals'  => $portalList,
            'availablePortals' => $availablePortals,
        ], 403);
    }
}
