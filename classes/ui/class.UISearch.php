<?php
/**
 * Genera form di ricerca riutilizzabili
 *
 * @access public
 * @author Lorenzo Monaco/Marco Lucidi
 * @package UserInterface
 */
 
 /* user defined includes */  

 /* user defined constants */


require_once("ui/class.UIObjects.php");

class UISearch extends UIForm {
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
    * Gestisce una generica form di ricerca.
    *
    * @access public
    *
	* @param string $xmlContainer, path del file xml descrittore della pagina.
    * @param int $item, determina l'occorrenza della form da printare all'interno del descrittore XML.
    * @param object $db connessione al database
    *
    * @return string
    */
    public function printSearchModule($xmlContainer, $connect){
    	
    	$page=XMLParser::getXMLSchema($xmlContainer);
    	$searchFormXML=XMLParser::getNode($page,'searchModule', 0);				
    	$fieldsXML=XMLParser::getNode($searchFormXML, 'fields', 0);
    	
		if(XMLParser::getNodeAttribute($searchFormXML, 'active')==1){	
	    	$formTable= "<br><form ".XMLParser::getNodeAttribute($searchFormXML, 'attr').">\n";
		    $formTable.= "<table ".XMLParser::getChildNodeAttribute($searchFormXML, 'tableSearch', 'attr').">\n";	
		    foreach (XMLParser::getChildNodes($fieldsXML) as $field){
			    if(XMLParser::getChildNodeValue($field, 'fieldView')==1){
			    	$formTable.= "<tr>";
			    	$formTable.= "<td valign=\"top\" class=\"internal\"><b>".XMLParser::getChildNodeValue($field, 'label')."</b></td>\n";
				    switch(XMLParser::getNodeAttribute($field, 'type')){
				    			
			    		case "select":
			    			//$formTable.="<td><select name=\"".XMLParser::getNodeAttribute($field, 'searchName')."\" ".XMLParser::getNodeAttribute($field, 'attr').">";
			    			$formTable.= $this->getComboSearch($connect, $field, "");
			    			//$formTable.= "</select></td>";
			    		break;
			    			
			    		case "text":
			    			$formTable.="<td class=\"internal\"><input type=\"text\" name=\"".XMLParser::getNodeAttribute($field, 'searchName')."\" ".XMLParser::getNodeAttribute($field, 'attr')."> ";
			    		break;	
			    	}
			    			$formTable.= "</tr>\n";
		    	}
		    }
			if(XMLParser::getChildNodeValue($searchFormXML, 'puntualSearch')==1){
				$formTable.= "<tr>
					<td valign=\"top\" class=\"internal\"><b>Ricerca puntuale</b></td>\n
					<td>
						<input type=\"checkbox\" name=\"ric_puntuale\" value=\"1\">
					</td>
				</tr>\n";
			}
			$imgSearch=URL_PATH.TEMPLATE_DIR.TEMPLATES_DIR.TEMPLATE."/img/view.jpg";	
	    	$formTable.= "<tr><td colspan=\"2\" align=\"right\" valign=\"top\" class=\"internal\"><hr noshade size=\"1\">Avvia ricerca&nbsp;<button type=\"Submit\" class=\"pulsantImg\" ".$imgSearch."><img src=\"".$imgSearch."\" border=\"0\" align=\"center\" title=\"Avvia la ricerca\"></button></td></tr>\n";
	    	$formTable.= "<tr><td colspan=\"2\" align=\"left\"><input type=\"hidden\" name=\"act\" value=\"S\">";
	    	$formTable.= "";
	    	$formTable.= "</td></tr>";
	    	$formTable.= "</table>"; 
	    	$formTable.= "</form>\n";
	    	return $formTable;
		}
    }
	
