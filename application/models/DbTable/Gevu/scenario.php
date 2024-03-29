<?php
/**
 * Ce fichier contient la classe Gevu_scenario.
 *
 * @copyright  2008 Gabriel Malkas
 * @copyright  2010 Samuel Szoniecky
 * @license    "New" BSD License
*/


/**
 * Classe ORM qui représente la table 'gevu_scenario'.
 *
 * @copyright  2010 Samuel Szoniecky
 * @license    "New" BSD License
 */
class Models_DbTable_Gevu_scenario extends Zend_Db_Table_Abstract
{
    
    /*
     * Identifiant du droit lié aux scénarios
     */
    protected $idDroit = 4;
	
	/*
     * Nom de la table.
     */
    protected $_name = 'gevu_scenario';
    
    /*
     * Clef primaire de la table.
     */
    protected $_primary = 'id_scenario';

    /**
     * tableau d'export
     */
    var $arrExport;
    
    /**
     * scenario XML
     */
    var $xmlScene;
    
    /**
     * Vérifie si une entrée Gevu_scenario existe.
     *
     * @param array $data
     *
     * @return integer
     */
    public function existe($data)
    {
		$select = $this->select();
		$select->from($this, array('id_scenario'));
		foreach($data as $k=>$v){
			$select->where($k.' = ?', $v);
		}
	    $rows = $this->fetchAll($select);        
	    if($rows->count()>0)$id=$rows[0]->id_scenario; else $id=false;
        return $id;
    } 
        
    /**
     * Ajoute une entrée Gevu_scenario.
     *
     * @param array $data
     * @param boolean $existe
     * @param boolean $ajoutScene
     *  
     * @return integer
     */
    public function ajouter($data, $existe=false, $ajoutScene=false)
    {    	
    	$id=false;
    	if($existe)$id = $this->existe($data);
    	if(!$id){
    		$data['maj'] = new Zend_Db_Expr('NOW()');
    	 	$id = $this->insert($data);
    	}
    	//mise à jour des droits de scénario
    	$this->editDroits();
    	
    	//ajoute la scène
    	if($ajoutScene){
    		$dbScene = new Models_DbTable_Gevu_scenes();
    		$idScn = $dbScene->ajouter(array("id_scenario"=>$id,"type"=>"arboCtl"));
    		//met à jour les params
    		$this->edit($id, array("params"=>$idScn));
    	}
    	
    	return $id;
    } 
           
    /**
     * Selectionne tous les scénarios pour mettre à jour les droits disponibles
     *
     * @return void
     */
    public function editDroits()
    {
        $query = $this->select()
                    ->from( array("gevu_scenario" => "gevu_scenario"),array("id"=>"id_scenario","lib"));
        $query->order("lib");

        $arr = $this->fetchAll($query)->toArray();
		$json = Zend_Json::encode($arr);
		
		$dbD = new Models_DbTable_Gevu_droits();
		$dbD->edit($this->idDroit, array("params"=>$json));

    }
    
    /**
     * Recherche une entrée Gevu_scenario avec la clef primaire spécifiée
     * et modifie cette entrée avec les nouvelles données.
     *
     * @param integer $id
     * @param array $data
     *
     * @return void
     */
    public function edit($id, $data)
    {
    	$data['maj'] = new Zend_Db_Expr('NOW()');        
        $this->update($data, 'gevu_scenario.id_scenario = ' . $id);
		//vérifie s'il faut faire la mise à jour des droits de scénario
        if(isset($data['lib']))$this->editDroits();
        
    }
    
    /**
     * Recherche une entrée Gevu_scenario avec la clef primaire spécifiée
     * et supprime cette entrée.
     *
     * @param integer $id
     *
     * @return integer
     */
    public function remove($id)
    {
    	$dbScene = new Models_DbTable_Gevu_scenes;
    	$dbScene->removeScenario($id);
        $this->delete('gevu_scenario.id_scenario = ' . $id);
    	
        //mise à jour des droits de scénario
    	$this->editDroits();
        
        return -1;
        
    }
    
    /**
     * Récupère toutes les entrées Gevu_scenario avec certains critères
     * de tri, intervalles
     */
    public function getAll($order=null, $limit=0, $from=0)
    {
        $query = $this->select()
                    ->from( array("gevu_scenario" => "gevu_scenario") );
                    
        if($order != null)
        {
            $query->order($order);
        }

        if($limit != 0)
        {
            $query->limit($limit, $from);
        }

        return $this->fetchAll($query)->toArray();
    }
  
    /*
     * Recherche une entrée Gevu_scenario avec la valeur spécifiée
     * et retourne cette entrée.
     *
     * @param int $id_scenario
     */
    public function findById_scenario($id_scenario)
    {
        $query = $this->select()
                    ->from( array("g" => "gevu_scenario") )                           
                    ->where( "g.id_scenario = ?", $id_scenario );

        return $this->fetchAll($query)->toArray(); 
    }
    
