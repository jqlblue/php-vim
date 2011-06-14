<?php
/**
 *  Class for displaying all the current projects that can
 *  be installed with the phpm program.
 *
 *  @package EPC_Phpm
 *  @copyright &copy; 2004 Havard Eide
 *  @license GPL
 */
class EPC_Phpm_ListAll extends EPC_Object
{
    // {{{ public function __construct( $settings )
    /**
     *  Constructor - Makes sure that the settings object contains
     *  the download listing URL, if not the constructor will die() with
     *  a message to the user.
     *
     *  @param object SimpleXMLElement Settings object that comes from "settings.xml"
     *  @access public
     *  @return void
     */
    public function __construct( SimpleXMLElement $settings )
    {
        $this->settings = $settings;
        if( !isset( $this->settings->download["listing"] ) )
        {
            die( "The download listing setting was not set!" );
        }
    }
    // }}}
    
    // {{{ public function run()
    /**
     *  The only function to run on the object.
     *
     *  It will attempt to download the settings file and list all the
     *  projects that can be downloaded from the central server.
     *
     *  @access public
     *  @return void
     */ 
    public function run()
    {
        $list = @file_get_contents( $this->settings->download["listing"] );
        if( $list )
        {
            echo ">>The following projects are available from the server:\n";
            $xml = new SimpleXMLElement( $list );
            foreach( $xml->project as $project )
            {
                echo 'Project: "' . $project->name . '" ';
                echo 'version: '. $project->version . ' '. "\n\t\t";
                echo 'key: "' . $project->key . '"' . "\n";
            }
            echo "\n>>Install any of these with: 'phpm --install {key}'\n";
        }
        else
        {
            echo ">>Could not get the settings file from the server.\n";
        }
    }
    // }}}
    
    // {{{ public function toXML()
    /** 
     *  Override the parent toXML().
     *
     *  @return string A XML string with the settings URL listed as variable.
     */
    public function toXML()
    {
        return parent::toXML( array( "settings" => $this->settings->download["listing"] ) );
    }
    // }}}
}
?>