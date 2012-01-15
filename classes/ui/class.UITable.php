<?php
/**
 * Genera oggetti html riutilizzabili
 *
 * @access public
 * @author Lorenzo Monaco / Marco Lucidi
 * @package UserInterface 
 */
 /* user defined includes */  

 /* user defined constants */


require_once("ui/class.UIObjects.php");

class UITable extends UIObjects {
	// --- ATTRIBUTES ---

	// --- OPERATIONS ---
	
	/**
	* @return void
	* @param string # ######
	*/
	public function __construct(){
		
	}
	
	/**
    * Produce l'output html per una tabella generica e per una tabella con azioni a partire dal 
    * descrittore XML utilizzando i metodi printTableWithActions e printTableWithoutActions 
    *
    * @access public
    *
    * @param string $xmlContainer, path del file xml descrittore della pagina.
    * @param array $dataObj, array di dati da printare nella tabella.
    * @param int $item, determina l'occorrenza della tabella da printare all'interno del descrittore XML.
    * @param int $groupId gruppo di appartenenza dell'utente.
    *
    * @return string
    */
    public function printTable($xmlContainer, $dataObj, $item, $groupId, $connect){	
    	$xmlParser=new XMLParser();
    	$xmlschema=$xmlParser->getXMLSchema($xmlContainer);
		$page=$xmlParser->getNode($xmlschema, 'page', $item);
	    $pageName=$xmlParser->getDocumentNodeAttribute($xmlschema, 'page', $item, 'name');
	    $tableXML=$xmlParser->getNode($xmlschema, 'table', $item);
		
		if($xmlParser->getChildNodeAttribute($tableXML, 'actions', 'active')==1){
			$retValue=$this->printTableWithActions($xmlParser, $page, $tableXML, $dataObj, $pageName, $groupId, $connect);
			return $retValue;
		}else{
			$retValue=$this->printTableWithoutActions($xmlParser, $tableXML, $dataObj);
			return $retValue;
		}		
    }
    
    
	/**
    * Produce l'output html per una tabella con azioni a partire dal descrittore XML.
    *
    * @access private
    *
    * @param DOMElement $tableXML, tabella del file xml descrittore della pagina.
    * @param array $dataObj, array di dati da printare nella tabella.
    * @param string $page, attibuto name del tag xml page del descrittore della pagina.
    *
    * @return string
    */
    private function printTableWithActions($xmlParser, $page, $tableXML, $dataObj, $pageName, $groupId, $connect){
    		
    	$fieldsXML=$xmlParser->getNode($tableXML, 'fields', 0);
    	$tdColor= "";
    	$retObj= "";
    	$retHead= "";
    	$retValue= "";
		
		// ****************************** Table data ******************************************************************
		$countRecord=0;
		foreach($dataObj as $values){
			// Alt color table
			if($xmlParser->getChildNodeValue($tableXML, 'alternativeColor')==1){
				if($tdColor=="tbl_c1"){ $tdColor= "tbl_c2"; }else{ $tdColor= "tbl_c1"; }
			}
			// Content
			$retObj.= "<tr>\n";
			
			// Delete, arch checkbox
			if($xmlParser->getChildNodeAttribute($tableXML, 'groupedActions', 'active')==1){
				$retObj.= "<td width=\"10\"><input type=\"checkbox\" name=\"recId_".$values[$xmlParser->getChildNodeValue($page, 'foreignKey')]."\" value=\"1\" class=\"textform\" title=\"Seleziona il record\"></td>\n";
			}
			
			// Data
			foreach($xmlParser->getChildNodes($fieldsXML) as $field){
				if($xmlParser->getChildNodeValue($field, 'fieldTblView')==1){
					switch ($xmlParser->getNodeAttribute($field, 'typeView')) {
						case "data":
						$retObj.= "<td class=\"".$tdColor."\">".UserInterface::getDate($values[$xmlParser->getNodeAttribute($field, 'dataName')], '-')."</td>\n";
						break;
						
						case "status":
						$dataStatus=UserInterface::getStatus($connect, $values[$xmlParser->getNodeAttribute($field, 'dataName')], $xmlParser->getNodeAttribute($field, 'statusArea'));
						$retObj.= "<td class=\"".$tdColor."\"> &nbsp;<font color=\"".$dataStatus[1]."\"><b>".$dataStatus[0]."</b></font></td>\n";
						break;
						
						default:
						$retObj.= "<td class=\"".$tdColor."\">".$values[$xmlParser->getNodeAttribute($field, 'dataName')]."</td>\n";
						break;
					}
			 	}
			}
			
			// single actions
			if($xmlParser->getChildNodeAttribute($tableXML, 'singleActions', 'active')==1){
				// View Detail
				if($xmlParser->getChildNodeAttribute($tableXML, 'actDetail', 'active')==1 AND $groupId<=$xmlParser->getChildNodeAttribute($tableXML, 'actDetail', 'permissionGroup')){
					$GET_ParamDetail=array('act'=>'V');
					foreach($xmlParser->getChildNodes($xmlParser->getChildNode($xmlParser->getChildNode($tableXML, 'actDetail'), 'GET_Params')) as $getParam){
						$GET_ParamDetail[$xmlParser->getChildNodeValue($getParam, 'GET_Value')]=$values[$xmlParser->getChildNodeValue($getParam, 'GET_Value')];
					}
					$msgView=$xmlParser->getChildNodeValue($xmlParser->getChildNode($tableXML, 'actDetail'), 'confirmMsg');
					$imgView="<img src=\"".URL_PATH.TEMPLATE_DIR.TEMPLATES_DIR.TEMPLATE."/img/view.jpg\" border=\"0\">";
					$retObj.= "<td align=\"left\">".$this->getUrl($pageName, $GET_ParamDetail, $imgView, 'Visualizza record', $msgView)."</td>\n";
				}
				// Modify
				if($xmlParser->getChildNodeAttribute($tableXML, 'actMod', 'active')==1 AND $groupId<=$xmlParser->getChildNodeAttribute($tableXML, 'actMod', 'permissionGroup')){
					$GET_ParamMod=array('act'=>'M');
					foreach($xmlParser->getChildNodes($xmlParser->getChildNode($xmlParser->getChildNode($tableXML, 'actMod'), 'GET_Params')) as $getParam){;
						$GET_ParamMod[$xmlParser->getChildNodeValue($getParam, 'GET_Value')]=$values[$xmlParser->getChildNodeValue($getParam, 'GET_Value')];
					}
					$msgMod=$xmlParser->getChildNodeValue($xmlParser->getChildNode($tableXML, 'actMod'), 'confirmMsg');
					$imgMod="<img src=\"".URL_PATH.TEMPLATE_DIR.TEMPLATES_DIR.TEMPLATE."/img/mod.jpg\" border=\"0\">";
					$retObj.= "<td align=\"left\">".$this->getUrl($pageName, $GET_ParamMod, $imgMod, 'Modifica record', $msgMod)."</td>\n";
				}
				// Print
				if($xmlParser->getChildNodeAttribute($tableXML, 'actPrint', 'active')==1 AND $groupId<=$xmlParser->getChildNodeAttribute($tableXML, 'actPrint', 'permissionGroup')){
					$GET_ParamPrint=array('act'=>'P');
					foreach($xmlParser->getChildNodes($xmlParser->getChildNode($xmlParser->getChildNode($tableXML, 'actPrint'), 'GET_Params')) as $getParam){;
						$GET_ParamPrint[$xmlParser->getChildNodeValue($getParam, 'GET_Value')]=$values[$xmlParser->getChildNodeValue($getParam, 'GET_Value')];
					}
					$msgPrint=$xmlParser->getChildNodeValue($xmlParser->getChildNode($tableXML, 'actPrint'), 'confirmMsg');
					$imgPrint="<img src=\"".URL_PATH.TEMPLATE_DIR.TEMPLATES_DIR.TEMPLATE."/img/print.jpg\" border=\"0\">";
					$retObj.= "<td align=\"left\">".$this->getUrl($pageName, $GET_ParamPrint, $imgPrint, 'Stampa record', $msgPrint)."</td>\n";
				}
				// Validazione
				if($xmlParser->getChildNodeAttribute($tableXML, 'actValidate', 'active')==1 AND $groupId<=$xmlParser->getChildNodeAttribute($tableXML, 'actValidate', 'permissionGroup')){
					switch ($values[$xmlParser->getNodeAttribute($field, 'dataName')]){
						case "1": // da validare.	
						$GET_ParamPlan=array('act'=>'VAL');
						foreach($xmlParser->getChildNodes($xmlParser->getChildNode($xmlParser->getChildNode($tableXML, 'actValidate'), 'GET_Params')) as $getParam){;
							$GET_ParamPlan[$xmlParser->getChildNodeValue($getParam, 'GET_Value')]=$values[$xmlParser->getChildNodeValue($getParam, 'GET_Value')];
						}
						$msgPrint=$xmlParser->getChildNodeValue($xmlParser->getChildNode($tableXML, 'actValidate'), 'confirmMsg');
						$imgPrint="<img src=\"".URL_PATH.TEMPLATE_DIR.TEMPLATES_DIR.TEMPLATE."/img/valida.jpg\" border=\"0\">";
						$retObj.= "<td align=\"left\">".$this->getUrl($pageName, $GET_ParamPlan, $imgPrint, 'Valida record', $msgPrint)."</td>\n";
						break;
						
						case "2":
						$imgPrint="<img src=\"".URL_PATH.TEMPLATE_DIR.TEMPLATES_DIR.TEMPLATE."/img/validato.jpg\" border=\"0\">";
						$retObj.= "<td align=\"left\">".$imgPrint."</td>\n";	
						break;
						
						case "3":
						$imgPrint="<img src=\"".URL_PATH.TEMPLATE_DIR.TEMPLATES_DIR.TEMPLATE."/img/evaso.jpg\" border=\"0\">";
						$retObj.= "<td align=\"left\">".$imgPrint."</td>\n";	
						break;
					}
				}
				// Planning
				if($xmlParser->getChildNodeAttribute($tableXML, 'actPlanning', 'active')==1 AND $groupId<=$xmlParser->getChildNodeAttribute($tableXML, 'actPlanning', 'permissionGroup')){
					$GET_ParamPlan=array('act'=>'PL');
					foreach($xmlParser->getChildNodes($xmlParser->getChildNode($xmlParser->getChildNode($tableXML, 'actPlanning'), 'GET_Params')) as $getParam){;
						$GET_ParamPlan[$xmlParser->getChildNodeValue($getParam, 'GET_Value')]=$values[$xmlParser->getChildNodeValue($getParam, 'GET_Value')];
					}
					$msgPrint=$xmlParser->getChildNodeValue($xmlParser->getChildNode($tableXML, 'actPlanning'), 'confirmMsg');
					$imgPrint="<img src=\"".URL_PATH.TEMPLATE_DIR.TEMPLATES_DIR.TEMPLATE."/img/planning.jpg\" border=\"0\">";
					$retObj.= "<td align=\"left\">".$this->getUrl($pageName, $GET_ParamPlan, $imgPrint, 'Visualizza Planning', $msgPrint)."</td>\n";
				}
				//timone
				if($xmlParser->getChildNodeAttribute($tableXML, 'actTimone', 'active')==1 AND $groupId<=$xmlParser->getChildNodeAttribute($tableXML, 'actTimone', 'permissionGroup')){
					$GET_ParamTimo=array('act'=>'T');
					foreach($xmlParser->getChildNodes($xmlParser->getChildNode($xmlParser->getChildNode($tableXML, 'actTimone'), 'GET_Params')) as $getParam){;
						$GET_ParamTimo[$xmlParser->getChildNodeValue($getParam, 'GET_Value')]=$values[$xmlParser->getChildNodeValue($getParam, 'GET_Value')];
					}
					$msgPrint=$xmlParser->getChildNodeValue($xmlParser->getChildNode($tableXML, 'actTimone'), 'confirmMsg');
					$imgPrint="<img src=\"".URL_PATH.TEMPLATE_DIR.TEMPLATES_DIR.TEMPLATE."/img/timone.jpg\" border=\"0\">";
					$retObj.= "<td align=\"left\">".$this->getUrl($pageName, $GET_ParamTimo, $imgPrint, 'Visualizza Timone', $msgPrint)."</td>\n";
				}
			}
			$retObj.= "</tr>\n";
			$countRecord++;
		}		
		// ****************************** Fine Table data ******************************************************************
		
		// ****************************** Table header ******************************************************************
 		if($xmlParser->getChildNodeValue($tableXML, 'tableHeader')==1){
			$retHead.= "<tr>\n";
			if($xmlParser->getChildNodeAttribute($tableXML, 'groupedActions', 'active')==1){
				$retHead.= "<td>&nbsp;</td>";
			}
			foreach($xmlParser->getChildNodes($fieldsXML) as $field){
				if($xmlParser->getChildNodeValue($field, 'fieldTblView')==1){
					$retHead.= "<td class=\"tbl_title\" width=\"".$xmlParser->getChildNodeValue($field, 'fieldTblWidth')."\"><b>"; 
					$retHead.="<a href=\"".$xmlParser->getChildNodeAttribute($tableXML, 'pageOrder', 'name')."?order=".$xmlParser->getNodeAttribute($field, 'dataName')."\"><font color=\"white\">".$xmlParser->getChildNodeValue($field, 'fieldView')."</font></a>";
					$retHead.= "</b></td>\n";
				}
			}
			
    		//grouped actions
			if($xmlParser->getChildNodeAttribute($tableXML, 'groupedActions', 'active')==1){
				// Archive
				if($xmlParser->getchildNodeValue($tableXML, 'actArchive')==1 AND $groupId<=$xmlParser->getChildNodeAttribute($tableXML, 'actArchive', 'permissionGroup')){
					$msgArc= UserInterface::confirmMsg("Archiviare i record selezionati?", 1);
					$imgArc=URL_PATH.TEMPLATE_DIR.TEMPLATES_DIR.TEMPLATE."/img/arch.jpg";
					$retHead.= "<td align=\"left\"><input type=\"image\" src=".$imgArc." name=\"act\" value=\"A\" class=\"pulsantImg\" ".$msgArc."></td>\n";
					
				}
				//Delete
				if($xmlParser->getchildNodeValue($tableXML, 'actDel')==1 AND $groupId<=$xmlParser->getChildNodeAttribute($tableXML, 'actDel', 'permissionGroup')){
					$msgDel= UserInterface::confirmMsg("Eliminare i record selezionati?", 1);
					$imgDel=URL_PATH.TEMPLATE_DIR.TEMPLATES_DIR.TEMPLATE."/img/trash.jpg";
					$retHead.= "<td align=\"left\"><input type=\"image\" src=".$imgDel." name=\"act\" value=\"E\" size=\"0\" class=\"pulsantImg\" ".$msgDel."></td>\n";
				}
			}
			// Return header 
			$retHead.= "</tr>\n";		
		}
		// ****************************** Fine Table header ******************************************************************		
		// new record
		if($xmlParser->getChildNodeAttribute($tableXML, 'actNew', 'active')==1 AND $groupId<=$xmlParser->getChildNodeAttribute($tableXML, 'actNew', 'permissionGroup')){
				$imgNew="<img src=\"".URL_PATH.TEMPLATE_DIR.TEMPLATES_DIR.TEMPLATE."/img/new.jpg\" border=\"0\" align=\"center\">";
				$msg="";
				$retValue.="<table ".$xmlParser->getNodeAttribute($tableXML, 'attr').">";
				$retValue.= "<tr><td class=\"internal\">".$this->getUrl($pageName, array('act'=>'N'), $imgNew, 'Inserimento nuovo record', $msg)."</td>";
				$retValue.= "<td class=\"internal\" align=\"right\">[<b>".$countRecord."</b> record trovati ]</td></tr>";
				$retValue.= "<tr><td>&nbsp;</td></tr>";
				$retValue.= "</table>";
			}else{
				$retValue.="<table ".$xmlParser->getNodeAttribute($tableXML, 'attr').">";
				$retValue.= "<tr><td class=\"internal\">&nbsp;</td>";
				$retValue.= "<td class=\"internal\" align=\"right\">[<b>".$countRecord."</b> record trovati ]</td></tr>";
				$retValue.= "<tr><td>&nbsp;</td></tr>";
				$retValue.= "</table>";
			}
			
    	$retValue.= "<form name=\"".$xmlParser->getNodeAttribute($tableXML, 'name')."\" action=\"".$pageName."\" method=\"post\">\n";
		$retValue.= "<table ".$xmlParser->getNodeAttribute($tableXML, 'attr').">
					".$retHead.$retObj."</table>\n";
		
		$retValue.= "</form>\n";
		return $retValue;
    }
    	
    	
    /**
    * Produce l'output html per una tabella senza azioni a partire dal descrittore XML.
    *
    * @access private
    *
    * @param DOMElement $tableXML, tabella del file xml descrittore della pagina.
    * @param array $dataObj, array di dati da printare nella tabella.
    *
    * @return string
    */
    private function printTableWithoutActions($xmlParser, $tableXML, $dataObj){			
		$fieldsXML=$xmlParser->getChildNode($tableXML, 'fields');
		
		// ****************************** Table data ******************************************************************
		foreach($dataObj as $values){
			// Alt color table
			if($xmlParser->getChildNodeValue($tableXML, 'alternativeColor')==1){
				if($tdColor=="tbl_c1"){ $tdColor= "tbl_c2"; }else{ $tdColor= "tbl_c1"; }
			}
			// Content
			$retObj.= "<tr>\n";
			
			// Data
			foreach($xmlParser->getchildNodes($fieldsXML) as $field){
				if($xmlParser->getChildNodeValue($field, 'fieldTblView')==1){
			 		$retObj.= "<td class=\"".$tdColor."\">".$values[$xmlParser->getNodeAttribute($field, 'dataName')]."</td>\n";
			 	}
			}
			$retObj.= "</tr>\n";
		}		
		// ****************************** Fine Table data ******************************************************************
		
		// ****************************** Table header ******************************************************************
 		if($xmlParser->getChildNodeValue($tableXML, 'tableHeader')==1){
			$retHead.= "<tr>\n";
			foreach($xmlParser->getChildNodes($fieldsXML) as $field){
				if($xmlParser->getChildNodeValue($field, 'fieldTblView')==1){
					$retHead.= "<td class=\"tbl_title\" width=\"".$xmlParser->getChildNodeValue($field, 'fieldTblWidth')."\"><b>";
					$retHead.="<a href=\"".$xmlParser->getChildNodeAttribute($tableXML, 'pageOrder', 'name')."?act=O&order=".$xmlParser->getNodeAttribute($field, 'dataName')."\"><font color=\"white\">".$xmlParser->getChildNodeValue($field, 'fieldView')."</font></a>";
					$retHead.= "</b></td>\n";
				}
			}
			// Return header 
			$retHead.= "</tr>\n";		
		}
		// ****************************** Fine Table header ******************************************************************		
		
		$retValue= "<table ".$tableXML->getAttribute('attr').">
					".$retHead.$retObj."</table>\n";
		return $retValue;
    }
} /* end of class UITable */
?>
