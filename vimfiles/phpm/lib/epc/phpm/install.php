<?php
/**
 *  Class for installing a single XML file from a remote server.
 *
 *  The class looks for a list of available projects in the settings file:
 *  &gt;download listing="http://eide.org/files/phpm/list.xml" /&lt;
 *  This contains a list of the project keys and when it was updated.
 *
 *  The class then downloads the file from the remote server and stores it
 *  according to the &gt;install path="/usr/share/php/phpm" /&lt; setting.
 *
 *  @package EPC_Phpm
 *  @license GPL
 */
// {{{Êclass EPC_Phpm_Install extends EPC_Object
class EPC_Phpm_Install extends EPC_Object
{
    // {{{ private variables
    /**
     *  The key for the project we are installing.
     *  @access private
     */
    private $install;
    /**
     *  SimpleXMLElement settings object.
     *  @access private
     */
    private $settings;
    // }}}
    
    // {{{ public function __construct( SimpleXMLElement $settings )
    /**
     *  Checks that all the settings are set properly, will die() if not.
     *
     *  @access public
     *  @return void
     *  @param object $settings SimpleXMLElement Settings object.
     */
    public function __construct( SimpleXMLElement $settings )
    {
        $this->settings = $settings;
        if( !isset( $this->settings->download["listing"] ) )
        {
            die( "No download listing available in the settings file! Nothing to work with....\n" );
        }
        if( !isset( $this->settings->install["path"] ) )
        {
            die( "No install path in the settings file.... you got to give me that!" );
        }
        if( !is_dir( $this->settings->install["path"] ) )
        {
            die( "You just gave me a non-existing install directory...." );
        }
    }
    // }}}
    
    // {{{ public function install( $key )
    /**
     *  Set the project key to install, this will be checked against the list on the
     *  server.
     *
     *  @access public
     *  @return void
     *  @param string $key A valid ( non-empty ) project key
     */
    public function install( $key )
    {
        $this->install = $key;
        if( $this->install == "" )
        {
            die( "Can't install a empty key!" );
        }
    }
    // }}}
    
    // {{{ public function run()
    /**
     *  Main function to run on the object.
     *  will connect to the server and get the latest download listing file,
     *  check the key ste to download and finally download the file from the server to 
     *  the install path as specified in the settings file.
     *
     *  @access public
     *  @return void
     */
    public function run(  )
    {
        echo ">> Getting project listing....\n";
        $list = @file_get_contents( $this->settings->download["listing"] );
        if( $list )
        {
            $xml = new SimpleXMLElement( $list );
            $download = $xml->download["path"];
            $key = false;
            foreach( $xml->project as $p )
            {
                if( $p->key == $this->install )
                {
                    $key = $p->key;
                    break;
                }
            }
            if( $key )
            {
                echo ">> Installing: " . $p->key. ", version: ". $p->version . ", ";
                echo "updated: ". $p->updated ." \n";
                $file = $download . $key . ".xml";
                $content = @file_get_contents( $file );
                $output = $this->settings->install["path"] . "/" . $key . ".xml";
                file_put_contents( $output, $content );
                echo ">> $name successfully installed\n";
            }
            else
            {
                echo ">> Could not find the project specified\n";
            }
        }
        else
        {
            echo ">> Could not get the download listing from the server...\n";
        }
    }
    // }}}
}
// }}}

?>