<?php

use App\Http\Response;
use App\Controller\Admin;

// Rota: Depoimentos (Admin)
$obRouter->get('/admin/posts', [
    'middlewares' => [
        'required-admin-login'
    ],
    function ($request) {
        return new Response(200, Admin\Post::getPosts($request, [
            'title' => 'Postagens - Admin'
        ]));
    }
]);

// Rota: Cadastro (Depoimento)
$obRouter->get('/admin/posts/new', [
    'middlewares' => [
        'required-admin-login'
    ],
    function ($request) {
        return new Response(200, Admin\Post::getNewPost($request, [
            'title' => 'Nova Postagem - Admin',
            'contentRender' => [
                'title' => 'Cadastrar Nova Postagem',
                'titulo' => null,
                'conteudo' => null,
                'status' => null,
                'this' => 'selected'
            ]
        ]));
    }
]);

// Rota: Cadastro (Depoimento/POST)
$obRouter->post('/admin/posts/new', [
    'middlewares' => [
        'required-admin-login'
    ],
    function ($request) {
        return new Response(200, Admin\Post::setNewPost($request));
    }
]);

// Rota: Edição (Depoimento)
$obRouter->get('/admin/posts/{id}/edit', [
    'middlewares' => [
        'required-admin-login'
    ],
    function ($request, $id) {
        return new Response(200, Admin\Post::getEditPost($request, $id, [
            'title' => 'Eitar Postagem - Admin',
            'contentRender' => [
                'title' => 'Editar Postagem',
                'this' => ''
            ]
        ]));
    }
]);

// Rota: Edição (Depoimento/POST)
$obRouter->post('/admin/posts/{id}/edit', [
    'middlewares' => [
        'required-admin-login'
    ],
    function ($request, $id) {
        return new Response(200, Admin\Post::setEditPost($request, $id));
    }
]);

// Rota: Edição (Depoimento)
$obRouter->get('/admin/posts/{id}/delete', [
    'middlewares' => [
        'required-admin-login'
    ],
    function ($request, $id) {
        return new Response(200, Admin\Post::getDeletePost($request, $id, [
            'title' => 'Excluir Postagem - Admin',
            'contentRender' => [
                'title' => 'Excluir Postagem'
            ]
        ]));
    }
]);

// Rota: Edição (Depoimento/POST)
$obRouter->post('/admin/posts/{id}/delete', [
    'middlewares' => [
        'required-admin-login'
    ],
    function ($request, $id) {
        return new Response(200, Admin\Post::setDeletePost($request, $id));
    }
]);
