<?php
////////////////////
//  Setting up env
///////////////////
require 'autoload.php'; // because this is not a cron script

if (file_exists( "config.php" )) {
    require "config.php";
}

// set up command line params here
$params = new ezcConsoleInput();

$helpOption = new ezcConsoleOption( 'h', 'help' );
$helpOption->mandatory = false;
$helpOption->shorthelp = "Show help information";
$params->registerOption( $helpOption );

$siteaccessOption = new ezcConsoleOption( 's', 'siteaccess', ezcConsoleInput::TYPE_STRING );
$siteaccessOption->mandatory = false;
$siteaccessOption->shorthelp = "The siteaccess name.";
$params->registerOption( $siteaccessOption );

/* 

// Example of setting other options

$objectIdOpt = new ezcConsoleOption( 'i', 'objectid', ezcConsoleInput::TYPE_STRING );
$objectIdOpt->mandatory = true;
$objectIdOpt->shorthelp = "Object Id";
$params->registerOption( $objectIdOpt );

$versionIdOpt = new ezcConsoleOption( 'v', 'version', ezcConsoleInput::TYPE_STRING );
$versionIdOpt->mandatory = true;
$versionIdOpt->shorthelp = "Version";
$params->registerOption( $versionIdOpt );

*/

// Process console parameters
try {
  $params->process();
} catch ( ezcConsoleOptionException $e ) {
	echo $e->getMessage(). "\n";
	echo "\n";
	echo $params->getHelpText( 'Some quick explanation' ) . "\n";
	echo "\n";
	exit();
}
// Init an eZ Publish script - needed for some API function calls
// and a siteaccess switcher

$ezp_script_env = eZScript::instance(array(
	'debug-message' => '',
	'use-session' => true,
	'use-modules' => true,
	'use-extensions' => true
));
$ezp_script_env->startup();

if( $siteaccessOption->value ) {
	$ezp_script_env->setUseSiteAccess( $siteaccessOption->value );
}
$ezp_script_env->initialize();

//////////////////////////
// Extra input validation
//////////////////////////

// process the available params, maybe throw an error if some combination is off or something (params->process only gets required part)
//if (!$bAllParamsGood) {
//	$params->getHelpText( 'Export recent content.' ) . "\n";
//	$ezp_script_env->shutdown();
//}

//////////////////////////
// Script process
//////////////////////////

$somethingManager = new eztagMetadataManager();
$somethingManager->autoGenerateMissingTags();

print "All tags should be in the database now.\n";

// Avoid fatal error at the end
$ezp_script_env->shutdown();

?>