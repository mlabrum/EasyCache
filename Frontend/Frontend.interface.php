<?php
/**
 * @author Matt Labrum <matt@labrum.me>
 * @license Beerware
 * @link url
 */
interface Cache_Frontend{

	/**
	* Initializes the frontend
	* @param Array options
	*/
	public function __construct(Array $options, Cache_Backend $backend);
}

?>