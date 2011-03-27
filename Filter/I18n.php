<?php
/** @author Incubatio
  * @depandancy Incube_Pattern_IFilter
  * @licence GPLv3.0 http://www.gnu.org/licenses/gpl.html
  */
class Incube_Filter_I18n implements Incube_Pattern_IFilter {

	/** @param string $i18n */
	public function __construct($i18n) {
		$this->_i18n = $i18n;	
		$this->_beginTag	= "{{";
		$this->_endTag		= "}}";
	}

	/** @param string $text 
	  * @return string */
	public function run($text) {
		preg_match_all('#' . addslashes($this->_beginTag) . ' ?\"?([\w]+?)\"? ? ' . addslashes($this->_endTag) . '#s', $text, $tags);
		foreach($tags[0] as $key => $tag) {
				$text = preg_replace("/".stripslashes($tag)."/", $this->_i18n->trans($tags[1][$key]), $text);
		}   
		return $text;
	}
}
