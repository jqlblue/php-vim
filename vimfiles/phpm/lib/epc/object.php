<?php
/**
 *  This file contains the base functionality of the EPC library: autoload etc.
 *  It also contains the EPC_Object that is the base for many of the objects in the library.
 *
 *  @copyright &copy;2004 Havard Eide
 */
 
/**
 *  Base path to the EPC library.
 */
define( "EPC_LIB_DIR", dirname( __FILE__ ) );

// {{{ class EPC_Object
/**
 *  The base object for the EPC library, still early in the development.
 *
 *  @package EPC
 */
class EPC_Object
{
    // {{{Êpublic function __construct()
    /**
     *  Empty constructor for now.
     *  
     *  @access public
     *  @return void
     */
    public function __construct()
    {
    
    }
    // }}}
    
    // {{{ public function toXML()
    /**
     *  Serialize the object to XML.
     *
     *  @param array $variables Defaults to NULL, if not: a array of key=>value pairs that
     *      will be parsed as the objects variables.
     *  @access public
     *  @return string A XML string with the serialized object
     */
    public function toXML( $variables = NULL )
    {
        $str = '<class name="' . get_class( $this ) . '">';
        if( is_array( $variables ) )
        {
            foreach( $variables as $k => $v )
            {
                $str .= '<var key="' . $k . '" value="' . $v . '" />';
            }
        }
        $str .= "</class>";
        $dom = new DomDocument( "1.0" );
        $dom->loadXML( $str );
        return $dom->saveXML();
    }
    // }}}
}
// }}}


// {{{ function __autoload()
/**
 *  Override the magic __autoload() function to automaticly load the
 *  classes needed in the EPC library.
 */
function __autoload( $class )
{
    $class = EPC_LIB_DIR . '/' . strtolower( str_replace( '_', '/', $class ) ) .'.php';
    $class = str_replace( '/epc/epc/', '/epc/', $class );
    if( is_file( $class ) )
    {
        include_once( $class );
    }
    
}
// }}}
?>