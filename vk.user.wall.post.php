<?php

require_once( 'engine/api/vk.php' );
require_once( 'settings/settings.php' );
require_once( 'engine/functions/common.php' );

/**
 * Posting to user wall.
 *
 * @return array|bool
 * ------------------------------------------------------------------------------------------------------------------ */

function vkUserWallPost() {
	$vk  = new Vk( apiConfig() );
	$set = extSettings();
	$dir = 'storage/user/images/';

	if ( isDirEmpty( $dir ) ) {
		return false;
	} else {
		$file = scandir( $dir )[2];
	}

	$attach = $vk->upload_photo( 0, array( $dir . $file ) );

	$response = $vk->api( 'wall.post', array(
		'owner_id'    => $set['user']['owner_id'],
		'message'     => $set['user']['tags'] . PHP_EOL . $set['user']['message'],
		'attachments' => implode( ',', $attach ) . ',' . $set['user']['link'],
	) );

	unlink( $dir . $file );

	return $response;
}

vkUserWallPost();
