<?php

/**
 * Knows what attributes tags are supposed to have, has the ability to generate missing ones (also see bin/php/tagGenerator.php)
 */
class eztagMetadataManager extends ezjscServerFunctions {
	private $attributes = array();
	private $pathsToAttributes = array(); // key = tagId, value is an array of attribute names

	function __construct() {
		$this->ini = eZINI::instance("bfeztag_metadata.ini");
	}

	function readExpectedAttributes() {
		$groups = $this->ini->groups();
		foreach ($groups as $section => $groupHash) {
			if (substr($section, 0, 13) == "TagAttribute_") {
				$attributeName = substr($section, 13);
				if (array_key_exists("Label", $groupHash)) {
					$attributeLabel = $groupHash["Label"];
				} else {
					$attributeLabel = $attributeName;
				}
				$this->attributes[$attributeName] = $attributeLabel;

				if (array_key_exists("ExistsUnderPath", $groupHash)) {
					$aPath = $groupHash["ExistsUnderPath"];
					foreach ($aPath as $iTagId) {
						if (!array_key_exists($iTagId, $this->pathsToAttributes)) {
							$this->pathsToAttributes[$iTagId] = array();
						}
						if (!in_array($attributeName, $this->pathsToAttributes[$iTagId])) {
							array_push($this->pathsToAttributes[$iTagId], $attributeName);
						}
					}
				}
			}
		}
	}

	/**
	 * [getExpectedAttributesForTagPath description]
	 * @param  eZTagsObject $oTag (you get this by taking a tag object, running ->attribute("path_string"))
	 * @return [type]          [description]
	 */
	function getExpectedAttributesForTagPath($oTag) {
		$this->readExpectedAttributes();

		if (!($oTag instanceof eZTagsObject)) {
			return(array());
		}

		// do the simple thing first, run path
		$sTagPath = $oTag->attribute("path_string");
		$tagParts = explode("/", $sTagPath);
		$aExpectedAttributes = array();

		foreach ($this->pathsToAttributes as $tagId => $aAttributeNames) {
			if (in_array($tagId, $tagParts)) {
				foreach ($aAttributeNames as $attributeName) {
					$aExpectedAttributes[$attributeName] = $this->attributes[$attributeName]; // get the label	
				}
			}
		}

		// TODO: then invoke the external handlers to find other attributes
		return($aExpectedAttributes);
	}

	function autoGenerateMissingTags() {
		$this->readExpectedAttributes(); // get all those first

		// work with the attribute list, create all
		foreach ($this->attributes as $attributeName => $junk) {
			eztagMetadataDBManager::createNewAttribute($attributeName);
		}
	}

	public static function writeAttribute( $args ) {
		$requiredArgs = array("tagId", "attributeName", "attributeValue");
		foreach ($requiredArgs as $requiredArg) {
			if (!array_key_exists($requiredArg, $_POST)) {
	           throw new InvalidArgumentException( "One of the required arguments missing." );
			}
		}

		$tagId = intval($_POST["tagId"]);
		$attributeName = $_POST["attributeName"];
		$attributeValue = $_POST["attributeValue"];

		$result = eztagMetadataDBManager::insertAttributeValue($tagId, $attributeName, $attributeValue);
		if ($result) {
			return("OK");
		} else {
           throw new Exception( "Problem with writing data." );
        }
	}


}

?>