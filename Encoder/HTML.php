<?php
namespace Incube\Encoder;
/** @author incubatio 
  * @licence GPLv3.0 http://www.gnu.org/licenses/gpl.html
  */
class HTML extends XML {

  protected $_docTypes = array(
      '4'         => array(
        'strict'        => '<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">',
        'transitional'  => '<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">',
        'frameset'      => '<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Frameset//EN" "http://www.w3.org/TR/html4/frameset.dtd">',
        'rdfa'          => '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML+RDFa 1.0//EN" "http://www.w3.org/MarkUp/DTD/xhtml-rdfa-1.dtd">'
        ),
      '5'         => '<!DOCTYPE html>'
  );

  protected static $_indentSize = 4;

  /** @param array $data
   * @return string */
  protected function _resolve(array $line, $content = '') {
    list($indent, $data) = $line;
    if(is_array($data)) {
      switch(true) {

        case array_key_exists('tag', $data):
          $tag = $data['tag'];
          unset($data['tag']);
        case array_key_exists('class', $data) || array_key_exists('id', $data):
          if(!isset($tag)) $tag = 'div';
          $result = self::create_tag($tag, $data, $content, $indent);
          break;

        case array_key_exists('code', $data):
          $params = '';
          $functionName = $data['code'];
          $codeSplitted = preg_split('/ +/', $data['code'], 2); 
          if(count($codeSplitted) == 2 ) list($functionName, $params) = $codeSplitted;

          if(count($codeSplitted) == 2 || PHP::isBlock($data['code'])) {
            if(PHP::isBlock($functionName)) {
              $result = PHP::createBlock($functionName, $params, $content, $indent);
            } else {
              $result = $this->exec($functionName, $params, $data['silent'], $indent);
            }
          } else {
            $result = PHP::createEmbededPHP($data['code'], $data['silent'], $indent);
          }
          break;

        case array_key_exists('doctype', $data):
          $version = array_key_exists('version', $data) ? $data['version'] : '5';
          if($version == '5') {
            $result = $this->_docTypes[$version]; 
          } else {
            $type = array_key_exists('type', $data) ? $data['type'] : 'strict'; 
            $result = $this->_docTypes[$version][$type]; 
          }
          break;
        default:
          //var_dump($data);
          trigger_error('Data described above are not supported by ' . __CLASS__, E_USER_ERROR);
          break;
      }
    } else $result = self::_indent($indent) . $data;

    return $result;
  }

  public function exec($functionName, $params, $silent, $indent = 0){

    $params = preg_split('/ *, */', $params); 
      foreach($params as $key => $param) { 
        if($param[0] == ':') $params[$key] = '"' . substr($param, 1) . '"';
      }
      $result = $functionName . '(' . implode(', ', $params) . ');';
    return self::_indent($indent) . PHP::createEmbededPHP($result, $silent);

  }
}
