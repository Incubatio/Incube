<?php
namespace Incube\Filter;
use Incube\Encoder\HTML;
class HAML {

  protected function _resolve($line, $content = '') {
    list($num, $indent, $data) = $line;

    if(array_key_exists('doctype', $data)) return $data['doctype'];

    if(!array_key_exists('tag', $data)) $data['tag'] = 'div';
    $tag = $data['tag'];
    unset($data['tag']);

    $out = array();
    return HTML::create_tag($tag, $data, $content, $indent);
    //return implode("\n", $out);
  }

  //recursive fonction filter
  public function filter(array $lines) {

    $buffer = array();
    while (!empty($lines)) {

      $line = array_shift($lines);
      $nextLine = current($lines);

      // if nextline is a child of current line
      if($nextLine[1] == $line[1] + 1) {
        $buffer[] = $this->_resolve($line, filter(&$lines));
        $nextLine = current($lines);
      } else $buffer[] = $this->_resolve($line);

      // if nextline is not a sibling or a child
      if($nextLine[1] < $line[1]) break;

    }
    return implode("\n", $buffer);
  }
}
