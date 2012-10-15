<?php

namespace Incube\Tools;
/** @author incubatio
  * @depandancies Incube_IArray, Incube_File_Explorer
  * @licence GPLv3.0 http://www.gnu.org/licenses/gpl.html
  */

class Console {

  /** recursive rendering
   * @param mixed data
   * @return string 
   */
  public static function render_data($data, $out = array()) { 
    foreach($data as $key => $datum) { 
      if($datum instanceof DateTime) $datum = $datum->format('d M Y'); 
      if(empty($datum)) $datum = 'N/A'; 
      if(is_array($datum)) { 
        $out[] = '| > ' . ucfirst($key); 
        $out[] = self::render_data($datum);
      } else {
        if(ucfirst($key)=='Email') { 
          $out [] = '| > ' . ucfirst($key) . ': ' . $datum;
        } else { 
          $out [] = '| > ' . ucfirst($key) . ': ' . $datum;
        }   
      }   
    }   
    $out[] = '';
    return implode("\n", $out);
  }   

  /** @param string $text */
  public static function render_title($text) {
      self::necho($text);
      self::necho(str_repeat('-', strlen($text)));
  }

  /** @param string $text */
  public static function necho($text) {
    echo $text . "\n";
  }

}

