<?php
/** @author incubatio
  * @depandancy Incube_HTML_Element
  * @licence GPLv3.0 http://www.gnu.org/licenses/gpl.html
  */
class Incube_HTML_Form {

	/** @var array */
	protected $_options;

	/** @var array */
	protected $_elements;

	/** @var array */
	protected $_labels;

	/** @var string */
	protected $_separator = "";

	/** @param string $target
	  * @param array $options
	  * @param string $method */
	public function __construct($target, array $options = array(), $method = "post"){
		$this->_options = $options;
		$this->_options["action"] = $target;	
		$this->_options["method"] = $method;	
		$this->_elements = new StdClass();
		$this->_labels = new Incube_Array();
	}

	/** @param string $key
	  * @param mixed $value */
	public function setOption($key, $value) {
		$this->_options[$key] = $value;
	}

	/** @param string $value */
	public function setSeparator($value) {
		$this->_separator = $value;
	}

	/** @param string $key
	  * @param mixed $value */
	public function __set($key, $value) {
		$this->_elements->$key = $value;
	}

	/** @param string $key
	  * @return mixed */
	public function __get($key) {
		//echo "<pre>";
		//debug_print_backtrace();
		//Incube_debug::dump($key);
		return $this->_elements->$key;
	}

	protected function _prepare(){
		foreach($this->_elements as $key => $element) {
			if(!$element instanceof Incube_HTML_Element) $this->_elements->$key = Incube_HTML_Element::factory($key, $element);	
		}
	}

	/** @param string $name
	  * @param mixed $value
	  * @param array $options
	  * @return string */
	public function add($name, $value, array $options = array()) {
		$type = array_key_exists("type", $options) ? $options["type"] : null;	
		$default = array_key_exists("default", $options) ? $options["default"] : null;	
		$this->_elements->$name = Incube_HTML_Element::factory($name, $value, $type, $default);	
		if(array_key_exists("label", $options)) $this->_labels->$name = $options["label"];
		return $this->_elements->$name;
	}

	/** @return string */
	public function render() {
		//preparation or not
		$this->_prepare();
		$content = "";
		foreach($this->_elements as $key => $element) {
			$content .= ucfirst($this->_labels->$key) . $element->render() . $this->_separator;
		}
		$content .= Incube_HTML::createTag("input", array( "type" => "submit", "value" => "ok"));
		//add variable is prepared ?
		return Incube_HTML::createTag("form", $this->_options, $content);
	}

	/** @param array $data
	  * @return array */
	public function filterData(array $data) {
		foreach($data as $key => $value) {
			$data[$key] = $this->_elements->$key->filter($value);
		}
		return $data;
	}

	/** return bool */
	public function isValid() {
		//if is prepared
		$this->_prepare();
		$bool = true;
		foreach($this->_elements as $key => $element) {
			if(!$element->isValid()) $bool = false;
		}
		return $bool;
	}
}
