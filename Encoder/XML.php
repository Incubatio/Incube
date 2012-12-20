<?php
namespace Incube\Encoder;
/** @author incubatio 
  * @licence GPLv3.0 http://www.gnu.org/licenses/gpl.html
  */
class XML {

  //TOFIX: doctype should be in HTML class as constants or something
  // List based on http://www.w3.org/QA/2002/04/valid-dtd-list.html + HAML spec (for XHTML 1.2 mobile)
  /*protected $_docTypes = array(
      //TODO: XML has no uri required in its doctype, it can be generated dynamically
      'XML'     => "<?xml version='1.0' encoding='utf-8' ?>",
  ); */

  protected static $_indentSize = 2;

  protected $_lineSeparator = "\n";

  protected static function _indent($num, $size = null) {
    if(is_null($size)) $size = self::$_indentSize;
    return str_repeat(' ', $num * $size);
  }

	/** @param string $tag
	  * @param array $params
	  * @param string label *
	  * @return string */
	public static function create_tag($tagName, array $params = array(), $content = null, $indent = 0) {
    $content = trim($content);
    $element = self::_indent($indent) . "<$tagName";
    foreach($params as $key => $value) {
      $element .= " $key=\"$value\"";
    }   
		if(preg_match("/\n/", $content) OR preg_match("/^<\w.+\>/", $content)) {
      $element .= isset($content) ? ">" : "/>";
      $element = array($element);
      isset($content) ? $element[] = self::_indent($indent + 1) . $content : $content = '';
      $element[] = self::_indent($indent) . "</$tagName>";
      $element = implode($element, "\n");
    } else { 
      $element .= !is_null($content) ? ">$content</$tagName>" : "/>";
    }
    return $element;
	}   

  /** @param array $data
   * @return string */
  protected function _resolve(array $line, $content = '') {
    list($indent, $data) = $line;
    if(is_array($data)) {
      switch(true) {
        case array_key_exists('doctype', $data):
          $version = array_key_exists('version', $data) ? $data['version'] : '1.0'; 
          $encoding = array_key_exists('encoding', $data) ? $data['encoding'] : 'utf-8'; 
          $result = "<?xml version='$version' encoding='$encoding' ?>";
          break;
        case array_key_exists('tag', $data):
          $node = $data['tag'];
          unset($data['tag']);
          $result = Incube_Encoder_XML::create_tag($tag, $data, $content, $indent);
          break;
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
