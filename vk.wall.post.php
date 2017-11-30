<?php

require_once( __DIR__ . '/engine/api/vk.php' );
require_once( __DIR__ . '/settings/settings.php' );


class VK_WallPost {

	private $vk;    // VK API.
	private $set;   // Settings.

	/**
	 * VK_WallPost constructor.
	 * -------------------------------------------------------------------------------------------------------------- */

	public function __construct() {
		$this->vk  = new Vk( apiConfig() );
		$this->set = extSettings();
	}

	/**
	 * Check empty directory.
	 *
	 * @param $dir
	 *
	 * @return bool|null
	 * -------------------------------------------------------------------------------------------------------------- */

	public function isDirEmpty( $dir ) {
		if ( ! is_readable( $dir ) ) {
			return null;
		}

		return ( count( scandir( $dir ) ) == 2 );
	}

	/**
	 * Posting to group wall.
	 *
	 * @return array|bool
	 * -------------------------------------------------------------------------------------------------------------- */

	public function vkGroupWallPost() {
		$dir = __DIR__ . $this->set['group']['dir'];

		if ( self::isDirEmpty( $dir ) ) {
			return false;
		} else {
			$file = scandir( $dir )[2];
		}

		$attach = $this->vk->upload_photo( 0, array( $dir . $file ) );

		$response = $this->vk->api( 'wall.post', array(
			'owner_id'    => $this->set['group']['owner_id'],
			'from_group'  => $this->set['group']['from_group'],
			'signed'      => $this->set['group']['signed'],
			'message'     => $this->set['group']['tags'] . PHP_EOL . $this->set['group']['message'],
			'attachments' => implode( ',', $attach ) . ',' . $this->set['group']['link'],
		) );

		unlink( $dir . $file );

		return $response;
	}

	/**
	 * Posting to user wall.
	 *
	 * @return array|bool
	 * -------------------------------------------------------------------------------------------------------------- */

	public function vkUserWallPost() {
		$dir = __DIR__ . $this->set['user']['dir'];

		if ( self::isDirEmpty( $dir ) ) {
			return false;
		} else {
			$file = scandir( $dir )[2];
		}

		$attach = $this->vk->upload_photo( 0, array( $dir . $file ) );

		$response = $this->vk->api( 'wall.post', array(
			'owner_id'    => $this->set['user']['owner_id'],
			'message'     => $this->set['user']['tags'] . PHP_EOL . $this->set['user']['message'],
			'attachments' => implode( ',', $attach ) . ',' . $this->set['user']['link'],
		) );

		unlink( $dir . $file );

		return $response;
	}
}

// Loading class.
$VK_WallPost = new VK_WallPost();

// CMD options.
$cmdOpt = getopt( 'p:' );

if ( isset( $cmdOpt['p'] ) ) {
	switch ( $cmdOpt['p'] ) {
		case 'group':
			$VK_WallPost->vkGroupWallPost();
			break;
		case 'user':
			$VK_WallPost->vkUserWallPost();
			break;
		default:
			return false;
	}
} else {
	return false;
}
