<?php

/**
 * Low Likes Add-On Setup file
 *
 * @package        low_likes
 * @author         Lodewijk Schutte <hi@gotolow.com>
 * @link           http://gotolow.com/
 */

return array(
	'author'         => 'Low',
	'author_url'     => 'http://gotolow.com/',
	'name'           => 'Low Likes',
	'description'    => 'Like enties or members',
	'version'        => '2.0.0',
	'namespace'      => 'Low\Likes',
	'settings_exist' => FALSE,
	'models' => [
		'Like' => 'Model\Like'
	],
	'models.dependencies' => [
		'Like' => ['ee:ChannelEntry', 'ee:Member']
	]
);