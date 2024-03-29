<?php
/**
 * Ce fichier contient la classe Gevu_batiments.
 *
 * @copyright  2010 Samuel Szoniecky
 * @license    "New" BSD License
*/


/**
 * Classe ORM qui représente la table 'gevu_batiments'.
 *
 * @copyright  2010 Samuel Szoniecky
 * @license    "New" BSD License
 */
class Models_DbTable_Gevu_batiments extends Zend_Db_Table_Abstract
{
    
    /*
     * Nom de la table.
     */
    protected $_name = 'gevu_batiments';
    
    /*
     * Clef primaire de la table.
     */
    protected $_primary = 'id_batiment';

    protected $_referenceMap    = array(
        'Lieux' => array(
            'columns'           => 'id_lieu',
            'refTableClass'     => 'Models_DbTable_Gevu_lieux',
            'refColumns'        => 'id_lieu'
        )
    );	
    
    /**
     * Vérifie si une entrée Gevu_batiments existe.
     *
     * @param array $data
     *
     * @return integer
     */
    public function existe($data)
    {
		$select = $this->select();
		$select->from($this, array('id_batiment'));
		foreach($data as $k=>$v){
			if($k != "maj" && $v)
				$select->where($k.' = ?', $v);
		}
	    $rows = $this->fetchAll($select);        
	    if($rows->count()>0)$id=$rows[0]->id_batiment; else $id=false;
        return $id;
    } 
        
    /**
     * Ajoute une entrée Gevu_batiments.
     *
     * @param array $data
     * @param boolean $existe
     *  
     * @return integer
     */
    public function ajouter($data, $existe=true)
    {
    	$id=false;
    	if($existe)$id = $this->existe($data);
    	if(!$id){
    	 	$id = $this->insert($data);
    	}
    	return $id;
    } 

    /**
     * Récupère ou Ajoute une entrée Gevu_groupe avec le lieu associé.
     *
     * @param string $ref
     * @param int $idInst
     * @param int $idLieuParent
     * @param array $data
     * @param string $idBase
     *  
     * @return integer
     */
    public function getByRef($ref, $idInst, $idLieuParent, $data=false, $idBase=false)
    {    	
		//vérification de l'existence de l'antenne
	    $arr = $this->findByRef($ref);
	    if(count($arr)==0){
	    	if(!$data)$data["lib"]="".$ref;
			$diag = new GEVU_Diagnostique();
	    	$idLieu = $diag->ajoutLieu($idLieuParent, -1, $idBase, $data["lib"], true, false, array("id_type_controle"=>45));
		    $data["id_lieu"]=$idLieu;
		    $data["id_instant"]=$idInst;
		    $data["ref"]=$ref;
		    //vérifie s'il faut créer le contact
		    if($data["contact_gardien"] && !is_int($data["contact_gardien"])){
		    	$dbC = new Models_DbTable_Gevu_contacts($this->_db);
		    	$data["contact_gardien"] = $dbC->ajouter(array("nom"=>$data["contact_gardien"]));
		    }
		    //vérifie si la date de construction est valide
		    if($data["date_achevement"] && strlen($data["date_achevement"])==4)
		    	$data["date_achevement"]=$data["date_achevement"]."-01-01";
		    $this->ajouter(array("id_lieu"=>$idLieu, "id_instant"=>$idInst, "ref"=> $ref));
		    $arr = $this->findByRef($ref);
	    }
    	return $arr[0];
    } 
    
    
    /**
     * Recherche une entrée Gevu_batiments avec la clef primaire spécifiée
     * et modifie cette entrée avec les nouvelles données.
     *
     * @param integer $id
     * @param array $data
     *
     * @return void
     */
    public function edit($id, $data)
    {        
        $this->update($data, 'gevu_batiments.id_batiment = ' . $id);
    }
    
    /**
     * Recherche une entrée Gevu_batiments avec la clef primaire spécifiée
     * et supprime cette entrée.
     *
     * @param integer $id
     *
     * @return void
     */
    public function remove($id)
    {
        $this->delete('gevu_batiments.id_batiment = ' . $id);
    }

