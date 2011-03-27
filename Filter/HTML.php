<?php
/** @author Incubatio
  * @depandancy Incube_Pattern_IFilter
  * @licence GPLv3.0 http://www.gnu.org/licenses/gpl.html
  */
class Incube_Filter_HTML implements Incube_Pattern_IFilter {

	/** @param string $text 
	  * @return string */
	public function run($text) {
		return htmlspecialchars($text);
	}
}
