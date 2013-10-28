<?php

/**
 * Provides all methods to easily insert/update/delete tag metadata
 */
class eztagMetadataDBManager {
	private static $tableAttributes = "bfeztags_metadata_attribute";
	private static $tableValues = "bfeztags_metadata_values";
	private static $tableTags = "eztags";

	private $db = null;

	function __construct() {
		// grab db connection from ez (context)
		$this->db = eZDb :: instance();
	}

	static function createNewAttribute($attributeName) {
		$db = eZDb :: instance();
		$attributeId = self::getAttributeId($attributeName);
		if (is_null($attributeId)) {
			$tableAttributes = self::$tableAttributes;
			$query = <<<EOQ
INSERT INTO $tableAttributes
(name)
VALUES ('$attributeName')
EOQ;
			$db->query($query);
		}
		return(true);
	}

	static function getAttributeId($attributeName) {
		$db = eZDb :: instance();
		$attributeName = $db->escapeString($attributeName);
		$tableAttributes = self::$tableAttributes;
		$query = <<<EOQ
SELECT attributeID
FROM $tableAttributes
WHERE name = '$attributeName'
EOQ;
		$rows = $db->arrayQuery($query);
		if (sizeof($rows) > 0) {
			return($rows[0]["attributeID"]);
		} else {
			return(null);
		}
	}

	static function getTagId($tagName) {
		$db = eZDb :: instance();
		$tagName = $db->escapeString($tagName);
		$tableTags = self::$tableTags;
		$query = <<<EOQ
SELECT id
FROM $tableTags
WHERE keyword = '$tagName'
EOQ;

		$rows = $db->arrayQuery($query);
		if (sizeof($rows) > 0) {
			return($rows[0]["id"]);
		} else {
			return(null);
		}
	}

	/**
	 * Inserts attribute value for a tag, if it doesn't exist
	 * @param  mixed $mTag - if string, does a tag lookup, otherwise needs to be an integer tagID
	 * @param  mixed $mAttribute - if string, does an attribute lookup, otherwise needs to be an integer attributeID
	 * @param  mixed $value - "string" under 200 chars
	 * @return true (if exists, or newly inserted), false if it doesn't exist at the end of this operation
	 *
	 * NOTE: not using ID's will result in extra database calls to look them up!
	 */
	static function insertAttributeValue($mTag, $mAttribute, $value) {
		$db = eZDb :: instance();

		$tagID = self::mTagToTagID($mTag);
		if (is_null($tagID)) { // failed: no such tag
			print "NO TAGID\n";
			return(false);
		}
		$attributeID = self::mAttributeToAttributeID($mAttribute);
		if (is_null($attributeID)) { // failed: no such attribute
			return(false);
		}

		// now check for existing combination
		$tableValues = self::$tableValues;
		$query = <<<EOQ
SELECT count(*) as attributeCount
FROM $tableValues
WHERE attributeID = $attributeID AND tagID = $tagID
EOQ;
		$rows = $db->arrayQuery($query);
		$escapedValue = $db->escapeString($value);
		if ($rows[0]["attributeCount"] > 0) { // already exists
			$query = <<<EOQ
UPDATE $tableValues
SET value = '$escapedValue'
WHERE attributeID = $attributeID
AND tagID = $tagID
EOQ;
			$db->query($query);
		} else { 
			// insert
			$query = <<<EOQ
INSERT INTO $tableValues
(attributeID, tagID, value)
VALUES ($attributeID, $tagID, '$escapedValue')
EOQ;
			$db->query($query);
		}
		return(true);
	}

	/**
	 * Inserts attribute value for a tag, if it doesn't exist
	 * @param  mixed $mTag - if string, does a tag lookup, otherwise needs to be an integer tagID
	 * @param  bool $bReturnSimple - see @return SIMPLE=TRUE or SIMPLE=FALSE
	 * @return (SIMPLE=TRUE) hash of attributes (attributeName => attributeValue) 
	 * @return (SIMPLE=FALSE) hash of attribute tuples (attributeId => array("attname" => attributeName, "attval" => attributeValue)) 
	 * NOTE: not using ID's will result in extra database calls to look them up!
	 */
	static function getAttributesForTag($mTag, $bReturnSimple = true) {
		$db = eZDb :: instance();

		$tagID = self::mTagToTagID($mTag);
		if (is_null($tagID)) { // failed: no such tag
			return(null);
		}

		$tableValues = self::$tableValues;
		$tableAttributes = self::$tableAttributes;

		$query = <<<EOQ
SELECT {$tableAttributes}.attributeID, {$tableAttributes}.name, value
FROM {$tableValues}
INNER JOIN {$tableAttributes} ON {$tableAttributes}.attributeID = {$tableValues}.attributeID
WHERE tagID = $tagID
EOQ;

		$rows = $db->arrayQuery($query);
		$aReturn = array();
		foreach ($rows as $row) {
			if ($bReturnSimple) {
				$aReturn[$row['name']] = $row['value'];
			} else {
				$aReturn[$row['attributeID']] = array(
					"attname" => $row['name'], 
					"attval" => $row['value']
				);
			}
		}
		return($aReturn);
	}

	static function getAttributeValueForTag($mTag, $sAttributeName) {
		
		$tagID = self::mTagToTagID($mTag);
		if (is_null($tagID)) { // failed: no such tag
			return(null);
		}

		$db = eZDb :: instance();
		
		$tableValues = self::$tableValues;
		$tableAttributes = self::$tableAttributes;

		$query = <<<EOQ
SELECT {$tableValues}.value
FROM {$tableValues}
INNER JOIN {$tableAttributes} ON {$tableAttributes}.attributeID = {$tableValues}.attributeID
WHERE tagID = $tagID and {$tableAttributes}.name = '{$sAttributeName}'
limit 1
EOQ;

		$rows = $db->arrayQuery($query);	
		if(count($rows)){
			return $rows[0]['value'];
		}	
		return false;
	}

	static function mTagToTagID($mTag) {
		if (is_int($mTag)) {
			$tagID = $mTag;
		} else {
			$tagID = self::getTagId($mTag);
		}
		return($tagID);
	}

	static function mAttributeToAttributeID($mAttribute) {
		if (is_int($mAttribute)) {
			$attributeID = $mAttribute;
		} else {
			$attributeID = self::getAttributeId($mAttribute);
		}
		return($attributeID);
	}

}

?>