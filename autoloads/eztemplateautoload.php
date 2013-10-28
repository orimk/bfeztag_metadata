<?php
$dirParts = explode(DIRECTORY_SEPARATOR, dirname(__FILE__));
$extensionName = $dirParts[sizeof($dirParts)-2];

if (!class_exists("bfCustomExtension")) { // should be in bfcore for sure
	// try a few extra places
	// first, in the local extension, classes/customExtension/customExtension.php_noauto (here for easier distribution, and so we don't have this autoincluded everywhere)
	$localCopyPath = dirname(__FILE__)."/"."../classes/bfCustomExtension/bfCustomExtension.php_noauto";
	if (file_exists($localCopyPath)) {
		include_once($localCopyPath);
	}
	if (!class_exists("bfCustomExtension")) {
		die("Extension $extensionName: you are missing the bfCustomExtension class. It should be supplied as a part of bfcore extension, in this local extension, or it should be placed at the PROJECTROOT/extension/ directory.\n");
	}
}
$className = $extensionName; // in this extension, in classes, you should have a class using the extension name
$operators = array_keys(bfCustomExtension::getExtensionDetails($className));
$eZTemplateOperatorArray = array();
$eZTemplateOperatorArray[] = array(
	'class' => $className,
	'script' => "extension/$className/classes/$className.php",
	'operator_names' => $operators,
);
?>