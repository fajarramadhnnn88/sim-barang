<?php
namespace App\Http\Middleware;
use Closure;
use Illuminate\Http\Request;

class RoleMiddleware {
    public function handle(Request $request, Closure $next, string ...$roles): mixed {
        if(!$request->user()) return redirect()->route('login');
        $userRole=$request->user()->role->value;
        if($userRole==='superadmin') return $next($request);
        if(!in_array($userRole,$roles)) abort(403,'Anda tidak memiliki akses ke halaman ini.');
        return $next($request);
    }
}
