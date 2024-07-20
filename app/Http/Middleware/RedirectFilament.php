<?php

namespace App\Http\Middleware;

use Closure;
use Filament\Facades\Filament;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class RedirectFilament
{
    /**
     * Handle an incoming request.
     *
     * @param \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response) $next
     */

    public function handle(Request $request, Closure $next): Response
    {
        $user = Auth::user();


        if ($user) {

            if ($user->role !== Filament::getCurrentPanel()->getId()) {
                if ($user->role == "receptionist") {
                    return redirect('/receptionist');
                }
                if ($user->role == "admin") {
                    return redirect('/');
                }
                if($user->role == "owner"){
                    return redirect('/owner');
                }
            }
        }


        return $next($request);
    }
}
