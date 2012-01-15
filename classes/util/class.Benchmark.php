<?php
/**
 * Permette la gestione dei benchmark degli script
 *
 * @author  Lorenzo Monaco
 */

/* user defined includes */
/* user defined constants */

/**
 * ###########################
 *
 * @access public
 * @author Lorenzo Monaco
 */
class Benchmark{
	// --- ATTRIBUTES ---

	// --- OPERATIONS ---

	/**
	* #########################
	*
	* @access public
	* @return string
	*/
	public function executionTime($tempoInizio){
		$tempoEsecuzione = $this->getmicrotime()-$tempoInizio;
		return number_format($tempoEsecuzione, 3, ",", ".");
	}
	
	/**
	* Ritorna il tempo di esecuzione in millisecondi
	*
	* @access public
	* @return float
	*/
	public  function getmicrotime(){
		$usec= 0;
		$sec= 0;
		list($usec, $sec) = explode(' ', microtime());
   		return ((float)$usec + (float)$sec);
	}

} /* end of class ############ */
?>