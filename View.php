<?php
/** @author incubatio 
  * @licence GPLv3.0 http://www.gnu.org/licenses/gpl.html
  */
class Incube_View {

    /** @var string */
    protected $_path = "";

    /** @var string */
    protected $_viewExtention = ".phtml";

    /** @var string */
    protected $_fileName;

	/** @var Class_I18n */
	protected $_i18n;

	/** @var string */
	protected $_layoutName;

	/** @var viewHelpers */
	protected $_viewHelpers = array();

    /** @param array $options */
    public function __construct(array $options = array()) {
        foreach($options as $key => $value) {
            $this->{"_$key"} = $value;
        }
    }

    /** @param string $fileName
      * @return string
      */
    public function render($fileName = null) {
        if(!$fileName) $fileName = $this->_fileName;
        $filePath = $this->_path . DS . $fileName . $this->_viewExtention;
		$contents = $this->_include($filePath);

		if($this->_layoutName) {
			$layoutPath = $this->_layoutPath . DS . $this->_layoutName . $this->_viewExtention;
			$this->contents = $contents;
			$contents = $this->_include($layoutPath);
		}
		return $contents;
    }

    /** @param string $contents
      * @return string */
    public function renderText($contents) {

		if($this->_layoutName) {
			$layoutPath = $this->_layoutPath . DS . $this->_layoutName . $this->_viewExtention;
			$this->contents = $contents;
			$contents = $this->_include($layoutPath);
		}
		return $contents;
    }

    /** @param string $filePath
      * @return string */
	protected function _include($filePath) {
		ob_start();
		if(file_exists($filePath)) include $filePath;
		//else trigger_error("$filePath doesn't exists");
		//TODO:File_Explorer could ensure read files( include + ob_get_clean )
		return ob_get_clean();
	}  


    /** @param string $fileName */
    public function setFileName($fileName) {
        $this->_fileName = $fileName;
    }

    /** @param string $fileName */
	public function setLayout($fileName) {
		$this->_layoutName = $fileName;
	}
	
	/** @return bool */
	public function isLayout() {
		return !empty($this->_layoutName);
	}

	/** Clean render by unseting view and layout */
	public function noRender() {
		$this->setLayout(null);
		$this->setFileName(null);
	}

	public function addViewHelper($viewHelper) {
		$this->_viewHelpers[] = $viewHelper;
	}

    /** @param string $method 
	  * @param array
	  * return mixed */
	public function __call($method, $args) {
		foreach($this->_viewHelpers as $viewHelper) {
			if(method_exists($viewHelper, $method)) return call_user_func_array(array($viewHelper, $method), $args);
		}
	//	debug_print_backtrace();
		//TODO: Ameliorer le bug tracking
		trigger_error("$method doesn't exists or isn't available from the view's helpers", E_USER_ERROR);
	}


    /** @return string */
    public function getFileName() {
        return $this->_fileName;
    }

    /** @param string $key
      * @param string $path */
    public function addPath($key, $path) {
        $this->_path[$key] = $path;
    }

    /** @param array $paths */
    public function addPaths(array $paths) {
        foreach($paths as $key => $path) {
            $this->addPath($key, $path);
        }
    }
}
