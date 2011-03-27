<?php
/** @author incubatio 
  * @licence GPLv3.0 http://www.gnu.org/licenses/gpl.html
  */
class Incube_Debug {

	/** @param mixed $param
	  * @param string $label 
	  * @return string */
	public static function dump($params, $label = ""){
		ob_start();
		var_dump($params);
		//trim(preg_replace("#\=\>\n(\s+)#", " => ",$temp)) . ";\n";
		$output = preg_replace("#\]\=\>\n(\s+)#m", "] => ", ob_get_clean());
		return self::_dump($output, $label);
	}

	/** Arraydump allow to dump arrays in it code form, Object rendering is not reusable code
	  * @param array $assoc
	  * @param string $label 
	  * @return string */
	public static function adump(array $assoc, $label = "") {
		foreach($assoc as $key => $param) {  
			ob_start();
			var_dump($param);

			$temp = "$" . $key . " = ";
			$temp .= preg_replace("#\[|\]#", "", ob_get_clean());
			$temp =	preg_replace("#string\(.\)|int\(|bool\(|object\(|\([0-9]|\)#", "", $temp);
			$temp = preg_replace("#{#", "(", $temp);
			$temp = preg_replace("#}#", ")", $temp);
			$temp = preg_replace("#\=\>\n(\s+)([^\n]+)#", " => \$2,",$temp);
			$temp = preg_replace("#\(,#", "(",$temp);
			$output[] = trim($temp) . ";\n";
		}
		return self::_dump(implode("\n", $output), $label);
	}

	/** @param string $text
	  * @param string $label 
	  * @return string */
	protected static function _dump($text, $label="") {
		if ($j = strlen($label)) {
			$label = "|= " . $label . " =\\" .  PHP_EOL;
			for($i=-4; $i<$j; $i++) { 
				$label .= "=="; 
			}
			$text = $label . PHP_EOL . $text;
		}
		echo "<pre>" . $text . "<pre>";
		return $text;
	}

}
