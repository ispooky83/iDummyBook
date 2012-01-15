<?php
/**
 * DataProcessors: elabora i dati provenienti da form dinamiche
 *
 * @access public
 * @author Marco Lucidi / Lorenzo Monaco
 */
class DataProcessors{
	// --- ATTRIBUTES ---
	
	// --- OPERATIONS ---
	/**
	* DataProcessors constructor.
	*
	* @access public
	*/
	public function __construct(){
	}
	
	/**
	* Valida le variabili utilizzate per il controllo delle chiamate ai metodi di eliminazione, modifica, visualizzazione ecc. 
	*
	* @access public
	* @param string $action
	* @return boolean
	*/
	public function actionValidator($action){
		if(isset($action) AND !empty($action)){
			return true;
		}else{
			return false;
		}
	}

	/**
	* Elimina i record indicati nel parametro $record
	*
	* @access public
	*
	* @param $table tabella di riferimento
	* @param $idFieldName nome del campo condizionale
	* @param $record array contenente i record da cancellare 
	*
	* @return string
	*/
	public  function deleteEntry($table, $idFieldName, $record){
		foreach($record as $key=>$value){
			$keyA= explode("recId_", $key);
			if(!empty($keyA[1])){
				$delRecords.= $idFieldName."='".$keyA[1]."' OR ";
			}
		}
		if(!empty($delRecords)){
			$delRecords= substr($delRecords, 0, strlen($delRecords)-3);
			$delString= "DELETE FROM ".$table." WHERE $delRecords";
		}else{
			$delString= 0;
		}
		return $delString;
	}
	
	/**
	* Archivia i record indicati nel parametro $record
	*
	* @access public
	*
	* @param $table tabella di riferimento
	* @param $idFieldName nome del campo condizionale
	* @param $record array contenente i record da modificare 
	*
	* @return string
	*/
	public function archiveEntry($table, $idFieldName, $statusFieldName, $record){
		foreach($record as $key=>$value){
			$keyA= explode("recId_", $key);
			if(!empty($keyA[1])){
				$arcRecords.= $idFieldName."=".$keyA[1]." OR ";
			}
		}
		if(!empty($arcRecords)){
			$arcRecords= substr($arcRecords, 0, strlen($arcRecords)-3);
			$arcString= "UPDATE ".$table." SET $statusFieldName=4 WHERE $arcRecords";
		}else{
			$arcString= 0;
		}
		return $arcString;
	}
	
	/**
	* crea la stringa di inserimento di  un nuovo record
	*
	* @access protected
	*
	* @param $table tabella di riferimento
	* @param $record array contenente i dati da inserire 
	*
	* @return string
	*/
	protected function createEntry($table, $data){
		$i= 0;
		$sql= "INSERT INTO ".$table." (";
		foreach ($data as $nomeCampo=>$valoreCampo){
			$sql.= $nomeCampo.", ";
		}
		$sql= substr($sql, 0, strlen($sql)-2);
		$sql.= ") VALUES (";
		foreach ($data as $keys=>$val){
			$sql.= "'".trim($val)."', ";
		}
		$sql= substr($sql, 0, strlen($sql)-2);
		$sql.= ")";
		return $sql;
	}
	
	/**
	* crea la stringa di modifica di un  record
	*
	* @access protected
	*
	* @param $table tabella di riferimento
	* @param $record array contenente i dati da inserire 
	*
	* @return string
	*/
	protected function modifyEntry($table, $data, $recordIdName, $recordId){
		$sql="UPDATE ".$table." SET ";
		foreach ($data as $nomeCampo=>$valoreCampo){
				$sql.= $nomeCampo."='".$valoreCampo."', ";
		}
		$sql= substr($sql, 0, strlen($sql)-2);
		$sql.= " WHERE ".$recordIdName."='".$recordId."'";
		
		return $sql;
	}
	
