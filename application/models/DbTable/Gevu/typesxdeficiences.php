<?php
/**
 * Ce fichier contient la classe Gevu_typesxdeficiences.
 *

 * @copyright  2010 Samuel Szoniecky
 * @license    "New" BSD License
*/


/**
 * Classe ORM qui représente la table 'gevu_typesxdeficiences'.
 *

 * @copyright  2010 Samuel Szoniecky
 * @license    "New" BSD License
 */
class Models_DbTable_Gevu_typesxdeficiences extends Zend_Db_Table_Abstract
{
    
    /*
     * Nom de la table.
     */
    protected $_name = 'gevu_typesxdeficiences';
    
    /*
     * Clef primaire de la table.
     */
    protected $_primary = 'id_type_deficience';

    
    /**
     * Vérifie si une entrée Gevu_typesxdeficiences existe.
     *
     * @param array $data
     *
     * @return integer
     */
    public function existe($data)
    {
		$select = $this->select();
		$select->from($this, array('id_type_deficience'));
		foreach($data as $k=>$v){
			$select->where($k.' = ?', $v);
		}
	    $rows = $this->fetchAll($select);        
	    if($rows->count()>0)$id=$rows[0]->id_type_deficience; else $id=false;
        return $id;
    } 
        
    /**
     * Ajoute une entrée Gevu_typesxdeficiences.
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
     * Recherche une entrée Gevu_typesxdeficiences avec la clef primaire spécifiée
     * et modifie cette entrée avec les nouvelles données.
     *
     * @param integer $id
     * @param array $data
     *
     * @return void
     */
    public function edit($id, $data)
    {        
        $this->update($data, 'gevu_typesxdeficiences.id_type_deficience = ' . $id);
    }
    
    /**
     * Recherche une entrée Gevu_typesxdeficiences avec la clef primaire spécifiée
     * et supprime cette entrée.
     *
     * @param integer $id
     *
     * @return void
     */
    public function remove($id)
    {
        $this->delete('gevu_typesxdeficiences.id_type_deficience = ' . $id);
    }
    
    /**
     * Récupère toutes les entrées Gevu_typesxdeficiences avec certains critères
     * de tri, intervalles
     */
    public function getAll($order=null, $limit=0, $from=0)
    {
        $query = $this->select()
                    ->from( array("gevu_typesxdeficiences" => "gevu_typesxdeficiences") );
                    
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

    /**
     * Récupère les spécifications des colonnes Gevu_typesxdeficiences 
     */
    public function getCols(){

    	$arr = array("cols"=>array(
    	   	array("titre"=>"id_type_deficience","champ"=>"id_type_deficience","visible"=>true),
    	array("titre"=>"lib","champ"=>"lib","visible"=>true),
        	
    		));    	
    	return $arr;
		
    }     
    
    /*
     * Recherche une entrée Gevu_typesxdeficiences avec la valeur spécifiée
     * et retourne cette entrée.
     *
     * @param int $id_type_deficience
     */
    public function findById_type_deficience($id_type_deficience)
    {
        $query = $this->select()
                    ->from( array("g" => "gevu_typesxdeficiences") )                           
                    ->where( "g.id_type_deficience = " . $id_type_deficience );

        return $this->fetchRow($query)->toArray(); 
    }
    /*
     * Recherche une entrée Gevu_typesxdeficiences avec la valeur spécifiée
     * et retourne cette entrée.
     *
     * @param varchar $lib
     */
    public function findByLib($lib)
    {
        $query = $this->select()
                    ->from( array("g" => "gevu_typesxdeficiences") )                           
                    ->where( "g.lib = " . $lib );

        return $this->fetchRow($query)->toArray(); 
    }
    
    
}
