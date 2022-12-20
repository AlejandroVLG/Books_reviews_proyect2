<?php

namespace App\Http\Middleware;

use App\Models\User;
use Closure;
use Illuminate\Http\Request;

class IsAdmin
{   
    const ROLE_ADMIN = 2;

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {   
        $userId = auth()->user()->id;

        $user = User::find($userId);

        $isAdmin = $user->roles->contains(self::ROLE_ADMIN);

        if (!$isAdmin) {
            return response()->json(
                [
                    'success' => false,
                    'message' => 'Route does not found'
                ],
                404
            );
        }
        return $next($request);
    }
}