	/**
	* Processa i dati provenienti da una form e li restituisce in un array pronto per il metodo createEntry.
	*
	* @access protected
	*
	* @param DOMNodelist $fields.
	* @param array $data array contenente i dati da inserire
	* @param $tableInsert nome della tabella nel quale  si effettua l'inserimento o la modifica.
	* @param Object $connect connessione al database.
	* @param string $mode indica se il processamento dei dati � per una form di modifica (M) o inserimento (N)
	*
	* @return string
	*/
	protected function controlDataForm($fields, $data, $oldData, $tableInsert, $connect, $mode){
	
		$dataProcessed=array();		
		//gestione controllo valore dei campi della form.
		foreach(XMLParser::getChildNodes($fields) as $field){

			$fieldFormName=XMLParser::getNodeAttribute($field, 'formName');
			$fieldDbName=XMLParser::getNodeAttribute($field, 'dbName');
			$fieldFormValue=$data[$fieldFormName];
			$oldFieldFormValue=$oldData[$fieldDbName];
			
			if($mode=="M"){$params="modifyEntryParameters";} else{$params="newEntryParameters";}
			
			$parameters=XMLParser::getChildNode($field, $params);
			if(XMLParser::getChildNodeValue($parameters, 'fieldProcess')==1){
				
				if(XMLParser::getChildNodeAttribute($field, 'fieldChecks', 'active')==1){
	
					$checks=$this->checkFieldForm($fieldFormValue, $oldFieldFormValue, $field, $tableInsert, $connect, $mode);
					
					if($checks){
						$dataProcessed[$fieldDbName]=$fieldFormValue;			
					}else{
						return -1;				
					}
				}else{
					$dataProcessed[$fieldDbName]=$fieldFormValue;			
				}
			}
		}
		return $dataProcessed;
	}	
	
	/**
	* Effettua i controlli definiti nel container XML sul generico campo della form.
	*
	* @access protected
	*
	* @param $fieldFormValue valore inserito nel campo della form.
	* @param DOMElement $fieldFormXML 
	* @param $tableInsert nome della tabella nel quale  si effettua l'inserimento o la modifica.
	* @param Object $connect connessione al database.
	* @param string $mode indica se il processamento dei dati � per una form di modifica (M) o inserimento (N)
	*
	* @return array
	*/
	protected  function checkFieldForm($fieldFormValue, $oldFieldFormValue, $fieldFormXML, $tableInsert, $connect, $mode){
		$control=0;
		$fieldType=XMLParser::getChildNodeAttribute($fieldFormXML, 'type', 'val');
		if(XMLParser::getChildNodeValue($fieldFormXML, 'empty')==1){	
			if(!$this->checkEmpty($fieldFormValue, XMLParser::getNodeAttribute($fieldFormXML, 'label'))){$control=1;}
		}
		if(XMLParser::getChildNodeValue($fieldFormXML, 'type')==1){
			
			if($fieldType=="int"){
				if(!$this->checkInt($fieldFormValue, XMLParser::getNodeAttribute($fieldFormXML, 'label'))){$control=1;}
			}
			if($fieldType=="float"){
				if(!$this->checkFloat($fieldFormValue, XMLParser::getNodeAttribute($fieldFormXML, 'label'))){$control=1;}
			}
			if($fieldType=="varchar"){
				
			}			
		}
		
		if(XMLParser::getChildNodeValue($fieldFormXML, 'whiteSpace')==1){	
			if(!$this->checkWhiteSpace($fieldFormValue,XMLParser::getNodeAttribute($fieldFormXML, 'label'))){$control=1;}
		}
		
		if($mode=="N"){//controllo del duplicato, solo per i nuovi inserimenti.
			if(XMLParser::getChildNodeValue($fieldFormXML, 'duplicate')==1){
				$fieldLabel=XMLParser::getNodeAttribute($fieldFormXML, 'label');
				$fieldDbName=XMLParser::getNodeAttribute($fieldFormXML, 'dbName');
				$caseSensitive=XMLParser::getChildNodeAttribute($fieldFormXML, 'duplicate', 'caseSensitive');	
				if(!$this->checkDuplicateEntry($connect, $fieldFormValue, $fieldLabel, $fieldDbName, $tableInsert, $caseSensitive)){$control=1;}
			}
		}elseif ($mode=="M"){
			if($fieldFormValue!=$oldFieldFormValue AND XMLParser::getChildNodeValue($fieldFormXML, 'duplicate')==1){
				$fieldLabel=XMLParser::getNodeAttribute($fieldFormXML, 'label');
				$fieldDbName=XMLParser::getNodeAttribute($fieldFormXML, 'dbName');
				$caseSensitive=XMLParser::getChildNodeAttribute($fieldFormXML, 'duplicate', 'caseSensitive');	
				if(!$this->checkDuplicateEntry($connect, $fieldFormValue, $fieldLabel, $fieldDbName, $tableInsert, $caseSensitive)){$control=1;}
			}
		}
		if($control==0){return true;}else{return false;}
	}	
	
