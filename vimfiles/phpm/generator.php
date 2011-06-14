<?php
/**
 *	This is the generator of the XML file that is needed to query
 *	the PHP functions, not needed if you don't have the CVS tree
 *	on your computer.
 */ 
echo ">> Starting up the XML generator...\n";

include( "lib/epc/object.php" );
$parser = new EPC_Phpm_Parser( "settings.xml" );

$parser->run();

echo ">> Finished\n\n";
?>
