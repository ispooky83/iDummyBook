<?php
/**
 * Si interessa del logging di eventi ed errori. Espone due metodi principali
 * il logging su database e per il logging su file
 *
 * @author Lorenzo Monaco
 */

/* user defined includes */
/* user defined constants */

/**
 * Si interessa del logging di eventi ed errori. Espone due metodi principali
 * il logging su database e per il logging su file
 *
 * @access public
 * @author Lorenzo Monaco
 */
class SystemLogger{
	// --- ATTRIBUTES ---
	private $str= "";
	private $servizio= "";
	private $utente= "";
	private $ip= "";
	private $absPath= LOG_PATH;
	private $fileName= LOG_FILE_NAME;
	private $remote_addr= REMOTE_ADDRESS;
	private $userUse= "";
	
	// --- OPERATIONS ---
	
	/**
	* @return void
	* @param string $s messaggio da loggare
	* @param string $se servizio che ha generato il log
	* @param string $u utente che ha generato il log
	* @desc Enter description here...
	*/
	public function __construct($s, $se, $u){
		$this->str= $s;
		$this->servizio= $se;
		$this->userUse= SESSIONUSER;
	}

	/**
	* Genera un log su file.
	*
	* @access public
	* @return void
	*/
	public function setSystemLogger(){	
		if(!file_exists($this->absPath.$this->fileName)){
			$fd= fopen($this->absPath.$this->fileName, "w") or die ("ATTENZIONE: Impossibile creare il file di log");
			fwrite($fd, "#Arcipelago log file\n"."[".date("Y-m-d H:i:s")."] [System Logger] [SystemLogger]: Reinizializzazione del log file arcipelago.log\n");
			$returnValue = (bool) false;
		}else{
			$fd= fopen($this->absPath.$this->fileName, "a");
			fwrite($fd, "[".date("Y-m-d H:i:s")."] [".$this->userUse."@".$this->remote_addr."] [".$this->servizio."]: ".$this->str."\n");
			fclose($fd);
			$returnValue = (bool) true;
		}
	}
	
	/**
	* Genera un log/messaggio da visualizzare sull'interfaccia grafica // Formattazione n¡1
	*
	* @access public
	* @return void
	*/
	public function setSystemMsg(){
		if(VIEW_LOG_ERROR==1){
			echo "<b>ATTENZIONE: si &egrave; verificato un errore, segue il tracing del problema</b><br>";
			echo "<b>Data</b>: ".date("Y-m-d H:i:s")."<br>";
			echo "<b>Utente</b>: ".$this->userUse."@".$this->remote_addr."<br>";
			echo "<b>Servizio</b>: ".$this->servizio."<br>";
			echo "<b>Debug</b>: ".$this->str."<br>";
		}
	}
	
	/**
	* Genera un log/messaggio da visualizzare sull'interfaccia grafica // Formattazione n¡2
	*
	* @access public
	* @return void
	*/
	public function setSystemMsgF($line, $file, $message){
		echo "<b>ATTENZIONE: si &egrave; verificato un errore, segue il tracing del problema</b><br>";
		echo "<b>Data</b>: ".date("Y-m-d H:i:s")."<br>";
		echo "<b>Utente</b>: ".$this->utente."@".$this->remote_addr."<br>";
		echo "<b>Servizio</b>: ".$this->servizio."<br>";
		echo "<b>Linea</b>: ".$line."<br>";
		echo "<b>File</b>: ".$file."<br>";
		echo "<b>Messaggio</b>: ".$message."<br>";
	}
	
	/**
	* Genera un log/messaggio da visualizzare in console
	*
	* @access public
	* @return void
	*/
	public function setSystemMsgConsole(){
		echo "ATTENZIONE: si  verificato un errore, segue il tracing del problema\n";
		echo "Data: ".date("Y-m-d H:i:s")."\n";
		echo "Utente: ".$this->userUse."@".$this->remote_addr."\n";
		echo "Servizio: ".$this->servizio."\n";
		echo "Debug: ".$this->str."\n";
	}
	
	/**
	* Formatta messaggio di errore generato dal compilatore PHP
	*
	* @access public
	* @param $line string
	* @param $file string
	* @param $message string
	* @return string
	*/
	public function formatMsg($line, $file, $message){
		return "Linea: ".$line." File: ".$file." Errore: ".$message;
	}
	
	/**
	* Genera un log su DB
	*
	* @access public
	* @return boolean
	*/
	public function setDBLogger(){
		$returnValue = (bool) false;
		return (bool) $returnValue;
	}
	
	/**
	* Recupera un log dal file log
	*
	* @access public
	* @return String
	*/
	public function getSystemLogger(){
		$returnValue = null;
		return $returnValue;
	}
	
	/**
	* Recupera un log dal database di sistema
	*
	* @access public
	* @return String
	*/
	public function getDBLogger(){
		$returnValue = null;
		return $returnValue;
	}

} /* end of class SystemLogger */

?>