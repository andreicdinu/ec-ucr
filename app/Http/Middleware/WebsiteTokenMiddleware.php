<?php

namespace App\Http\Middleware;

use App\Environment;
use Closure;
use Illuminate\Support\Facades\Log;

class WebsiteTokenMiddleware
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
        if(!($hash = $request->header('UCR-TOKEN'))) {
            Log::channel('api_requests')->error('UCR_TOKEN header missing');
            abort(403, 'Unauthorized - please provide hash');
        }

        if(!($websiteUrl = $request->get('website_url'))) {
            Log::channel('api_requests')->error('Website url param missing from request');
            abort(403, 'Unauthorized - could not identify website reason [1]');
        }

        $websiteToken = $this->getWebsiteToken($websiteUrl);
        if(!$websiteToken) {
            Log::channel('api_requests')->error('Could not load website by url ' . $websiteUrl);
            abort(403, 'Unauthorized - could not identify website reason [2]');
        }


        if(!$this->validateToken($hash, $websiteToken)) {
            Log::channel('api_requests')->error('Wrong Website hash');
            abort(403, 'Unauthorized - wrong hash');
        }
        return $next($request);
    }

    protected function validateToken($hash , $websiteToken)
    {
        $salt = substr($hash, 0, 4);
        $clientHash = substr($hash, 4);

        $date = date("Ymd");
        $localHasH = hash("SHA512", $salt . $websiteToken . $date);

        if($clientHash == $localHasH) {
            return true;
        }

        return false;
    }

    protected function getWebsiteToken($websiteUrl)
    {
        $token = false;

        // identifying the environment after $websiteUrl
        if(substr($websiteUrl, -1) == '/') {
            $websiteUrl = substr($websiteUrl, 0, -1);
        }
        $environment = Environment::where('url', $websiteUrl)
            ->orWhere('url', $websiteUrl . '/')
            ->first();

        if($environment && $environment->getAttribute('token')) {
            $token = $environment->getAttribute('token');
        }

        return $token;
    }
}
