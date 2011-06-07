<?php
/**
 * @author Matt Labrum <matt@labrum.me>
 * @license Beerware
 * @link url
 */
 
namespace EasyCache;
class Backend_File implements Cache_Backend{

	/**
	* Stores the options for the backend
	* @var Array
	*/
	private $options = Array(
		'lifeTime' => 60,
		'cacheDir' => "./"
	);

	/**
	* Initializes the backend
	* @param Array options
	*/
	public function __construct($options){
		$this->options = array_merge($this->options, $options);
		$this->createPath($options['cacheDir']);
	}

	/**
	* Gets the filename of the cache file
	* @param String name
	*/
	private function getFileName($name){
		return $this->options['cacheDir'] . $name . ".cache";
	}

	/**
	* Loads the cache file and returns the data
	* @param String name
	* @param String fullpath
	*/
	public function load($name, $fullPath=false){
		$file = $fullPath ? $fullPath : $this->getFileName($name);
		
		if(file_exists($file)){
			$data = explode("\t", file_get_contents($file), 2);
			$time = (int) $data[0];
			
			// tests if the file has expired 
			if(time() >= $time && $time != -1){
				$this->remove($name, $fullPath);
			}else{
				return $data[1];
			}
		}
		return false;
	}

	/**
	* Saves the data to the cache
	* @param String name
	* @param String value
	* @param int time
	*/
	public function save($name, $value, $time=false){
		$time = $time ? $time : $this->options['lifeTime'];
		$value =  (($time!=-1) ? time() + $time : -1) . "\t" .$value;
		return file_put_contents($this->getFileName($name), $value);
	}

	/**
	* Removes the data from the cache
	* @param String name
	* @param String fullpath
	*/
	public function remove($name, $fullPath=false){
		$file = $fullPath ? $fullPath : $this->getFileName($name);
		if(file_exists($file)){
			unlink($file);
		}
	}
	
	public function clean(){
		foreach(glob($this->options['cacheDir'] . "*.cache") as $cache){
			//50% chance of file being tested
			if(rand(0,1) == 1){
				$this->load($cache, true);
			}
		}
	}


	/**
	* Creates the directory structure for the path
	* @param String path
	*/
	public function createPath($path){
		$path = trim($path, "/\\");
		if(!file_exists($path)){
			$parts = preg_split("/[\/\\\\]/", $path);
			$fullpath = "";
			foreach($parts as $part){
				if(empty($part))continue;
				if($part == "."){
					$fullpath = "./";
					continue;
				}
				
				$fullpath .= $part . "/";
				if(!file_exists($fullpath)){
					mkdir($fullpath);
				}
			}
		}
	}

}

?>