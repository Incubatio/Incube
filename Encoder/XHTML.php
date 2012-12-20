<?php
namespace Incube\Encoder;
/** @author incubatio 
  * @licence GPLv3.0 http://www.gnu.org/licenses/gpl.html
  */
class XHTML extends XML {

  protected $_docTypes = array(

      'XHTML'   => array(
        '1.0'       => array( 
          'strict'        => '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">', 
          'transitional'  => '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">',
          'frameset'      => '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Frameset//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-frameset.dtd">' 
          ),
        '1.1'       => array( 
          ''              => '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1//EN" "http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd">', 
          'basic'         => '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML Basic 1.1//EN" "http://www.w3.org/TR/xhtml-basic/xhtml-basic11.dtd">' 
          ),
        '1.2'       => array(
          'mobile'        => '<!DOCTYPE html PUBLIC "-//WAPFORUM//DTD XHTML Mobile 1.2//EN" "http://www.openmobilealliance.org/tech/DTD/xhtml-mobile12.dtd">',
          ),
        ),

      'HTML'    => array(
        '4'         => array(
          'strict'        => '<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">',
          'transitional'  => '<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">',
          'frameset'      => '<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Frameset//EN" "http://www.w3.org/TR/html4/frameset.dtd">',
          'rdfa'          => '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML+RDFa 1.0//EN" "http://www.w3.org/MarkUp/DTD/xhtml-rdfa-1.dtd">'
          ),
        '5'         => '<!DOCTYPE html>'
        )
  );

  protected static $_indentSize = 4;

  /** @param array $data
   * @return string */
  protected static function _encode(array $data) {
    list($num, $indent, $data) = $line;
    switch(true) {
      case array_key_exists('doctype', $data):
        $version = array_key_exists('version', $data) ? $data['version'] : '1.1'; 
        $encoding = array_key_exists('encoding', $data) ? $data['encoding'] : ''; 
    }


    if(!array_key_exists('tag', $data)) $data['tag'] = 'div';
    $tag = $data['tag'];
    unset($data['tag']);

    return HTML::create_tag($tag, $data, $content, $indent);
  }
}
