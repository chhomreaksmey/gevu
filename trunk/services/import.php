<?php
require_once( "../param/ParamAppli.php" );

$imp = new GEVU_Import();
//$imp->addScenario("C:/wamp/www/gevu/param/scenarisation.xml");
//$imp->addScenario("C:/wamp/www/gevu/param/logementV3.xml");


$imp->addDoc($_REQUEST);
$dataDoc = array(
	"url"=>"url","titre"=>"name","content_type"=>"text/csv"
    ,"path_source"=>"path_source"
    ,"tronc"=>'objName'
    );			        
$imp->saveDoc($_REQUEST, $dataDoc);

//$imp->traiteDoc(10);

?>