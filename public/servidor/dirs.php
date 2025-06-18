<?php
// Definir la ruta base del proyecto (dos niveles arriba desde 'servidor')
define('BASE_PATH', realpath(__DIR__ . '/..') . DIRECTORY_SEPARATOR);

// Rutas absolutas a las carpetas 'clases' y 'servidor'
define('CLASS_PATH', BASE_PATH . 'clases' . DIRECTORY_SEPARATOR);
define('SERVER_PATH', BASE_PATH . 'servidor' . DIRECTORY_SEPARATOR);
