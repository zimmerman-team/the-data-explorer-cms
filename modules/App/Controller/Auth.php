<?php

namespace App\Controller;

/**
 * Class Controller
 * @package App
 */
class Auth extends Base {

    protected $layout = 'app:layouts/canvas.php';

    public function login() {

        if ($this->helper('auth')->getUser()) {
            $this->app->reroute('/');
        }

        $this->app->response->nocache = true;

        $redirectTo = $this->param('to', '/');

        if (\substr($redirectTo, 0, 1) !== '/') {
            $redirectTo = '/';
        }

        $redirectTo = htmlspecialchars($this->routeUrl($redirectTo), ENT_QUOTES, 'UTF-8');

        $this->helper('theme')->pageClass('login-page');

        return $this->render('app:views/auth/login.php', \compact('redirectTo'));
    }

    public function logout() {

        $this->helper('auth')->logout();

        if ($this->app->request->is('ajax')) {
            return ['logout' => true];
        } else {
            $this->app->reroute('/auth/login?logout=1');
        }
    }

    public function check() {

        if ($this->helper('auth')->getUser()) {
            return false;
        }

        $auth = $this->param('auth');

        if (!$auth || !isset($auth['user'], $auth['password']) || !\is_string($auth['user']) || !\is_string($auth['password'])) {
            return $this->stop(412);
        }

        if (!$this->helper('csrf')->isValid('login', $this->param('csrf'), true)) {
            return $this->stop(412);
        }

        $auth = [
            'user' => $auth['user'],
            'password' => $auth['password'],
        ];

        if (isset($auth['user']) && $this->helper('utils')->isEmail($auth['user'])) {
            $auth['email'] = $auth['user'];
            $auth['user']  = '';
        }

        $user = $this->helper('auth')->authenticate($auth);

        if ($user && $user['role'] == 'public') {
            return $this->stop(412);
        }

        if ($user) {

            $this->app->trigger('app.user.disguise', [&$user]);

            $this->helper('auth')->setUser($user);
            $this->helper('session')->write('app.session.start', time());

            $this->app->trigger('app.user.login', [$user]);

            return ['success' => true, 'user' => $user];
        }

        return ['success' => false];
    }
}