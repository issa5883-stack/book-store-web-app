<?php

namespace App\Http\Middleware;

use App\Models\Admin;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\View;
use Symfony\Component\HttpFoundation\Response;

/**
 * Allows the request only when an admin is logged in (session "admin_id"),
 * and shares the current admin with every view as $currentAdmin.
 */
class AdminAuth
{
    public function handle(Request $request, Closure $next): Response
    {
        $admin = Admin::find($request->session()->get('admin_id'));

        if (! $admin) {
            $request->session()->forget('admin_id');

            return redirect()->route('admin.login')->with('error', __('Please sign in as an administrator.'));
        }

        $request->attributes->set('admin', $admin);
        View::share('currentAdmin', $admin);

        return $next($request);
    }
}
