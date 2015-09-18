<?php
include "_settings.inc.php";
include "_files.inc.php";
include "_splitter.inc.php";

echo "\nBakunin in Context - Text splitter\n";

// get all files in source directory
$files = Files::getListOfFiles( $settings['source_dir'], $settings['recursive'] );

// loop all found file
foreach ( $files as $file ) {
	// split each file
	$oSplitter = new Splitter($file, $settings['source_dir'], $settings['target_dir'], $settings['filenumber_length'], $textConversion);
	$oSplitter->split();
}
