<?php
date_default_timezone_set('Europe/Paris');
setlocale (LC_TIME, 'fr_FR.utf8','fra'); 
ini_set("memory_limit",'1600M');
$www = "/Applications/XAMPP/xamppfiles/htdocs";
//$www = "C:/wamp/www";
define ("WEB_ROOT","http://localhost/gevu");
define ("ROOT_PATH",$www."/gevu");
define ("WEB_ROOT_AJAX",WEB_ROOT."/public");
define ("SEP_PATH","/");

//code de sécurité pour l'administation
define ("CODE_ADMIN","simple");


// Define path to application directory
defined('APPLICATION_PATH')
    || define('APPLICATION_PATH', ROOT_PATH . '/application');

// Define application environment
defined('APPLICATION_ENV')
    || define('APPLICATION_ENV', (getenv('APPLICATION_ENV') ? getenv('APPLICATION_ENV') : 'production'));


set_include_path(ROOT_PATH.'/library/php');       
set_include_path(get_include_path().PATH_SEPARATOR.$www."/Zend/library");
set_include_path(get_include_path().PATH_SEPARATOR.$www."/Zend/extras/library");

//ajout de librairie supplémentaires
require_once(ROOT_PATH.'/library/php/odtphp/odf.php');

require_once '/Applications/XAMPP/xamppfiles/htdocs/Zend/library/Zend/Application.php';

// Create application, bootstrap, and run
$application = new Zend_Application(
    APPLICATION_ENV,
    APPLICATION_PATH . '/configs/application.ini'
);

$application->bootstrap();

?>