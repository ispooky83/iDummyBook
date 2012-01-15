<?php
/**
 * Genera oggetti html riutilizzabili
 *
 * @author  Marco Lucidi / Lorenzo Monaco
 * @package UserInterface
 */

/* user defined includes */  

/* user defined constants */
class UIObjects{
	// --- ATTRIBUTES ---
		
	// --- OPERATIONS ---
	
	/**
	* @return void
	*/
	public function __construct(){
		
	}
	
	
	/**	
	*
	* Genera un generico url all'interno di una pagina html.
	*
	* @access public.
	*
	* @param string $page pagina a cui deve puntare il link.
	* @param array $parameters parametri da passare nell'url.
	* @param string $linkName nome visualizzato per il link.
	* @param string $title title del link.
	* @param string $confirmMsg messaggio di conferma nel caso in cui lo si desiderasse.
	*
	* @return string.
	*/
	public function getUrl($page, $parameters, $linkName, $title, $confirmMsg){
		$msg= "";
		if(!empty($confirmMsg)){
			$msg= UserInterface::confirmMsg($confirmMsg, 1);
		}
		$retVal="<a href=\"".$page;
		if(count($parameters)>=1){
			$retVal.="?";
			$counter=1;
			foreach ($parameters as $nameParam=>$valueParam){
				if($counter==1){
					$retVal.=$nameParam."=".$valueParam;
				}else{
					$retVal.="&".$nameParam."=".$valueParam;
				}
				$counter++;
			}
		}
		$retVal.="\" title=\"".$title."\" ".$msg.">".$linkName."</a>";
		return $retVal;
	}
	
	/**	
	*
	* Genera un generico url all'interno di una pagina html con visualizzazione in funzione dei permessi dell'utente.
	*
	* @access public.
	*
	* @param string $page pagina a cui deve puntare il link.
	* @param array $parameters parametri da passare nell'url.
	* @param string $linkName nome visualizzato per il link.
	* @param string $title title del link.
	* @param string $confirmMsg messaggio di conferma nel caso in cui lo si desiderasse.
	* @param int $groupId gruppo di appartenenza dell'utente.
	* @param string $type tipologia di azione
	* @param $containerXML 
	*
	* @return string.
	*/
	public function getUrlPerm($page, $parameters, $linkName, $title, $confirmMsg, $groupId, $type, $containerXML){
		$msg= "";
		
		$xmlParser=new XMLParser();
		$xmlSchema=$xmlParser->getXMLSchema($containerXML);
		$actionPermissionsGroup=$xmlParser->getChildNode($xmlSchema, "actionPermissionsGroup");
		$groupAction=$xmlParser->getChildNodeValue($actionPermissionsGroup, $type);
		//echo $type;
		if($groupId<$groupAction){
		
			if(!empty($confirmMsg)){
				$msg= UserInterface::confirmMsg($confirmMsg, 1);
			}
			$retVal="<a href=\"".$page;
			if(count($parameters)>=1){
				$retVal.="?";
				$counter=1;
				foreach ($parameters as $nameParam=>$valueParam){
					if($counter==1){
						$retVal.=$nameParam."=".$valueParam;
					}else{
						$retVal.="&".$nameParam."=".$valueParam;
					}
					$counter++;
				}
			}
			$retVal.="\" title=\"".$title."\" ".$msg.">".$linkName."</a>";
			return $retVal;
		}else{	
			return "";
		}
	}
	
	public function getButtonRedir(){
		$butt="";
		
	}
	
	
	/**	
	*
	* cre un link per la apertura di una popUp per quegli utenti che ne hanno il permesso
	*
	* @access public.
	*
	* @param string $url 
	* @param int $width 
	* @param int $height 
	* @param string $scroll (yes/no)
	* @param string $label 
	* @param int $groupId gruppo di appartenenza dell'utente.
	* @param string $type tipologia di azione
	* @param $containerXML 
	*
	* @return string.
	*/
	public function openPopUpPerm($url, $width, $height, $scroll, $label, $groupId, $type, $containerXML){
		$xmlParser=new XMLParser();
		$xmlSchema=$xmlParser->getXMLSchema($containerXML);
		$actionPermissionsGroup=$xmlParser->getChildNode($xmlSchema, "actionPermissionsGroup");
		$groupAction=$xmlParser->getChildNodeValue($actionPermissionsGroup, $type);
		if($groupId<$groupAction){
			return "<a href=\"#\" onClick=\"window.open('$url','genid','width=".$width.", height=".$height.", location=no, menubar=no, status=no, toolbar=no, scrollbars=".$scroll.", resizable=yes');\">".$label."</a>";	
		}else{
			return "";
		}
	}
	
	/**	
	*
	* crea un link per la apertura di una popUp.
	* @access public.
	*
	* @param string $url 
	* @param int $width 
	* @param int $height 
	* @param string $scroll (yes/no)
	* @param string $label 
	*
	* @return string.
	*/
	public function openPopUp($url, $width, $height, $scroll, $label){		
		return "<a href=\"#\" onClick=\"window.open('$url','genid','width=".$width.", height=".$height.", location=no, menubar=no, status=no, toolbar=no, scrollbars=".$scroll.", resizable=no');\">".$label."</a>";	
	}
	
	/**	
	*
	* apre una popUp.
	* @access public.
	*
	* @param string $url 
	* @param int $width 
	* @param int $height 
	* @param string $scroll (yes/no)
	* @param string $label 
	*
	* @return string.
	*/
	public function popUp($url, $width, $height, $scroll, $label){		
		return "<script>window.open('".$url."','".$label."','width=".$width.", height=".$height.", location=no, menubar=no, status=no, toolbar=no, scrollbars=".$scroll.", resizable=no');</script>";	
	}
	
	/**	
	*
	* genera il bottone di chiusura di una popUp
	*
	* @access public.
	*
	* @return string.
	*/
	public function closePopUpButton(){
		$imgClose="src=\"".URL_PATH.TEMPLATE_DIR.TEMPLATES_DIR.TEMPLATE."/img/close.jpg\" border=\"0\" title=\"Chiudi popup\"";
		$out.="<input type=\"image\" ".$imgClose." onClick=\"closePopUp();\" class=\"pulsantImg\">";
		return $out;
	}
	
	
	/*
	public function getJumpMenu($connect, $query, $forwards){
		$select="";
		$selectDomains=DBInterface::execQuery($connect, $select, "", 0);
		$ret="<select name=\"jumpMenu\" class=\"textform\" onChange=\"MM_jumpMenu('parent',this,0)\">
		<option value=\"\"></option>";
		foreach ($selectDomains as $key=>$row){
			$ret.="<option value=\"?sys_domain_id=".$row['sys_domain_id']."\">".$row['sys_domain_nome']."</option>";
		}
		$ret.="</select>";
		return $ret;
	}
	*/
	
} /* end of class UIObjects */
?>