<?php

namespace App\Http\Middleware;

use App\Models\User;
use App\Services\ErrorLoggingService;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;

class RoleMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */




    public function handle(Request $request, Closure $next)
    {
        try {
            if (!Auth::check()) {
                return redirect()->route('login');
            }

            $user = Auth::user();
    //            $targetUser = $request->route('user');
    //
    //
    //
    //            if (!$targetUser || !($targetUser instanceof User)) {
    //                return $this->checkPermissionOnly($user, $next, $request);
    //            }


            if ($user->hasRole('Admin') && $user->can('view all dashboards')) {
                return $next($request);
            }


            if ($user->hasRole('HR') && $user->can('view hr and below dashboards')) {
                if (
                    $targetUser->hasRole('HR') && $user->id === $targetUser->id ||
                    $targetUser->hasRole('Manager') ||
                    $targetUser->hasRole('Employee')
                ) {
                    return $next($request);
                }
            }


            if ($user->hasRole('Manager') && $user->can('view manager and below dashboards')) {
                if (
                    $targetUser->hasRole('Manager') && $user->id === $targetUser->id ||
                    $targetUser->hasRole('Employee')
                ) {
                    return $next($request);
                }
            }


            if ($user->hasRole('Employee') && $user->can('view own dashboard')) {
                if ($user->id === $targetUser->id) {
                    return $next($request);
                }
            }

           abort(403, 'Unauthorized access to this dashboard');
//            return $next($request);
        } catch (\Throwable $e) {
            ErrorLoggingService::log($e);
            abort(503, 'Something went wrong');
        }
    }


    private function checkPermissionOnly($user, $next, $request)
    {

        if (
            $user->hasRole('Admin') && $user->can('view all payrolls') ||
            $user->hasRole('HR') && $user->can('view hr and below dashboards') ||
            $user->hasRole('Manager') && $user->can('view manager and below dashboards') ||
            $user->hasRole('Employee') && $user->can('view own dashboard')
        ) {
            return $next($request);
        }

      abort(403, 'Unauthorized access');
  //      return $next($request);
    }


}
