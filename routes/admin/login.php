<?php

use App\Http\Response;
use App\Controller\Admin;

// Rota: Login
$obRouter->get('/admin/login', [
    'middlewares' => [
        'required-admin-logout'
    ],
    function ($request) {
        return new Response(200, Admin\Login::getLogin($request, null, [
            'title' => 'Login - Admin'
        ]));
    }
]);

// Rota: Login (Post)
$obRouter->post('/admin/login', [
    'middlewares' => [
        'required-admin-logout'
    ],
    function ($request) {
        return new Response(200, Admin\Login::setLogin($request));
    }
]);

// Rota: Logout
$obRouter->get('/admin/logout', [
    'middlewares' => [
        'required-admin-login'
    ],
    function ($request) {
        return new Response(200, Admin\Login::setLogout($request));
    }
]);
