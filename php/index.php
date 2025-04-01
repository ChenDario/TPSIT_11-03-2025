<?php
    use Slim\Factory\AppFactory;

    require __DIR__ . '/vendor/autoload.php';
    require __DIR__ . '/controllers/AlunniController.php';
    require __DIR__ . '/includes/Db.php';

    $app = AppFactory::create();

    //                  Chi deve gestire la richiesta e buttare fuori la risposta
    //$app->get('/alunni', "AlunniController:index");
    //curl http://localhost:8080/alunni/1
    $app->get('/alunni', "AlunniController:search");
    //curl -X POST http://localhost:8080/alunni -H "Content-Type: application/json" -d '{"nome": "Dario","cognome": "Chen"}'
    $app->post('/alunni', "AlunniController:create");
    //curl -X PUT http://localhost:8080/alunni/3 -H "Content-Type: application/json" -d '{"nome": "Ruji"}'
    $app->put('/alunni/{id}', "AlunniController:update");
    //curl -X DELETE http://localhost:8080/alunni/3
    $app->delete('/alunni/{id}', "AlunniController:destroy");

    $app->run();
?>