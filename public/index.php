<?php

require __DIR__ . '/../includes/app.php';

use App\Http\Router;

// Inicia o Router
$obRouter = new Router(URL);

// Incluindo as rotas de páginas
include __DIR__ . '/../routes/pages.php';

// Incluindo as rotas de páginas
include __DIR__ . '/../routes/admin.php';

// Imprime a Response da rota
$obRouter->run()->sendResponse();
