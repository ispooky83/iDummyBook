<?php
/**
 * Genera form html riutilizzabili
 *
 * @access public
 * @author Lorenzo Monaco/Marco Lucidi
 * @package UserInterface
 */
 
 /* user defined includes */  

 /* user defined constants */


require_once("ui/class.UIObjects.php");

class UIForm extends UIObjects {
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
    * Gestisce i campi hidden della form
    *
    * @access private
    *
   	* @param DOMElement $formXML
    * @param Object $xmlParser
    * @param array $jumpHidden
    *
    * @return string
    */
	private function getHiddenFields($formXML, $xmlParser, $jumpHidden){
		$out="";
		$out.= "<tr height=\"10\"><td colspan=\"2\">&nbsp;";
		$hiddens=$xmlParser->getChildNode($formXML, 'hiddens');
		
		foreach ($xmlParser->getChildNodes($hiddens) as $hidden){
			//valore non è passato staticamente
			if(trim($xmlParser->getNodeAttribute($hidden, 'value'))==""){
					if(isset($_GET[$xmlParser->getNodeAttribute($hidden, 'name')])){
						//valore passato da una get.
						$valHidden=$_GET[$xmlParser->getNodeAttribute($hidden, 'name')];
					}elseif (isset($_POST[$xmlParser->getNodeAttribute($hidden, 'name')])){
						//valore passato da una post.
						$valHidden=$_POST[$xmlParser->getNodeAttribute($hidden, 'name')];
					}else{
					//valore recuperato da una variabile.
					$valHidden=$xmlParser->getNodeAttribute($hidden, 'varName');
					$valHidden=$$valHidden;
				}
			}else{
				//valore passato staticamente
				$valHidden=$xmlParser->getNodeAttribute($hidden, 'value');
			}	
			$out.="<input type=\"hidden\" name=\"".$xmlParser->getNodeAttribute($hidden, 'name')."\" value=\"".$valHidden."\">";
		}
		
		//gestione inserimento nuovi campi hidden provenienti dalle select jump.
		foreach ($jumpHidden as $key=>$value){
			if($key!=""){
				$out.="<input type=\"hidden\" name=\"".$key."\" value=\"".$value."\">";
			}
		}
		$out.= "</td></tr>\n";
		return $out;
	}
	
