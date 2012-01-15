<?php
/**
 * Gestisce le funzionalitˆ di profiling utente.
 *
 * @author Lorenzo Monaco
 */

/* user defined includes */
/* user defined constants */

/**
 * Inizializza le variabili di sistema e le rende visibili alle classi che
 * chiamano il metodo getVar()
 *
 * @access public
 * @author Lorenzo Monaco
 */
class SystemPermission{
	 // --- ATTRIBUTES ---
	 private $area= 0;
	 private $user= 0;
	 private $group= 0;
	 private $permission=array();
	 
	// --- OPERATIONS ---
	/**
	* @param int $a area ID di riferimento per l'interrogazione al DB di sistema
	* @param int $g user ID di riferimento per l'interrogazione al DB di sistema
	* @param int $c oggetto per la connessione al DB di sistema		
	* @return void
	*/
	public function __construct($a, $u, $c){
		$this->area= $a;
		$this->user= $u;
		$this->setPermissions($c);
	}
	
	// --- OPERATIONS ---
	/**	
	* @return void
	*/
	public function getPermission(){
		return $this->permission;
	}
	
	
	/**
    	* Verifica se il permesso specificato nel parametro $code, e' tra quelli appartenenti
    	* all'utente nell'area di interesse.
    	*
   	* @access public
     	* @param int userid
     	* @return boolean
     	*/
    	public function checkPermission($user, $permission, $connect){
    		$userRetrieve= DBInterface::execQuery($connect, "SELECT ".TABLE_UTENTI.".sys_user_rif_group_id FROM ".TABLE_UTENTI." 
    									WHERE ".TABLE_UTENTI.".sys_user_id='".$user."'", "", 0);
    		foreach ($userRetrieve as $key=>$value){
    			foreach($value as $val){
    				$groupId= $val;
    			}
    		}
    		
    		if($groupId<=$permission){
    			$retvalue= true;
    		}else{
    			$retvalue= false;
    		}
    		
    		return array("auth"=>$retvalue, "userGroup"=>$groupId);
    	}
	
    	/**
     	*/
    	private function setPermissions($connect){
    	}
    	/**
     	*/
    	private function setGroup($connect){
    	}
} /* end of class SystemPermission */
?>