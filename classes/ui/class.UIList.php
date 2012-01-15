<?
/**
 * Genera liste html riutilizzabili
 *
 * @access public
 * @author Lorenzo Monaco/Marco Lucidi
 * @package UserInterface
 */
 
 /* user defined includes */  

 /* user defined constants */


require_once("ui/class.UIObjects.php");

class UIList extends UIObjects {
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
    * Gestisce una generica form di visualizzazione.
    *
    * @access public
    *
    * @param string $xmlContainer, path del file xml descrittore della pagina.
    * @param int $item, determina l'occorrenza della form da printare all'interno del descrittore XML.
    * @param array $data, array di dati da inserire nella form di modifica.
    * @param string $recordIdName nome della chiave esterna per il reperimento dei dati del record 
    * @param $recordIdValue valore della chiave esterna.
    *
    * @return string
    */
    public function printList($xmlContainer, $item, $data){
    	$xmlParser=new XMLParser();
    	$page=$xmlParser->getXMLSchema($xmlContainer);
    	if($tableXML=$xmlParser->getNode($page,'list', $item)){
	    	$tableXML=$xmlParser->getNode($page,'list', $item);				
	    	$fieldsXML= $xmlParser->getNode($tableXML, 'fields', 0);
	    	
	    	$tdColor= "";
	    	$list= "<table ".$xmlParser->getChildNodeAttribute($tableXML, 'tableList', 'attr').">\n";	
		    	foreach ($xmlParser->getChildNodes($fieldsXML) as $field){
					if($xmlParser->getChildNodeValue($field, 'fieldView')==1){
						if($xmlParser->getChildNodeValue($tableXML, 'alternativeColor')==1){
							if($tdColor=="tbl_c1"){ $tdColor= "tbl_c2"; }else{ $tdColor= "tbl_c1"; }
						}else{
							$tdColor="internal";
						}
						
						switch ($xmlParser->getNodeAttribute($field, 'typeView')) {
							
							case "img":
								$pathImg= GENERAL_FILE_STORE."images/".$data[$xmlParser->getNodeAttribute($field, 'dataName')];
								if(is_file($pathImg)){		
									$list.= "<tr>";
							    	$list.= "<td valign=\"center\" class=\"".$tdColor."\" width=\"".$xmlParser->getChildNodeValue($field, 'fieldTblWidth')."\"><b>".$xmlParser->getChildNodeValue($field, 'label')."</b></td>\n";
							    	$list.="<td class=\"".$tdColor."\"><img src=\"".GENERAL_FILE_STORE_URL."images/".$data[$xmlParser->getNodeAttribute($field, 'dataName')]."\"></td>";
							    	$list.= "</tr>\n";
								}else{
									$list.= "<tr>";
							    	$list.= "<td valign=\"center\" class=\"".$tdColor."\" width=\"".$xmlParser->getChildNodeValue($field, 'fieldTblWidth')."\"><b>".$xmlParser->getChildNodeValue($field, 'label')."</b></td>\n";
							    	$list.="<td class=\"".$tdColor."\">[no image]</td>";
							    	$list.= "</tr>\n";
								}	
							break;
							
							case "data":
								$list.= "<tr>";
						    	$list.= "<td valign=\"top\" class=\"".$tdColor."\" width=\"".$xmlParser->getChildNodeValue($field, 'fieldTblWidth')."\"><b>".$xmlParser->getChildNodeValue($field, 'label')."</b></td>\n";
						    	$list.="<td class=\"".$tdColor."\">".UserInterface::getDate($data[$xmlParser->getNodeAttribute($field, 'dataName')], '-')."</td>";
						    	$list.= "</tr>\n";
							break;
							
							case "label":
								$list.= "<tr>";
						    	$list.= "<td colspan=\"2\" align=\"left\" valign=\"top\" class=\"".$tdColor."\" width=\"".$xmlParser->getChildNodeValue($field, 'fieldTblWidth')."\"><b><u>".$xmlParser->getChildNodeValue($field, 'label')."</u></b></td>\n";
						    	$list.= "</tr>\n";
							break;
							
							case "hr":
								$list.= "<tr>";
						    	$list.= "<td colspan=\"2\" valign=\"top\" class=\"".$tdColor."\" width=\"".$xmlParser->getChildNodeValue($field, 'fieldTblWidth')."\"><hr ".$xmlParser->getNodeAttribute($field, 'attr')."></td>\n";
						    	$list.= "</tr>\n";
							break;
							
							default:
								$list.= "<tr>";
						    	$list.= "<td valign=\"top\" class=\"".$tdColor."\" width=\"".$xmlParser->getChildNodeValue($field, 'fieldTblWidth')."\"><b>".$xmlParser->getChildNodeValue($field, 'label')."</b></td>\n";
						    	$list.="<td class=\"".$tdColor."\">".$data[$xmlParser->getNodeAttribute($field, 'dataName')]."</td>";
						    	$list.= "</tr>\n";
							break;
						}
					}
		    	}
	    	$list.= "</table>"; 	
	    	return $list;
    	}else{
    		return "";	
    	}
    }

}// end of class "UIList"
?>