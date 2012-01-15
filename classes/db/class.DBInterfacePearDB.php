<?php
/**
 * Gestisce l'interfaccia per pear DB.
 *
 * @author Lorenzo Monaco
 */

/* user defined includes */
require_once('DB.php');
/* user defined constants */

/**
 * Gestisce i metodi di accesso a pear DB, la formulazione delle query e implementa un metodo base [tableLocker] per il l'elaborazione in modalit&agrave; transazionale,
 *
 * @access public
 * @author Lorenzo Monaco
 */
class DBInterfacePearDB{
	// --- ATTRIBUTES ---
	private $db_user= '';
	private $db_password= '';
	private $db_host= '';
	private $db_name= '';
	
	// --- OPERATIONS ---
	/**
	* DBInterface Constructor.
	*
	* @param string $user username accesso al db
	* @param string $password password accesso al db
	* @param string $host host del database
	* @param string $dbname nome del database da utilizzare
	* @access public
	*/
	public function __construct($user, $password, $host, $dbname){
		$this->db_user= $user;
		$this->db_password= $password;
		$this->db_host= $host;
		$this->db_name= $dbname;
	}
	/**
	* Crea una connessione col database
	*
	* @access public
	* @return object
	*/
	public function setConnect(){
		$dns= MYSQL_CONN_TYPE.'://'.$this->db_user.':'.$this->db_password.'@'.$this->db_host.'/'.$this->db_name;
		$options = array(
    				'debug'       => 2,
    				'portability' => DB_PORTABILITY_ALL,);
    		
    		$db=& DB::connect($dns, $options);
    		
    		if(DB::isError($db)){
    			$systemLogger= new SystemLogger("Errore di connessione al database", "System call", "arcipelago");
    			$systemLogger->setSystemLogger();
    			$systemLogger->setSystemMsg();
    		}
		return $db;
	}
	/**
	* Distrugge la connessione col database
	*
	* @access public
	* @return object
	*/
	public function setDisConnect($db){
		$db->disconnect();
		if(DB::isError($db)){
    			$systemLogger= new SystemLogger("Errore di disconnessione dal database", "System call", "arcipelago");
    			$systemLogger->setSystemLogger();
    			$systemLogger->setSystemMsg();
    		}
	}
	/**
	* Effettua una generica SELECT sul database e se necesario chiama tableLocker.
	* Ritorna un array associativo con i dati della query.
	*
	* @access public
	* @param object $db connessione al database
	* @param string $query query da eseguire
	* @param string $table array contenente la/le tabella/e su cui effettuare l'eventuale lock
	* @param int $lock 0 se non deve essere effettuato il lock, 1 se deve essere effettuato
	* @return array
	*/
	public function execQuery($db, $query, $table, $lock){			
		$db->setFetchMode(DB_FETCHMODE_ASSOC);
		$data=& $db->getAll($query);
		
		if(DB::isError($data)){
    			$systemLogger= new SystemLogger("Impossibile effettuare la query: ".$query, "System call", $_SESSION['_authsession']['username']);
    			$systemLogger->setSystemLogger();
    			$systemLogger->setSystemMsg();
    		}
	
		return $data;
	}
	
	/**
	* Effettua una generica query sul database
	*
	* @access public
	* @param object $db connessione al database
	* @param string $query query da eseguire
	* @param string $table array contenente la/le tabella/e su cui effettuare l'eventuale lock
	* @param int $lock 0 se non deve essere effettuato il lock, 1 se deve essere effettuato
	* @return array
	*/
	public function genericQuery($db, $query, $table, $lock){			
		$db->setFetchMode(DB_FETCHMODE_ASSOC);
		$data=& $db->query($query);
		
		if(DB::isError($data)){
    			$systemLogger= new SystemLogger("Impossibile effettuare la query: ".$query, "System call", "arcipelago");
    			$systemLogger->setSystemLogger();
    			$systemLogger->setSystemMsg();
    		}	
    		
		return $data;
	}
	/**
	* Restituisce un elemento generico del Database
	*
	* @access public
	* @param object $db connessione al database
	* @param string $filed campo scpecifico del db
	* @param string $table tabella di interesse
	* @param string $condValue stringa condizionale
	* @return array
	*/
	function getElement($db, $table, $field, $condValue){
		$sel="SELECT $field FROM $table WHERE $condValue";
		$query=DBInterface::execQuery($db, $sel, "", "");
		foreach($query as $key=>$val){
			foreach($val as $v){
				$value= $v;
			}
		}
		return $value;
	}

	/**
	* Richiede un lock per la tabella ed il record correnti
	*
	* @access public
	* @param string $db db su cui effettuare l'eventuale lock
	* @param string $table tabella su cui effettuare l'eventuale lock
	* @param string $recid id del record da lockare
	* @return void
	*/
	public function tableLocker($db, $table, $recid, $user){
		foreach($table as $keys=>$values){
			DBInterface::execQuery($db, "INSERT INTO sys_dblocker (sys_dblocker_dbname, sys_dblocker_tblname, sys_dblocker_recid, sys_dblocker_user) 
								VALUES ('".DOMAIN."', '".$values."', '".$recid."', '".$user."')", "", 0);
		}
	}
	/**
	* Elimina il lock per la tabella ed il record correnti
	*
	* @access public
	* @param string $db db su cui effettuare l'eventuale lock
	* @param string $table tabella su cui effettuare l'eventuale lock
	* @param string $recid id del record da lockare
	* @return void
	*/
	public function tableUnLocker($db, $table, $recid, $user){
		foreach($table as $keys=>$values){
			DBInterface::execQuery($db, "DELETE FROM sys_dblocker 
							WHERE sys_dblocker_tblname='".$values."'
							AND sys_dblocker_recid='".$recid."' 
							AND sys_dblocker_user='".$user."'", "", 0);
		}
	}
	/**
	* Interroga il dblocker controllare il lock per la tabella ed il record selezionati
	*
	* @access public
	* @param string $db db da controllare
	* @param string $table tabella da controllare
	* @param string $recid id del record da controllare
	* @return boolean
	*/
	public function lockReader($db, $table, $recid){
		$results= DBInterface::execQuery($db, "SELECT * FROM sys_dblocker WHERE sys_dblocker_tblname='".$table."' AND sys_dblocker_recid='".$recid."'", "", 0);
		foreach ($results as $key=>$values){
			if((count($values))>=1 AND $values['sys_dblocker_user']!=$_SESSION['_authsession']['username']){
				echo UserInterface::confirmMsg("ATTENZIONE RECORD LOCKATO -- UTENTE: ".$values['sys_dblocker_user']."", 2);
				return true;
			}else{
				return false;
			}
		}
	}
} /* end of class NomeClasse */
?>
