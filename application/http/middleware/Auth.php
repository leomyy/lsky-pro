<?php

namespace app\http\middleware;

use think\facade\Session;

class Auth
{
    /**
     * 无需登录可访问的方法
     *
     * @var array
     */
    private $noNeedLogin = [
        'Index/index',
        'Upload/upload',
        'Auth/*',
        'Api/*'
    ];

    public function handle($request, \Closure $next)
    {
        $uri = strtolower($request->controller() . '/' . $request->action());
        $noNeedLogin = array_map('strtolower', $this->noNeedLogin);
        if (!in_array($uri, $noNeedLogin)) {
            foreach ($noNeedLogin as &$value) {
                list($controller, $action) = explode('/', strtolower($value));
                if ($controller === strtolower($request->controller())) {
                    if ('*' === $action) {
                        return $next($request);
                    }
                }
            }

            if (!Session::has('uid') || !Session::has('token')) {
                return redirect(url('auth/login'));
            }
        }

        return $next($request);
    }
}
