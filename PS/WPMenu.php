<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
/**
 * WP menu
 * */
class PS_WPMenu {
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
	public static function get_instance()
  {

		/*
		 * - Uncomment following lines if the admin class should only be available for super admins
		 */
		/* if( ! is_super_admin() ) {
			return;
		} */

		// If the single instance hasn't been set, set it now.
		if (null == self::$instance)
    {
			self::$instance = new self;
		}

		return self::$instance;
	}

	public function __construct()
  {
    add_action( 'admin_menu', [$this, 'init'] );
  }

  public function init()
  {
    add_menu_page(
        __('Product Scheduler'),
        'Product Scheduler',
        'delete_posts',
        'product-scheduler',
        //[WA_Dashboard_Controller::get_instance() , 'controller'],
        '',
        'dashicons-welcome-widgets-menus',
        6
    );

    // add_submenu_page(
    //     'app-dashboard',
    //     __( 'Warehouse Dashboard'),
    //     __( 'Warehouse'),
    //     'delete_posts',
    //     'warehouse',
    //     [WA_Warehouse_Controller::get_instance() , 'controller']
    // );

  }

}//
