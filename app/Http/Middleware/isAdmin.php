<?php

namespace App\Http\Middleware;

use App\Models\Role;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class isAdmin
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    { {
            $user = auth()->user();

            $role = Role::where('name', 'admin')->first();

            if ($user->role_id == $role->id) {
                return $next($request);
            }

            return response()->json([
                'message' => 'Anda Tidak Memiliki Akses kesini',
            ], 401);
        }
    }
}
