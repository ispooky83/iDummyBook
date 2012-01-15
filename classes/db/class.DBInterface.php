<?php
/**
 * Gestisce l'interfaccia per PDO [Php Data Objects].
 *
 * @author Lorenzo Monaco
 */

/* user defined includes */
//require_once('DB.php');
/* user defined constants */

/**
 * Gestisce i metodi di accesso a PDO, la formulazione delle query e implementa un metodo base [tableLocker] per il l'elaborazione in modalit&agrave; transazionale,
 *
 * @access public
 * @author Lorenzo Monaco
 */
class DBInterface extends PDO{
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
		try{
			$db= new PDO("mysql:host=".$this->db_host.";dbname=".$this->db_name."", $this->db_user, $this->db_password, array(PDO::ATTR_PERSISTENT => true));
			$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
			
		}catch(PDOException $e){
    			$systemLogger= new SystemLogger("Errore di connessione al database: ".$e->getMessage(), "System call", "");
    			$systemLogger->setSystemLogger();
    			$systemLogger->setSystemMsg();
    		}
		return $db;
	}
	/**
	* Distrugge la connessione col database
	*
	* @access public
	* @return void
	*/
	public function setDisConnect($db){
		$db= null;
		try{
			$db= null;
		}catch (PDOException $e){
			$systemLogger= new SystemLogger("Errore di disconnessione dal database: ".$e->getMessage(), "System call", "");
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
		$data= array();		
		try{
			$dataObj= $db->query($query, PDO::FETCH_ASSOC);
			if($dataObj!=NULL){
				foreach ($dataObj as $row) {
					$data[]= $row;
				}
			}
		}catch(PDOException $e){
			$systemLogger= new SystemLogger("Impossibile effettuare la query: ".$query." :".$e->getMessage(), "System call", "");
    			$systemLogger->setSystemLogger();
    			$systemLogger->setSystemMsg();
		}
		return $data;
	}
	
	/**
	* Insert
	* 
	* @access public
	* @param object $db connessione al database
	* @param string $query query da eseguire
	* @return array
	*/
	public function execTransaction($db, $queryArray){				
		try{
			$db->beginTransaction();
			foreach ($queryArray as $queryValue){
				$dataObj= $db->exec($queryValue);
			}
			$db->commit();
		}catch(PDOException $e){
			$db->rollback();
			$systemLogger= new SystemLogger("Impossibile effettuare la query: ".$queryValue." :".$e->getMessage(), "System call", "");
    			$systemLogger->setSystemLogger();
    			$systemLogger->setSystemMsg();
		}
		//return $dataObj;
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
		try{
			$data= $db->query($query, PDO::FETCH_ASSOC);
		}catch(PDOException $e){
			$systemLogger= new SystemLogger("Impossibile effettuare la query: ".$query." :".$e->getMessage(), "System call", "");
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
		if(DBLOCKER_CONTROL==1){
			foreach($table as $keys=>$values){
				DBInterface::execQuery($db, "INSERT INTO sys_dblocker (sys_dblocker_dbname, sys_dblocker_tblname, sys_dblocker_recid, sys_dblocker_user) 
									VALUES ('".DOMAIN."', '".$values."', '".$recid."', '".$user."')", "", 0);
			}
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
		if(DBLOCKER_CONTROL==1){
			foreach($table as $keys=>$values){
				DBInterface::execQuery($db, "DELETE FROM sys_dblocker 
								WHERE sys_dblocker_tblname='".$values."'
								AND sys_dblocker_recid='".$recid."' 
								AND sys_dblocker_user='".$user."'", "", 0);
			}
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
		if(DBLOCKER_CONTROL==1){
			$results= DBInterface::execQuery($db, "SELECT * FROM sys_dblocker WHERE sys_dblocker_tblname='".$table."' AND sys_dblocker_recid='".$recid."'", "", 0);
			foreach ($results as $key=>$values){
				if((count($values))>=1 AND $values['sys_dblocker_user']!=$_SESSION['_authsession']['username']){
					echo UserInterface::confirmMsg("ATTENZIONE RECORD LOCKATO -- UTENTE: ".$values['sys_dblocker_user']."", 2);
					return true;
				}else{
					return false;
				}
			}
		}else{
			return false;
		}
	}
//########################################## METODI GESTIONE DELLE TRANSAZIONI ######################################	
	/**
	* esegue una select nel caso in cui la si debba gestire  all'interno di una transazione
	*
	* @access public
	* 
	* @param Object $db oggetto connessione al database.
	* @param string $query istruzione sql select.
	*
	* @return  array contenente il risultato della select.
	*/
	public function transactionSelect($db, $query){
		$sth = $db->prepare($query);
		$sth ->execute();
		$result = $sth->fetchAll();
		return $result;
	}
	
	/**
	* esegue una generica query (che non sia una select) all'interno di una transazione.
	*
	* @access public
	* 
	* @param Object $db oggetto connessione al database.
	* @param string $query istruzione sql.
	*
	* @return void
	*/
	public function transactionQuery($db, $query){
		$db ->exec($query);
	}
	
	/**
	* inizializza una transazione.
	*
	* @access public
	* 
	* @param Object $db oggetto connessione al database.
	*
	* @return void
	*/
	public function beginTransaction($db){
		$db->beginTransaction();
	}
	
	/**
	* esegue il commit di una transazione.
	*
	* @access public
	* 
	* @param Object $db oggetto connessione al database.
	*
	* @return void
	*/
	public function commit($db){
		$db->commit();
	}
	
	/**
	* esegue il rollback di una transazione.
	*
	* @access public
	* 
	* @param Object $db oggetto connessione al database.
	*
	* @return void
	*/
	public function rollback($db){
		$db->rollback();
	}

//########################################## FINE METODI GESTIONE DELLE TRANSAZIONI ######################################	
} /* end of class NomeClasse */
?>
