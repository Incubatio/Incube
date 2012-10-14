<?php
/** @author Incubatio 
  * @depandancies Incubatio_URI, Incube_Action_Exception
  * @licence GPLv3.0 http://www.gnu.org/licenses/gpl.html
	*
	* TODO: Manage contentyp and header in a SingleTime class
  */
class Incube_Controller_Action {

	/** @var Class_Config **/
	protected $_config;

    /** Refer to the name of the Actions' set
	  * @var String **/
    protected $_name;

    /** @var String **/
    protected $_suffix = 'Controller';

    /** @var Class_View **/
    protected $_view;

    /** @var Class_Connection **/
    protected $_dataModel;

    /** @var Class_Router **/
    protected $_router;

    /** @var Class_URI **/
    protected $_URI;

	/** Refer to the current action which is going to trigger
	  * @var String **/
	protected $_current;

	/** @var array **/
	//TODO: Contenttypes doesn't belong to controller action, but to HTTP class or ??.
	protected $_contentTypes = array(
		"text"  => "text/plain",
		"html"  => "text/html",
		"rich"  => "text/enriched",
		"css"   => "text/css",
		"css"   => "text/html",
		"js"    => "text/javascript",
		"gif"   => "image/gif",
		"png"   => "image/x-png",
		"jpeg"  => "image/jpeg",
		"tiff"  => "image/tiff",
		"bmp"   => "image/x-ms-bmp",
		"svf"   => "image/vnd.svf",
		"pdf"   => "application/pdf",
		"zip"   => "application/zip",
		"json"  => "application/json",
		"xhtml" => "application/xhtml+xml",
		"xml"	=> "application/xml",
		"latex" => "application/x-latex"
		);  

	/** @var string **/
	protected $_contentType = "html";
	
	/** @var bool **/
	protected $_IsContentTypeSet;

	/** @param array $options */
	public function __construct(array $options = array()) {
		$this->_name = str_replace($this->_suffix, '', get_class($this));
		$this->init($options);
	}

	//optional
	/** @param Class_Config **/
	public function setConfig($config) {
		$this->_config = $config;
	}

	//compulsory
	/** @param Object $router */
	public function setRouter($router) {
		$this->_router = $router;
	}

	//compulsory
	/** @param Incube_Pattern_IURI $URI */
	public function setURI(Incube_Pattern_IURI $URI) {
		$this->_URI = $URI;
	}

	/** @param string $key */
	public function getParam($key) {
		return $this->_URI->getParam($key);
	}

	//optional
	/** @param Object $dataModel */
	public function setDataModel($dataModel) {
		$this->_dataModel = $dataModel;
	}

	//optional
	/** @param Object | null $view */
	public function setView($view) {
		$this->_view = $view;
	}

	/** @param string $name */
	public function setName($name) {
		$this->_current = $name;
	}

	/** @return string */
	public function getName() {
		return $this->_current;
	}


	/** Possibilities to add common behavour before every actions */
	public function preAct() {
	}

	/** Possibilities to add common behavour after every action */
	public function postAct() {
	}

	/** @param array $schemeParams
	  * @param array $params */
	protected function _redirect(array $schemeParams, array $params = array()) {
		$url = $this->_router->formatUrl($schemeParams, $params);
		header("Location: $url");
	}


	/** @param string $controllerName
	  * @param string $actionName
	  * @param array  $params 
    * @return string */
	protected function _call($controllerName, $actionName, array $params = array()) {
    $action = Incube_Controller::actionFactory($this->_router->getPath("controller") . DS . $controllerName . 'Controller.php', $actionName, $params);
    return Incube_Controller::act($action);
	}

	/** @param array $params */
	public function init(array $params) {
		foreach($params as $key => $param) {
			$this->{"_$key"} = $param;
		}
	}

	/** @param string $actionName */
	public function initContentType($actionName) {
		// check content-type + little hack to give dynamic response changes
		if(preg_match("/\./", $actionName)) {
			list($actionName, $extention) = explode('.', $actionName);
			if(array_key_exists($extention, $this->_contentTypes)) {
        // TOFIX: what is that thing below about contentType(...)
				$this->_contentType(array($extention));
				header('Content-type: ' . $this->_contentTypes[$extention]);
			} else $this->_contentType = array();
		} else {
			$this->_contentType = $this->_URI->getContentType();	
		}
		return $actionName;
	}

	/** @param string $contentType
	  * @return boolean */
	public function contentIs($contentType) {
		return in_array($contentType, $this->_contentType);
	}


	/** Default Render function, format handled by the view
	  * @param string $actionName
	  * @param string $contents 
	  * @return string */
	public function render($actionName, $contents = null) {
		switch(true) {
			case in_array("html", $this->_contentType):
			case in_array("xhtml+xml", $this->_contentType):
			case in_array("xaml+xml", $this->_contentType):
				// Prepare view
				if(isset($this->_view)) {
					//TOTHINK:check usability of controllerName and ActionName inside View
					$this->_view->controllerName = $this->_name;
					$this->_view->actionName = $actionName;
					$viewFileName = $this->_view->getFileName();
					if(empty($viewFileName)) $viewFileName = $this->_name .  DS . $actionName;
				}
			return $contents ? $this->_view->RenderText($contents) : $this->_view->render($viewFileName);

			case in_array("json", $this->_contentType):
				if(isset($contents)) return json_encode($contents);

			default:
				throw new Incube_Controller_Action_Exception("Please disable render, content-type is not suppported or result is empty");
		}
	}
}
