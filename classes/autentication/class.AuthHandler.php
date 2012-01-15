<?php
/**
 * Gestisce l'autenticazione tramite pear AUTH
 *
 * @author Lorenzo Monaco
 */
 
/* user defined includes */
require_once('Auth.php');
/* user defined constants */

/**
 * Gestisce l'autenticazione dell'utente
 *
 * @access public
 * @author Lorenzo Monaco
 */
class AuthHandler{
	 // --- ATTRIBUTES ---
	private $outputVar= array();
	private $suHeaderMenu;
	private $userName;
	/**
    	* Crea la maschera di login
    	* @access public
    	* @return void
    	*/
 	public function printLogin(){
 		$this->outputVar['pageTitle']= 'Login';
 		$userInterface= new UserInterface(false, 0, $this->outputVar, 'login.tpl');
 		$userInterface->templateHandler();
 		$userInterface->guiOutput(true, 0, 0);
 	}
    	/**
    	* Gestisce l'autenticazione utente.
     	* @access public
     	* @return void
     	*/
    	public function performAuth($out, $setLog){
    		$params = array("dsn" => MYSQL_CONN_TYPE."://".DB_USER.":".DB_PASSWORD."@".DB_HOST."/".DOMAIN."",
    		"table" => TABLE_UTENTI,
    		"usernamecol" => FIELD_UTENTI_USERNAME,
    		"passwordcol" => FIELD_UTENTI_PASSWORD);
    		
    		$auth= new Auth("DB", $params, 'loginFunction', false);
    		//$auth->setExpire(USER_SESSION_TIME, true);
    		$auth->start();

    		if ($auth->checkAuth()) {
			$this->userName= $auth->getUsername();

    		if($out==0){
    				if($setLog==1){
    					$systemLogger= new SystemLogger('Eseguito login', 'System Logger', $this->userName);
    					$systemLogger->setSystemLogger();
    				}
			}
    		}else{
    			$systemLogger= new SystemLogger('Tentativo di accesso fallito', 'System Logger', $this->userName);
    			$systemLogger->setSystemLogger();
    			$this->printLogin();
    			exit;
    		}
    			
    		if($out==1){
    			$this->userName= $auth->getUsername();
    			$systemLogger= new SystemLogger('Eseguito logout', 'System Logger', $this->userName);
    			$systemLogger->setSystemLogger();
    			$auth->logout();
    			$auth->start();
    			$this->printLogin();
    			exit;
    		}
    	}
    	/**
    	* Restituisce lo userId per l'utente corrente.
     	* @access public
     	* @param obejct $connnect connection object per l'interrogazione al DB di sistema
     	* @return string
     	*/
	 	public function getUserId($connect){
	 		$userId= DBInterface::execQuery($connect, "SELECT sys_user_id 
	 						FROM ".TABLE_UTENTI." 
	 						WHERE sys_user_username='".$this->userName."'", TABLE_UTENTI, 0);
	 		foreach ($userId as $key=>$value){
	 			if($userId[0]){
	 				foreach ($value as $kk=>$vv){
	 					return $vv;
	 				}
	 			}
	 		}
	 	}
	 	
	 	/**
    	* Restituisce lo username per l'utente corrente.
     	* @access public
     	* @return string
     	*/
	 	public function getUsername(){
	 		return $this->userName;
	 	}
	 	
	 	/**
    	* Restituisce il nome del gruppo di apparteneneza per l'utente corrente.
    	* @param obejct $connnect connection object per l'interrogazione al DB di sistema
     	* @access public
     	* @return string
     	*/
	 	public function getUserGroup($connect, $groupId){
	 		$groupName= DBInterface::execQuery($connect, "SELECT sys_group_nome 
	 						FROM sys_group 
	 						WHERE sys_group_id='".$groupId."'", "sys_group", 0);
	 		if($groupName[0]){
	 			return $groupName[0]['sys_group_nome'];
	 		}else{
	 			return "";
	 		}
	 	}
} /* end of class SystemVariables */

?>