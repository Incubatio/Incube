<?php
/** @author incubatio
  * @depandancy Incube_Pattern_IArray
  * @licence GPLv3.0 http://www.gnu.org/licenses/gpl.html
  */
class Incube_Array implements Incube_Pattern_IArray{

	/** @var array */
	protected $_data;

	/** @param array $data */
	public function __construct(array $data = array()) {
		$this->_data = $data;
	}

	/** @param string $key
	  * return mixed */
	public function __get($key) {
		return isset($this->_data[$key]) ? $this->_data[$key] : null;
	}

	/** @param string $key
	  * @param mixed $value */
	public function __set($key, $value) {
		$this->_data[$key] = $value;
	}
	
	/** @return array */
	public function toArray() {
		$data = array();
		foreach($this->_data as $key => $datum) {
			$data[$key] = ($datum instanceof Incube_Array) ? $datum->toArray() : $datum; 
		}
		return $data;
	}

	/** Merge mutltiple array 
	  * @params array $data
	  * @return array */
	public static function mergeArrays(array $data) {
		$assoc = array(); 
		foreach($data as $datum) { 
			$assoc = array_merge($assoc, $datum); 
		} 
		return $assoc;
	}

	/** @param array $assoc
	  * @return Incube_Array */
	public static function arrayToIArray(array $assoc) {
		$object = new Incube_Array();
		foreach($assoc as $key => $value) {
			$key = trim($key);
			$object->$key = (is_array($value)) ? self::arrayToIArray($value) : $value; 
		}
		return $object;
	}

}
?>
