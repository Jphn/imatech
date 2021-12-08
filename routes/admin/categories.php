<?php

use App\Http\Response;
use App\Controller\Admin;

// Rota: Categorias (Admin)
$obRouter->get('/admin/categories', [
    'middlewares' => [
        'required-admin-login'
    ],
    function ($request) {
        return new Response(200, Admin\Category::getCategories($request, [
            'title' => 'Categorias - Admin'
        ]));
    }
]);

// Rota: Nova Categoria (Admin)
$obRouter->get('/admin/categories/new', [
    'middlewares' => [
        'required-admin-login'
    ],
    function ($request) {
        return new Response(200, Admin\Category::getNewCategory($request, [
            'title' => 'Nova Categoria - Admin',
            'contentRender' => [
                'title' => 'Cadastrar Nova Categoria',
                'nome' => null,
                'status' => null
            ]
        ]));
    }
]);

// Rota: Nova Categoria (Admin/POST)
$obRouter->post('/admin/categories/new', [
    'middlewares' => [
        'required-admin-login'
    ],
    function ($request) {
        return new Response(200, Admin\Category::setNewCategory($request));
    }
]);

// Rota: Categorias (Edição)
$obRouter->get('/admin/categories/{id}/edit', [
    'middlewares' => [
        'required-admin-login'
    ],
    function ($request, $id) {
        return new Response(200, Admin\Category::getEditCategory($request, $id, [
            'title' => 'Editar Categoria - Admin',
            'contentRender' => [
                'title' => 'Editar Categoria'
            ]
        ]));
    }
]);

// Rota: Categorias (Edição/POST)
$obRouter->post('/admin/categories/{id}/edit', [
    'middlewares' => [
        'required-admin-login'
    ],
    function ($request, $id) {
        return new Response(200, Admin\Category::setEditCategory($request, $id));
    }
]);

// Rota: Categorias (Exclusão)
$obRouter->get('/admin/categories/{id}/delete', [
    'middlewares' => [
        'required-admin-login'
    ],
    function ($request, $id) {
        return new Response(200, Admin\Category::getDeleteCategory($request, $id, [
            'title' => 'Excluir Categoria - Admin',
            'contentRender' => [
                'title' => 'Excluir Categoria'
            ]
        ]));
    }
]);

// Rota: Categorias (Exclusão/POST)
$obRouter->post('/admin/categories/{id}/delete', [
    'middlewares' => [
        'required-admin-login'
    ],
    function ($request, $id) {
        return new Response(200, Admin\Category::setDeleteCategory($request, $id));
    }
]);
