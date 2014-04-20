<?php
require('app/core/autoloader.php');

//define routes
Router::get('', 'welcome@index');
Router::get('admin', 'admin@admin');
Router::get('admin/login', 'admin@login');
Router::post('admin/login', 'admin@login');
Router::get('admin/logout', 'admin@logout');

//if no route found
Router::error('error@index');

//execute matched routes
Router::dispatch();
ob_flush();