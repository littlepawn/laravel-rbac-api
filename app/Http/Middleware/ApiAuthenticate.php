<?php

namespace App\Http\Middleware;

use App\Constants\response\UserConstants;
use App\Http\Controllers\Controller;
use App\Http\Repositories\UserRepository;
use App\Http\Traits\HttpResponses;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Laravel\Sanctum\PersonalAccessToken;
use Symfony\Component\HttpFoundation\Response;

class ApiAuthenticate extends Controller
{
    use HttpResponses;

    /**
     * @param Request $request
     * @param Closure $next
     * @return Response
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = auth()->check() ? auth()->user() : $this->getUserFromToken($request);

        if (!$user) {
            return $this->error(UserConstants::USER_UNAUTHORIZED);
        }

        if ($user->is_banned) {
            return $this->error(UserConstants::USER_BANNED);
        }

        if ($user->is_locked) {
            return $this->error(UserConstants::USER_LOCKED);
        }

        if (!auth()->check()) {

            if ($this->refreshToken($request)) {
                return $next($request);
            }
            return $this->error(UserConstants::USER_UNAUTHORIZED);

//            $response = $next($request);
//            $user->tokens()->delete();
//            $expiresAt = now()->addMinutes((new UserRepository)->getTokenExpireIn());
//            $newToken = $user->createToken(config('app.name'), [], $expiresAt)->plainTextToken;
//            $response->headers->set('Authorization', 'Bearer ' . $newToken);
//            return $response;
        }

        return $next($request);
    }

    /**
     * Get user from token.
     *
     * @param Request $request
     * @return mixed
     */
    private function getUserFromToken(Request $request): mixed
    {
        $token = $request->bearerToken();
        $personalAccessToken = PersonalAccessToken::findToken($token);

        return $personalAccessToken ? $personalAccessToken->tokenable : null;
    }

    private function refreshToken(Request $request): bool
    {
        $token = $request->bearerToken();
        $personalAccessToken = PersonalAccessToken::findToken($token);
        if (!$personalAccessToken) return false;
        $expiresAt = Carbon::now()->addMinutes((new UserRepository)->getTokenExpireIn());
        $personalAccessToken->update([
            'expires_at' => $expiresAt
        ]);
        return true;
    }

}
