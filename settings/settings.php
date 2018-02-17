<?php

/**
 * API settings.
 *
 * Get user access token:
 * https://oauth.vk.com/authorize?response_type=token&client_id=XXXXX&redirect_uri=https%3A%2F%2Foauth.vk.com%2Fblank.html&display=page&version=5.69&scope=offline%2Cwall%2Cphotos%2Cfriends%2Cgroups
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
			'path'       => dirname( __FILE__, 2 ) . '/storage/group/images/',
		],
		// User settings.
		'user'  => [
			'owner_id' => $api['user_id'],
			'message'  => 'user api demo',
			'tags'     => '#vk_api_user',
			'link'     => '',
			'path'     => dirname( __FILE__, 2 ) . '/storage/user/images/',
		],
	];

	return $setting;
}
