<?php

use MVC\Server\Route;

return array(
    new Route(Route::$validMethods, '/', 'InstallerModule\\Controller\\InstallerController::index', 'installer_index'),
    new Route(Route::$validMethods, '/execute/[*:command]', 'InstallerModule\\Controller\\InstallerController::execute', 'installer_execute'),
);