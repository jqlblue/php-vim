<?php
/**
 *	PHP.net DocBook manual parser.
 *   
 *  The purpose of the class is to generate a XML file that contains all the
 *  function calls available in the PHP manual, this will be used to make a CLI
 *  function lookup program.
 *
 *	@package EPC_Phpm
 *	@license GPL
 */

// {{{ class EPC_Xml_ManualParser
class EPC_Phpm_Parser
{
    // {{{ private variables
    private $startDir;
    // }}}

    // {{{ public function __construct( $conf )
    /**
     *	Create a new manual parser
     *
     *	@param string $startDir Where are we starting the parsing
     *  @access public
     *  @return void
     */ 
    public function __construct( $conf )
    {
        if( file_exists( $conf ) )
        {
            $this->settings = simplexml_load_file( $conf );
            if( !file_exists( $this->settings->cvsdir ) )
            {
                die( "Can't find the CVS dir in: " . $this->settings->cvsdir );
            }
        }
        else
        {
            die( "Can't load a settings file that is missing...." );
        }
        $this->startDir = $startDir;
    }
    // }}}

    // {{{ public function run()
    /**
     *	Main function on the object, initiates the parsing
     *  of the main document tree.
     *
     *  @access public
     *  @return void
     */
    public function run()
    {
        // XML declaration is good to have:
        $this->buffer = '<?xml version="1.0" encoding="iso-8859-1"?>' . "\n<manual>";
        /*
         *  We loop over all the directories in the dir given in the 
         *  constructor: each of these will have a directory called "functions", this
         *  is where all the XML files we want to process are.
         */
        $dir = new RecursiveDirectoryIterator( $this->settings->cvsdir );
        foreach( $dir as $file )
        {
            echo ">>Processing: $file\n";
            if( $file->isDir() && $file->getFileName() != "CVS" )
            {
                foreach( $file->getChildren() as $f )
                {
                    if( $f->getFileName() == "functions" )
                    {
                        $this->processDirectory( $f->getChildren() );
                    }   
                }
            }
        }
        $this->buffer .= "\n</manual>";
        file_put_contents( $this->settings->save, $this->buffer );
    }
    // }}}
    
    // {{{ private function processDirectory( $file )
    /**
     *  Helper function that iterates over the XML files in a single "functions/" 
     *  directory.
     *  It passes each file to the processFile() function.
     *
     *  @access private
     *  @return void
     *  @param $file object A DirectoryIterator instance
     */
    private function processDirectory( DirectoryIterator $file )
    {
        foreach( $file as $f )
        {
            $this->processFile( $f );
        }
    }
    // }}}
    
    // {{{ private function processFile( $file )
    /**
     *  This function is where all the lookup of the manual entry is.
     *
     *  We are very lazy here: we suppress any errors that the Dom might throw at
     *  us, and we load the XML data as HTML - we're just interested in a particular
     *  node - no need to have a whole document that is valid, as long as we are able
     *  to get to the "methodsynopsis" node.
     *
     
     *  @param object $file A DirectoryIterator instance
     *  @access private
     *  @return void
     */
    private function processFile( DirectoryIterator $file )
    {
        $dom = new DomDocument();
        @$dom->loadHTML( file_get_contents( $file->getPathName() ) );
        $xpath = new DomXpath( $dom );
        
        // Get the node where the description of the function call is:
        $result = @$xpath->query( "//methodsynopsis" );
        // Make sure we find a node:
        if( $result->item( 0 ) )
        {
        	$item = $result->item( 0 );
            if( $this->settings->debug == 1 )
            {
                echo "\tparsing: " . $file->getFileName() . "\n";
            }
            
            /*
             *	Get the first para in the documentation, this might come
             *	out wrong for some of the functions, but: it's the closest
             *	we can come to something useful.
             */
            $result = @$xpath->query( "//para" );
        	if( $result->item( 0 ) )
        	{
        		$para = simplexml_import_dom( $result->item( 0 ) );
        		$desc = $para->asXml();
        		// defenently don't need tags for the terminal :)
        		$desc = htmlentities( strip_tags( $desc ) );
        		// We don't need all the space/newlines around either
        		$desc = preg_replace( "/[\r\n][\s]+/", " ", $desc );
        		
        		// Some of the functions are undocumented:
        		if( strstr( $desc, "warn.undocumented.func;" ) )
        		{
        			$desc = "Undocumented";
        		}
        		$item->appendChild(
        			$dom->createElement(
        				"desc",
       					$desc
        			)	
        		);
        	}            
            $xml = simplexml_import_dom( $item );
            // Set the "function" attribute - this is what we will look for later.
            $xml['function'] = (string)$xml->methodname;
        	$result = @$xpath->query( "//para" );
       		$this->buffer .= "\n" . $xml->asXml();
        }
    }

}
// }}}

// }}}
?>