	 /*
     * Recherche l'entrée Gevu_exisxdroits pour un utilisateur et un droit
     * et retourne les paramètres de cette entrée.
     *
     * @param int $idExi
     * 
     * @return array
     */
    public function findByExiDroit($idExi)
    {
	    $dbED = new Models_DbTable_Gevu_exisxdroits();
    	$arr = $dbED->findByExiDroit($idExi, $this->idDroit);

    	$arr = Zend_Json::decode($arr[0]['params']);
    	
        return $arr; 

    }
    
    
    /*
     * Recherche une entrée Gevu_scenario avec la valeur spécifiée
     * et retourne cette entrée.
     *
     * @param varchar $lib
     */
    public function findByLib($lib)
    {
        $query = $this->select()
                    ->from( array("g" => "gevu_scenario") )                           
                    ->where( "g.lib = ?", $lib );

        return $this->fetchAll($query)->toArray(); 
    }
    /*
     * Recherche une entrée Gevu_scenario avec la valeur spécifiée
     * et retourne cette entrée.
     *
     * @param datetime $maj
     */
    public function findByMaj($maj)
    {
        $query = $this->select()
                    ->from( array("g" => "gevu_scenario") )                           
                    ->where( "g.maj = ?", $maj );

        return $this->fetchAll($query)->toArray(); 
    }
    /**
     * Recherche une entrée Gevu_scenario avec la valeur spécifiée
     * et retourne cette entrée.
     *
     * @param longtext $params
     */
    public function findByParams($params)
    {
        $query = $this->select()
                    ->from( array("g" => "gevu_scenario") )                           
                    ->where( "g.params = ?", $params );

        return $this->fetchAll($query)->toArray(); 
    }
    
    /**
     * Retourne l'xml des scènes pour une entrée Gevu_scenario
     *
     * @param int $idScenario
     * 
     * return SimpleXMLElement
     */
    public function getXmlScene($idScenario)
    {
    	//récupère l'arboressence des scenes
        $query = $this->select()
                ->setIntegrityCheck(false) //pour pouvoir sÃ©lectionner des colonnes dans une autre table
            ->from(array('so' => 'gevu_scenario'))
            ->joinInner(array('se' => 'gevu_scenes'),
                'se.id_scene = so.params', array("id_scene", "type", "paramsCtrl", "id_scenario"))
            ->where('so.id_scenario ='.$idScenario);    	
        $result = $this->fetchAll($query)->toArray();
        $params = json_decode($result[0]["paramsCtrl"]);
		$this->xmlScene = simplexml_load_string($params[0]->idCritSE);

    	return $this->xmlScene;
    }
    
    /**
     * Exporte les données d'une entrée Gevu_scenario
     * et retourne les valeurs.
     *
     * @param int $idScenario
     * 
     * return array
     */
    public function exporteScenarProduits($idScenario)
    {
    	//récupère l'arboressence des scenes
    	$this->getXmlScene($idScenario);

    	$dbScene = new Models_DbTable_Gevu_scenes();
    	$dbProduit = new Models_DbTable_Gevu_produits();
    	 
    	$arr = $dbScene->getScenarProduits($idScenario);
    	$this->arrExport = array();
    	 
    	foreach ($arr as $n){
    		$arrType = explode("_",$n['type']);
    		//récupère le noeud et ses parents
    		$path = "//node[@uid='".$arrType[2]."']/ancestor-or-self::node";
    		$result = $this->xmlScene->xpath($path);
    		//construction du tableau des produits
    		$ariane = "";
    		$nbA = count($result);
    		foreach ($result as $k => $node) {
    			//$ariane .= $k."(".$node["idCtrl"]."):".$node["lib"];
    			$ariane .= $node["lib"];
    			if($k == ($nbA-1)){
    				$obj = json_decode($n['paramsProd']);
    				//ajoute les produits
    				foreach ($obj as $o) {
    					$arrInterv = $dbProduit->getProduitInterv($o->id_produit);
    					foreach ($arrInterv as $int) {
    						$this->arrExport[] = array("idScenario"=>$idScenario,"idScene"=>$n['id_scene'],"ariane"=>$ariane,"uid"=>$node["uid"].""
    							, "idProduit"=>$int["id_produit"], "ref"=>$int["ref"], "description"=>$int["description"], "id_interv"=>$int["id_interv"], "interv"=>$int["interv"]
    							, "lblInterv"=>$int["lblInterv"], "unite"=>$int["unite"], "lblUnite"=>$int["lblUnite"], "frequence"=>$int["frequence"], "cout"=>$int["cout"]
    						);    						
    					}    							
    				}    				
    				$ariane = "";    				
    			}else{
    				$ariane .= " _ ";    				
    			}
    		}
    	}
    	    	    
    	return $this->arrExport;
    }
    
}
