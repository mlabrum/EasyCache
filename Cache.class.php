<?php

/**
 * @author Matt Labrum <matt@labrum.me>
 * @license Beerware
 * @link url
 */
namespace EasyCache;
abstract class EasyCache{

	/**
	* Creates an instance of the cache interface class and the backend class
	* @param string $backend
	* @param string $frontend
	* @param array $backendOptions
	* @param array $frontendOptions
	* @return FrontendCache
	*/
	static public function Create($backend, $frontend, Array $backendOptions, Array $frontendOptions){
		
		$backendDirectory = dirname(__FILE__) . DIRECTORY_SEPARATOR . "Backend" . DIRECTORY_SEPARATOR;
		$frontendDirectory = dirname(__FILE__) . DIRECTORY_SEPARATOR . "Frontend" . DIRECTORY_SEPARATOR;
		
		$backendClassName = "Backend_" . $backend;
		$frontendClassName = "Frontend_" . $frontend;
		
		$backendFile =  $backendDirectory . $backendClassName . ".class.php";
		$frontendFile =  $frontendDirectory . $frontendClassName . ".class.php";
		
		if(file_exists($backendFile)){
			if(file_exists($frontendFile)){
				
				// Load the interface files
				require_once($backendDirectory . "Backend.interface.php");
				require_once($frontendDirectory . "Frontend.interface.php");
				
				//Load the frontend and backend class files
				require_once($backendFile);
				require_once($frontendFile);				
				
				//Initialize the classes
				$backendClass = new $backendClassName($backendOptions);
				$frontendClass = new $frontendClassName($frontendOptions, $backendClass);
				
				return $frontendClass;
			}else{
				throw new CacheBackendDoesntExist($backendFile);
			}
		}else{
			throw new CacheFrontendDoesntExist($backendFile);
		}
	}
}

class CacheBackendDoesntExist extends \exception{}
class CacheFrontendDoesntExist extends \exception{}

?>