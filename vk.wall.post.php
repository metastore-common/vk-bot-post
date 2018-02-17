<?php

require_once( __DIR__ . '/src/Vk_API/Vk.class.php' );
require_once( __DIR__ . '/src/MetaStore/Vk_API/WallPost.class.php' );
require_once( __DIR__ . '/settings/settings.php' );

// Loading class.
$WallPost = new \METASTORE\Vk_API\WallPost();

// CMD options.
$cmdOpt = getopt( 'p:' );

if ( isset( $cmdOpt['p'] ) ) {
	switch ( $cmdOpt['p'] ) {
		case 'group':
			$WallPost->groupWallPost();
			break;
		case 'user':
			$WallPost->userWallPost();
			break;
		default:
			return false;
	}
} else {
	return false;
}
