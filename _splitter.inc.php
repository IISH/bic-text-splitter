<?php
class Splitter {
	var $filename = '';
	var $source_dir = '';
	var $target_dir = '';
	var $filenumber_length = 0;
	var $textConversion = array();

	function __construct($filename, $source_dir, $target_dir, $filenumber_length = 4, $textConversion = null) {
		$this->filename = $filename;
		$this->source_dir = $source_dir;
		$this->target_dir = $target_dir;
		$this->filenumber_length = $filenumber_length;
		if ( is_array($textConversion) ) {
			$this->textConversion = $textConversion;
		}
	}

	// split file
	public function split() {
		echo "\nSplitting file: " . $this->getSourceFileName() . "\n";

		// get list of 'paragraphs' (each paragraph stops at a <br> tag)
		$arr = $this->createParagraphs();

		$fileNumber = '';
		$fileText = '';

		// loop through each paragraph
		foreach ( $arr as $paragraph ) {
			// try to find a line starting with a pipe and after that only a number
			$pattern = '/^\|([0-9]+)$/';
			preg_match($pattern, $paragraph, $matches);

			if ( count($matches) > 1 ) {
				// number found, remember file number
				$fileNumber = $matches[1];
			} else {
				// number not found, normal row
				// if file number is set, start saving the text
				if ( $fileNumber != '' ) {

					// check if end of paragaph
					$pattern2 = '/(#)$/';
					preg_match($pattern2, $paragraph, $matches2);
					if ( count($matches2) > 1 ) {
						// end of paragraph

						// save text
						$fileText .= $this->convertText(substr($paragraph, 0, strlen($paragraph)-1)) . PHP_EOL;

						// save file
						$targetFileName = $this->calculateTargetFileName($fileNumber);
						$this->saveFile( $targetFileName, $fileText );

						// reset number/text
						$fileNumber = '';
						$fileText = '';
					} else {
						// not end of paragraph
						$fileText .= $this->convertText($paragraph) . PHP_EOL;
					}

				}
			}
		}

		// save 'left over' to file (last page not closed with an #)
		if ( $fileNumber != '' ) {
			$targetFileName = $this->calculateTargetFileName($fileNumber);
			$this->saveFile( $targetFileName, $fileText );
		}
	}

	// a paragraph is in 'this' example text closed with a <br> tag at the end of the line
	private function createParagraphs() {
		$arr = array();

		$content = file_get_contents ( $this->source_dir . DIRECTORY_SEPARATOR . $this->filename );

		// remove spaces after the break
		while ( stripos($content, '<br> ') !== false ) {
			$content = str_replace('<br> ', '<br>', $content);
		}

		// create array
		$arrContent = explode("\n", $content);

		$p = '';
		foreach ( $arrContent as $paragraph ) {
			// strip carriage return and next line feed
			$paragraph = str_replace(array("\r", "\n"), '', $paragraph);

			//
			$p .= $paragraph;

			// if 'last' character is the break tag, then we have a complete paragraph, save the paragraph in the array
			if ( strtolower(substr($p, -4)) == '<br>' ) {
				$arr[] = substr( $p, 0, strlen($p)-4 );
				$p = '';
			}
		}

		// save 'leftover' to array
		$arr[] = $p;

		return $arr;
	}

	private function saveFile( $filename, $content ) {
		// create directory
		$path_parts = pathinfo($filename);
		if ( !file_exists( $path_parts['dirname'] ) ) {
			echo "Directory created: " . $path_parts['dirname'] . "\n";
			mkdir( $path_parts['dirname'], null, true);
		}

		// save file
		file_put_contents($filename, $content);

		//
		echo "File saved: " . $filename . "\n";
	}

	// calculate target file name
	private function calculateTargetFileName( $number ) {
		$path_parts = pathinfo($this->filename);

		// create target filename
		$ret = $this->target_dir . $path_parts['dirname'] . DIRECTORY_SEPARATOR . $path_parts['filename'] . '.';
		$ret .= substr(str_repeat("0", $this->filenumber_length) . $number, -$this->filenumber_length);
		$ret .= '.' . $path_parts['extension'];

		// remove double directory separator
		$ret = str_replace(DIRECTORY_SEPARATOR . DIRECTORY_SEPARATOR, DIRECTORY_SEPARATOR, $ret);

		return $ret;
	}

	// convert text
	private function convertText( $text ) {
		if ( isset($this->textConversion['replaceNbspWithSpaces']) && $this->textConversion['replaceNbspWithSpaces'] ) {
			$text = str_ireplace("&nbsp;", " ", $text);
		}

		if ( isset($this->textConversion['stripHtmlTags']) && $this->textConversion['stripHtmlTags'] ) {
			$text = strip_tags($text);
		}

		if ( isset($this->textConversion['decodeChars']) && $this->textConversion['decodeChars'] ) {
			$text = html_entity_decode($text);
		}

		return $text;
	}

	// get source file name
	private function getSourceFileName() {
		$name = $this->source_dir . $this->filename;

		// remove double directory separator
		$name = str_replace(DIRECTORY_SEPARATOR . DIRECTORY_SEPARATOR, DIRECTORY_SEPARATOR, $name);

		return $name;
	}
}
