<?php
    ## DEFINE JWT ALGORITHM & KEY	
    define("ALGO", "HS256");
    define("KEY", "1cc49918411d96d2d0224940db9bca239fb01b3d");
	
    ## DEFINE USER ROLES
    define("ROLE_USER", "User");
    define("ROLE_ADMINISTRATOR", "Administrator");
	
    ## DEFINE PROJECT_ROOT PATH
    define("PROJECT_ROOT", $_SERVER["DOCUMENT_ROOT"]);
	
	## DEFINE UPLOAD DIRECTORY
    define("DOCUMENT_BASE_PATH", $_SERVER["DOCUMENT_ROOT"] . DIRECTORY_SEPARATOR . "uploads" . DIRECTORY_SEPARATOR . "documents" . DIRECTORY_SEPARATOR);
    define("DOCUMENT_BASE_URL", "http://apis.shadowforms.com/uploads/documents/");
	
    function autoloadHelpers($class_name) {
        $file_name = PROJECT_ROOT . DIRECTORY_SEPARATOR . 'helpers' . DIRECTORY_SEPARATOR . $class_name . '.php';
        if (is_readable($file_name)) {
            require $file_name;
        }
    }
	
    function autoloadUtilities($class_name) {
        $file_name = PROJECT_ROOT . DIRECTORY_SEPARATOR . 'utilities' . DIRECTORY_SEPARATOR . $class_name . '.php';
        if (is_readable($file_name)) {
            require $file_name;
        }
    }
	
    function autoloadInterfaces($class_name) {
        $file_name = PROJECT_ROOT . DIRECTORY_SEPARATOR . 'interfaces' . DIRECTORY_SEPARATOR . $class_name . '.php';
        if (is_readable($file_name)) {
            require $file_name;
        }
    }
	
    function autoloadControllers($class_name) {
        $file_name = PROJECT_ROOT . DIRECTORY_SEPARATOR . 'controllers' . DIRECTORY_SEPARATOR . $class_name . '.php';
        if (is_readable($file_name)) {
            require $file_name;
        }
    }
	
    function autoloadMiddlewares($class_name) {
        $file_name = PROJECT_ROOT . DIRECTORY_SEPARATOR . 'middlewares' . DIRECTORY_SEPARATOR . $class_name . '.php';
        if (is_readable($file_name)) {
            require $file_name;
        }
    }
	
    spl_autoload_register("autoloadHelpers");
    spl_autoload_register("autoloadUtilities");
    spl_autoload_register("autoloadInterfaces");
    spl_autoload_register("autoloadControllers");
    spl_autoload_register("autoloadMiddlewares");
?>
