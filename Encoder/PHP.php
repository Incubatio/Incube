<?php
namespace Incube\Encoder;
/** @author incubatio 
  * @licence GPLv3.0 http://www.gnu.org/licenses/gpl.html
  */
class PHP {

  protected static $_indentSize = 2;

  protected $_lineSeparator = "\n";

  protected static function _indent($num, $size = null) {
    if(is_null($size)) $size = self::$_indentSize;
    return str_repeat(' ', $num * $size);
  }

	/** @param string $PHPCode
	  * @param string $startTag
	  * @return string */
	public static function createEmbededPHP($PHPCode, $silent = true, $indent = 0) {
    $startTag = $silent ? '<?php' : '<?=';
    return self::_indent($indent) . implode(' ', array($startTag, $PHPCode, '?>'));
  }

	/** @param string $tag
	  * @param array $params
	  * @param string label *
    * @return string */
  public static function createBlock($blockName, $params, $content = null, $indent = 0) {
    //$content = trim($content);
    /*switch($blockName) {
      case 'for':
        $block = (count($params) == 3) ? '(' . $params[0] . ';' . $param[1] . ';' . $param[2] . ')' : 3;
        break;
      case 'foreach':
        var_dump($params);
        die;
        $block = (count($params) == 2 ) ? '(' . $params[0] . ' as ' . $param[1] . ')' : 2;
        break;
      case 'while':
        $block = (count($params) == 1 ) ? '(' . $params[0] . ')' : 1;
        break;
      case 'if':
        $block = (count($params) == 1 ) ? '(' . $params[0] . ')' : 1;
        break;
      case 'else':
        $block = '';
        break;
      default:
        $error = 'Invalid type of block: ' . $blockName;
    }
*/
    if(is_int($params)) {
      $error = "$blockName require $block params";
    } else {
      return implode("\n", 
          array( 
            self::_indent($indent) . self::createEmbededPHP("$blockName$params {"), 
            $content, 
            self::_indent($indent) . self::createEmbededPHP('}')
            ));
    }
    trigger_error("$error", E_USER_ERROR);

  }


  public static function isBlock($blockName) {
    return in_array($blockName, array('for', 'while', 'foreach', 'if', 'else'));
  }

  /** @param array $data
   * @return string */
  protected function _resolve(array $line, $content = '') {
    list($indent, $data) = $line;
    if(is_array($data)) {
      switch(true) {
        default:
          var_dump($data);
          trigger_error('Data described above are not supported by ' . __CLASS__, E_USER_ERROR);
      }
    } else $result = $data;

    return $result;

  }

  public function encode(array &$lines) {

    $buffer = array();
    while (!empty($lines)) {

      $line = array_shift($lines);
      $nextLine = current($lines);

      // if nextline is a child of current line
      if($nextLine[0] == $line[0] + 1) {
        $buffer[] = $this->_resolve($line, $this->encode($lines));
        $nextLine = current($lines);
      } else $buffer[] = $this->_resolve($line);

      // if nextline is not a sibling or a child
      if($nextLine[0] < $line[0]) break;

    }
    return implode($this->_lineSeparator, $buffer);
  }
}
