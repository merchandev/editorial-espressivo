<?php
require_once('../../../wp-load.php');
if ( class_exists('Google\Site_Kit\Plugin') ) {
    echo "Site Kit is installed.\n";
    // List some classes or endpoints
    $rest_server = rest_get_server();
    $routes = $rest_server->get_routes();
    foreach($routes as $route => $handlers) {
        if (strpos($route, 'google-site-kit') !== false) {
            echo $route . "\n";
        }
    }
} else {
    echo "Site Kit not found.\n";
}
