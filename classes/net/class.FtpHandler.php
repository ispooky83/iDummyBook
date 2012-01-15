<?php
/**
 * Gestisce il sistema Ftp
 *
 * @author Lorenzo Monaco
 */

/* user defined includes */
/* user defined constants */

/**
 * Gestisce il sistema Ftp:
 *
 * @access public
 * @author Lorenzo Monaco
 */
class FtpHandler{
	// --- ATTRIBUTES ---
	private $mailHost= "";
	private $mailPort= "";
	private $mailAuth= false;
	private $mailType= "";
	
	// --- OPERATIONS ---

	public function __construct(){
			
	}
	
	/**
	* Gestisce la connessione ftp
	*
	* @access public
	* @return object
	*/
	public function ftpConnRet($ftpHost, $ftpUsername, $ftpPassword){
		// Open ftp connection
		$connId= ftp_connect($ftpHost); 
		// Login
		$loginResult = ftp_login($connId, $ftpUsername, $ftpPassword); 
	
		// controllo della connessione
		if ((!$connId) || (!$loginResult)){ 
			//echo ftpSync::dateNow().": Connessione al server $ftpHost FTP fallita.\n";
			die; 
		}else{
			//echo ftpSync::dateNow().": Connessione al server $ftpHost.\n";
		}
	
		return $connId;
	}
	
	/**
	* Upload di un file
	*
	* @access public
	* @return object
	*/
	public function nftpPut(){
		
	}

} /* end of class FtpHandler */
?>