<?php
/** @author incubatio 
  * @licence GPLv3.0 http://www.gnu.org/licenses/gpl.html
  */
class Incube_HTML {

	/** @param string $tag
	  * @param array $params
	  * @param string label *
	  * @return string */
	public static function createTag($tag, array $params = array(), $label = null) {
		$element = array();
		$element[] =  "<$tag";
		foreach($params as $key=>$value) {
			$element[] = "$key=\"$value\"";
		}   
		$element[] = isset($label) ? ">$label</$tag>" : "/>";
		return implode($element, " ");
	}   

}
