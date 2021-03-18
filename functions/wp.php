<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

function psCheckCurrentScreen($screenId = null)
{
  return PS_WPHelper::get_instance()->checkCurrentScreen($screenId);
}