	/**
	* Controlla che il valore inserito nel campo della form sia un intero.
	*
	* @access private
	*
	* @param $value valore inserito nel campo della form.
	* @param string $fieldLabel label associata al campo della form
	*
	* @return boolean true se il controllo va a buon fine altrimenti false.
	*/
	private function checkInt($value, $fieldLabel){
		if(trim($value=='0') OR trim($value=='')){
			return true;
		}else{
			$parsed=str_split($value);
			$control=0;
			foreach ($parsed as $key=>$val){
				if(intval($val)==0 AND $val!="0"){
					$control=1;
				}
			}
			if($control==0){
				return true;
			}else{
				echo UserInterface::confirmMsg("ATTENZIONE: campo ".$fieldLabel." vuoto o non e un intero", 3);
				return false;	
			}
		}
	}
	
	/**
	* Controlla che il valore inserito nel campo della form sia un float.
	*
	* @access private
	*
	* @param $value valore inserito nel campo della form.
	* @param string $fieldLabel label associata al campo della form
	*
	* @return boolean true se il controllo va a buon fine altrimenti false.
	*/
	private function checkFloat($value, $fieldLabel){
		if(trim($value=='0')){
			return true;
		}else{
			$parsed=str_split($value);
			$control=0;
			foreach ($parsed as $key=>$val){
				if(intval($val)==0 AND $val!="."){
					$control=1;
				}
			}
			if($control==0){
				return true;
			}else{
				echo UserInterface::confirmMsg("ATTENZIONE: campo ".$fieldLabel." vuoto o non e un numero con la virgola", 3);
				return false;	
			}
		}
	}
	
	/**
	* Controlla che il valore inserito nel campo della form non sia vuoto.
	*
	* @access private
	*
	* @param $value valore inserito nel campo della form.
	*
	* @return boolean true se il controllo va a buon fine altrimenti false.
	*/
	protected function checkEmpty($value, $fieldLabel){
		if(trim($value=='0')){
			return true;
		}else{
			if(trim(empty($value))){
				echo UserInterface::confirmMsg("ATTENZIONE: campo ".$fieldLabel." vuoto", 3);
				return false;
			}else{
				return true;	
			}
		}
	}
	
	/**
	* Controlla che il valore da inserire nella tabella specificata non sia gi� esistente nelle form.
	*
	* @access private
	*
	* @param Object $connect connessione al db.
	* @param $value valore inserito nel campo della form.
	* @param string $fieldLabel label associata al campo della form
	* @param string $fieldDbName nome del campo del db da controllare.
	* @param string $table tabella che contiene il valore da controllare.
	* @param int $caseSensitive flag che ci dice se fare il controllo in maniera case sensitive.
	*
	* @return boolean true se il controllo va a buon fine altrimenti false.
	*/
	private function checkDuplicateEntry($connect, $value, $fieldlabel, $fieldDbName, $table, $caseSensitive){
		
		$sql="SELECT ".$fieldDbName." 
				FROM ".$table." 
				WHERE ".$fieldDbName."='".trim($value)."'";
		$contrValue=DBInterface::execQuery($connect,  $sql, "", 0);
		
		$control=0;
		if($caseSensitive==0){
			foreach ($contrValue as $key=>$row){
				if(strtolower(trim($row[$fieldDbName]))==strtolower(trim($value))){
					$control=1;
				}		
			}
		}else{
			foreach ($contrValue as $key=>$row){
				if(trim($row[$fieldDbName])==trim($value)){
					$control=1;
				}		
			}
		}
		
		if($control==1){
			echo UserInterface::confirmMsg("ATTENZIONE: ".$fieldlabel." gia esistente", 3);
			return false;
		}else{
			return true;
		}
	}
	
