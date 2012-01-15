<?php
/**
 * Gestisce le classi di amministrazione dell'interfaccia utente.
 * Estende la main class dello Smarty Template Engine
 *
 * @author Lorenzo Monaco
 * @package UserInterface
 */

/* user defined includes */
require(SMARTY_DIR."Smarty.class.php");

/* user defined constants */

/**
 * Estende Smarty class ed espone metodi specifici per l'output utente.
 *
 * @access public
 * @author Lorenzo Monaco
 */
class UserInterface extends Smarty{
	 // --- ATTRIBUTES ---
	 private $mycache= '';
	 private $template= '';
	 private $outpage= '';
	 private $variables= array();
	 private $templateOut;
	 private $cssPath;
	 private $imgPath;

	// --- OPERATIONS ---
	/**
	* @return void
	* @param boolean $c caching abilitato o disabilitato
	* @param string $t template in uso; se non specificato verrˆ usato quello di sistema
    	* @param array $v variabili da assegnare e visualizzare in output
    	* @param $outpage nome della pagina di template da usare
	*/
	public function __construct($c, $t, $v, $o){
		$this->mycache= $c;
		$this->template= $t;
		$this->variables= $v;
		$this->outpage= $o;
	}
    	/**
    	* Estende Smarty template system e aggiungiungendo le variabili di sistema:
    	* caching e directory per il funzionamento del template engine.
     	* @access public
     	* @return void
     	*/
    	public function templateHandler(){
    		try{
    			$this->Smarty();
    			
    			if(!opendir(DOCUMENT_ROOT.TEMPLATE_DIR.'templates')){
    				throw new Exception('Impossibile aprire la directory. Directory <b>templates</b> non presente');
    			}else{
    				$this->template_dir= DOCUMENT_ROOT.TEMPLATE_DIR.'templates';
    			}
    			
    			if(!opendir(DOCUMENT_ROOT.TEMPLATE_DIR.'templates_c')){
    				throw new Exception('Impossibile aprire la directory. Directory <b>templates_c</b> non presente');
    			}else{
    				$this->compile_dir= DOCUMENT_ROOT.TEMPLATE_DIR.'templates_c';
    			}
    			
    			if(!opendir(DOCUMENT_ROOT.TEMPLATE_DIR.'configs')){
    				throw new Exception('Impossibile aprire la directory. Directory <b>configs</b> non presente');
    			}else{
    				$this->config_dir= DOCUMENT_ROOT.TEMPLATE_DIR.'configs';
    			}
    			
    			if(!opendir(DOCUMENT_ROOT.TEMPLATE_DIR.'cache')){
    				throw new Exception('Impossibile aprire la directory. Directory <b>cache</b> non presente');
    			}else{
    				$this->cache_dir= DOCUMENT_ROOT.TEMPLATE_DIR.'cache';
    			}
    			
    		}catch(Exception $e){
    			$msg= SystemLogger::formatMsg($e->getLine(), $e->getFile(), $e->getMessage());
    			$systemLogger= new SystemLogger($msg, 'System call', 'arcipelago');
    			$systemLogger->setSystemLogger();
    			$systemLogger->setSystemMsgF($e->getLine(), $e->getFile(), $e->getMessage());
		}
        		$this->caching = $this->mycache;
    	}
    	/**
    	* Produce l'output html. Chiama i metodi specifici di Smarty assign() e display().
    	* @access public
    	* @param booolean $visualization visualizza o meno lo standard output
     	* @return void
    	*/
    	public function guiOutput($visualization, $userGroup, $connect){
    		try{
    			if($visualization){
    				if(count($this->variables)>=1 AND $this->variables!=''){
    					foreach ($this->variables as $key=>$value){
    						$this->assign($key, $value);
    					}
    				}else{
    					throw new Exception('Impossibile assegnare variabili per l\'output html');
    				}
    			}else{
    				$this->assign('user', $this->variables['user']);
    				$this->assign('userGroup', $this->variables['userGroup']);
    				$this->assign('leftMenu', $this->variables['leftMenu']);
    				$this->assign('subHeaderMenu', $this->printSubHeaderMenu($connect, $userGroup));
    				$this->assign('body', 'PERMESSI INSUFFICIENTI PER ESEGUIRE L\'OPERAZIONE RICHIESTA');
    			}

    			if(!opendir(DOCUMENT_ROOT.TEMPLATE_DIR.TEMPLATES_DIR.TEMPLATE.'css/')){
    				throw new Exception('Impossibile aprire la directory. Directory <b>css</b> non presente');
    			}else{
    				$this->cssPath= URL_PATH.TEMPLATE_DIR.TEMPLATES_DIR.TEMPLATE.'css/';
    				$this->assign('cssPath', $this->cssPath);
    			}

    			if(!opendir(DOCUMENT_ROOT.TEMPLATE_DIR.TEMPLATES_DIR.TEMPLATE.'img/')){
    				throw new Exception('Impossibile aprire la directory. Directory <b>img</b> non presente');
    			}else{
    				$this->imgPath= URL_PATH.TEMPLATE_DIR.TEMPLATES_DIR.TEMPLATE.'img/';
    				$this->assign('imgPath', $this->imgPath);
    			}
				
    			if($this->template==0){
    				$this->templateOut= TEMPLATE;
    			}else{
    				$this->templateOut= $this->template;
    			}

    			$this->display($this->templateOut.$this->outpage);

    		}catch (Exception $e){
    			$msg= SystemLogger::formatMsg($e->getLine(), $e->getFile(), $e->getMessage());
    			$systemLogger= new SystemLogger($msg, 'System call', 'arcipelago');
    			$systemLogger->setSystemLogger();
    			$systemLogger->setSystemMsgF($e->getLine(), $e->getFile(), $e->getMessage());
    		}
    	}
    	
