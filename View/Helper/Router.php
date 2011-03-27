<?php
/** @author incubatio 
  * @depandancy Incube_HTML
  * @licence GPLv3.0 http://www.gnu.org/licenses/gpl.html
  */
class Incube_View_Helper_Router {

	protected $_router;

	public function __construct($router) {
		$this->_router = $router;	
	}

   //public function formatUrl($params = array()) {
		//return $this->_router->formatUrl($params);
	//}

	public function path($key) {
		return $this->_router->getPath($key);
	}

	public function url($params = null) {
		if(is_array($params)) return $this->_router->formatUrl($params);
		else return $this->_router->getUrl($params);
	}

}

?>