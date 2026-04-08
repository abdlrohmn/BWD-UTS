<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
$routes->get('/', 'Auth::index');
$routes->post('/auth/process', 'Auth::process');
$routes->get('/auth/logout', 'Auth::logout');
$routes->get('/dashboard', 'Dashboard::index', ['filter' => 'auth']);
