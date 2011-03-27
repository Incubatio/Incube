<?php
/** @author incubatio 
  * @depandancy Incube_Pattern_IURI 
  * @licence GPLv3.0 http://www.gnu.org/licenses/gpl.html
  *
  * TOTHINK: this class name is a URI parser, not a request object URI_Parser
  */
class Incube_URI implements Incube_Pattern_IURI {

	/** @var string */
    protected $_scheme = /*http://myWebsite.com/(lang/)*/'controller/action'/*/id/1*/;
	
	/** @var string */
    protected $_separator = "/";

	/** @var string */
	protected $_variableDelimiter = ":";

	/** @var array */
    protected $_params;

	/** @var array */
    protected $_mainParams;

	/** @var array */
    protected $_unplannedParams;

	/** @var string */
    protected $_indexFile = 'index.php';

	/** @var string */
	protected $_headers;

	/** @param string $URI
	  * @param array options */
    public function __construct($URI, array $options = array()) {
        
         $this->initOptions($options);

         $params = explode($this->_separator, $URI);
         unset($params[0]);

        $this->_params = $this->_parseParams($params);
    }

	/** @param array options */
    public function initOptions(array $options) {
        foreach ($options as $key => $option) {
            $this->{"_$key"} = $option;
        }
    }

	/** @param strin $key
	  * @return mixed */
	public function __get($key) {
		switch(true) {
			case isset($_SERVER[$key]):
			return $_SERVER[$key];
			case isset($_ENV[$key]):
			return $_ENV[$key];
			default:
			return null;
		}
	}


	/** TODO: REVIEW getContentType Incube_Router_Request method
	  * No priority, just check if Http request allow content-type
	  * @return mixed  */
	public function getContentType() {
		if ($this->HTTP_ACCEPT) {
			$mediaTypes = explode(",", $this->HTTP_ACCEPT);
			$type = array();
			foreach ($mediaTypes as $mediaType) {
				$tmp = explode(";", $mediaType);
				$tmp = explode("/", $tmp[0]);
				$type[] = $tmp[1];
			}
			return $type;
		}   
		return false;
	}

    /** @param array $params
      * @return array */
    protected function _parseParams($params) {
        //Get main params from the $URI thanks to the _scheme
        $params = array_values($params);
        $mainParams = array();
        foreach(explode($this->_separator, $this->_scheme) as $key => $value) {
			if(substr($value, 0, 1) === $this->_variableDelimiter) {
				$value = substr($value, 1);
				if(empty($params[$key])){
					if(empty($this->_default->$value)) throw new Incube_Exception("Dynamic Params missing");
					else $mainParams[$value] = $this->_default[$value];
				} else {
					//Incube_debug::dump(isset(preg_match("/" . $this->_validation->$value . "/", $params[$key])));
					if(isset($this->_validation[$value]) AND !preg_match("/" . $this->_validation[$value] . "/", $params[$key])) 
						throw new Incube_Exception("Wrong URL format");
					$mainParams[$value] = $params[$key];
				}
			} else {
				if(!empty($params[$key])){
					if($params[$key] != $value) throw new Incube_Exception("Static Params missing");
				}
				$mainParams[$value] = $value;
			}
			unset($params[$key]);
        }
		$this->_mainParams = $mainParams;

        //Get the other params form the $URI like : /id/1 makes $param['id'] = 1
        //$otherParams is stocked by the class to be reused by a router if necessary
        $this->_unplannedParams = $params;
        $otherParams = array();
        foreach($params as $key => $param) {
            if(empty($k)) {
                $k = $param;
            } else {
                $otherParams[$k] = $param;
                unset($k);
            }
            unset($params[$key]);
        }
        return array_merge($otherParams, $mainParams);
    }

	/** @return array */
	public function getMainParams() {
		return $this->_mainParams;
	}

    /** @return array */
    public function getParams() {
        return $this->_params;
    }

    /** @param string $key
      * @return string | false */
    public function getParam($key) {
        return array_key_exists($key, $this->_params) ? $this->_params[$key] : false;
    }

    /** @return string */
    public function getWebSiteBaseUrl() {
        return 'http://' . $_SERVER["SERVER_NAME"];
    }

    /** @return string */
    public function getScheme() {
        return $this->_scheme;
    }
}
