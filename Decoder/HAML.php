<?php
class Incube_Decoder_HAML {

  // TODO: test if [0, 1] == {0 => 1}
  protected $_types =  array(
    array('indent' , '^\n( *)'),
    array('tag'    , '^%(\w+)'),
    array('id'     , '^#([\w-]+)'),
    array('class'  , '^\.([\w-]+)'),
    array('params' , '^{([^}]*)}'),
    array('params', '^\(([^)]*)\)'),
    array('space'  , '^ +'),
    array('filter' , '^:([\w-]+)'),
    array('comment', '^(-#|\/)[^\n]*'),

    /*'phpcode'   => '^<\?.*\?>', */
    //'executable'  => '^-([^\n]*)',
    //'displayable' => '^=([^\n]*)',
    array('code'   , '^([=-]) *([^\n]*)'),

    array('escape' , '^\\\\\\\\([^\n]+)'),
    array('doctype', '^!!! ?(\w+)'),
    array('string' , '^[^\n]*'),

  );

  protected $_indentSize = 2;


  public function decode($content) {
    $stack = array();
    $indent = 0;
    $line  = array($indent, array());
    $typeCount = count($this->_types);

    //$i = 1;

    while($lastLength = strlen($content)) {
      foreach($this->_types as $assoc) {
        list($type, $regex) = $assoc;

        $regex = '/' . $regex . '/';
        if (preg_match($regex, $content, $matches)) {
          //echo $i++ . "\n";
          
          //TODO tokenize or replace directly ?
          $content = preg_replace($regex, '', $content);

          switch($type) {
            case 'indent': 
              //echo $matches[0];  // TODO: check usage of indentation here
              if(!empty($line[1])) $stack[] = $line;
              $indent = strlen($matches[1]) / $this->_indentSize;
              $line  = array($indent, array());
              break;
            case 'escape':
            case 'string':
              $line[1] = $matches[0];
              break;
            case 'code': // - if ... || = ...
              if(!empty($line[1])) { 
                $stack[] = $line;
                $indent += 1;
              }
              $silent = ($matches[1] == '-');
              $line = array($indent, array($type => $matches[2], 'silent'=> $silent));
              break;
            case 'comment':
            case 'space':
              break; 
            case 'params':
              $params = explode(',', $matches[1]);
              foreach($params as $param) {
                if(preg_match('/=>/', $param)) {
                  list($key, $value) = explode('=>', $param);
                  $key = preg_replace('/^[^\w- ]/', '', $key);
                  $line[1][$key] = $value;
                }
              }
              break;
            default:
              if(isset($line[1][$type]))  $line[1][$type] .= ' ' . $matches[1];
              else                        $line[1][$type] = $matches[1];
          }   
          break;
        }   
      }   
      if($lastLength === strlen($content)) {
        trigger_error('The parser failed to parse: "' . substr($content, 0, 100) . ' ..."', E_ERROR);  
      }   
    }
    //die;
    return $stack;
  }

}
