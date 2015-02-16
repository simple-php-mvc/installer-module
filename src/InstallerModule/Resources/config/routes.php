<?php

use MVC\Server\Route;

return array(
    new Route(Route::$validMethods, '/', 'InstallerModule\\Controller\\InstallerController::index', 'installer_index')
    
);