	/**
    * Gestisce una la redirezione del printing di generica form di inserimento o ad una form di inserimento con jump menu.
    *
    * @access public
    *
   	* @param string $xmlContainer, path del file xml descrittore della pagina.
    * @param int $item, determina l'occorrenza della form da printare all'interno del descrittore XML.
    * @param object $db connessione al database
    * @param array $data.
    *
    * @return string
    */
	public function getFormInsert($xmlContainer, $item, $connect, $data){
		$xmlParser=new XMLParser();
    	$page=$xmlParser->getXMLSchema($xmlContainer);
    	$formXML=$xmlParser->getNode($page,'form', $item);
    	if($xmlParser->getNodeAttribute($formXML, 'jump')==1){
    		return $this->printFormInsertJump($xmlContainer, $item, $connect, $data);
    	}else{
			return $this->printFormInsert($xmlContainer, $item, $connect);    		
    	}
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
    public function printFormInsert_old($xmlContainer, $item, $connect){
    	$xmlParser=new XMLParser();
    	$page=$xmlParser->getXMLSchema($xmlContainer);
    	$formXML=$xmlParser->getNode($page,'form', $item);				
    	$fieldsXML= $xmlParser->getNode($formXML, 'fields', 0);
    	
    	$formTable= "<br><form ".$xmlParser->getNodeAttribute($formXML, 'attr').">\n";
    	$formTable.= "<table ".$xmlParser->getChildNodeAttribute($formXML, 'tableForm', 'attr').">\n";	
	    	foreach ($xmlParser->getChildNodes($fieldsXML) as $field){
	    		$newParameters=$xmlParser->getChildNode($field, 'newEntryParameters');
		    	if($xmlParser->getChildNodeValue($newParameters, 'fieldView')==1){
		    		
		    		if($xmlParser->getChildNodeValue($field, 'mandatory')==1){
		    			$mandatory="(*)";
		    		}else{
		    			$mandatory="";
		    		}
		    		
		    		if($xmlParser->getChildNodeAttribute($field, 'fieldDescr', 'active')==1){
		    			$descrizione="<br>(".$xmlParser->getChildNodeValue($field, 'fieldDescr').")";
		    		}else{
		    			$descrizione="";
		    		}
		    		
		    		$formTable.= "<tr>";
		    		switch($xmlParser->getNodeAttribute($field, 'type')){
		    			case "select":
		    			$formTable.= "<td valign=\"top\" class=\"internal\"><b>".$xmlParser->getChildNodeValue($field, 'label')."</b> ".$mandatory." ".$descrizione."</td>\n";
		    			$formTable.= $this->getCombo($connect, $field, "");
		    			break;
		    				
		    			case "textarea":
		    			$formTable.= "<td valign=\"top\" class=\"internal\"><b>".$xmlParser->getChildNodeValue($field, 'label')."</b> ".$mandatory." ".$descrizione."</td>\n";
		    			$formTable.= "<td class=\"internal\"><textarea  name=\"".$xmlParser->getNodeAttribute($field, 'name')."\" class=\"textform\" cols=\"45\" rows=\"20\"></textarea></td>\n";
		    			break;
		    				
		    			case "checkbox":
		    			$formTable.= "<td valign=\"top\" class=\"internal\"><b>".$xmlParser->getChildNodeValue($field, 'label')."</b> ".$mandatory." ".$descrizione."</td>\n";
		    			$formTable.= "<td class=\"internal\"><input type=\"checkbox\"  name=\"".$xmlParser->getNodeAttribute($field, 'name')."\" class=\"textform\" value=\"1\"></td>\n";
		    			break;
		    			
		    			case "hr":
		    			$formTable.= "";
		    			$formTable.= "<td class=\"internal\" colspan=\"2\"><hr noshade size=\"1\"></td>\n";
		    			break;
		    			
		    			default:
		    			$formTable.= "<td valign=\"top\" class=\"internal\"><b>".$xmlParser->getChildNodeValue($field, 'label')."</b> ".$mandatory." ".$descrizione."</td>\n";
			    		$formTable.= "<td class=\"internal\"><input type=\"".$xmlParser->getNodeAttribute($field, 'type')."\" name=\"".$xmlParser->getNodeAttribute($field, 'name')."\" class=\"textform\" ".$xmlParser->getNodeAttribute($field, 'attr')."></td>\n";
		    			break;				
		    		}
		    		$formTable.= "</tr>\n";
	    		}
	    	}
	    	//************************ INSERIMENTO CAMPI HIDDENS *************************
	    	$formTable.=$this->getHiddenFields($formXML, $xmlParser, array());
			//**************************************************************************** 	
   
    	$formTable.= "<tr><td colspan=\"2\" align=\"left\"><input type=\"hidden\" name=\"act\" value=\"N\">";
    	$formTable.= "<input type=\"submit\" name=\"invia\" value=\"Registra\" class=\"pulsanti\">";
    	$formTable.= "";
    	$formTable.= "</td></tr>";
    	$formTable.= "<tr><td colspan=\"2\">&nbsp;</td></tr>";
    	$formTable.= "<tr><td colspan=\"2\" align=\"left\" class=\"internal\">(*) Campi obbligatori</td></tr>"; 
    	$formTable.= "</table>"; 
    	$formTable.= "</form>\n";
    		
    	return $formTable;
    }
	
    
    /**
    * Gestisce una generica form di inserimento
    *
    * @access public
    *
   	* @param string $xmlContainer, path del file xml descrittore della pagina.
    * @param int $item, determina l'occorrenza della form da printare all'interno del descrittore XML.
    * @param object $db connessione al database
    * @param object $cache dati della cache, ripassati alla form.
    *
    * @return string
    */
    public function printFormInsert($xmlContainer, $item, $connect, $cache){
    	$xmlParser=new XMLParser();
    	$page=$xmlParser->getXMLSchema($xmlContainer);
    	$formXML=$xmlParser->getNode($page,'form', $item);				
    	$fieldsXML= $xmlParser->getNode($formXML, 'fields', 0);
    	
    	$formTable= "<br><form ".$xmlParser->getNodeAttribute($formXML, 'attr').">\n";
    	$formTable.= "<table ".$xmlParser->getChildNodeAttribute($formXML, 'tableForm', 'attr').">\n";	
	    	foreach ($xmlParser->getChildNodes($fieldsXML) as $field){
	    		$newParameters=$xmlParser->getChildNode($field, 'newEntryParameters');
		    	if($xmlParser->getChildNodeValue($newParameters, 'fieldView')==1){
		    		
		    		if($xmlParser->getChildNodeValue($field, 'mandatory')==1){
		    			$mandatory="(*)";
		    		}else{
		    			$mandatory="";
		    		}
		    		
		    		if($xmlParser->getChildNodeAttribute($field, 'fieldDescr', 'active')==1){
		    			$descrizione="<br>(".$xmlParser->getChildNodeValue($field, 'fieldDescr').")";
		    		}else{
		    			$descrizione="";
		    		}
		    		
		    		$formTable.= "<tr>";
		    		switch($xmlParser->getNodeAttribute($field, 'type')){
		    			case "select":
		    			$formTable.= "<td valign=\"top\" class=\"internal\"><b>".$xmlParser->getChildNodeValue($field, 'label')."</b> ".$mandatory." ".$descrizione."</td>\n";
		    			$formTable.= $this->getCombo($connect, $field, $cache[$xmlParser->getNodeAttribute($field, 'name')]);
		    			break;
		    				
		    			case "textarea":
		    			$formTable.= "<td valign=\"top\" class=\"internal\"><b>".$xmlParser->getChildNodeValue($field, 'label')."</b> ".$mandatory." ".$descrizione."</td>\n";
		    			$formTable.= "<td class=\"internal\"><textarea  name=\"".$xmlParser->getNodeAttribute($field, 'name')."\" class=\"textform\" cols=\"45\" rows=\"20\"></textarea></td>\n";
		    			break;
		    				
		    			case "checkbox":
		    			$formTable.= "<td valign=\"top\" class=\"internal\"><b>".$xmlParser->getChildNodeValue($field, 'label')."</b> ".$mandatory." ".$descrizione."</td>\n";
		    			if($cache[$xmlParser->getNodeAttribute($field, 'name')]==1){
		    				$formTable.= "<td class=\"internal\"><input type=\"checkbox\" checked name=\"".$xmlParser->getNodeAttribute($field, 'name')."\" class=\"textform\" value=\"1\"></td>\n";
		    			}else{
		    				$formTable.= "<td class=\"internal\"><input type=\"checkbox\"  name=\"".$xmlParser->getNodeAttribute($field, 'name')."\" class=\"textform\" value=\"1\"></td>\n";
		    			}
		    			break;
		    			
		    			case "hr":
		    			$formTable.= "";
		    			$formTable.= "<td class=\"internal\" colspan=\"2\"><hr noshade size=\"1\"></td>\n";
		    			break;
		    
		    			default:
		    			$formTable.= "<td valign=\"top\" class=\"internal\"><b>".$xmlParser->getChildNodeValue($field, 'label')."</b> ".$mandatory." ".$descrizione."</td>\n";
		    			$formTable.= "<td class=\"internal\"><input type=\"".$xmlParser->getNodeAttribute($field, 'type')."\" name=\"".$xmlParser->getNodeAttribute($field, 'name')."\" class=\"textform\" value=\"".$cache[$xmlParser->getNodeAttribute($field, 'name')]."\" ".$xmlParser->getNodeAttribute($field, 'attr')."></td>\n";
		    			break;				
		    		}
		    		$formTable.= "</tr>\n";
	    		}
	    	}
	    	//************************ INSERIMENTO CAMPI HIDDENS *************************
	    	$formTable.=$this->getHiddenFields($formXML, $xmlParser, array());
			//**************************************************************************** 	
   
    	$formTable.= "<tr><td colspan=\"2\" align=\"left\"><input type=\"hidden\" name=\"act\" value=\"N\">";
    	$formTable.= "<input type=\"submit\" name=\"invia\" value=\"Registra\" class=\"pulsanti\">";
    	$formTable.= "";
    	$formTable.= "</td></tr>";
    	$formTable.= "<tr><td colspan=\"2\">&nbsp;</td></tr>";
    	$formTable.= "<tr><td colspan=\"2\" align=\"left\" class=\"internal\">(*) Campi obbligatori</td></tr>"; 
    	$formTable.= "</table>"; 
    	$formTable.= "</form>\n";
    		
    	return $formTable;
    }
    
    /**
    * Gestisce una generica form di inserimento con combo jump
    *
    * @access public
    *
   	* @param string $xmlContainer, path del file xml descrittore della pagina.
    * @param int $item, determina l'occorrenza della form da printare all'interno del descrittore XML.
    * @param object $db connessione al database
    * @param array $data.
    *
    * @return string
    */
    public function printFormInsertJump($xmlContainer, $item, $connect, $data){
    	//Recupero dati dal descrittore.
    	$xmlParser=new XMLParser();
    	$page=$xmlParser->getXMLSchema($xmlContainer);
    	$formXML=$xmlParser->getNode($page,'form', $item);				
    	$fieldsXML= $xmlParser->getNode($formXML, 'fields', 0);
    	
    	//inizializzo un array che conterr� i nuovi campi hidden generati dalle select jump.
    	$jumpHidden=array();
    	
    	$formTable= "<br><form ".$xmlParser->getNodeAttribute($formXML, 'attr').">\n";
    	$formTable.= "<table ".$xmlParser->getChildNodeAttribute($formXML, 'tableForm', 'attr').">\n";	
			//per ogni campo della form    	
	    	foreach ($xmlParser->getChildNodes($fieldsXML) as $field){
	    		//recupero le propriet� del campo relativamente ad un suo inserimento.
	    		$newParameters=$xmlParser->getChildNode($field, 'newEntryParameters');
	    		//se il campo deve essere visualizzato in un nuovo inserimento lo printo nella form.
		    	if($xmlParser->getChildNodeValue($newParameters, 'fieldView')==1){
		    		//gestione visualizzazione mandatory.
		    		if($xmlParser->getChildNodeValue($field, 'mandatory')==1){
		    			$mandatory="(*)";
		    		}else{
		    			$mandatory="";
		    		}
		    		//gestione visualizzazione della descrizione del campo.
		    		if($xmlParser->getChildNodeAttribute($field, 'fieldDescr', 'active')==1){
		    			$descrizione="<br>(".$xmlParser->getChildNodeValue($field, 'fieldDescr').")";
		    		}else{
		    			$descrizione="";
		    		}
		    		//costruzione dell'input.
		    		$formTable.= "<tr>";
		    		$formTable.= "<td valign=\"top\" class=\"internal\"><b>".$xmlParser->getChildNodeValue($field, 'label')."</b> ".$mandatory." ".$descrizione."</td>\n";
		    		//switch sul tipo di campo.
		    		switch($xmlParser->getNodeAttribute($field, 'type')){
		    			
		    			case "select":
		    			//se � settato ad 1 l'attributo jump del campo	
		    			if($xmlParser->getNodeAttribute($field, "jump")==1){
		    				$comboInfo=$this->getComboJump($connect, $field, $data);
		    				$formTable.= $comboInfo[0];
		    				$jumpHidden[$comboInfo[1]]=$comboInfo[2];
		    			}else{
		    				$formTable.= $this->getCombo($connect, $field, "");
		    			}
		    			
		    			break;
		    				
		    			case "textarea":
		    			$formTable.= "<td class=\"internal\"><textarea  name=\"".$xmlParser->getNodeAttribute($field, 'name')."\" class=\"textform\" cols=\"45\" rows=\"20\"></textarea></td>\n";
		    			break;
		    				
		    			case "checkbox":
		    			$formTable.= "<td class=\"internal\"><input type=\"checkbox\"  name=\"".$xmlParser->getNodeAttribute($field, 'name')."\" class=\"textform\" value=\"1\"></td>\n";
		    			break;
		    			
		    			default:
		    			$formTable.= "<td class=\"internal\"><input type=\"".$xmlParser->getNodeAttribute($field, 'type')."\" name=\"".$xmlParser->getNodeAttribute($field, 'name')."\" class=\"textform\"></td>\n";
		    			break;				
		    		}
		    		$formTable.= "</tr>\n";
	    		}
	    	}
	    	//************************ INSERIMENTO CAMPI HIDDENS *************************
	    	$formTable.=$this->getHiddenFields($formXML, $xmlParser, $jumpHidden);
			//**************************************************************************** 	
   
    	$formTable.= "<tr><td colspan=\"2\" align=\"left\"><input type=\"hidden\" name=\"act\" value=\"N\">";
    	$formTable.= "<input type=\"submit\" name=\"invia\" value=\"Registra\" class=\"pulsanti\">";
    	$formTable.= "";
    	$formTable.= "</td></tr>";
    	$formTable.= "<tr><td colspan=\"2\">&nbsp;</td></tr>";
    	$formTable.= "<tr><td colspan=\"2\" align=\"left\" class=\"internal\">(*) Campi obbligatori</td></tr>"; 
    	$formTable.= "</table>"; 
    	$formTable.= "</form>\n";
    		
    	return $formTable;
    }
    
    
    	
    /**
    * Gestisce una generica form di modifica.
    *
    * @access public
    *
    * @param string $xmlContainer, path del file xml descrittore della pagina.
    * @param int $item, determina l'occorrenza della form da printare all'interno del descrittore XML.
    * @param array $data, array di dati da inserire nella form di modifica.
    * @param object $db connessione al database
    * @param string $recordIdName nome della chiave esterna per il reperimento dei dati del record 
    * @param $recordIdValue valore della chiave esterna.
    *
    * @return string
    */
    public function printFormModify($xmlContainer, $item, $data, $connect, $recordIdName, $recordIdValue){
    	$xmlParser=new XMLParser();
    	$page=$xmlParser->getXMLSchema($xmlContainer);
    	$formXML=$xmlParser->getNode($page,'form', $item);				
    	$fieldsXML= $xmlParser->getNode($formXML, 'fields', 0);
    	
    	$formTable= "<br><form ".$xmlParser->getNodeAttribute($formXML, 'attr').">\n";
    	$formTable.= "<table ".$xmlParser->getChildNodeAttribute($formXML, 'tableForm', 'attr').">\n";	
	    	foreach ($xmlParser->getChildNodes($fieldsXML) as $field){
	    		$modifyParameters=$xmlParser->getChildNode($field, 'modifyEntryParameters');
		    	if($xmlParser->getChildNodeValue($modifyParameters, 'fieldView')==1){
		    		
		    		if($xmlParser->getChildNodeValue($field, 'mandatory')==1){
		    			$mandatory="(*)";
		    		}else{
		    			$mandatory="";
		    		}
		    		
		    		if($xmlParser->getChildNodeAttribute($field, 'fieldDescr', 'active')==1){
		    			$descrizione="<br>(".$xmlParser->getChildNodeValue($field, 'fieldDescr').")";
		    		}else{
		    			$descrizione="";
		    		}
		    		
		    		$formTable.= "<tr>";
		    		switch($field->getAttribute('type')){
		    			case "select":
		    			$formTable.= "<td valign=\"top\" class=\"internal\"><b>".$xmlParser->getChildNodeValue($field, 'label')."</b> ".$mandatory." ".$descrizione."</td>\n";
		    			$formTable.= $this->getCombo($connect, $field, $data[$xmlParser->getNodeAttribute($field, 'dbName')]);
		    			break;
		    				
		    			case "data":
		    			$formTable.= "<td valign=\"top\" class=\"internal\"><b>".$xmlParser->getChildNodeValue($field, 'label')."</b> ".$mandatory." ".$descrizione."</td>\n";
		    			$formTable.= "<td class=\"internal\"><input type=\"".$filedsType[$i]."\" name =\"data1\" value=\"\" class=\"textform\" size=\"2\" onChange=\"collectData(document.form1.data1.value, document.form1.data2.value, document.form1.data3.value, '".$fieldsRec[$i]."')\">";
		    			$formTable.= "<input type=\"".$filedsType[$i]."\" name =\"data2\" value=\"\" class=\"textform\" size=\"2\" onChange=\"collectData(document.form1.data1.value, document.form1.data2.value, document.form1.data3.value, '".$fieldsRec[$i]."')\">";
		    			$formTable.= "<input type=\"".$filedsType[$i]."\" name =\"data3\" value=\"\" class=\"textform\" size=\"4\" onChange=\"collectData(document.form1.data1.value, document.form1.data2.value, document.form1.data3.value, '".$fieldsRec[$i]."')\">";
		    			$formTable.= "<input type=\"hidden\" name=\"$fieldsRec[$i]\" value=\"\">";
		    			$formTable.= "</td>\n";
		    			break;
		    			
		    			case "file":
		    			$formTable.= "<td valign=\"top\" class=\"internal\"><b>".$xmlParser->getChildNodeValue($field, 'label')."</b> ".$mandatory." ".$descrizione."</td>\n";
		    				if($data[$xmlParser->getNodeAttribute($field, 'dbName')]!=""){
		    					$formTable.= "<td class=\"internal\"><img src=\"".GENERAL_FILE_STORE_URL."images/".$data[$xmlParser->getNodeAttribute($field, 'dbName')]."\"><br><br>";
		    					$formTable.= "<input type=\"file\" name =\"".$xmlParser->getNodeAttribute($field, 'name')."\" class=\"textform\"><br>lasciare vuoto il campo per <font color=\"Red\">non</font> modificare l'immagine</td>\n";	
		    				}else{
		    					$formTable.= "<td class=\"internal\"><input type=\"file\" name =\"".$xmlParser->getNodeAttribute($field, 'name')."\" class=\"textform\"></td>\n";	
		    				}
		    				
		    			break;
	    				
		    			case "text":
		    			$formTable.= "<td valign=\"top\" class=\"internal\"><b>".$xmlParser->getChildNodeValue($field, 'label')."</b> ".$mandatory." ".$descrizione."</td>\n";
		    			$formTable.="<td class=\"internal\"><input type=\"text\" name=\"".$xmlParser->getNodeAttribute($field, 'name')."\" ".$xmlParser->getNodeAttribute($field, 'attr')." value=\"".$data[$xmlParser->getNodeAttribute($field, 'dbName')]."\"> ";
		    			break;
		    			
		    			case "hr":
		    			$formTable.= "";
		    			$formTable.= "<td class=\"internal\" colspan=\"2\"><hr noshade size=\"1\"></td>\n";
		    			break;
		    			
		    			case "textarea":
		    			$formTable.= "<td valign=\"top\" class=\"internal\"><b>".$xmlParser->getChildNodeValue($field, 'label')."</b> ".$mandatory." ".$descrizione."</td>\n";
		    			$formTable.= "<td class=\"internal\"><textarea  name=\"".$xmlParser->getNodeAttribute($field, 'name')."\" class=\"textform\" cols=\"45\" rows=\"20\">".$data[$xmlParser->getNodeAttribute($field, 'dbName')]."</textarea></td>\n";
		    			break;
		    			
		    			default:
		    			$formTable.= "<td valign=\"top\" class=\"internal\"><b>".$xmlParser->getChildNodeValue($field, 'label')."</b> ".$mandatory." ".$descrizione."</td>\n";
		    			$formTable.= "<td class=\"internal\"><input type=\"".$field->getAttribute('type')."\" name=\"".$xmlParser->getNodeAttribute($field, 'name')."\" value=\"".$data[$xmlParser->getNodeAttribute($field, 'dbName')]."\" class=\"textform\"></td>\n";
		    			break;
		    		}
		    		$formTable.= "</tr>\n";
	    		}
	    	}
	    	$formTable.= "<tr height=\"10\"><td colspan=\"2\">&nbsp;</td></tr>\n";
   
    	$formTable.= "<tr><td colspan=\"2\" align=\"left\"><input type=\"hidden\" name=\"act\" value=\"M\">";
    	$formTable.= "<input type=\"hidden\" name=\"".$recordIdName."\" value=\"".$recordIdValue."\">";
    	$formTable.= "<input type=\"submit\" name=\"invia\" value=\"Registra\" class=\"pulsanti\">";
    	$formTable.= "";
    	$formTable.= "</td></tr>";
    	$formTable.= "<tr><td colspan=\"2\">&nbsp;</td></tr>";
    	$formTable.= "<tr><td colspan=\"2\" align=\"left\" class=\"internal\">(*) Campi obbligatori</td></tr>"; 
    	$formTable.= "</table>"; 
    	$formTable.= "</form>\n";
    		
    	return $formTable;
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
    public function printFormRedirect($xmlContainer, $item, $connect){
    	$xmlParser=new XMLParser();
    	$page=$xmlParser->getXMLSchema($xmlContainer);
    	$formXML=$xmlParser->getNode($page,'form', $item);				
    	$fieldsXML= $xmlParser->getNode($formXML, 'fields', 0);
    	
    	$formTable= "<br><form ".$xmlParser->getNodeAttribute($formXML, 'attr').">\n";
    	$formTable.= "<table ".$xmlParser->getChildNodeAttribute($formXML, 'tableForm', 'attr').">\n";	
	    	foreach ($xmlParser->getChildNodes($fieldsXML) as $field){
	    		$newParameters=$xmlParser->getChildNode($field, 'newEntryParameters');
		    	if($xmlParser->getChildNodeValue($newParameters, 'fieldView')==1){
		    		
		    		if($xmlParser->getChildNodeValue($field, 'mandatory')==1){
		    			$mandatory="(*)";
		    		}else{
		    			$mandatory="";
		    		}
		    		
		    		if($xmlParser->getChildNodeAttribute($field, 'fieldDescr', 'active')==1){
		    			$descrizione="<br>(".$xmlParser->getChildNodeValue($field, 'fieldDescr').")";
		    		}else{
		    			$descrizione="";
		    		}
		    		
		    		$formTable.= "<tr>";
		    		$formTable.= "<td valign=\"top\" class=\"internal\"><b>".$xmlParser->getChildNodeValue($field, 'label')."</b> ".$mandatory." ".$descrizione."</td>\n";
		    		switch($xmlParser->getNodeAttribute($field, 'type')){
		    			case "select":
		    			$formTable.= $this->getCombo($connect, $field, "");		    			
		    			break;
		    				
		    			case "textarea":
		    			$formTable.= "<td class=\"internal\"><textarea  name=\"".$xmlParser->getNodeAttribute($field, 'name')."\" class=\"textform\" cols=\"45\" rows=\"20\"></textarea></td>\n";
		    			break;
		    				
		    			case "checkbox":
		    			$formTable.= "<td class=\"internal\"><input type=\"checkbox\"  name=\"".$xmlParser->getNodeAttribute($field, 'name')."\" class=\"textform\" value=\"1\"></td>\n";
		    			break;

		    			default:
		    			$formTable.= "<td class=\"internal\"><input type=\"".$xmlParser->getNodeAttribute($field, 'type')."\" name=\"".$xmlParser->getNodeAttribute($field, 'name')."\" class=\"textform\"></td>\n";
		    			break;				
		    		}
		    		$formTable.= "</tr>\n";
	    		}
	    	}
	    	//************************ INSERIMENTO CAMPI HIDDENS *************************
	    	$formTable.=$this->getHiddenFields($formXML, $xmlParser, array());
			//**************************************************************************** 	
   
    	$formTable.= "<tr><td colspan=\"2\" align=\"left\"><input type=\"hidden\" name=\"act\" value=\"R\">";
    	$formTable.= "<input type=\"submit\" name=\"invia\" value=\"Continua\" class=\"pulsanti\">";
    	$formTable.= "";
    	$formTable.= "</td></tr>";
    	$formTable.= "<tr><td colspan=\"2\">&nbsp;</td></tr>";
    	$formTable.= "</table>"; 
    	$formTable.= "</form>\n";
    		
    	return $formTable;
    }	
    	
	/** 
	* Crea una combo box
	*
	* @access public
	*
	* @param object $db connessione al database
	* @param DOMElement $field riferimento all'elemento select del descrittore xml della pagina.
	* @param $val valore della combobox.
	*
	* @return string
	*/
	protected function getCombo($db, $field, $selected){
		$xmlParser=new XMLParser();
		if($xmlParser->getChildNodeAttribute($field, 'optionForDB', 'active')==1){	
			$selectDB=$xmlParser->getChildNode($field, 'selectDB');
			$select="SELECT ".$xmlParser->getNodeAttribute($selectDB, 'optionValue').", ".$xmlParser->getNodeAttribute($selectDB, 'optionVis')." 
					FROM ".$xmlParser->getNodeAttribute($selectDB, 'tableName')." " 
					.$xmlParser->getNodeAttribute($selectDB, 'condition');		
			$query= DBInterface::execQuery($db, $select, "", 0);
			$str="";
			$str.="<td><select name=\"".$xmlParser->getNodeAttribute($field, 'name')."\" ".$xmlParser->getNodeAttribute($field, 'attr').">";
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
			$str.="<td><select name=\"".$xmlParser->getNodeAttribute($field, 'name')."\" ".$xmlParser->getNodeAttribute($field, 'attr').">";
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
	* Crea una combo box di tipo jump menu
	*
	* @access public
	*
	* @param object $db connessione al database
	* @param DOMElement $field riferimento all'elemento select del descrittore xml della pagina.
	* @param $val valore della combobox.
	*
	* @return string	
	*/
	protected function getComboJump($db, $field, $data){
		$xmlParser=new XMLParser();
		$outVal=array();
		//caso in cui la combox sia generata da database.
		if($xmlParser->getChildNodeAttribute($field, 'optionForDB', 'active')==1){	
			$selectDB=$xmlParser->getChildNode($field, 'selectDB');
			
			//se è settato il valore associato alla combobox allora ne printo il valore.
			if(isset($data[$xmlParser->getNodeAttribute($field, 'name')]) AND trim($data[$xmlParser->getNodeAttribute($field, 'name')])!=""){
				$select="SELECT ".$xmlParser->getNodeAttribute($selectDB, 'optionValue').", ".$xmlParser->getNodeAttribute($selectDB, 'optionVis')." 
					FROM ".$xmlParser->getNodeAttribute($selectDB, 'tableName')." WHERE " 	
					.$xmlParser->getNodeAttribute($selectDB, 'optionValue')."='" 		
					.$data[$xmlParser->getNodeAttribute($field, 'name')]."'";
					$query= DBInterface::execQuery($db, $select, "", 0);
					foreach ($query as $key=>$value){
						$str=$value[$xmlParser->getNodeAttribute($selectDB, 'optionVis')];
					}
					$out="<td class=\"internal\">".$str."</td>";
					//devo aggiungere il dato nei campi hidden della form.
					//print del campo
					$outVal[0]=$out;
					//nome della variabile da inserire come campo hidden della form
					$outVal[1]=$xmlParser->getNodeAttribute($field, 'name');
					//valore della variabile da inserire come campo hidden della form
					$outVal[2]=$data[$xmlParser->getNodeAttribute($field, 'name')];
					return $outVal;
			}else{
				//se invece è settata la variabile a cui è legata la combobox, faccio una select in base a quel valore.(es: pubblicazioni solo di unn certo committente) 
				$reference=$xmlParser->getChildNodeValue($field, 'reference');
				if(isset($data[$reference]) AND trim($data[$reference])!=""){
					$select="SELECT ".$xmlParser->getNodeAttribute($selectDB, 'optionValue').", ".$xmlParser->getNodeAttribute($selectDB, 'optionVis')." 
						FROM ".$xmlParser->getNodeAttribute($selectDB, 'tableName')." WHERE ".  		
						$xmlParser->getNodeAttribute($selectDB, 'jumpConditionRef')."=" 		
						.$data[$reference];
						
					$query= DBInterface::execQuery($db, $select, "", 0);
					
					$str="";
					//final =1 indica che questa è l'ultima select jump della form in esame e quindi non metto la funz. javascript jumpMenu.
					if($xmlParser->getNodeAttribute($field, "final")==1){
						$str.="<td><select name=\"".$xmlParser->getNodeAttribute($field, 'name')."\" ".$xmlParser->getNodeAttribute($field, 'attr').">";
					}else{
						$str.="<td><select name=\"".$xmlParser->getNodeAttribute($field, 'name')."\" ".$xmlParser->getNodeAttribute($field, 'attr')." onChange=\"MM_jumpMenu('parent',this,0)\">";
					}
					$str.="<option value=\"\"></option>";
					if($xmlParser->getNodeAttribute($field, 'final')==1){
						foreach ($query as $key=>$value){
							$str.="<option value=\"".$value[$xmlParser->getNodeAttribute($selectDB, 'optionValue')]."\">".$value[$xmlParser->getNodeAttribute($selectDB, 'optionVis')]."</option>";
						}		
					}else{
						//definisco quelle variabili che la select deve ripassare come GET.
						$forwards=$xmlParser->getChildNode($field, "forwards");
						$retValue="";
						foreach ($xmlParser->getChildNodes($forwards) as $forward){
							$retValue.="&".$xmlParser->getNodeValue($forward)."=".$_GET[$xmlParser->getNodeValue($forward)];
						}
						
						foreach ($query as $key=>$value){
							$str.="<option value=\"?".$xmlParser->getNodeAttribute($field, 'name')."=".$value[$xmlParser->getNodeAttribute($selectDB, 'optionValue')].$retValue."\">".$value[$xmlParser->getNodeAttribute($selectDB, 'optionVis')]."</option>";
						}
					}
					$str.= "</select></td>";
				}else{
				//caso in cui non è settata alcuna variabile.	
				$select="SELECT ".$xmlParser->getNodeAttribute($selectDB, 'optionValue').", ".$xmlParser->getNodeAttribute($selectDB, 'optionVis')." 
						FROM ".$xmlParser->getNodeAttribute($selectDB, 'tableName')." " 
						.$xmlParser->getNodeAttribute($selectDB, 'condition');		
						
					$query= DBInterface::execQuery($db, $select, "", 0);
					
					$str="";
					if($xmlParser->getNodeAttribute($field, "final")==1){
						$str.="<td><select name=\"".$xmlParser->getNodeAttribute($field, 'name')."\" ".$xmlParser->getNodeAttribute($field, 'attr').">";
					}else{
						$str.="<td><select name=\"".$xmlParser->getNodeAttribute($field, 'name')."\" ".$xmlParser->getNodeAttribute($field, 'attr')." onChange=\"MM_jumpMenu('parent',this,0)\">";
					}
					
					$str.="<option value=\"\"></option>";
					if($xmlParser->getNodeAttribute($field, 'final')==1){
						foreach ($query as $key=>$value){
							$str.="<option value=\"".$value[$xmlParser->getNodeAttribute($selectDB, 'optionValue')]."\">".$value[$xmlParser->getNodeAttribute($selectDB, 'optionVis')]."</option>";
						}		
					}else{
						//definisco quelle variabili che la select deve ripassare come GET.
						$forwards=$xmlParser->getChildNode($field, "forwards");
						$retValue="";
						foreach ($xmlParser->getChildNodes($forwards) as $forward){
							$retValue.="&".$xmlParser->getNodeValue($forward)."=".$_GET[$xmlParser->getNodeValue($forward)];
						}
						foreach ($query as $key=>$value){
							$str.="<option value=\"?".$xmlParser->getNodeAttribute($field, 'name')."=".$value[$xmlParser->getNodeAttribute($selectDB, 'optionValue')].$retValue."\">".$value[$xmlParser->getNodeAttribute($selectDB, 'optionVis')]."</option>";
						}
					}
					$str.= "</select></td>";
				}
				$outVal[0]=$str;
				return $outVal;
			
			}
		//caso in cui la combobox non sia generata da database.	
		}else{
			$str.="<td><select name=\"".$xmlParser->getNodeAttribute($field, 'name')."\" ".$xmlParser->getNodeAttribute($field, 'attr').">";
			$str.="<option value=\"\"></option>";
			$options=$xmlParser->getChildNode($field, 'options');
			foreach ($xmlParser->getChildNodes($options) as $option){
				$str.="<option value=\"".$xmlParser->getNodeAttribute($option, 'value')."\" ".$xmlParser->getNodeAttribute($option, 'attr').">".$xmlParser->getNodeValue($option)."</option>";
			}
			$str.= "</select></td>";
			$outVal[0]=$str;
			return $outVal;			
		}	
	}
	
	
} /* end of class UIForm */	
