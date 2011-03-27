<?php
/** @author incubatio
  * @depandancies Incube_IArray, Incube_File_Explorer
  * @licence GPLv3.0 http://www.gnu.org/licenses/gpl.html
  */

class Incube_Config {

    /** Load a configuration file
      * @param 	string 	$file
      * @param 	bool $allowModifications
      * @return Incube_Array
	  * TODO: validates config files !!!!! */
	public function getConfig($path, $assoc = true){

		if(Incube_File_Explorer::exists($path)) {
			//$ext = strtolower(pathinfo($path, PATHINFO_EXTENSION));
			$path_parts = pathinfo($path);
			$ext = array_key_exists("extension", $path_parts) ? strtolower($path_parts["extension"]) : "";
			switch ($ext) {
				case 'ini':
				$data = parse_ini_file($path, true);
				return ($assoc) ? $data : Incube_Array::arrayToIArray($data);

				case 'ser':
				$data = unserialize(Incube_File_Explorer::read($path));
				return ($assoc) ? $data : Incube_Array::arrayToIArray($data);

				case 'php':
				include_once $path;
				return ($assoc) ? $data : Incube_Array::arrayToIArray($data);

				case '':
				return null;

				default:
				trigger_error(__class__ . " doesn't support $ext filetype.");
				
				//XML is not designed for configuration, but for storage (or as a relational database)
				//case 'xml':
				//if you insist by using xml as config file, use php extention like PECL which much faster than any php parser.

				//case 'yaml':
				//you need to install PECL module, No Way I implement a "not very efficient" parser like symphony");
			}
		}
		return false;
    }

    /** Load a configuration file
      *
      * @param 	string 	$folder
      * @param 	bool 	$isAssoc
      * @return Incube_Array */
    public function getConfigByFolder($folder, $isAssoc = true){
        
        $files = Incube_File_Explorer::list_files($folder);

        $config = $isAssoc ? array() : new stdClass();
        foreach($files as $file) {
			//PHP5.3 >>
            //$filename = basename($file, '.ini');
            //$filename = basename($file, '.xml');
			//$filename = strtolower(pathinfo($file, PATHINFO_FILENAME));
			//$path_parts = pathinfo($file);
			//$filename = strtolower($path_parts["filename"]);
			$path_parts = pathinfo($file);
			$ext = array_key_exists("extension", $path_parts) ? "." . $path_parts["extension"]: "";
			$filename = basename($file, $ext); 

            if($configFile = $this->getConfig($folder . DS . $file, $isAssoc)) {
				$isAssoc ? $config[$filename] = $configFile : $config->$filename = $configFile;
            }
            unset($configFile);
        }

        return $isAssoc ? Incube_Array::arrayToIArray($config) : $config;
    }

	/** Convert recursively arrays to Objects
	  * @param array $assoc 
	  * @return StdClass */
	public function arrayToObject(array $assoc) {
		$object = new stdClass();
		foreach($assoc as $key => $value) {
			$key = trim($key);
			$object->$key = (is_array($value)) ? $this->arrayToObject($value) : $value; 
		}
		return $object;
	}


	/** Write a file in a specific format to a specific location
	  * @param string $path 
	  * @param array $data */
	public function save($path, array $data) {
        $ext = strtolower(pathinfo($path, PATHINFO_EXTENSION));
		switch($ext) {
			case "ser":
				Incube_File_Explorer::write($path, serialize($data));
			break;
			default:
				trigger_error(__class__ . " doesn't support $ext filetype.");
		}
	}
}
?>
