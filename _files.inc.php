<?php
class Files {
	// get list of files found given directory
	public static function getListOfFiles( $directory, $recursive = false, $directory__prefix = '' ) {
		$arr = array();

		$dir = new DirectoryIterator( $directory );
		foreach ($dir as $fileinfo) {
			// check if not a dot directory
			if ( !$fileinfo->isDot() ) {
				// check if directory, if directory and recursive then create also list of files in subdirectory
				if ( $fileinfo->isDir() ) {
					if ( $recursive ) {
						$subDirFiles = Files::getListOfFiles($directory . DIRECTORY_SEPARATOR . $fileinfo->getFilename(), $recursive, DIRECTORY_SEPARATOR . $fileinfo->getFilename());
						$arr = array_merge($arr, $subDirFiles);
					}
				} else {
					// add file to list
					$arr[] = $directory__prefix . DIRECTORY_SEPARATOR . $fileinfo->getFilename();
				}
			}
		}

		return $arr;
	}
}