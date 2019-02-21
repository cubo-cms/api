<?php
namespace Cubo;

// Added to allow debugging
if(true || isset($_GET['debug'])) {
	error_reporting(E_ALL);
	ini_set('display_errors',1);
	
	define('DEBUG',true);

	// Shows variable
	function show(&$var,$terminate = true) {
		echo "<pre>";
		print_r($var);
		echo "</pre>";
		$terminate && die("Application terminated");
	}
}

// Auto-start Cubo framework
require_once($_SERVER['DOCUMENT_ROOT'].DIRECTORY_SEPARATOR.'.autoload.php');
?>