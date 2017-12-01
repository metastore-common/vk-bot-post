<?php

require_once( __DIR__ . '/src/Vk.php' );
require_once( __DIR__ . '/src/WallPost.php' );
require_once( __DIR__ . '/settings/settings.php' );

// Loading class.
$WallPost = new \METASTORE\VkBotPost\WallPost();

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
