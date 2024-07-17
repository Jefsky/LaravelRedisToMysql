<?php

namespace App\Http\Middleware;

use App\Helpers\ApiResponse;
use App\Helpers\ResponseEnum;
use App\Service\Api\UserTokenService;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redis;

class VerifyUserToken
{
    use ApiResponse;

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        if (empty($request->user_id)) {
            return $this->fail(ResponseEnum::CLIENT_PARAMETER_ERROR, 'Login first');
        }
        if (empty($request->header('userToken'))) {
            return $this->fail(ResponseEnum::CLIENT_PARAMETER_ERROR, 'Login first');
        }
        $user_token_info = UserTokenService::getUserToken($request->user_id);
        if (empty($user_token_info)) {
            return $this->fail(ResponseEnum::CLIENT_HTTP_UNAUTHORIZED, 'Login first');
        } else {
            if ($user_token_info['token'] <> $request->header('userToken')) {
                return $this->fail(ResponseEnum::CLIENT_HTTP_UNAUTHORIZED, 'Login first');
            } else {
                if ($user_token_info['expire_time'] < time()) {
                    return $this->fail(ResponseEnum::CLIENT_HTTP_UNAUTHORIZED, 'Login first');
                }
            }
        }
        
        // 改为暂时redis存储
        $update_data = json_decode(Redis::get('user_active_behavior'), true);
        $update_data[] = [
            'type' => 1,
            'part' => 2,
            'user_id' => $request->user_id,
            'content' => 'UserID:' . $request->user_id . ' | IP：' . $request->ip() . ' | 操作 ' . $request->path(),
            'add_time' => time()
        ];
        Redis::set('user_active_behavior', json_encode($update_data));
        return $next($request);
    }
}
