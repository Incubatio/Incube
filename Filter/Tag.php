<?php
/** @author Incubatio
  * @depandancy Incube_Pattern_IFilter
  * @licence GPLv3.0 http://www.gnu.org/licenses/gpl.html
  *
  * TODO: Check Filter_tag depandancy to View
  */
class Incube_Filter_Tag implements Incube_Pattern_IFilter {

	/** @param Object $view */
	public function __construct($view) {
		$this->_view = $view;
		$this->_beginTag	= "{%";
		$this->_endTag		= "%}";
	}

	/** @param string $key */
	public function __get($key) {
		return $this->_view->$key;
	}

	/** @param string $method 
	  * @param array $args
	  * @return mixed */
	public function __call($method, $args)
	{   
		// Call user func will try to get method from the view, and indirectly from view helpers.
		$object = $this->_view;
		return call_user_func_array(array($object, $method), $args);
	}   

	/** @param string $text 
	  * @return string */
	public function run($text) {
		//we first catch the <tag> and <params> from the {% tag param1 ... paramN %}
		preg_match_all('#' . addslashes($this->_beginTag) . ' ?(\w+) (.+?)' . addslashes($this->_endTag) . '#s', $text, $tags);
		//preg_match_all('#' . addslashes($this->_beginTag) . '(?:([\w\/\\\.]+)|.)*?' . addslashes($this->_endTag) . '#s', $text, $tags);
		foreach($tags[0] as $key => $tag) {
			$method = $tags[1][$key];
			if (method_exists($this, $method)) {
				//preg_match_all('#\"([\w\/\\\.\{\} \-]+)\"|([\w\/\\\.\-]+)#s', trim($tags[2][$key]), $args);
				preg_match_all('#\"([^"]+)\"|([^ ]+)#s', trim($tags[2][$key]), $args);
				$args = array_filter($args[1]) + array_filter($args[2]);
				ksort($args);
				//TODO: design od appli, add q an b around window ;)
				$text = preg_replace("#".stripslashes($tag)."#", call_user_func_array(array($this, $method), $args), $text);
			}
			else trigger_error("Tag " . $method . " does not exists");
		}   
		return $text;
	}

	/** @param string $type 
	  * @return string 
	  * TODO: Change func name to createURI */
	public function url($type) {
		return $this->_view->url($type);
	}

	/** @param string $file
	  * @return string */
	public function js($file) {
		$params["src"] = $this->_view->url("javascript") . DS . $file;
		return $this->_view->js($params);
	}

	/** @param string $file
	  * @return string */
	public function css($file) {
		$params["href"] = $this->_view->url("styles") . DS . $file;
		return $this->_view->css($params);
	}

	/** @param string $label
	  * @param mixed $params
	  * @return string */
	public function link($label, $params) {
		return $this->_view->link($label, $params);
	}


	/** @param string $type
	  * @param string $file
	  * @return string */
	public function render($type, $file) {

		$filePath = $this->_view->path($type) . DS . $file;
		//include $filePath;
		ob_start();
		if(file_exists($filePath)) include $filePath;
		else trigger_error("$filePath doesn't exists");
		return $this->run(ob_get_clean());
	}


}