	/**
	* Controlla che il valore inserito nel campo della form non contenga spazi vuoti.
	*
	* @access private
	*
	* @param $value valore inserito nel campo della form.
	* @param string $fieldLabel label associata al campo della form
	*
	* @return boolean true se il controllo va a buon fine altrimenti false.
	*/
	private function checkWhiteSpace($value, $fieldLabel){
		$control=true;	
		for ($i=0;$i<=strlen($value);$i++){
			if($value[$i]==" "){
		 		$control=false;
		 		break;
		 	}
		}
		if(!$control){
			echo UserInterface::confirmMsg("ATTENZIONE: il nome del campo ".$fieldLabel." non puo contenere spazi vuoti", 3);
		}
		return $control;
	}
	
	/**
	* Recupera i dati per la form di modifica e li restituisce in un array destinato al printing della form di modifica.
	*
	* @access protected
	*
	* @param DOMDocument $xmlSchema
	* @param $recordId valore della chiave esterna per la select sulla tabella.
	* @param Object $connect connessione al database.
	* @param $recordIdName nome della chiave esterna per la select sulla tabella.
	*
	* @return string
	*/
	protected function getDataFormForModify($xmlSchema, $recordId, $connect, $recordIdName){
		
		$form=XMLParser::getNode($xmlSchema, 'form', 0);
		$fields=XMLParser::getChildNode($form, 'fields');
		$tableMod=XMLParser::getDocumentNodeValue($xmlSchema, 'table', 0);
		$foreignKey=XMLParser::getDocumentNodeValue($xmlSchema, 'foreignKey', 0);
		
		//costruizione della select per il recupero dei dati del record da modificare per la singola tabella.
		$sqlEntry="SELECT ";
		foreach(XMLParser::getChildNodes($fields) as $field){
			$modifyParameters=XMLParser::getChildNode($field, 'modifyEntryParameters');
			$fieldDbName=XMLParser::getNodeAttribute($field, 'dbName');
			if(XMLParser::getChildNodeValue($modifyParameters, 'fieldProcess')==1){
				$sqlEntry.=$fieldDbName.", ";
			}
		}
		$sqlEntry= substr($sqlEntry, 0, strlen($sqlEntry)-2);
		$sqlEntry.=" FROM ".$tableMod ." WHERE ".$foreignKey."=".$recordId;
		
		//esecuzione della select dei campi che mi interessano sulla tabella.
		$dataSelect=DBInterface::execQuery($connect,  $sqlEntry, "", 0);
		
		return $dataSelect[0];
	}
	
	/**
	* Recupera i dati per la visualizzazione e li restituisce in un array destinato al printing delle informazioni.
	*
	* @access protected
	*
	* @param DOMDocument $xmlSchema
	* @param $recordId valore della chiave esterna per la select sulla tabella.
	* @param Object $connect connessione al database.
	* @param $recordIdName nome della chiave esterna per la select sulla tabella.
	*
	* @return string
	*/
	protected function getDataVis($xmlSchema, $recordId, $connect, $recordIdName){
		
		$form=XMLParser::getNode($xmlSchema, 'form', 0);
		$fields=XMLParser::getChildNode($form, 'fields');
		$tableVis=XMLParser::getDocumentNodeValue($xmlSchema, 'table', 0);
		$foreignKey=XMLParser::getDocumentNodeValue($xmlSchema, 'foreignKey', 0);
		
		//costruizione della select per il recupero dei dati del record da visualizzare.
		$sqlEntry="SELECT ";
		foreach(XMLParser::getChildNodes($fields) as $field){
			$fieldDbName=XMLParser::getNodeAttribute($field, 'dbName');
			$sqlEntry.=$fieldDbName.", ";
		}
		$sqlEntry= substr($sqlEntry, 0, strlen($sqlEntry)-2);
		$sqlEntry.=" FROM ".$tableVis ." WHERE ".$foreignKey."=".$recordId;
		
		//esecuzione della select dei campi che mi interessano sulla tabella.
		$dataSelect=DBInterface::execQuery($connect,  $sqlEntry, "", 0);
		
		return $dataSelect[0];
	}
	
} /* end of class DataProcessors */
?>