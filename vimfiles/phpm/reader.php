<?php
/**
 *  Progam to query a XML file with API calls.
 *
 *  See the epc/xml/manualreader.php
 */
include( "lib/epc/object.php" );
$xml = new EPC_Phpm_Reader( "settings.xml" );
if( $xml->query( $argv ) )
{
	echo $xml->display();
}
else
{
	echo "Could not find the function specified";
	echo "\n";
}
?>
