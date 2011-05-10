<?php 
/** @author Incubatio
  * @depandancy Incube_Pattern_IChecker
  * @licence GPLv3.0 http://www.gnu.org/licenses/gpl.html
  *
  * This class is a checker used to authenticate user thanks to an Access Control list 
  * TOTHINK: allow restriction, even if it's rarelly usefull and responsible 
  */
class Incube_ACL implements Incube_Pattern_IChecker {

	/** @var string */
	protected $_group;

	/** @var array */
	protected $_groups;

	/** @var array */
	protected $_rights; 

	/** @var array */
	//protected $_restriction = array("*" => "*");

	/** @param int $groupId
	  * @param array $acl */
	public function __construct($groupId, array $acl) {
		$this->_group = $acl["groups"][$groupId];
		$this->_groups = $acl["groups"];
		$this->_rights = empty($acl["rights"]) ? array("*" => "*") : $acl["rights"];
	}

	/** @param array $resource 
	  * @return bool */
	public function isCheckable(array $resource) {
		//TODO: think about this module part
		if(array_key_exists("module", $resource)) {

		}
		if(array_key_exists("controller", $resource)) {
			$controller = $resource["controller"] . "Controller";
		}
		$action = array_key_exists("action", $resource) ? $resource["action"] : "index" ;

		if(array_key_exists("*", $this->_rights)) {
			$group = $this->_rights["*"];
			if($group == "*" || is_array($group) && in_array($this->_group, $group)) {
				return true;
			}
		}
		if(array_key_exists($controller, $this->_rights)) {
			foreach($this->_rights[$controller] as $keyAction => $group) {
				if( ( $keyAction == "*" || preg_match("#$keyAction#", $action)) && ($group == "*" || $group == $this->_group || is_array($group) && in_array($this->_group, $group)))
					return true;
			}
		}
		return false;	
	}
}
