<?php
/**
 * Definisce i metodi per il parsing di un file xml.
 *
 * @access public
 * @author Lorenzo Monaco / Marco Lucidi
 */
 
 /* user defined includes */  

 /* user defined constants */


class XMLParser {
	// --- ATTRIBUTES ---

	// --- OPERATIONS ---
	
	/**
	* Costruttore della classe.
	*
	* @return void
	*/
	public function __construct(){
		
	}
	
	/**
    * Gestisce una generica form di inserimento
    *
    * @access public
    *
   	* @param string $xmlContainer, path del file xml descrittore della pagina.
    * @param int $item, determina l'occorrenza della form da printare all'interno del descrittore XML.
    * @param object $db connessione al database
    *
    * @return string
    */
    public function getXMLSchema($xmlContainer){
    	// Xml container parse
	    $xmlSchema = new domDocument;
		$xmlSchema->preserveWhiteSpace = false;
		$xmlSchema->load($xmlContainer);
		return $xmlSchema;
    }
    /**
    * retituisce un nodo del file xml
    *
    * @access public
    *
   	* @param DOMDocument $DOMDocument.
    * @param string $name, nome del nodo
    * @param int $item
    *
    * @return string
    */
    public function getNode($DOMDocument, $name, $item){
    	if($DOMDocument->getElementsByTagName($name)->item($item) instanceof DOMElement){
    		return $DOMDocument->getElementsByTagName($name)->item($item);
    	}else{
    		return false;
    	}
    }
    
    /**
    * retituisce il valore di un nodo del file xml
    *
    * @access public
    *
   	* @param DOMDocument $DOMDocument.
    * @param string $name, nome del nodo
    * @param int $item
    *
    * @return string
    */
    public function getDocumentNodeValue($DOMDocument, $name, $item){
    	if($DOMDocument->getElementsByTagName($name)->item($item) instanceof DOMElement){
    		return trim($DOMDocument->getElementsByTagName($name)->item($item)->nodeValue);
    	}else{
    		return false;
    	}
    }
    
    /**
    * retituisce il valore di un attributo di un nodo del file xml
    *
    * @access public
    *
   	* @param DOMDocument $DOMDocument.
    * @param string $name, nome del nodo
    * @param int $item
    * @param string $attrName.
    *
    * @return string
    */
    public function getDocumentNodeAttribute($DOMDocument, $name, $item, $attrName){
    	if($DOMDocument->getElementsByTagName($name)->item($item) instanceof DOMElement){
    		return trim($DOMDocument->getElementsByTagName($name)->item($item)->getAttribute($attrName));
    	}else{
    		return false;
    	}
    }
    
    /**
    * retituisce il valore di un nodo del file xml accettando come parametro il nodo stesso.
    *
    * @access public
    *
   	* @param DOMElement $DOMElement.
    *
    * @return string
    */
    public function getNodeValue($DOMElement){
    	if($DOMElement instanceof DOMElement){
    		return trim($DOMElement->nodeValue);
    	}else{
    		return false;
    	}
    }
    
    /**
    * retituisce il valore dell'attributo di un nodo del file xml accettando come parametro il nodo stesso.
    *
    * @access public
    *
   	* @param DOMElement $DOMElement.
   	* @param string $attrName nome dell'attributo.
    *
    * @return string
    */
    public function getNodeAttribute($DOMElement, $attrName){
    	if($DOMElement instanceof DOMElement){
    		return trim($DOMElement->getAttribute($attrName));
    	}else{
    		return false;
    	}
    }
    
    /**
    * retituisce il nodo figlio di un nodo del file xml
    *
    * @access public
    *
   	* @param DOMElement $DOMElement.
   	* @param string $childName nome del figlio.
    *
    * @return string
    */
    public function getChildNode($DOMElement, $childName){
    	if($DOMElement->getElementsByTagName($childName)->item(0) instanceof DOMElement){
    		return $DOMElement->getElementsByTagName($childName)->item(0);
    	}else{
    		return false;
    	}	
    }
    
    /**
    * retituisce il valore del nodo figlio di un nodo del file xml
    *
    * @access public
    *
   	* @param DOMElement $DOMElement.
   	* @param string $childName nome del figlio.
    *
    * @return string
    */
    public function getChildNodeValue($DOMElement, $childName){
    	if($DOMElement instanceof DOMElement ){
    		return trim($DOMElement->getElementsByTagName($childName)->item(0)->nodeValue);
    	}else{
    		return false;
    	}
    }
    
    /**
    * retituisce il valore dell'attibuto del  nodo figlio di un nodo del file xml
    *
    * @access public
    *
   	* @param DOMElement $DOMElement.
   	* @param string $childName nome del figlio.
   	* @param string $attrName nome dell'attributo.
    *
    * @return string
    */
    public function getChildNodeAttribute($DOMElement, $childName, $attrName){
    	if($DOMElement->getElementsByTagName($childName)->item(0) instanceof DOMElement ){
    		return trim($DOMElement->getElementsByTagName($childName)->item(0)->getAttribute($attrName));
    	}else{
    		return false;
    	}
    }
    
    /**
    * retituisce i nodi figlii di un nodo del file xml
    *
    * @access public
    *
   	* @param DOMElement $DOMElement.
    *
    * @return string
    */
    public function getChildNodes($DOMElement){
    	return $DOMElement->childNodes;
    }

}//end of class "class.XMLParser.php" 	
?>