<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class CheckMerideHookSignature
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        if (!$request->hasHeader('token') or !$request->hasHeader('signature')) {
            abort(400);
        }
        $my_signature = hash_hmac('sha256', $request->header('token'), config('meride.webhookSecretKey'));

        if(!hash_equals($my_signature, $request->header('signature'))){
            abort(401);
        }

        return $next($request);
    }
}
