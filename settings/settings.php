<?php

/**
 * API settings.
 *
 * @return array
 * ------------------------------------------------------------------------------------------------------------------ */

function api_config() {
	$config = [
		'secret_key'   => '',
		'client_id'    => '',
		'user_id'      => '',
		'access_token' => '',
		'scope'        => 'wall,photos,friends,groups',
		'v'            => '5.69',
	];

	return $config;
}

/**
 * Extended settings.
 *
 * @return array
 * ------------------------------------------------------------------------------------------------------------------ */

function settings() {
	// Get API settings.
	$api = api_config();

	$setting = [
		// Group settings.
		'group' => [
			'owner_id'   => '',
			'from_group' => '1',
			'signed'     => '0',
			'message'    => 'group api demo',
			'tags'       => '#vk_api_group',
			'link'       => '',
			'dir'        => dirname( __FILE__, 2 ) . '/storage/group/images/',
		],
		// User settings.
		'user'  => [
			'owner_id' => $api['user_id'],
			'message'  => 'user api demo',
			'tags'     => '#vk_api_user',
			'link'     => '',
			'dir'      => dirname( __FILE__, 2 ) . '/storage/user/images/',
		],
	];

	return $setting;
}
