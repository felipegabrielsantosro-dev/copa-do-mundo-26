<?php

#Importa a classe AppFactory do Slim Framework, responsavel por criar a instancia da aplicação.
use Slim\Factory\AppFactory;

#Carrega automaticamente todas as dependências instalada via Composer (Incluindo Slim e outras bibliotecas).
#Sem essa autoload, o framework e as classes utilizandas no projeto não poderiam ser encontradas.
require __DIR__ . '/../vendor/autoload.php';

#Cria a aplicação Slim,retornando um objeto que representa o servidor HTTP e gerenciador de rotas.
$app = AppFactory::create();

#Adiciona o middleware responsável por interpretar as rotas e direcionar cada requisição HTTP para a rota correta.
#Sem este middleware, o Slim não saberia como ler com ou processar as rotas definidas.
$app->addRoutingMiddleware();

$errorMiddleware = $app->addErrorMiddleware(true, true, true);

require __DIR__ . '/../app/helper/settings.php';
require __DIR__ . '/../app/route/route.php';

$app->run();