    /** 
	* Crea una combo box per il modulo di ricerca
	*
	* @access public
	*
	* @param object $db connessione al database
	* @param DOMElement $field riferimento all'elemento select del descrittore xml della pagina.
	* @param $val valore della combobox.
	*
	* @return string
	*/
	protected function getComboSearch($db, $field, $selected){
		$xmlParser=new XMLParser();
		if($xmlParser->getChildNodeAttribute($field, 'optionForDB', 'active')==1){	
			$selectDB=$xmlParser->getChildNode($field, 'selectDB');
			$select="SELECT ".$xmlParser->getNodeAttribute($selectDB, 'optionValue').", ".$xmlParser->getNodeAttribute($selectDB, 'optionVis')." 
					FROM ".$xmlParser->getNodeAttribute($selectDB, 'tableName')." " 
					.$xmlParser->getNodeAttribute($selectDB, 'condition');		
			$query= DBInterface::execQuery($db, $select, "", 0);
			$str="";
			$str.="<td><select name=\"".$xmlParser->getNodeAttribute($field, 'searchName')."\" ".$xmlParser->getNodeAttribute($field, 'attr').">";
			$str.="<option value=\"\"></option>";
			foreach ($query as $key=>$value){
				if($value[$xmlParser->getNodeAttribute($selectDB, 'optionValue')]==$selected AND $value[$xmlParser->getNodeAttribute($selectDB, 'optionValue')]!=""){
					$sel="selected";
				}else{
					$sel="";
				}
				$str.="<option value=\"".$value[$xmlParser->getNodeAttribute($selectDB, 'optionValue')]."\" ".$sel.">".$value[$xmlParser->getNodeAttribute($selectDB, 'optionVis')]."</option>";
			}
			$str.= "</select></td>";
			return $str;
		}else{
			$str="";
			$str.="<td><select name=\"".$xmlParser->getNodeAttribute($field, 'searchName')."\" ".$xmlParser->getNodeAttribute($field, 'attr').">";
			$str.="<option value=\"\"></option>";
			$options=$xmlParser->getChildNode($field, 'options');
			foreach ($xmlParser->getChildNodes($options) as $option){
				$str.="<option value=\"".$xmlParser->getNodeAttribute($option, 'value')."\" ".$xmlParser->getNodeAttribute($option, 'attr').">".$xmlParser->getNodeValue($option)."</option>";
			}
			$str.= "</select></td>";
			return $str;			
		}	
	}
    
    
   /**
    * Gestisce la generazione delle condizioni di ricerca richimando il metodo getSearchCondition ed aggiungendo la clausula WHERE
    *
    * @access public
    *
	* @param string $xmlContainer, path del file xml descrittore della pagina.
    * @param array $data dati passati dalla form di ricerca.
    *
    * @return string
    */
	public function getSearchConditionWithWHERE($XMLcontainer, $data){
		$condition=" WHERE ";
		$condition.= $this->getSearchCondition($XMLcontainer, $data, 0);
		if($condition==" WHERE "){
			$condition="";
		}
		return $condition;
	}
 
	/**
    * Gestisce la generazione delle condizioni di ricerca richimando il metodo getSearchCondition (non agigunge la clausula WHERE)    
    *
    * @access public
    *
	* @param string $xmlContainer, path del file xml descrittore della pagina.
    * @param array $data dati passati dalla form di ricerca.
    *
    * @return string
    */
	public function getSearchConditionWithoutWHERE($XMLcontainer, $data){
		$condition=$this->getSearchCondition($XMLcontainer, $data, 1);
		return $condition;
	}
	 
    /**
    * Gestisce la generazione delle condizioni di ricerca
    *
    * @access private
    *
	* @param string $xmlContainer, path del file xml descrittore della pagina.
    * @param array $data dati passati dalla form di ricerca.
    * @param int $ANDPosition posizione dell'operatore AND nella clausula di ricerca (1, all'inizio 0,  alla fine)
    *
    * @return string
    */
	public function getSearchCondition($XMLcontainer, $data, $ANDPosition){
		
    	$xmlSchema=XMLParser::getXMLSchema($XMLcontainer);
		$searchModule=XMLParser::getNode($xmlSchema, 'searchModule', 0);
		$fields=XMLParser::getChildNode($searchModule, 'fields');
		if(isset($data['ric_puntuale']) AND $data['ric_puntuale']==1){
			foreach (XMLParser::getChildNodes($fields) as $field){
				if(XMLParser::getChildNodeValue($field, 'fieldProcess')==1){
					if($ANDPosition==0){
						if(!empty($data[XMLParser::getNodeAttribute($field, 'searchFormName')])){
							$condition.=XMLParser::getNodeAttribute($field, 'searchDbName')."='".$data[XMLParser::getNodeAttribute($field, 'searchFormName')]."'";
							$condition.=" AND ";
						}
					}else{
						if(!empty($data[XMLParser::getNodeAttribute($field, 'searchFormName')])){
							$condition.=" AND ";
							$condition.=XMLParser::getNodeAttribute($field, 'searchDbName')."='".$data[XMLParser::getNodeAttribute($field, 'searchFormName')]."'";
						}

					}
				}
			}
		}else{
			foreach (XMLParser::getChildNodes($fields) as $field){
				if(XMLParser::getChildNodeValue($field, 'fieldProcess')==1){
					if($ANDPosition==0){
						if(!empty($data[XMLParser::getNodeAttribute($field, 'searchFormName')])){
							$condition.=XMLParser::getNodeAttribute($field, 'searchDbName')." LIKE '%".$data[XMLParser::getNodeAttribute($field, 'searchFormName')]."%'";
							$condition.=" AND ";
						}
					}else{
						if(!empty($data[XMLParser::getNodeAttribute($field, 'searchFormName')])){
							$condition.=" AND ";
							$condition.=XMLParser::getNodeAttribute($field, 'searchDbName')." LIKE '%".$data[XMLParser::getNodeAttribute($field, 'searchFormName')]."%'";
						}

					}
				}
			}
		}
		if(!empty($condition) AND $ANDPosition==0){
			$condition=substr($condition, 0, strlen($condition)-5);	
		}
		return $condition;
	}

}//end of class UISearch
?>
