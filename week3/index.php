<?php

/**
 * Controller
 *
 * Database-driven Webtechnology
 * Taught by Stijn Eikelboom
 * Based on code by Reinard van Dalen
 */

/* Require composer autoloader */
require __DIR__ . '/vendor/autoload.php';

/* Include model.php */
include 'model.php';

/* Connect to DB */
$db = connect_db('localhost', 'ddwt21_week3', 'ddwt21', 'ddwt21');

/* Credentials */
$cred = set_cred('ddwt21', 'ddwt21');

/* Create Router instance */
$router = new \Bramus\Router\Router();

// Add routes here
$router->mount('/api', function() use ($router, $db, $cred) {
    /* Set header to json */
    http_content_type();

    /* Check credentials */
    $router->before('GET|POST|PUT|DELETE', '/api/.*', function() use($cred){
        if(!check_cred($cred)) {
            echo json_encode('User not authenticated. No access granted.');
            exit();
        }
    });

    /* Index route to overview page */
    $router->get('/', function () {
        echo 'series overview';
    });

    /* GET for reading all series */
    $router->get('/series', function() use($db) {
        $series_list = get_series($db);
        echo json_encode($series_list);
    });

    /* GET for reading individual series */
    $router->get('/series/(\d+)', function($id) use($db) {
        $series_info = get_series_info($db, $id);
        echo json_encode($series_info);
    });

    /* DELETE for deleting individual series */
    $router->delete('/series/(\d+)', function($id) use($db) {
        remove_series($db, $id);
        echo json_encode('Series succesfully deleted');
    });

    /* POST for adding series */
    $router->post('/series', function() use($db) {
        $series_info = $_POST;
        add_series($db, $series_info);
        echo json_encode('Series succesfully added');
    });

    /* PUT for adding series */
    $router->put('/series/(/d+)', function($id) use($db) {
        $_PUT = array();
        parse_str(file_get_contents('php://input'), $_PUT);
        $serie_info = $_PUT + ["serie_id" => $id];
        update_series($db, $serie_info);
        echo json_encode('Series succesfully updated');
    });


});

/* 404 Error route */
$router->set404(function() {
    header('HTTP/1.1 404 Not Found');
    echo json_encode(['Page not found.']);
});

/* Run the router */
$router->run();
