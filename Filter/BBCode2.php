<?php
/** @author Incubatio
  * @depandancy Incube_Pattern_IFilter
  * @licence GPLv3.0 http://www.gnu.org/licenses/gpl.html 
  * TODO: compatibility of regex for PHP 5.0
  */
class Incube_Filter_BBCode implements Incube_Pattern_IFilter {

	/** @var array */
	protected $_tags = array(
		array("/\n/i","<br />"),
		array("/\[b\]/i","<strong>"),
		array("/\[\/b\]/i","</strong>"),
		array("/\[i\]/i","<em>"),
		array("/\[\/i\]/i","</em>"),
		array("/\[u\]/i","<u>"),
		array("/\[\/u\]/i","</u>"),
		//$1 id PHP 5.3 for preg replace
    array("/\[url=([^\]]+)\](.*?)\[\/url\]/i", array("<a href=\"", "\">", "</a>")),
		//array("/\[url\](.*?)\[\/url\]/i", "<a href=\"$1\">$1</a>"),
		//array("/\[img\](.*?)\[\/img\]/i", "<img src=\"$1\" />"),
		//array("/\[color=(.*?)\](.*?)\[\/color\]/i", "<font color=\"$1\">$2</font>"),
		//array("/\[code\](.*?)\[\/code\]/i", "<span class=\"codeStyle\">$1</span>&nbsp;"),
		//array("/\[quote.*?\](.*?)\[\/quote\]/i", "<span class=\"quoteStyle\">$1</span>&nbsp;"),
		//array("/\[(center|right|left|justify)\](.*?)\[\/\\1\]/i", "<p style=\"text-align: \${1};\">\${2}<\/p>"),
		//array("/\[(\/?(ul|li|ol))\]/i", "<$1>"),
    array("/([^\[].*)/i", ""),
		);

	/** @param string $text 
	  * @return string */
	public function run($text) {
/*    $run = true;
    while ($run) {
        preg_match_all("/\[([^\]]*)\]/i", $text, &$matches);
        Incube_Debug::dump($matches);die(); 
        break;
    }*/
		return $text;
	}
}
