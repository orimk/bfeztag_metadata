bfeztag_metadata eZPublish extension
==================

Purpose: adds metadata to ezTags

In particular:

1. changes the admin eztags interface so you can edit metadata
2. adds ability to define and add any number of metadata items per tag (they're all limited to 200 chars now)

Software Dependencies:
--------------------------------

Written and tested in eZPublish 4.7.
Dependencies: None.

Multilanguage considerations:
--------------------------------

None.

Multisite considerations:
--------------------------------

None.

Installation and Setup
--------------------------------

1. Copy extension to your project, activate it, regenerate autoloads.
2. Copy bfeztag_metadata.ini.append.php to your extension, fill with information about your project's metadata.
3. From the root of ez, run: php extension/bfeztag_metadata/bin/php/autogenerateAllTags.php. Note: ANY TIME YOU ADD A NEW ATTRIBUTE, YOU NEED TO RUN THIS!

Usage
--------------------------------

1. To massively preload attributes, use the eztagMetadataDBManager.
2. Do use this from the admin interface, just keep editing your tags. When you click into each tag, you'll see a new section called "Tag Meta Data". Your attributes should be in there.

TODOs
--------------------------------

1. Create a hook to have more control over which attributes show under which tags
2. Deal with different types of metadata (currently only what fits into varchar(200)
3. Say which attributes are being auto-generated during autogen process.
4. Have a way to enable a more aggresive saving in the web interface (as opposed to just when pressing the submit button)