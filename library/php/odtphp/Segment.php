<?php
require 'SegmentIterator.php';
class SegmentException extends Exception
{}
/**
 * Class for handling templating segments with odt files
 * You need PHP 5.2 at least
 * You need Zip Extension or PclZip library
 * Encoding : ISO-8859-1
 * Last commit by $Author: neveldo $
 * Date - $Date: 2009-06-17 12:12:59 +0200 (mer., 17 juin 2009) $
 * SVN Revision - $Rev: 44 $
 * Id : $Id: Segment.php 44 2009-06-17 10:12:59Z neveldo $
 *
 * @copyright  GPL License 2008 - Julien Pauli - Cyril PIERRE de GEYER - Anaska (http://www.anaska.com)
 * @license    http://www.gnu.org/copyleft/gpl.html  GPL License
 * @version 1.3
 */
class Segment implements IteratorAggregate, Countable
{
    protected $xml;
    protected $xmlParsed = '';
    protected $name;
    protected $children = array();
    protected $vars = array();
	protected $images = array();
	protected $odf;
	protected $file;
    /**
     * Constructor
     *
     * @param string $name name of the segment to construct
     * @param string $xml XML tree of the segment
     */
    public function __construct($name, $xml, $odf)
    {
        $this->name = (string) $name;
        $this->xml = (string) $xml;
		$this->odf = $odf;
        $zipHandler = $this->odf->getConfig('ZIP_PROXY');
        $this->file = new $zipHandler();	
        $this->_analyseChildren($this->xml);
    }
    /**
     * Returns the name of the segment
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }
    /**
     * Does the segment have children ?
     *
     * @return bool
     */
    public function hasChildren()
    {
        return $this->getIterator()->hasChildren();
    }
    /**
     * Countable interface
     *
     * @return int
     */
    public function count()
    {
        return count($this->children);
    }
    /**
     * IteratorAggregate interface
     *
     * @return Iterator
     */
    public function getIterator()
    {
        return new RecursiveIteratorIterator(new SegmentIterator($this->children), 1);
    }
    /**
     * Replace variables of the template in the XML code
     * All the children are also called
     *
     * @return string
     */
    public function merge()
    {
        $this->xmlParsed .= str_replace(array_keys($this->vars), array_values($this->vars), $this->xml);
        if ($this->hasChildren()) {
            foreach ($this->children as $child) {
                $this->xmlParsed = str_replace($child->xml, ($child->xmlParsed=="")?$child->merge():$child->xmlParsed, $this->xmlParsed);
                $child->xmlParsed = '';
            }
        }
        $reg = "/\[!--\sBEGIN\s$this->name\s--\](.*)\[!--\sEND\s$this->name\s--\]/sm";
        $this->xmlParsed = preg_replace($reg, '$1', $this->xmlParsed);
        $this->file->open($this->odf->getTmpfile());
        foreach ($this->images as $imageKey => $imageValue) {
			if ($this->file->getFromName('Pictures/' . $imageValue) === false) {
				$this->file->addFile($imageKey, 'Pictures/' . $imageValue);
			}
            $this->odf->addImageToManifest($imageValue);
        }
        $this->file->close();		
        return $this->xmlParsed;
    }
    /**
     * Analyse the XML code in order to find children
     *
     * @param string $xml
     * @return Segment
     */
    protected function _analyseChildren($xml)
    {
        // $reg2 = "#\[!--\sBEGIN\s([\S]*)\s--\](?:<\/text:p>)?(.*)(?:<text:p\s.*>)?\[!--\sEND\s(\\1)\s--\]#sm";
        $reg2 = "#\[!--\sBEGIN\s([\S]*)\s--\](.*)\[!--\sEND\s(\\1)\s--\]#sm";
        preg_match_all($reg2, $xml, $matches);
        for ($i = 0, $size = count($matches[0]); $i < $size; $i++) {
            if ($matches[1][$i] != $this->name) {
                $this->children[$matches[1][$i]] = new self($matches[1][$i], $matches[0][$i], $this->odf);
            } else {
                $this->_analyseChildren($matches[2][$i]);
            }
        }
        return $this;
    }
    /**
     * Assign a template variable to replace
     *
     * @param string $key
     * @param string $value
     * @throws SegmentException
     * @return Segment
     */
    public function setVars($key, $value, $encode = true, $charset = 'UTF-8')
    {
        if (strpos($this->xml, $this->odf->getConfig('DELIMITER_LEFT') . $key . $this->odf->getConfig('DELIMITER_RIGHT')) === false) {
            throw new SegmentException("var $key not found in {$this->getName()}");
        }
        if($this->odf->oSite)$this->odf->oSite->trace($key." setVars 1");
        
        $value = $encode ? htmlspecialchars($value) : $value;
        if($this->odf->oSite)$this->odf->oSite->trace($key." setVars 2");
        
        $value = ($charset == 'ISO-8859') ? utf8_encode($value) : $value;
        if($this->odf->oSite)$this->odf->oSite->trace($key." setVars 3");
        
        $this->vars[$this->odf->getConfig('DELIMITER_LEFT') . $key . $this->odf->getConfig('DELIMITER_RIGHT')] = str_replace("\n", "<text:line-break/>", $value);
        if($this->odf->oSite)$this->odf->oSite->trace($key." setVars 4");
        
        return $this;
    }
    /**
     * Assign a template variable as a picture
     *
     * @param string $key name of the variable within the template
     * @param string $value path to the picture
     * @throws OdfException
     * @return Segment
     */
    public function setImage($key, $value, $width=0, $height=0)
    {

        $filename = strtok(strrchr($value, '/'), '/.');
        $file = substr(strrchr($value, '/'), 1);
        //modif samszo pour optimiser l'ajout multiple d'une même image
        if(!isset($this->odf->imagesVerif[$file])){
	        $size = @getimagesize($value);
	        if ($size === false) {
	            throw new SegmentException("Invalid image");
	        }
	        if (($width==0)&&($height==0)){
	            list ($width, $height) = $size;
	            $width *= Odf::PIXEL_TO_CM;
	            $height *= Odf::PIXEL_TO_CM;
	        } else {
	            list ($owidth, $oheight) = $size;
	            if (($width > 0) && ($height == 0)){
	                $height = $width * ($oheight/$owidth);
	            }
	            if (($width == 0) && ($height > 0)){
	                $width = $height * ($owidth/$oheight);
	            }
	            /*Remove this section if no GD/temp directory
	            $widthp = round($width / Odf::PIXEL_TO_CM, 0);
	            $heightp = round($height / Odf::PIXEL_TO_CM, 0);
	            $save = $yourtempdirectory . date("Y-m-d_H-i-s") . rand() . '.jpg';
	            $tn = imagecreatetruecolor($widthp, $heightp) ;   
	            $image = imagecreatefromjpeg($value);
	            imagecopyresampled($tn, $image, 0, 0, 0, 0, $widthp, $heightp, $owidth, $oheight) ;
	            imagejpeg($tn, $save, 100);
	            $value = $save;
	            $filename = strtok(strrchr($value, '/'), '/.');
	            $file = substr(strrchr($value, '/'), 1);
	           Remove to here*/
	        }
	       
	        $xml = <<<IMG
<draw:frame draw:style-name="fr1" draw:name="$filename" text:anchor-type="as-char" svg:width="{$width}cm" svg:height="{$height}cm" draw:z-index="3"><draw:image xlink:href="Pictures/$file" xlink:type="simple" xlink:show="embed" xlink:actuate="onLoad"/></draw:frame>
IMG;
			$this->odf->imagesVerif[$file] = $xml;
	    	$this->odf->images[$value] = $file;
        	if($this->odf->oSite)$this->odf->oSite->trace($key.":".$file." size");
        }else{
        	$xml = $this->odf->imagesVerif[$file];
        	if($this->odf->oSite)$this->odf->oSite->trace($key.":".$file." direct");	 
        }	         
        $this->setVars($key, $xml, false);
        return $this;
        
    
    }	
    /**
     * Shortcut to retrieve a child
     *
     * @param string $prop
     * @return Segment
     * @throws SegmentException
     */
    public function __get($prop)
    {
        if (array_key_exists($prop, $this->children)) {
            return $this->children[$prop];
        } else {
            throw new SegmentException('child ' . $prop . ' does not exist');
        }
    }
    /**
     * Proxy for setVars
     *
     * @param string $meth
     * @param array $args
     * @return Segment
     */
    public function __call($meth, $args)
    {
        try {
            return $this->setVars($meth, $args[0]);
        } catch (SegmentException $e) {
            throw new SegmentException("method $meth nor var $meth exist");
        }
    }
    /**
     * Returns the parsed XML
     *
     * @return string
     */
    public function getXmlParsed()
    {
        return $this->xmlParsed;
    }
}

?>