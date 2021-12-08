<?php

use App\Http\Response;
use App\Controller\Pages;

// Rota: Home
$obRouter->get('/', [
    function () {
        return new Response(200, Pages\Home::getHome([
            'title' => 'Home - Imatech'
        ]));
    }
]);

// Rota: Sobre
$obRouter->get('/sobre', [
    function () {
        return new Response(200, Pages\About::getAbout([
            'title' => 'Sobre - Imatech'
        ]));
    }
]);

// Rota: Dinâmica
$obRouter->get('/pagina/{idPagina}/{acao}', [
    function ($idPagina, $acao) {
        return new Response(200, 'Página Nº: ' . $idPagina . ' / Ação: ' . $acao);
    }
]);

// Rota: Blog
$obRouter->get('/blog', [
    function ($request) {
        return new Response(200, Pages\Post::getPosts($request, [
            'title' => 'Blog - Imatech'
        ]));
    }
]);

// Rota: Blog
$obRouter->post('/blog', [
    function ($request) {
        return new Response(200, Pages\Post::setPost($request));
    }
]);
