<?php

/**
 * API settings.
 *
 * @return mixed
 * ------------------------------------------------------------------------------------------------------------------ */

function apiConfig() {
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
 * @return mixed
 * ------------------------------------------------------------------------------------------------------------------ */

function extSettings() {
	// Get API settings.
	$cfg = apiConfig();

	$setting = [
		// Group settings.
		'group' => [
			'owner_id'   => '',
			'from_group' => '1',
			'signed'     => '0',
			'message'    => 'group api demo',
			'tags'       => '#vk_api_group',
			'link'       => '',
			'dir'        => '/storage/group/images/',
		],
		// User settings.
		'user'  => [
			'owner_id' => $cfg['user_id'],
			'message'  => 'user api demo',
			'tags'     => '#vk_api_user',
			'link'     => '',
			'dir'      => '/storage/user/images/',
		],
	];

	return $setting;
}
