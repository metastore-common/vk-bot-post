<?php

/**
 * API settings.
 *
 * @return mixed
 * ------------------------------------------------------------------------------------------------------------------ */

function apiConfig() {
	$config['secret_key']   = '';
	$config['client_id']    = '';
	$config['user_id']      = '';
	$config['access_token'] = '';
	$config['scope']        = 'wall,photos,friends,groups';

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

	// Group settings.
	$setting['group']['owner_id']   = '';
	$setting['group']['from_group'] = '1';
	$setting['group']['signed']     = '0';
	$setting['group']['message']    = 'group api demo';
	$setting['group']['tags']       = '#vk_api_group';
	$setting['group']['link']       = '';

	// User settings.
	$setting['user']['owner_id'] = $cfg['user_id'];
	$setting['user']['message']  = 'user api demo';
	$setting['user']['tags']     = '#vk_api_user';
	$setting['user']['link']     = '';

	return $setting;
}
