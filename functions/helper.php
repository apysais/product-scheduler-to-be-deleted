<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

function ps_dd($arr = [], $exit = false)
{
	echo '<pre>';
	print_r($arr);
	echo '</pre>';

	if ($exit) {
		exit();
	}
}

function ps_enable_schedule(array $args = null)
{
	$post_id = $args['post_id'];
	$ret = PS_SchedulePostMeta::get_instance()->ps_enable_schedule([
		'post_id' => $post_id,
		'action' => 'r',
		'single' => true
	]);
	return ( $ret == 'yes' ) ? true : false;
}
