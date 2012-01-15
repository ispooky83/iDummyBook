<?php
/**
 * Gestisce il sistema di mailing
 *
 * @author Lorenzo Monaco
 */

/* user defined includes */
/* user defined constants */

/**
 * Gestisce il sistema di mailing:
 *
 * @access public
 * @author Lorenzo Monaco
 */
class MailHandler{
	// --- ATTRIBUTES ---
	private $mailHost= "";
	private $mailPort= "";
	private $mailAuth= false;
	private $mailType= "";
	
	// --- OPERATIONS ---

	/**
	* @access public
	* @return void
	* @desc
	*/
	public function __construct($host, $port, $auth, $type){
		$this->mailHost= $host;
		$this->mailType= $type;
		$this->mailPort= $port;
		$this->mailAuth= $auth;	
	}

	/**
	* Istanzia un nuovo oggetto di Pear:Mail ed invia una mail semplice
	*
	* @access public
	* @return string
	*/
	public function sendMail($rcptTo, $headers, $body){
		$retValue= "";
		try{
			$params['host']= $this->mailHost;
			$params['port']= $this->mailPort;
			$params['auth']= $this->mailAuth;

			$mailFactory=& Mail::factory("smtp", $params);
			if($mailFactory->send($rcptTo, $headers, stripslashes($body))){
				$retValue= "Mail di notifica inviata con successo";
			}else{
				throw new Exception('Impossibile inviare la mail di notifica.');
			}
			
		}catch(Exception $e){
			$systemLogger= new SystemLogger("Sync Service: ".$e->getMessage(), "System call", "");
    			$systemLogger->setSystemLogger();
    			$systemLogger->setSystemMsgConsole();
		}
		
		return $retValue;
	}

} /* end of class Init */
?>