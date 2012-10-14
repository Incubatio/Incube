<?php
/** @author Incubatio
  * @depandancies Incube_Pattern_IURI, Incube_Pattern_IChecker, Incube_Pattern_IFilter, Incube_Controller, Incube_Controller_Action, Incube_View, Incube_Application_Exception
  * @licence GPL3.0 http://www.gnu.org/licenses/gpl.html
  *  
  * For User: Before construct an Application_MVC Initialise every component common 
  * to your application: Config, URI, DataModel, Internationalisation, session.
  *
  * $application = new Application_MVC:
  * - Init $_URI and options
  *
  * For User: It's now time to add $_checkers, $_viewHelper and $_filters
  *
  * $application->start():
  * - Init Router Run Checkers (e.g. acl)
  * - Init application component: view, view's helpers, controller
  * give $_options to controller, run the action 
  *
  * For User: Don't Forget to try catch $application in case of an Exception
  */
class Incube_Application_MVC implements Incube_Pattern_IApplication {

	/** @var string */
	protected $_name;

	/** @var stdClass | Incube_Pattern_IArray
	 * $_option->config contains application configurations
	 * $_option->config->router can be set to modify default router behavior
	 * $_option->anything will be available as controller attribute */
	protected $_options;

	/** @var Incube_Pattern_IURI */
	protected $_URI;

	/** @var array 
	  * Contains checker executed before the application's initialisation (e.g auth, acls) */
	protected $_checkers = array();

	/** @var array 
	  * Contain contents' filters executed before render (e.g. tags) */
	protected $_filters = array();

	/** @var string 
	  * Exception handled outside or inside by a controller name contained in $_exceptionHandler */
	protected $_exceptionController = "exceptionController.php";

	/** @param string $appName
	  * @param Incube_Pattern_IURI $URI
	  * @param array $options */
	public function __construct($appName, Incube_Pattern_IURI $URI, array $options = array()) {
		$this->_name = $appName;
		$this->_URI = $URI;
		//TOTHINK: parse options in stdClass ?
		$this->_options = (object) $options;

		// TOTHINK: check the declaration or the accessibility of dynamics var.
		//foreach($options as $key => $option) {
		//$this->{"_$key"} = $option;
		//}

	}


	public function start() {
		try {
			//TODO: add more customization capabilities.
			// Check for custom application configuration in options->config
			$routerConfig =  isset($this->_options->config) && isset($this->_options->config->router) ? $this->_options->config->router : array();
			if ($routerConfig instanceof Incube_Pattern_IArray) $routerConfig = $routerConfig->toArray();

			// Prepare router
			$router = new Incube_Router(APPS_PATH . DS . $this->_name, $this->_URI->getMainParams(), $routerConfig);
			$router->setBaseUrl($this->_URI->getWebSiteBaseUrl());

			// Prepare the view object
			$view = new Incube_View(array('path' => $router->getPath('view'), 'layoutPath' => $router->getPath('layout')));
			$view->addViewHelper(new Incube_View_Helper_HTML($router));

			// Prepare params for controllers
			$params = array_merge(array(
						"URI" => $this->_URI,
						"router"  => $router,
						"view"    => $view,
						), (array) $this->_options);


			// Prepare Action and check existance of ressource URIed (possible 404: Ressource not found)
            Incube_Controller::setParams($params);
			$action = Incube_Controller::actionFactory($router->getFilePath('controller'), $this->_URI->getParam('action'));

			// Authorisation check: authentications, Acl, security token ... (possible 401: Unauthorised access)
			$checked = $this->_runCheckers();

			// Execute Action
			$contents = Incube_Controller::act($action);
		} catch (Exception $e) {
			if(!$this->_exceptionController || !file_exists($router->getPath("controller") . DS . $this->_exceptionController)) throw $e;

			$params["e"] = $e;
            Incube_Controller::setParams($params);
			$action = Incube_Controller::actionFactory($router->getPath("controller") . DS . $this->_exceptionController, "index");
			$contents = Incube_Controller::act($action);
		}
		// TOTHINK: For better performances and more flexibility, filter should be added from outside the app
		$this->addFilter(new Incube_Filter_Tag($view));
		echo $this->_runFilters($contents);
	}

	/** @param Incube_Pattern_Checker checker */
	public function addChecker(Incube_Pattern_IChecker $checker) {
		$this->_checkers[] = $checker;
	}

	/** @param Incube_Pattern_IFilter */
	public function addFilter(Incube_Pattern_IFilter $object) {
		$this->_filters[] = $object;
	}

	/** @return bool */
	protected function _runCheckers() {
		foreach($this->_checkers as $checker) {
			if(!$checker->isCheckable($this->_URI->getParams())) throw New Incube_Application_Exception("access refused to this resource, you must be authenticated and have the suficient privileges");
		}
		return true;
	}

	/** @param string $contents 
	  * @return string */
	protected function _runFilters($contents) {
		//TOTHINK: find a way to activate tag filter from outside and remove the array_reverse below
		foreach(array_reverse($this->_filters) as $key => $value) {
			$contents = $value->run($contents);
		}
		return $contents;
	}
}
