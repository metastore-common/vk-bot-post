<?php

/**
 * Check empty directory.
 *
 * @param $dir
 *
 * @return bool|null
 * ------------------------------------------------------------------------------------------------------------------ */

function isDirEmpty( $dir ) {
	if ( ! is_readable( $dir ) ) {
		return null;
	}

	return ( count( scandir( $dir ) ) == 2 );
}
