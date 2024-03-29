<?php
/**
 * Ce fichier contient la classe Gevu_rapports.
 *

 * @copyright  2010 Samuel Szoniecky
 * @license    "New" BSD License
*/


/**
 * Classe ORM qui représente la table 'gevu_rapports'.
 *

 * @copyright  2010 Samuel Szoniecky
 * @license    "New" BSD License
 */
class Models_DbTable_Gevu_rapports extends Zend_Db_Table_Abstract
{
    
    /*
     * Nom de la table.
     */
    protected $_name = 'gevu_rapports';
    
    /*
     * Clef primaire de la table.
     */
    protected $_primary = 'id_rapport';

    
    /**
     * Vérifie si une entrée Gevu_rapports existe.
     *
     * @param array $data
     *
     * @return integer
     */
    public function existe($data)
    {
		$select = $this->select();
		$select->from($this, array('id_rapport'));
		foreach($data as $k=>$v){
			$select->where($k.' = ?', $v);
		}
	    $rows = $this->fetchAll($select);        
	    if($rows->count()>0)$id=$rows[0]->id_rapport; else $id=false;
        return $id;
    } 
        
    /**
     * Ajoute une entrée Gevu_rapports.
     *
     * @param array $data
     * @param boolean $existe
     *  
     * @return integer
     */
    public function ajouter($data, $existe=false)
    {
    	$id=false;
    	if($existe)$id = $this->existe($data);
    	if(!$id){
    		$data['maj'] = new Zend_Db_Expr('NOW()');
    	 	$id = $this->insert($data);
    	}
    	return $id;
    } 
           
    /**
     * Recherche une entrée Gevu_rapports avec la clef primaire spécifiée
     * et modifie cette entrée avec les nouvelles données.
     *
     * @param integer $id
     * @param array $data
     *
     * @return void
     */
    public function edit($id, $data)
    {        
        $this->update($data, 'gevu_rapports.id_rapport = ' . $id);
    }
    
    /**
     * Recherche une entrée Gevu_rapports avec la clef primaire spécifiée
     * et supprime cette entrée.
     *
     * @param integer $id
     *
     * @return void
     */
    public function remove($id)
    {
        $this->delete('gevu_rapports.id_rapport = ' . $id);
    }
    
    /**
     * Récupère toutes les entrées Gevu_rapports avec certains critères
     * de tri, intervalles
     */
    public function getAll($order=null, $limit=0, $from=0)
    {
        $query = $this->select()
                    ->from( array("gevu_rapports" => "gevu_rapports") );
                    
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
     * Recherche une entrée Gevu_rapports avec la valeur spécifiée
     * et retourne cette entrée.
     *
     * @param int $id_rapport
     */
    public function findById_rapport($id_rapport)
    {
        $query = $this->select()
                    ->from( array("g" => "gevu_rapports") )                           
                    ->where( "g.id_rapport = ?", $id_rapport );

        return $this->fetchAll($query)->toArray(); 
    }
    /*
     * Recherche une entrée Gevu_rapports avec la valeur spécifiée
     * et retourne cette entrée.
     *
     * @param int $id_lieu
     */
    public function findByIdLieu($id_lieu)
    {
        $query = $this->select()
            ->setIntegrityCheck(false) //pour pouvoir sélectionner des colonnes dans une autre table
        	->from( array("r" => "gevu_rapports") )                           
            	->joinInner(array('dr' => 'gevu_docsxrapports'),
                	'dr.id_rapport = r.id_rapport',array())
            	->joinInner(array('d' => 'gevu_docs'),
                	'd.id_doc = dr.id_doc',array("url","id_doc","titre","content_type"))
            ->where( "r.id_lieu = ?", $id_lieu );

        return $this->fetchAll($query)->toArray(); 
    }
    /*
     * Recherche une entrée Gevu_rapports avec la valeur spécifiée
     * et retourne cette entrée.
     *
     * @param int $id_exi
     */
    public function findById_exi($id_exi)
    {
        $query = $this->select()
                    ->from( array("g" => "gevu_rapports") )                           
                    ->where( "g.id_exi = ?", $id_exi );

        return $this->fetchAll($query)->toArray(); 
    }
    /*
     * Recherche une entrée Gevu_rapports avec la valeur spécifiée
     * et retourne cette entrée.
     *
     * @param datetime $maj
     */
    public function findByMaj($maj)
    {
        $query = $this->select()
                    ->from( array("g" => "gevu_rapports") )                           
                    ->where( "g.maj = ?", $maj );

        return $this->fetchAll($query)->toArray(); 
    }
    /*
     * Recherche une entrée Gevu_rapports avec la valeur spécifiée
     * et retourne cette entrée.
     *
     * @param text $selection
     */
    public function findBySelection($selection)
    {
        $query = $this->select()
                    ->from( array("g" => "gevu_rapports") )                           
                    ->where( "g.selection = ?", $selection );

        return $this->fetchAll($query)->toArray(); 
    }
    
    
}
