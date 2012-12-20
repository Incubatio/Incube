<?php 
namespace Incube\HTML;
/** @author incubatio
  * @depandancies Incube_Validator, Incube_HTML, Incube_Filter
  * @licence GPLv3.0 http://www.gnu.org/licenses/gpl.html
  */
use Incube\Encoder\HTML;
use Incube\Filter\HTML as HTMLFilter;
use Incube\HTML\Element;

class Element {

	/** @var array */
	protected $_validators = array();

	/** @var array */
	protected $_filters = array();

	/** @var string */
	protected $_errors;

	/** @var string */
	protected $_tag;

	/** @var string */
	protected $_label;

	/** @var string */
	protected $_name;

	/** @var array */
	protected $_options;

	/** @param string $tag
	  * @param array $options
	  * @param string $label */
	public function __construct($tag, $options, $label = null) {
		$this->_tag = $tag;
		$this->_options = $options;
		$this->_label = $label;
	}

	/** @param string $key 
	  * @return string */
	public function getOption($key) {
		return $this->_options[$key];
	}

	/** @param string $key 
	  * @param string $value */
	public function setOption($key, $value) {
		$this->_options[$key] = $value;
	}

	/** @return string */
	public function render() {
		// Generate multiple tag for multi-choice if value is an array
		if(array_key_exists("value", $this->_options) && is_array($this->_options["value"])) {
			$html = "";
			foreach($this->_options["value"] as $label => $value) {
				$options = $this->_options;
				$options["value"] = $value;
				$html .= HTML::create_tag($this->_tag, $options, $label);
			}
		// Generate single tag for a single element
		} else  {
			$html = HTML::create_tag($this->_tag, $this->_options, $this->_label);
		}
		return $html;
	}


	/** @param string $value
	  * @return string */
	public function filter($value) {
		foreach($this->_filters as $filter) {
			$value = $filter->run($value);
		}
		return $value;
	}

	/** @return bool */
	public function isValid() {
		$bool = true;
		foreach($this->_validators as $validator) {
			if(!$validator->isValid($this->_value)) {
				$bool = false;
				break;
			}
		}
		return $bool;
	}

	/** @param Incube_Pattern_IValidator $validator */
	public function addValidator(Incube_Validator $validator) {
		$this->_validators[] = $validator;
	}

	/** @param array $validators */
	public function setValidators(array $validators) {
		$this->_validators = $validators;
	}

	/** @param array $filters */
	public function setFilters(array $filters) {
		$this->_filters = $filters;
	}

	/** Build Preconfigured html tag with validator and filters
	  *
	  * @param string $name
	  * @param string|array $values
	  *	@param string $type
	  * @param string|array (for checkboxs) $default 
	  * @return Incube_Element */
	public static function factory($name, $values, $options = array(), $default = array()) {
        $options = (array) $options;
		$default = (array) $default;
		if(!array_key_exists('type', $options)) {
			if(is_bool($values)) $type = "bool";
			elseif(is_float($values)) $type = "float";
			elseif(is_numeric($values)) $type = "int";
			elseif(is_string($values)) $type = "string";
			elseif(is_array($values)) $type = "select";
			else trigger_error("unidentified type of data, can't dynamically create this element", E_USER_ERROR);
		} else {
            $type = $options['type'];
            unset($options['type']);
        }
		$label = null;
		$validators = array();
		$filters = array();
		switch($type) {
			case "string":
				$tag = "input";
				$options2 = array("type" => "text", "value" => $values);
				$filters[] = new HTMLFilter();
			break;
			case "password":
				$tag = "input";
				$options2 = array("type" => "password"); 
				//$validator = new Incube_Validator_Regex();
			break;
			case "text":
				$tag = "textarea";
				$options2 = array("type" => "text", "style" => "width:100%; min-height:150px;");
				//$label = $values;
				$label = $values;
				$filters[] = new HTMLFilter();
			break;
			case "bool":
				$tag = "input";
				$options2 = array("type" => "checkbox", "value" => $values);
				$validator[] = new Incube_Validator_Number("bool");
			break;
			case "int":
				$tag = "input";
				$options2 = array("type" => "text", "value" => $values);
				$validators[] = new Incube_Validator_Number("int");
			break;
			case "float":
				$tag = "input";
				$options2 = array("type" => "text", "value" => $values);
				$validators[] = new Incube_Validator_Number("float");
			break;
			case "select":
				$tag = "option";
				foreach($values as $key => $value) {
					$tmp = in_array($value, $default) ? "$tag selected": $tag; 
					$label .= HTML::create_tag($tag, array("value" => $key), $value);
				}
				$tag = "select";
			break;
			case "radio":
				$tag = "input";
				$options2 = array("value" => $values, "type" => "radio");
			break;
			case "checkbox":
				$tag = "input";
				$options2 = array("value" => $values, "type" => "checkbox");
			break;
			case "hidden":
				$tag = "input";
				$options2 = array("type" => "hidden", "value" => $values);
			break;
				//$validator = array(new Incube_Validator_TextField());
		}
        $options = array_merge($options, $options2);
		$options["name"] = "data[$name]";
		//TODO: add validators
		$element =  new Element($tag, $options, $label);
		$element->setValidators($validators);
		$element->setFilters($filters);
		return $element;
	}
}
