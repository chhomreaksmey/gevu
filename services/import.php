<?php
try {
	require_once( "../application/configs/config.php" );
	$application->bootstrap();
	
	$imp = new GEVU_Import();
	//$imp->addScenario("C:/wamp/www/gevu/param/scenarisation.xml");
	//$imp->addScenario("C:/wamp/www/gevu/param/logementV3.xml");
	
	//echo $imp->traiteImportLogement(2, 2, 1);
	
	$imp->addDoc($_REQUEST);

}catch (Zend_Exception $e) {
	echo "Récupère exception: " . get_class($e) . "\n";
    echo "Message: " . $e->getMessage() . "\n";
}
