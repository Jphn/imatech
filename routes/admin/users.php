<?php

use App\Http\Response;
use App\Controller\Admin;

// Rota: Usuários (Admin)
$obRouter->get('/admin/users', [
    'middlewares' => [
        'required-admin-login'
    ],
    function ($request) {
        return new Response(200, Admin\User::getUsers($request, [
            'title' => 'Usuários - Admin'
        ]));
    }
]);

// Rota: Cadastro (Usuários)
$obRouter->get('/admin/users/new', [
    'middlewares' => [
        'required-admin-login'
    ],
    function ($request) {
        return new Response(200, Admin\User::getNewUser($request, [
            'title' => 'Novo Usuário - Admin',
            'contentRender' => [
                'title' => 'Cadastrar Novo Usuário',
                'nome' => null,
                'login' => null
            ]
        ]));
    }
]);

// Rota: Cadastro (Usuários/POST)
$obRouter->post('/admin/users/new', [
    'middlewares' => [
        'required-admin-login'
    ],
    function ($request) {
        return new Response(200, Admin\User::setNewUser($request));
    }
]);

// Rota: Edição (Usuário)
$obRouter->get('/admin/users/{id}/edit', [
    'middlewares' => [
        'required-admin-login'
    ],
    function ($request, $id) {
        return new Response(200, Admin\User::getEditUser($request, $id, [
            'title' => 'Editar Usuário - Admin',
            'contentRender' => [
                'title' => 'Editar Usuário'
            ]
        ]));
    }
]);

// Rota: Edição (Usuário/POST)
$obRouter->post('/admin/users/{id}/edit', [
    'middlewares' => [
        'required-admin-login'
    ],
    function ($request, $id) {
        return new Response(200, Admin\User::setEditUser($request, $id));
    }
]);

// Rota: Exclusão (Usuário)
$obRouter->get('/admin/users/{id}/delete', [
    'middlewares' => [
        'required-admin-login'
    ],
    function ($request, $id) {
        return new Response(200, Admin\User::getDeleteUser($request, $id, [
            'title' => 'Exclusão Usuário - Admin',
            'contentRender' => [
                'title' => 'Excluir Usuário'
            ]
        ]));
    }
]);

// Rota: Exclusão (Usuário/POST)
$obRouter->post('/admin/users/{id}/delete', [
    'middlewares' => [
        'required-admin-login'
    ],
    function ($request, $id) {
        return new Response(200, Admin\User::setDeleteUser($request, $id));
    }
]);
