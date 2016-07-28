<?php

namespace App\Http\Middleware;

use Carbon\Carbon;
use Closure;
use Webpatser\Uuid\Uuid;

class Statistic
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $page = $request->path();
        $browser = \Agent::browser();
        $platform = \Agent::platform();
        $ip = $request->ip();
        $referrer = $request->server('HTTP_REFERER');
        $countryCode = \GeoIP::getLocation()['isoCode'];
        $time = microtime(true);

        if ($referrer) $referrer = parse_url($referrer)['host'];

        if (!\Cookie::has('statCookie')) {

            $cookieVal = (string)Uuid::generate(4);
            // На 10 дней
            \Cookie::queue(\Cookie::make('statCookie', $cookieVal, 14400));
            $cookie = $cookieVal;
        } else {
            $cookie = \Cookie::get('statCookie');
        }

        /** @var \App\Components\Statistic\Storage $storage */
        $storage = app(\App\Components\Statistic\Storage::class);
        $storage->collect($page, $browser, $platform, $ip, $countryCode, $cookie, $time, $referrer);

        return $next($request);
    }
}
