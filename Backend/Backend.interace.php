<?php
/**
 * @author Matt Labrum <matt@labrum.me>
 * @license Beerware
 * @link url
 */
interface Cache_Backend{
	
	/**
	* Initializes the backend
	* @param Array options
	*/
	public function __construct($options);
	
	/**
	* Loads the save data under $name
	* @param String name
	*/
	public function load($name);

	/**
	* Saves the provided data
	* @param String name
	*/
	public function save($name, $value, $time=false);
	
	/**
	* Removes the data
	* @param String name
	*/
	public function remove($name);
	
	/**
	* Cleans any old data, or data with a time limit
	* @param String name
	*/
	public function clean();
}

?>