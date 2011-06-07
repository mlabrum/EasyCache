<?php

/**
 * @author Matt Labrum <matt@labrum.me>
 * @license Beerware
 * @link url
 */
 
namespace EasyCache;
class Frontend_Core implements Cache_Frontend{

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
		"serialize" => false
	);
	
	/**
	* Initializes the frontend
	* @param Array options
	* @param Cache_Backend backend
	*/
	public function __construct(Array $options, Cache_Backend $backend){
		$this->backend = $backend;
		$this->options = array_merge($this->options, $options);
	}
	
	/**
	* Stores the data into the backend
	* @param String name
	* @param String data
	* @param mixed time
	*/
	public function set($name, $data, $time=false){
		if($this->options['serialize']){
			$data = serialize($data);
		}
		$this->backend->save($name, $data, $time);
	}
	
	/**
	* Fetches data from the backend
	* @param String name
	*/
	public function get($name){
		$return = $this->backend->load($name);
		if($this->options['serialize'] && $return){
			return unserialize($return);
		}
		return $return;
	}
	
	/**
	* Caches a function call
	* @param Callback callback
	* @param ...
	*/
	public function call($callback){
		$args = func_get_args();
		array_shift($args); // Remove the first arg
		
		if(is_callable($callback, false, $name)){
			$id = "callable_" . md5($name) . $this->paramArrayToHash($args);
			
			if($return = $this->get($id)){
				$data = unserialize($return);
				
				// Load any heads, and echo'd data
				if(isset($data['headers'])){
					foreach($data['header'] as $header){
						header($header);
					}
				
				if(isset($data['echo'])){
					echo $data['echo'];
				}
				
				return $data['return'];
			}else{

				// Save the headers so that we can compare them later
				$headers = headers_list();
				
				// Run the function and save the returned data and echo'd data
				ob_start();
				$return	= call_user_func_array($callback, $args);
				$echo	= ob_get_contents();
				ob_end_clean();
				
				// Compare the headers, to see if the function used any headers
				$headers2 		= headers_list();
				$headersDiff 	= array_diff($headers2, $headers);
				
				$toSave = Array("return" => $return);
				
				if(!empty($echo)){
					echo $echo;
					$toSave['echo'] = $echo;
				}
				
				if(!empty($headersDiff)){
					$toSave['header'] = $headersDiff;
				}
				
				$this->save($id, serialize($toSave));
				
				return $return;
			}
		}else{
			throw new Exception("Cache Error: Cannot call function in Callback");
		}
	}
	
	/**
	* Converts an array of aruguments to a hash
	* @param Array args
	*/
	public function paramArrayToHash(Array $args){
		$str = "";
		foreach($args as $arg){
			$str .= serialize($arg);
		}
		return md5($str);
	}
	
	/**
	* Deletes data from the backend
	* @param String name
	*/
	public function delete($name){
		$this->backend->remove($name);
	}
}

?>