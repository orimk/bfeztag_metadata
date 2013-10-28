CREATE TABLE `bfeztags_metadata_attribute` (
  `attributeID` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(45) NOT NULL,
  PRIMARY KEY (`attributeID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


CREATE TABLE `bfeztags_metadata_values` (
  `metadataValueID` int(11) NOT NULL AUTO_INCREMENT,
  `attributeID` int(11) NOT NULL,
  `tagID` int(11) NOT NULL,
  `value` varchar(200) DEFAULT NULL,
  PRIMARY KEY (`metadataValueID`),
  KEY `FK_eztagattributeID_idx` (`attributeID`),
  KEY `FK_eztagtagID_idx` (`tagID`),
  CONSTRAINT `FK_eztagattributeID` FOREIGN KEY (`attributeID`) REFERENCES `bfeztags_metadata_attribute` (`attributeID`) ON DELETE CASCADE ON UPDATE NO ACTION,
  CONSTRAINT `FK_eztagtagID` FOREIGN KEY (`tagID`) REFERENCES `eztags` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
