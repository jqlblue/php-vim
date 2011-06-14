<?php
/**
 *  Class for listing the installed XML files that keeps the
 *  function definitions.
 *  The class outputs the result directly to the terminal.
 *
 *  @package EPC_Phpm
 *  @copyright &copy; 2004 Havard Eide
 *  @license GPL
 */
// {{{ class EPC_Phpm_ListInstalled extends EPC_Object 
class EPC_Phpm_ListInstalled extends EPC_Object
{
    // {{{ private variables
    private $settings;
    // }}}
    
    // {{{Êpublic function __construct( SimpleXMLElement $settings )
    /**
     *  Checks the settings and if the install directory is intact for reading.
     *
     *  @access public
     *  @return void
     */
    public function __construct( SimpleXMLElement $settings )
    {
        $this->settings = $settings;
        if( !isset( $this->settings->install["path"] ) )
        {
            die( "No download path specified!!!\nAboring..." );
        }
        if( !is_dir( $this->settings->install["path"] ) )
        {
            die( "The instlll directory is not a directory... can't work with that..." );
        }
    }
    // }}}
    
    // {{{ public function run()
    /**
     *  Main function on the object.
     *  Iterates over the install directory and lists the installed files 
     *  with instructions on how to install/update version.
     *
     *  @access public
     *  @return void
     */
    public function run()
    {
        $it = new EPC_Phpm_ListInstalledIterator( 
            new DirectoryIterator( $this->settings->install["path"] )
        );
        echo ">>You have currently installed the following: \n";
        foreach( $it as $file )
        {   
            echo "  " . $file->getPathname() . " \tkey='" . str_replace( ".xml", "", $file ) . "'\n";
        }
        echo ">>Update by executing: 'phpm --install {key}' \n";
        echo ">>Check available projects by executing: 'phpm --list-all'\n";
    }
    // }}}
}
// }}}

/**
 *  Filteriterator for checking for valid files to list
 *  in the install directory.
 *
 *  @package EPC_Phpm
 */
// {{{ class EPC_Phpm_ListInstalledIterator extends FilterIterator
class EPC_Phpm_ListInstalledIterator extends FilterIterator
{
    // {{{ public function accept()
    /**
     *  Implement the accept() function to only allow XML files.
     *
     *  @access public
     *  @return bool If the current file is a XML file or not.
     */
    public function accept()
    {
        if( preg_match( '/\.xml$/', $this->getInnerIterator()->current() ) )
        {
            return true;
        }
        return false;
    }
    // }}}
}
// }}}
?>