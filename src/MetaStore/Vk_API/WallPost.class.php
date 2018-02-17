<?php

namespace MetaStore\Vk_API;

use \Vk_API\Vk;

class WallPost {

	private $vk;
	private $setting;

	/**
	 * VK_WallPost constructor.
	 * -------------------------------------------------------------------------------------------------------------- */

	public function __construct() {
		$this->vk      = new Vk( api_config() );
		$this->setting = settings();
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

		$output = ( count( scandir( $dir ) ) == 2 );

		return $output;
	}

	/**
	 * Posting to group wall.
	 *
	 * @return bool|mixed|string
	 * @throws \Vk_API\VkException
	 * -------------------------------------------------------------------------------------------------------------- */

	public function groupWallPost() {
		$dir = $this->setting['group']['path'];

		if ( self::isDirEmpty( $dir ) ) {
			return false;
		} else {
			$file = scandir( $dir )[2];
		}

		$attach = $this->vk->uploadPhoto( 0, array( $dir . $file ) );

		$response = $this->vk->api( 'wall.post', array(
			'owner_id'    => $this->setting['group']['owner_id'],
			'from_group'  => $this->setting['group']['from_group'],
			'signed'      => $this->setting['group']['signed'],
			'message'     => $this->setting['group']['tags'] . PHP_EOL . $this->setting['group']['message'],
			'attachments' => implode( ',', $attach ) . ',' . $this->setting['group']['link'],
		) );

		unlink( $dir . $file );

		return $response;
	}

	/**
	 * Posting to user wall.
	 *
	 * @return bool|mixed|string
	 * @throws \Vk_API\VkException
	 * -------------------------------------------------------------------------------------------------------------- */

	public function userWallPost() {
		$dir = $this->setting['user']['path'];

		if ( self::isDirEmpty( $dir ) ) {
			return false;
		} else {
			$file = scandir( $dir )[2];
		}

		$attach = $this->vk->uploadPhoto( 0, array( $dir . $file ) );

		$response = $this->vk->api( 'wall.post', array(
			'owner_id'    => $this->setting['user']['owner_id'],
			'message'     => $this->setting['user']['tags'] . PHP_EOL . $this->setting['user']['message'],
			'attachments' => implode( ',', $attach ) . ',' . $this->setting['user']['link'],
		) );

		unlink( $dir . $file );

		return $response;
	}
}
