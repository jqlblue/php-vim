<?php
@include( "Console/Color.php" );

/**
 *  PHP manual function-lookup class.
 *  Together with the EPC_Xml_ManualReader class this makes function-lookup
 *  in the terminal a breeze.
 *
 *  @package EPC_Xml
 *	@license GPL
 */
class EPC_Phpm_Reader extends EPC_Object
{
    // {{{ constants
    const EXACT_QUERY = "exact";
    const START_QUERY = "start";
    const CONTAINS_QUERY = "contains";
    // }}} 
    
    // {{{ private variables 
    /**
	 *	The DomDocument that loads the functions file
	 *	@access private
	 */
	private $dom;
	/**
	 *	XPath object that is used to query the content
	 *	@access private
	 */
    private $xpath;
    /**
     *  Array of queries with both case and case-insensitive queries.
     *  @access private
     */
    private $xpathQueries;
    /**
     *  Holds info on if we are using case or case-insensitive query
     *  @access private
     */
    private $case;
	/**
	 *	The result from the XPath query, a array of SimpleXMLElement objects
	 *	@access private
	 */
    private $xml;
	/**
	 *	If the user have the Console_Color package from pear.php.net
	 *	@access private
	 */
	private $consoleColor;
	/**
	 *	Flag to decide if the user wants to display colors based on the settings.
	 *	@access private
	 */
	private $showColors;
	/**
	 *	Shortcut to the color element of the settings file.
	 *	@access private
	 */
	private $color;
	/**
	 *	Wether to show the short description of the function call.
	 *	Defaults to false, but will be true if the $longDescription is set to true
	 *	@access private
	 */
	private $shortDescription;
	/**
	 *	Wether to show the long description of the function call
	 *	@access true
	 */
	private $longDescription;
    // }}}
    