    	/**
    	* Produce l'output html per il subheader menu
    	* @access public
     	* @return string
    	*/
    	public static function printSubHeaderMenu($db, $groupId){
    		$retValue= "";
    		$menuRec= DBInterface::execQuery($db, "SELECT * FROM ui_subheader WHERE  ui_subheader_group>=".$groupId." ORDER BY ui_subheader_order", "", 0);
    		foreach($menuRec as $value){
    			$retValue.= "<a href=\"".URL_PATH.$value['ui_subheader_url']."\">".$value['ui_subheader_nome']."</a> - \n";
    		}
    		$retValue= substr($retValue, 0, strlen($retValue)-3);
    		return $retValue;
    	}
    	/**
    	* Produce l'output html per i left menu contestuali
    	* @access public
    	* @param int $area area di riferimento del sistema
     	* @return string
    	*/
    	public function printLeftMenu($db, $groupId, $area){
    		$leftMenu= "";
    		try{
    			$menuRec= DBInterface::execQuery($db, "SELECT * FROM ui_leftmenu WHERE  ui_leftmenu_group>=".$groupId." AND ui_leftmenu_areaId='".$area."' ORDER BY ui_leftmenu_order", "", 0);
    			foreach($menuRec as $value){
    				if($value['ui_leftmenu_label']==1){
    					$leftMenu.="<tr><td class=\"internal\"><B title=\"".$value['ui_leftmenu_title']."\">".$value['ui_leftmenu_nome']."</B></td></tr>\n";
    				}

    				if($value['ui_leftmenu_link']==1){
    					$leftMenu.="<tr><td class=\"internal\">&nbsp;&nbsp;<a href=\"".URL_PATH.$value['ui_leftmenu_url']."\" title=\"".$value['ui_leftmenu_title']."\">".$value['ui_leftmenu_nome']."</a></td></tr>\n";
    				}

    				if($value['ui_leftmenu_ulink']==1){
    					$leftMenu.="<tr><td class=\"internal\">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;-<a href=\"".URL_PATH.$value['ui_leftmenu_url']."\" title=\"".$value['ui_leftmenu_title']."\">".$value['ui_leftmenu_nome']."</a></td></tr>\n";
    				}

    				if($value['ui_leftmenu_hr']==1){
    					$leftMenu.="<tr><td class=\"internal\"><hr noshade size=\"1\" align=\"left\" width=\"95%\"></td></tr>\n";
    				}
    			}
    		}catch (Excetion $e){
    			$systemLogger= new SystemLogger("Left menu non valorizzato: ".$e->getMessage(), "System call", "arcipelago");
    			$systemLogger->setSystemLogger();
    			$systemLogger->setSystemMsg();
    		}
    		return $leftMenu;
    	}
    	/**
    	* Produce l'output html per una immagine
    	* @access public
     	* @return string
    	*/
    	public static function printImage($imgPath, $imgTitle){
    		$retValue= "<img src=\"".$imgPath."\" border=\"0\" title=\"".$imgTitle."\">";
    		return $retValue;
    	}
    	/**
    	* Produce l'output html per i right menu contestuali
    	* @access public
    	* @param int $subArea Subarea di riferimento del sistema
     	* @return string
    	*/
    	public function printRightMenu($subArea){
    		return "";
    	}
	
