<?php
/** @author incubatio 
  * @licence GPLv3.0 http://www.gnu.org/licenses/gpl.html
  */
class Incube_Router {

    /** @var array */
    protected $_directoryNames =  array("view" => "views", "layout" => "layouts", "controller" => "controllers");

    /** @var string */
    protected $_appPath;

    /** @var string */
    protected $_conteneurDir;
    
	/** @var string */
    protected $_baseUrl;
    
	/** @var array */
	// TODO: define default utls   
		protected $_urls = array(
				"images" => "static/images",
				"javascript" => "static/js",
				"styles" => "static/css",
				"files" => "static/selif"
				);
    
	/** Unified Resource names
	  * @var array */
	protected $URNs;

    /** @param string $appsPath
      * @param array $URNs
      * @param array $options */
    public function __construct($appsPath, array $URNs, array $options = array()) {
        $this->init($options);
        $this->_appPath		= $appsPath;
				$this->_URNs	= $URNs;


        //$this->_schemaParams = explode('/', $this->_URI->getScheme());
		//TOTHINK: if optional directory name are not define, we check the architecture dir exists ?, it exists we use this conf.
		//TOTHINK: Check if implementation of module is usefull
        if(array_key_exists("module", $URNs)) {
            $this->_conteneurDir = $URNs["module"];
            //$this->_names['module'] = $URNs[$moduleDir];
        } else {
            $this->_conteneurDir = $this->_directoryNames["controller"];
        }


        $this->_files['controller'] = $URNs["controller"] . ucfirst("controller") . '.php';
        //$this->_view = $this->getDirName('view') . DS . $this->_names['action'] . '.phtml';

    }

    /** @param string $options */
    protected function init(array $options) {
        foreach($options as $key => $option) {
            $this->{"_$key"} = $option;
        }
    }


    /** @param string $item
      * @return string */
    public function getPath($item) {
        $moduleDir = "";
        if($this->_conteneurDir == $this->getDirName('module')) {
            $moduleDir = $this->_conteneurDir . $this->getDirName('controller') . DS;
        }
        return $this->_appPath . DS . $moduleDir . $this->getDirName($item);
    }

    /** @param string $key
      * @return string */
    public function getDirName($key) {
        return array_key_exists($key, $this->_directoryNames) ? $this->_directoryNames[$key] : null;
    }

    /** @param string $item
     * @return string */
    public function getFilePath($item) {
       return $this->getPath($item) . DS . $this->_files[$item];
    }

    /** @param string $item
     * @return string */
    public function getUrl($item = "") {
        $urlEnd = array_key_exists($item, $this->_urls) ? DS . $this->_urls[$item] :"";
        return $this->_baseUrl . $urlEnd;
    }

    /** @param string $url
     * @return string */
    public function setBaseUrl($url) {
        $this->_baseUrl = $url;
    }

    /** @param array $schemeParams
      * @param array $params
      * @return string */
    public function formatUrl(array $schemeParams = array(), array $params = array()) {
		$schemeModel = array_keys($this->_URNs);
        $endUrl = array();
        foreach($schemeModel as $label) {
            $endUrl[] = array_key_exists($label, $schemeParams) ? $schemeParams[$label] : $this->_URNs[$label];
        }
        foreach($params as $key => $param) {
            $endUrl[] = "$key/$param";
        }
        return $this->_baseUrl . DS . implode(DS, $endUrl);
    }
}