    // {{{ public function __construct( $conf )
    /**
     *  Constructor.
     *  Makes sure that the settings file is correct and we have everything
     *  needed to execute the application.
     *
     *  @param string $conf  Path to the same settings file that is used in EPC_Xml_ManualParser
     *  @access public
     *  @return void
     */
    public function __construct( $conf )
    {
        if( file_exists( $conf ) )
        {
            $this->settings = simplexml_load_file( $conf );    
        }
        else
        {
            die( "Can't load a missing settings file" );
        }
        $this->dom = new DomDocument();
        $this->xmlFile = "php";
        if( $this->settings->defaultproject )
        {
            $this->xmlFile = $this->settings->defaultproject["value"];
        }
		$this->showColors = false;
		$this->shortDescription = false;
		$this->longDescription = false;
		$this->color = $this->settings->color;
		if( $this->settings->color["display"] == "yes" )
		{
			$this->showColors = TRUE;
		}
    	    $this->consoleColor = FALSE;
    	    $this->xml = array();
    	    $this->xpathQueries = array(
    	       self::EXACT_QUERY => array(
    	           "c" => "//methodsynopsis[@function=\"%s\"]",
    	           "ci" => "//methodsynopsis[translate( @function, 
    	           										'ABCDEFGHIJKLMNOPQRSTUVWXYZ', 
    	           										'abcdefghijklmnopqrstuvqxyz' 
    	           									   )=\"%s\"]"
    	       ),
    	       self::START_QUERY => array(
    	           "c" => "//methodsynopsis[starts-with(@function, \"%s\") ]",
    	           "ci" => "//methodsynopsis[starts-with(
    	           		translate( 
    	           			@function, 
    	           			'ABCDEFGHIJKLMNOPQRSTUVWXYZ', 
    	           			'abcdefghijklmnopqrstuvqxyz' 
    	           		), \"%s\") ]"
    	       ),
    	       self::CONTAINS_QUERY => array(
    	           "c" => "//methodsynopsis[contains(  @function, \"%s\" ) ]",
    	           "ci" => "//methodsynopsis[contains( 
    	           		translate( 
    	           			@function, 
    	           			'ABCDEFGHIJKLMNOPQRSTUVWXYZ', 
    	           			'abcdefghijklmnopqrstuvqxyz' 
    	           		), \"%s\" ) ]"
    	       )
    	    );
    	    $this->case = "ci";
	}
    // }}}
    
    // {{{ public function query( $func )
    /**
     *  Query the XML file that keeps all the function calls with a 
     *  XPath query to see if the user have entered a valid PHP function call.
     *
     *  @param string $argv A valid PHP function call
     *  @return bool
     *  
     */
    public function query( $argv )
    {
        $this->query = $argv[1];
        $this->parseArgs( $argv );
        if( $this->case == "ci" )
        {
            $this->query = strtolower( $this->query );
        }
        
        $file = $this->settings->install["path"] . "/" . $this->xmlFile . ".xml";
        if( is_file( $file ) )
        {
            $this->dom->load( $file );
        }
        else
        {
            die( ">>File: '$file' could not be found!\n" );
        }
        $this->xpath = new DomXpath( $this->dom );
        
        if( $this->functionQuery( self::EXACT_QUERY ) )
        {
            return true;
        }
        if( $this->functionQuery( self::START_QUERY ) )
        {
            return true;
        }
        if( $this->functionQuery( self::CONTAINS_QUERY ) )
        {
            return true;
        }
        return false;
    }
    // }}}
    
    // {{{Êprivar function functionQuery( $query )
    /**
     *  Privar helper function for query() to query the XML file and build up the
     *  array of SimpleXMLElement objects that is displayed.
     *
     *  @access private
     *  @return bool If the query was successfull or not
     *  @param string $query A string formatted for sprintf() to query the DomXpath object with.
     */
    private function functionQuery( $query )
    {
        $result = $this->xpath->query( sprintf( $this->xpathQueries[$query][$this->case], $this->query ) );
        if( $result->item( 0 ) )
        {
            foreach( $result as $res )
            {
                $this->xml[] = simplexml_import_dom( $res );
            }
            return true;
        }
        return false;
    }
    // }}}
    
    // {{{ public function display()
    /**
     *  Display the function queried for to the user.
     *  This function dumps the content out, no special considerations
     *  are taken for now on where to output the content.
     *
     *  If you want to use the result somewhere else: pipe it or use the
     *  ob_*() functions to collect the content.
     *
     *  @acccess public
     *  @return void
     */
    public function display()
    {
        foreach( $this->xml as $simple )
        {
            $this->displaySingleMethod( $simple );
        }
    }
    // }}}
     
    // {{{ private function displaySingleMethod( SimpleXMLElement $xml )
    /**
     *  Helper function for display() to print out a single method call.
     *
     *  @param object $xml A SimpleXMLElement object
     *  @access private 
     *  @return void
     */
    private function displaySingleMethod( SimpleXMLElement $xml )
    {
		if( class_exists( "Console_Color"  ) ) 
		{
			$this->consoleColor = TRUE;
		}
        echo $this->color( $xml->type, "return" ) . " ";
		echo $this->color( $xml['function'], "func" ) . " ( ";
        $param = '';
        foreach( $xml->methodparam as $p )
        {
            $tmp  = $this->color( $p->type, "type" ) . " ";
			$tmp .= $this->color( "$" . trim( $p->parameter ), "var" ) ;
            if( isset($p['choice'] ) && $p['choice'] == "opt" )
            {
                $tmp = "[" . $tmp . "]";
            }
            $param .= $tmp . ", ";
        }
        $param = preg_replace( "/, $/", "", $param );
        echo $param;
        echo " )\n";
        if( $this->shortDescription )
        {
        	if( $xml->sdesc != "" )
        	{
        		echo "\t" . wordwrap( $xml->sdesc, 65, "\n\t" ) . "\n";
    		}
    	}
    	if( $this->longDescription )
    	{
    		echo "\t" . wordwrap( $xml->desc, 65, "\n\t" )  . "\n";
    	}
    }
    // }}}
    
    // {{{ private function parseArgs( $argv )
    /**
     *  Very crude argument parser that discovers the project we
     *  want to query and the query itself ( the default project is "php" ).
     *
     *  @param array $argv  The arguments passed to this program
     *  @access private
     *  @return void
     */
    private function parseArgs( $argv )
    {
    	if( count( $argv ) == 1 )
    	{
    		$this->displayHelp();
    		die();
    	}
        foreach( $argv as $k => $v )
        {
            switch( $v )
            {
                case "-p":
                    $this->xmlFile = $argv[ $k + 1];
                    break;
                case '-c':
                case '--case':
                    $this->case = "c";
                    break;
                case '-ci':
                case '--case-insensitive';
                    $this->case = "ci";
                    break;
                case '-e':
                case '--extended':
                	$this->longDescription = true;
                	$this->shortDescription = true;
                	break;
              	case '-s':
              		$this->shortDescription = true;
              		break;    
                case '-i':
                case '--install':
                    $install = new EPC_Phpm_Install( $this->settings );
                    $install->install( $argv[ $k + 1] );
                    $install->run();                    
                    die();   
                case '-la':     
                case '--list-all':
                    $list = new EPC_Phpm_ListAll( $this->settings );
                    $list->run();
                    die();
                case '-l':
                case '--list':
                    $list = new EPC_Phpm_ListInstalled( $this->settings );
                    $list->run();
                    die();        
                case '-h':
                case '--help':
                	$this->displayHelp();
                	die();             
            }
        }
        $this->query = $argv[count($argv) - 1];
    }
    // }}}

	// {{{ private function color( $str, $type )
	/**
	 *	Private helper function that will color the content based
	 *	on the setting on the current object. 
	 *	The coloring is based on the existance of Console_Color or 
	 *	the "display" flag in the settings.xml file.
	 *
	 *	@access private
	 *	@return string The colored text
	 */
	private function color( $str, $type ) 
	{
		if( !$this->consoleColor || !$this->showColors )
		{
			return $str;
		}
		switch( $type ) 
		{
			case "func":
				return Console_Color::convert( "%" .  $this->color->func . $str . "%n" );	
				break;
			case "return":
				return Console_Color::convert( "%" . $this->color->return . $str . "%n" );
				break;
			case "type":
				return Console_Color::convert( "%" . $this->color->type . $str . "%n" );
				break;
			case "var":
				return Console_Color::convert( "%" . $this->color->var . $str . "%n" );
			default:
				return $str;
		}
	}
	// }}}
	
	// {{{ private function displayHelp()
	/** 
	 *	Display the help for the program
	 *
	 *	@access private
	 *	@return void
	 */
	private function displayHelp()
	{
		echo ">> Usage: phpm [options] key\n";
		echo "'-p project' Choose project\n";
		echo "'-e/--extended' View extended information\n";
		echo "'-s' View short information, this is true if '-e' is set\n";
		echo "'-c/--case' Query case sensitive \n";
		echo "'-ci/--case-insensive' Query case insensitive\n";
		echo "'-i/--install project' Install a project from the server\n";
		echo "'-la/--list-all' List all the projects that can be installed\n";
		echo "'-l/--list' List all the projects already installed\n";
		echo ">> http://eide.org/?epc=php\n";
	}
	// }}}
}
?>
