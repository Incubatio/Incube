<?php
/** @author incubatio */
//TOTHINK: maybe add customisation of the static params, maybe remove the params
class Incube_Controller {

  /** @var String **/
  protected static $_actionSuffix = 'Action';

  /** @var String **/
  protected static $_fileSuffix = '.php';

  /** @var Array **/
  protected static $_params = array();

	/** Main method of every action of a controller
	  * @param Incube_Controller_Action $actionName
	  * @return string */
	public static function act(Incube_Controller_Action $action) {
		$actionMethod = $action->getName() . self::$_actionSuffix;
		$action->preAct();
		$result = $action->$actionMethod();
		$action->postAct();

		return $action->render($action->getName(), $result);
	}

	/** @param string $controllerPath
	  * @param string $action
	  * @return Incube_Controller_action */
	public static function actionFactory($controllerPath, $actionName) {
		if(!file_exists($controllerPath)) throw new Incube_Controller_Exception("Resource not found or does not exists");
		include_once $controllerPath;

		$actionClassName = basename($controllerPath, self::$_fileSuffix);

		$action = new $actionClassName();
		$action->init(self::$_params);

    // TOFIX: the command below indicate indirectly a dependancy with a Incube_Pattern_IURI object
		$actionName = $action->initContentType($actionName);
		$actionMethod = $actionName . self::$_actionSuffix;
		if(!method_exists($action, $actionMethod)) {
			throw new Incube_Controller_Exception("The action you're attempting to join doesn't exists");
		}
		$action->setName($actionName);
		return $action;
	}

	/** @param array $params */
  public static function setParams(array $params) {
    self::$_params = $params;
  }

}

?>
