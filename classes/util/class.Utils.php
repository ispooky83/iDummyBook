<?php
/**
 * Contiene metodi generici di publbica utilità.
 *
 * @access public
 * @author Lorenzo Monaco
 * @package Utils
 */
 
class Utils{
	// --- ATTRIBUTES ---

	// --- OPERATIONS ---
	
	/**
	*costruttore della classe.
	*
	* @return void
	*/
	public function __construct(){
		
	}

	/**
	* Restituisce una stringa random.
	*
	* @access public
	* @return boolean
	*/
	public function getRandomStr(){
		$md5Node= md5(uniqid(rand(), true));
		return $md5Node;
	}
	
} /* end of class Utils */
?>