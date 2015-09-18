<?php
$settings = array();

// source directory
$settings['source_dir'] = 'source';

// target directory
$settings['target_dir'] = 'target';

// when searching for files, check also subdirectories?
$settings['recursive'] = true;

// filenumber format 0000, how long?
$settings['filenumber_length'] = 4;

$textConversion = array();

// should &nbsp; be replace with a normal space
$textConversion['replaceNbspWithSpaces'] = true;

// should html tags like <u> <i> be removed
$textConversion['stripHtmlTags'] = false;

// should characters like &egrave; be converted to é
$textConversion['decodeChars'] = true;