    /**
     * Recherche les entrées de Gevu_batiments avec la clef de lieu
     * et supprime ces entrées.
     *
     * @param integer $idLieu
     *
     * @return void
     */
    public function removeLieu($idLieu)
    {
        $this->delete('gevu_batiments.id_lieu = ' . $idLieu);
    }
    
    /**
     * Récupère toutes les entrées Gevu_batiments avec certains critères
     * de tri, intervalles
     */
    public function getAll($order=null, $limit=0, $from=0)
    {
        $query = $this->select()
                    ->from( array("gevu_batiments" => "gevu_batiments") );
                    
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
     * Recherche une entrée Gevu_batiments avec la valeur spécifiée
     * et retourne cette entrée.
     *
     * @param int $id_batiment
     */
    public function findById_batiment($id_batiment)
    {
        $query = $this->select()
                    ->from( array("g" => "gevu_batiments") )                           
                    ->where( "g.id_batiment = ?", $id_batiment );

        return $this->fetchAll($query)->toArray(); 
    }
    /*
     * Recherche une entrée Gevu_batiments avec la valeur spécifiée
     * et retourne cette entrée.
     *
     * @param int $id_lieu
     */
    public function findById_lieu($id_lieu)
    {
        $query = $this->select()
                    ->from( array("g" => "gevu_batiments") )                           
                    ->where( "g.id_lieu = ?", $id_lieu );

        return $this->fetchAll($query)->toArray(); 
    }
    /*
     * Recherche une entrée Gevu_batiments avec la valeur spécifiée
     * et retourne cette entrée.
     *
     * @param int $id_instant
     */
    public function findById_instant($id_instant)
    {
        $query = $this->select()
                    ->from( array("g" => "gevu_batiments") )                           
                    ->where( "g.id_instant = ?", $id_instant );

        return $this->fetchAll($query)->toArray(); 
    }
    /*
     * Recherche une entrée Gevu_batiments avec la valeur spécifiée
     * et retourne cette entrée.
     *
     * @param varchar $nom
     */
    public function findByNom($nom)
    {
        $query = $this->select()
                    ->from( array("g" => "gevu_batiments") )                           
                    ->where( "g.nom = ?", $nom );

        return $this->fetchAll($query)->toArray(); 
    }
    /*
     * Recherche une entrée Gevu_batiments avec la valeur spécifiée
     * et retourne cette entrée.
     *
     * @param varchar $ref
     */
    public function findByRef($ref)
    {
        $query = $this->select()
                    ->from( array("g" => "gevu_batiments") )                           
                    ->where( "g.ref = ?", $ref );

        return $this->fetchAll($query)->toArray(); 
    }
    /*
     * Recherche une entrée Gevu_batiments avec la valeur spécifiée
     * et retourne cette entrée.
     *
     * @param varchar $adresse
     */
    public function findByAdresse($adresse)
    {
        $query = $this->select()
                    ->from( array("g" => "gevu_batiments") )                           
                    ->where( "g.adresse = ?", $adresse );

        return $this->fetchAll($query)->toArray(); 
    }
    /*
     * Recherche une entrée Gevu_batiments avec la valeur spécifiée
     * et retourne cette entrée.
     *
     * @param varchar $commune
     */
    public function findByCommune($commune)
    {
        $query = $this->select()
                    ->from( array("g" => "gevu_batiments") )                           
                    ->where( "g.commune = ?", $commune );

        return $this->fetchAll($query)->toArray(); 
    }
    /*
     * Recherche une entrée Gevu_batiments avec la valeur spécifiée
     * et retourne cette entrée.
     *
     * @param varchar $pays
     */
    public function findByPays($pays)
    {
        $query = $this->select()
                    ->from( array("g" => "gevu_batiments") )                           
                    ->where( "g.pays = ?", $pays );

        return $this->fetchAll($query)->toArray(); 
    }
    /*
     * Recherche une entrée Gevu_batiments avec la valeur spécifiée
     * et retourne cette entrée.
     *
     * @param varchar $code_postal
     */
    public function findByCode_postal($code_postal)
    {
        $query = $this->select()
                    ->from( array("g" => "gevu_batiments") )                           
                    ->where( "g.code_postal = ?", $code_postal );

        return $this->fetchAll($query)->toArray(); 
    }
    /*
     * Recherche une entrée Gevu_batiments avec la valeur spécifiée
     * et retourne cette entrée.
     *
     * @param varchar $contact_proprietaire
     */
    public function findByContact_proprietaire($contact_proprietaire)
    {
        $query = $this->select()
                    ->from( array("g" => "gevu_batiments") )                           
                    ->where( "g.contact_proprietaire = ?", $contact_proprietaire );

        return $this->fetchAll($query)->toArray(); 
    }
    /*
     * Recherche une entrée Gevu_batiments avec la valeur spécifiée
     * et retourne cette entrée.
     *
     * @param varchar $contact_delegataire
     */
    public function findByContact_delegataire($contact_delegataire)
    {
        $query = $this->select()
                    ->from( array("g" => "gevu_batiments") )                           
                    ->where( "g.contact_delegataire = ?", $contact_delegataire );

        return $this->fetchAll($query)->toArray(); 
    }
    /*
     * Recherche une entrée Gevu_batiments avec la valeur spécifiée
     * et retourne cette entrée.
     *
     * @param varchar $contact_gardien
     */
    public function findByContact_gardien($contact_gardien)
    {
        $query = $this->select()
                    ->from( array("g" => "gevu_batiments") )                           
                    ->where( "g.contact_gardien = ?", $contact_gardien );

        return $this->fetchAll($query)->toArray(); 
    }
    /*
     * Recherche une entrée Gevu_batiments avec la valeur spécifiée
     * et retourne cette entrée.
     *
     * @param varchar $horaires_gardien
     */
    public function findByHoraires_gardien($horaires_gardien)
    {
        $query = $this->select()
                    ->from( array("g" => "gevu_batiments") )                           
                    ->where( "g.horaires_gardien = ?", $horaires_gardien );

        return $this->fetchAll($query)->toArray(); 
    }
    /*
     * Recherche une entrée Gevu_batiments avec la valeur spécifiée
     * et retourne cette entrée.
     *
     * @param varchar $horaires_batiment
     */
    public function findByHoraires_batiment($horaires_batiment)
    {
        $query = $this->select()
                    ->from( array("g" => "gevu_batiments") )                           
                    ->where( "g.horaires_batiment = ?", $horaires_batiment );

        return $this->fetchAll($query)->toArray(); 
    }
    /*
     * Recherche une entrée Gevu_batiments avec la valeur spécifiée
     * et retourne cette entrée.
     *
     * @param int $superficie_parcelle
     */
    public function findBySuperficie_parcelle($superficie_parcelle)
    {
        $query = $this->select()
                    ->from( array("g" => "gevu_batiments") )                           
                    ->where( "g.superficie_parcelle = ?", $superficie_parcelle );

        return $this->fetchAll($query)->toArray(); 
    }
    /*
     * Recherche une entrée Gevu_batiments avec la valeur spécifiée
     * et retourne cette entrée.
     *
     * @param int $superficie_batiment
     */
    public function findBySuperficie_batiment($superficie_batiment)
    {
        $query = $this->select()
                    ->from( array("g" => "gevu_batiments") )                           
                    ->where( "g.superficie_batiment = ?", $superficie_batiment );

        return $this->fetchAll($query)->toArray(); 
    }
    /*
     * Recherche une entrée Gevu_batiments avec la valeur spécifiée
     * et retourne cette entrée.
     *
     * @param date $date_achevement
     */
    public function findByDate_achevement($date_achevement)
    {
        $query = $this->select()
                    ->from( array("g" => "gevu_batiments") )                           
                    ->where( "g.date_achevement = ?", $date_achevement );

        return $this->fetchAll($query)->toArray(); 
    }
    /*
     * Recherche une entrée Gevu_batiments avec la valeur spécifiée
     * et retourne cette entrée.
     *
     * @param date $date_depot_permis
     */
    public function findByDate_depot_permis($date_depot_permis)
    {
        $query = $this->select()
                    ->from( array("g" => "gevu_batiments") )                           
                    ->where( "g.date_depot_permis = ?", $date_depot_permis );

        return $this->fetchAll($query)->toArray(); 
    }
    /*
     * Recherche une entrée Gevu_batiments avec la valeur spécifiée
     * et retourne cette entrée.
     *
     * @param date $date_reha
     */
    public function findByDate_reha($date_reha)
    {
        $query = $this->select()
                    ->from( array("g" => "gevu_batiments") )                           
                    ->where( "g.date_reha = ?", $date_reha );

        return $this->fetchAll($query)->toArray(); 
    }
    /*
     * Recherche une entrée Gevu_batiments avec la valeur spécifiée
     * et retourne cette entrée.
     *
     * @param varchar $reponse_1
     */
    public function findByReponse_1($reponse_1)
    {
        $query = $this->select()
                    ->from( array("g" => "gevu_batiments") )                           
                    ->where( "g.reponse_1 = ?", $reponse_1 );

        return $this->fetchAll($query)->toArray(); 
    }
    /*
     * Recherche une entrée Gevu_batiments avec la valeur spécifiée
     * et retourne cette entrée.
     *
     * @param varchar $reponse_2
     */
    public function findByReponse_2($reponse_2)
    {
        $query = $this->select()
                    ->from( array("g" => "gevu_batiments") )                           
                    ->where( "g.reponse_2 = ?", $reponse_2 );

        return $this->fetchAll($query)->toArray(); 
    }
    /*
     * Recherche une entrée Gevu_batiments avec la valeur spécifiée
     * et retourne cette entrée.
     *
     * @param varchar $reponse_3
     */
    public function findByReponse_3($reponse_3)
    {
        $query = $this->select()
                    ->from( array("g" => "gevu_batiments") )                           
                    ->where( "g.reponse_3 = ?", $reponse_3 );

        return $this->fetchAll($query)->toArray(); 
    }
    /*
     * Recherche une entrée Gevu_batiments avec la valeur spécifiée
     * et retourne cette entrée.
     *
     * @param varchar $reponse_4
     */
    public function findByReponse_4($reponse_4)
    {
        $query = $this->select()
                    ->from( array("g" => "gevu_batiments") )                           
                    ->where( "g.reponse_4 = ?", $reponse_4 );

        return $this->fetchAll($query)->toArray(); 
    }
    /*
     * Recherche une entrée Gevu_batiments avec la valeur spécifiée
     * et retourne cette entrée.
     *
     * @param varchar $reponse_5
     */
    public function findByReponse_5($reponse_5)
    {
        $query = $this->select()
                    ->from( array("g" => "gevu_batiments") )                           
                    ->where( "g.reponse_5 = ?", $reponse_5 );

        return $this->fetchAll($query)->toArray(); 
    }
    /*
     * Recherche une entrée Gevu_batiments avec la valeur spécifiée
     * et retourne cette entrée.
     *
     * @param varchar $reponse_6
     */
    public function findByReponse_6($reponse_6)
    {
        $query = $this->select()
                    ->from( array("g" => "gevu_batiments") )                           
                    ->where( "g.reponse_6 = ?", $reponse_6 );

        return $this->fetchAll($query)->toArray(); 
    }
    /*
     * Recherche une entrée Gevu_batiments avec la valeur spécifiée
     * et retourne cette entrée.
     *
     * @param varchar $reponse_7
     */
    public function findByReponse_7($reponse_7)
    {
        $query = $this->select()
                    ->from( array("g" => "gevu_batiments") )                           
                    ->where( "g.reponse_7 = ?", $reponse_7 );

        return $this->fetchAll($query)->toArray(); 
    }
    /*
     * Recherche une entrée Gevu_batiments avec la valeur spécifiée
     * et retourne cette entrée.
     *
     * @param varchar $reponse_8
     */
    public function findByReponse_8($reponse_8)
    {
        $query = $this->select()
                    ->from( array("g" => "gevu_batiments") )                           
                    ->where( "g.reponse_8 = ?", $reponse_8 );

        return $this->fetchAll($query)->toArray(); 
    }
    /*
     * Recherche une entrée Gevu_batiments avec la valeur spécifiée
     * et retourne cette entrée.
     *
     * @param varchar $reponse_9
     */
    public function findByReponse_9($reponse_9)
    {
        $query = $this->select()
                    ->from( array("g" => "gevu_batiments") )                           
                    ->where( "g.reponse_9 = ?", $reponse_9 );

        return $this->fetchAll($query)->toArray(); 
    }
    /*
     * Recherche une entrée Gevu_batiments avec la valeur spécifiée
     * et retourne cette entrée.
     *
     * @param varchar $reponse_10
     */
    public function findByReponse_10($reponse_10)
    {
        $query = $this->select()
                    ->from( array("g" => "gevu_batiments") )                           
                    ->where( "g.reponse_10 = ?", $reponse_10 );

        return $this->fetchAll($query)->toArray(); 
    }
    /*
     * Recherche une entrée Gevu_batiments avec la valeur spécifiée
     * et retourne cette entrée.
     *
     * @param varchar $reponse_11
     */
    public function findByReponse_11($reponse_11)
    {
        $query = $this->select()
                    ->from( array("g" => "gevu_batiments") )                           
                    ->where( "g.reponse_11 = ?", $reponse_11 );

        return $this->fetchAll($query)->toArray(); 
    }
    /*
     * Recherche une entrée Gevu_batiments avec la valeur spécifiée
     * et retourne cette entrée.
     *
     * @param varchar $reponse_12
     */
    public function findByReponse_12($reponse_12)
    {
        $query = $this->select()
                    ->from( array("g" => "gevu_batiments") )                           
                    ->where( "g.reponse_12 = ?", $reponse_12 );

        return $this->fetchAll($query)->toArray(); 
    }
    /*
     * Recherche une entrée Gevu_batiments avec la valeur spécifiée
     * et retourne cette entrée.
     *
     * @param varchar $reponse_13
     */
    public function findByReponse_13($reponse_13)
    {
        $query = $this->select()
                    ->from( array("g" => "gevu_batiments") )                           
                    ->where( "g.reponse_13 = ?", $reponse_13 );

        return $this->fetchAll($query)->toArray(); 
    }
    /*
     * Recherche une entrée Gevu_batiments avec la valeur spécifiée
     * et retourne cette entrée.
     *
     * @param varchar $reponse_14
     */
    public function findByReponse_14($reponse_14)
    {
        $query = $this->select()
                    ->from( array("g" => "gevu_batiments") )                           
                    ->where( "g.reponse_14 = ?", $reponse_14 );

        return $this->fetchAll($query)->toArray(); 
    }
    /*
     * Recherche une entrée Gevu_batiments avec la valeur spécifiée
     * et retourne cette entrée.
     *
     * @param varchar $reponse_15
     */
    public function findByReponse_15($reponse_15)
    {
        $query = $this->select()
                    ->from( array("g" => "gevu_batiments") )                           
                    ->where( "g.reponse_15 = ?", $reponse_15 );

        return $this->fetchAll($query)->toArray(); 
    }
    /*
     * Recherche une entrée Gevu_batiments avec la valeur spécifiée
     * et retourne cette entrée.
     *
     * @param int $id_donnee
     */
    public function findById_donnee($id_donnee)
    {
        $query = $this->select()
                    ->from( array("g" => "gevu_batiments") )                           
                    ->where( "g.id_donnee = ?", $id_donnee );

        return $this->fetchAll($query)->toArray(); 
    }
    /*
     * Recherche une entrée Gevu_batiments avec la valeur spécifiée
     * et retourne cette entrée.
     *
     * @param datetime $maj
     */
    public function findByMaj($maj)
    {
        $query = $this->select()
                    ->from( array("g" => "gevu_batiments") )                           
                    ->where( "g.maj = ?", $maj );

        return $this->fetchAll($query)->toArray(); 
    }

    
}
