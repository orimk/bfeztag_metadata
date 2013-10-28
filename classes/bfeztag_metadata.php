<?php

class bfeztag_metadata extends bfCustomExtension {
	// Extension definitions, please see the bottom of bfCustomExtension/bfCustomExtension.php_noauto for details
	static $operatorDetailType = "array";
	static $operatorDetail = array(
		"tag_get_expected_attributes" => array(
			"oTag" => array("any", true),
		), // which attributes is a tag supposed to have?
		"tag_get_extra_attributes" => array(
			"tagId" => array("integer", true),
		), // which attributes does this particular tag have?
		"tag_get_attribute_value" => array(
			"tagId" => array("integer", true),
			"attrName" => array("string", true),
		), // which attributes does this particular tag have?
	);

	// If you want to have offload some template operators to other classes, here's where you do it (see bfCustomExtension for detail)
	static $operatorLocation = array();

	function __construct() {
		parent::__construct();
	}

	function tag_get_expected_attributes($operatorParameters, $namedParameters, $pipedParam=null) {
		$oTag = $namedParameters["oTag"]; // tag object
		$tagDataManager = new eztagMetadataManager();
		$aAttributes = $tagDataManager->getExpectedAttributesForTagPath($oTag);
		return($aAttributes);
	}

	function tag_get_extra_attributes($operatorParameters, $namedParameters, $pipedParam=null) {
		$tagId = intval($namedParameters["tagId"]);
		return(eztagMetadataDBManager::getAttributesForTag($tagId));
	}

	function tag_get_attribute_value($operatorParameters, $namedParameters, $pipedParam=null) {
		$tagId = intval($namedParameters["tagId"]);
		$attrName = $namedParameters["attrName"];
		return(eztagMetadataDBManager::getAttributeValueForTag($tagId,$attrName));
	}


//	public function sample_operator($operatorParameters, $namedParameters, $pipedParam=null) {
//
//	}

}

?>
