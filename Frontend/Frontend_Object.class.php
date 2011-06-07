<?php
/**
 * @author Matt Labrum <matt@labrum.me>
 * @license Beerware
 * @link url
 */
 
namespace EasyCache;
class Frontend_Object implements Cache_Frontend{

	/**
	* Stores an instance of a backend class
	* @var Backend
	*/
	private $backend;
	
	/**
	* Stores the options 
	* @var Array
	*/
	private $options= Array(
		"Id" => "default", //an unique name to tell your object apart
		"LifeTime" => -1
	);
	
	/**
	* Initializes the frontend
	* @param Array options
	* @param Cache_Backend backend
	*/
	public function __construct(Array $options, Cache_Backend $backend){
		$this->backend = $backend;
		$this->options = array_merge($this->options, $options);
		$this->options['Id'] = "CacheObject_" . $this->options['Id'] . "_";
	}
	
	/**
	* Stores the data into the backend
	* @param String name
	* @param String data
	*/
	public function __set($name, $value){
		$this->backend->save($this->options['Id'] . md5($name), serialize($value), -1);
	}
	
	/**
	* Gets the data into the backend
	* @param String name
	* @return Mixed
	*/
	public function __get($name){
		if($value = $this->backend->load($this->options['Id'] . md5($name))){
			return unserialize($value);
		}else{
			throw new Exception("Error accessing property, property doesnt exist");
		}
	}
	
	/**
	* Tests if the value exists
	* @param String name
	*/
	public function __isset($name){
		return ($this->backend->load($this->options['Id'] . md5($name)) ? true : false);
	}
	
	/**
	* Deletes the value
	* @param String name
	*/
	public function __unset($name){
		$this->backend->remove($this->options['Id'] . md5($name));
	}
	
	
}

?>