<?php

namespace METASTORE\VkBotPost;

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
	 * @return array|bool
	 * -------------------------------------------------------------------------------------------------------------- */

	public function groupWallPost() {
		$dir = dirname( __FILE__ ) . $this->setting['group']['dir'];

		if ( self::isDirEmpty( $dir ) ) {
			return false;
		} else {
			$file = scandir( $dir )[2];
		}

		$attach = $this->vk->upload_photo( 0, array( $dir . $file ) );

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
	 * @return array|bool
	 * -------------------------------------------------------------------------------------------------------------- */

	public function userWallPost() {
		$dir = dirname( __FILE__ ) . $this->setting['user']['dir'];

		if ( self::isDirEmpty( $dir ) ) {
			return false;
		} else {
			$file = scandir( $dir )[2];
		}

		$attach = $this->vk->upload_photo( 0, array( $dir . $file ) );

		$response = $this->vk->api( 'wall.post', array(
			'owner_id'    => $this->setting['user']['owner_id'],
			'message'     => $this->setting['user']['tags'] . PHP_EOL . $this->setting['user']['message'],
			'attachments' => implode( ',', $attach ) . ',' . $this->setting['user']['link'],
		) );

		unlink( $dir . $file );

		return $response;
	}
}
