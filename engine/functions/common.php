<?php

/**
 * Check empty directory.
 *
 * @param $dir
 *
 * @return bool|null
 * ------------------------------------------------------------------------------------------------------------------ */

function isDirEmpty( $dir ) {
	$iterator   = new FilesystemIterator( $dir );
	$isDirEmpty = ! $iterator->valid();

	return $isDirEmpty;
}