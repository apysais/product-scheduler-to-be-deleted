<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
use Carbon_Fields\Container;
use Carbon_Fields\Field;
/**
 *
 * @since 0.0.1
 * */
class PS_Options {
	/**
	* instance of this class
	*
	* @since 0.0.1
	* @access protected
	* @var	null
	* */
	protected static $instance = null;

	/**
	* Return an instance of this class.
	*
	* @since     0.0.1
	*
	* @return    object    A single instance of this class.
	*/
	public static function get_instance() {
		/*
		 * - Uncomment following lines if the admin class should only be available for super admins
		 */
		/* if( ! is_super_admin() ) {
			return;
		} */

		// If the single instance hasn't been set, set it now.
		if ( null == self::$instance ) {
			self::$instance = new self;
		}

		return self::$instance;
	}

	public function __construct() {

	}

	public function init()
	{
		Container::make( 'theme_options', __( 'Store Options' ) )
		->set_page_parent( 'product-scheduler' )
		->add_fields(
			array(
				Field::make( 'time', 'crb_ps_pickup_start_time', __( 'Store start pickup' ) ),
				Field::make( 'time', 'crb_ps_pickup_close_time', __( 'Store least pickup' ) ),
				Field::make( 'text', 'crb_ps_global_time_interval', __( 'Global Time Interval Pickup' ) )
		  		->set_attribute( 'placeholder', 'Set Interval time pickup ' )
		  		->set_default_value('0:30'),
				Field::make( 'text', 'crb_ps_global_time_prepare_time', __( 'Global Time Interval Prepare' ) )
		  		->set_attribute( 'placeholder', 'Set Global time preparetions ' )
		  		->set_default_value('1:00'),
			)
		);
	}

	public function getStartPickupTime()
	{
		return carbon_get_theme_option('crb_ps_pickup_start_time');
	}

	public function getEndPickupTime()
	{
		return carbon_get_theme_option('crb_ps_pickup_close_time');
	}

	public function getGlobalTimeInterval()
	{
		return carbon_get_theme_option('crb_ps_global_time_interval');
	}

	public function getGlobalPrepareTimeInterval()
	{
		return carbon_get_theme_option('crb_ps_global_time_prepare_time');
	}

}//
