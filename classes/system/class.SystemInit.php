<?php
/**
 * Gestisce l'inizializzazione del sistema:
 *
 * @author Lorenzo Monaco
 */

/* user defined includes */
/* user defined constants */

/**
 * Gestisce l'inizializzazione del sistema:
 *
 * @access public
 * @author Lorenzo Monaco
 */
class Init{
	// --- ATTRIBUTES ---

	// --- OPERATIONS ---
	

	/**
	*
	*
	* @access public
	* @return boolean
	*/
	public function checkMountedVolumes(){
		$returnValue = (bool) false;
		return (bool) $returnValue;
	}

	/**
	* 
	*
	* @access public
     	* @return boolean
     	*/
     	public function checkSystemConnectivity(){
     		$returnValue = (bool) false;
     		return (bool) $returnValue;
     	}

} /* end of class Init */
?>