	/**
	* Gestisce i messaggi di conferma javascript
	* @param $msg Messaggio di conferma
	* @access public
	* @return string
	*/
	public function confirmMsg($msg, $type){
		switch ($type){
			case 1:
			$msg= "onClick=\"javascript: GP_popupConfirmMsg('".$msg."'); return document.MM_returnValue;\"";
			break;
			
			case 2:
			$msg= "<script>alert('".$msg."')</script>";
			break;
		
			case 3:
			$msg= "<script>alert('".$msg."');\n";
			$msg.= "history.back();</script>";
			break;
			
			case 4:
			$msg= "<script>alert('".$msg."');\n";
			$msg.= "window.opener.location.reload();
					self.close();
					</script>";
			break;
		}
	return $msg;
	}
	
	/**
	* Gestisce i redirect all'url specificato.
	* @param $url 
	* @access public
	* @return string
	*/
	public function redirect($url, $type){
		switch ($type){
			case 1:
			$red= "<script>document.location='".$url."'</script>";
			break;
		}
	return $red;
	}
	
	/**
	* Gestisce la creazione e visualizzazione di una finestra popup
	* @param $popFile file della popup
	* @param $wh lunghezza e altezza
	* @param $name popup name
	* @return string
	*/
	public function displayPopUp($popFile, $popName, $wh, $scriptParam){
		if($scriptParam==1){
			$out= "<script>window.open('".$popFile."','".$popName."','width=".$wh[0].", height=".$wh[1].", location=no, menubar=no, status=no, toolbar=no, scrollbars=yes, resizable=yes');</script>";
		}else{
			$out= "window.open('".$popFile."','".$popName."','width=".$wh[0].", height=".$wh[1].", location=no, menubar=no, status=no, toolbar=no, scrollbars=yes, resizable=yes');";
		}
		return $out;
	}
	
    /**
	* Gestisce la formattazione di una data in formato aaaammgg
	* @param $strdate stringa data
	* @param $sep separatore
	* @return string
	*/
	public function getDate($strdate, $sep){
		$anno=substr($strdate,0,4);
		$mese=substr($strdate,4,2);
		$giorno=substr($strdate,6,2);
	
		return $giorno.$sep.$mese.$sep.$anno;
	}
	
	/**
	* Gestisce la suddivisione di una data in formato aaaammgg
	* @param $strdate stringa data
	* @return array
	*/
	public function substrDate($strdate){
		$anno=substr($strdate,0,4);
		$mese=substr($strdate,4,2);
		$giorno=substr($strdate,6,2);
	
		return array($giorno, $mese, $anno);
	}
	
	/**
	* Gestisce la suddivisione di una data con ora in formato aaaammgghhmm
	* @param $strdate stringa data
	* @return string
	*/
	public function substrDateTime($strdate){
		$strdate= explode("-", $strdate);
		$giorno= $strdate[0];
		$mese= $strdate[1];
		$annoOra= $strdate[2];
		$annoOra= explode(" ", $annoOra);
		$anno= $annoOra[0];
		$oreMinuti= $annoOra[1];
		$oreMinuti= explode(":", $oreMinuti);
		$ore= $oreMinuti[0];
		$minuti= $oreMinuti[1];		
		
		return $anno.$mese.$giorno.$ore.$minuti;
	}
	
	/**
	* Gestisce la suddivisione di una data con ora in formato aaaammgghhmm
	* @param $strdate stringa data
	* @return array
	*/
	public function getDateTime($strdate, $sep1, $sep2){
		if(!empty($strdate)){
			$anno= substr($strdate,0,4);
			$mese= substr($strdate,4,2);
			$giorno= substr($strdate,6,2);
			$ore= substr($strdate, 8,2);
			$minuti= substr($strdate, 10,2);
			return $giorno.$sep1.$mese.$sep1.$anno." ".$ore.$sep2.$minuti;
		}else{
			return "";
		}
	}
	
	/**
	* Gestisce la visualizzazione dei messaggi associati ai vari status 
	*
	* @param Object $db oggetto connessione al database 
	* @param int $idStatus id dello status nella tabella arc_status
	*
	* @return string messaggio associato allo status
	*/
	public function getStatus($db, $idStatus, $statusArea){
		try{
			$ret=array();
			$status= DBInterface::execQuery($db, "SELECT * FROM arc_status WHERE  arc_status_id='".$idStatus."' AND arc_status_area='".$statusArea."'", "", 0);
			$ret[0]=$status[0]['arc_status_nome'];
			$ret[1]=$status[0]['arc_status_css'];
			return $ret;
			
		}catch(Exception $e){
			$systemLogger= new SystemLogger("Errore esecuzione query ".$e->getMessage(), "System call", "");
    			$systemLogger->setSystemLogger();
    			$systemLogger->setSystemMsg();
			return "";
		}
	}
	
} /* end of class SystemVariables */
?>
