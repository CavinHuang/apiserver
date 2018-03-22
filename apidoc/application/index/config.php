<?php
return array(
	'session' => [
		'prefix'       => 'mydoc_',
		'type'         => '',
		'auto_start'   => true,
	],
    'view_replace_str' =>  [
        '__PUBLIC__'   => '/',
        '__STATIC__'   => '/static',
        '__JS__'       => '/static/index/js',
        '__CSS__'      => '/static/index/css',
        '__IMG__'      => '/static/index/images',
    ],
);
