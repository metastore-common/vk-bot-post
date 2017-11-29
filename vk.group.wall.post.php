<?php

require_once( 'engine/api/vk.php' );
require_once( 'settings/settings.php' );
require_once( 'engine/functions/common.php' );

/**
 * Posting to group wall.
 *
 * @return array|bool
 * ------------------------------------------------------------------------------------------------------------------ */

function vkGroupWallPost() {
	$vk  = new Vk( apiConfig() );
	$set = extSettings();
	$dir = 'storage/group/images/';

	if ( isDirEmpty( $dir ) ) {
		return false;
	} else {
		$file = scandir( $dir )[2];
	}

	$attach = $vk->upload_photo( 0, array( $dir . $file ) );

	$response = $vk->api( 'wall.post', array(
		'owner_id'    => $set['group']['owner_id'],
		'from_group'  => $set['group']['from_group'],
		'signed'      => $set['group']['signed'],
		'message'     => $set['group']['tags'] . PHP_EOL . $set['group']['message'],
		'attachments' => implode( ',', $attach ) . ',' . $set['group']['link'],
	) );

	unlink( $dir . $file );

	return $response;
}

vkGroupWallPost();
