<?php
if ( ! defined( 'ABSPATH' ) ) {
exit; // Exit if accessed directly
}
/**
* Ajax
* @since 0.0.1
* */
class PS_Calendar_Ajax {
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
		if ( null == self::$instance ) {
		self::$instance = new self;
		}

		return self::$instance;
	}

	public function __construct()
	{
		add_action( 'wp_ajax_ps_fullcalendar_events', [$this, 'getEvents'] );
	}

	public function getEvents()
	{
		$output_arrays = [];

		if ( isset($_POST['start']) || isset($_POST['end']) ) {
			$date_start = date_create($_POST['start']);
			$date_start_format = date_format( $date_start, "Y-m-d");

			$date_end = date_create($_POST['end']);
			$date_end_format = date_format( $date_end, "Y-m-d");

			$args_orders = [
				'limit' => -1,
				'date_created' => $date_start_format .'...'. $date_end_format,
				'status' => array('wc-processing', 'wc-on-hold')
			];
			$orders = wc_get_orders($args_orders);

			if ( $orders ) {
				$output_arrays = [];
				foreach( $orders as $order ) {
					foreach ( $order->get_items() as $item_id => $item ) {
						$date = $item->get_meta( 'ps_date_range_select', true );
						$time_prepare = $item->get_meta( 'ps_time_select', true );
						$time_pickup = $item->get_meta( 'ps_time_pickup_select', true );
						if ( $date != '' ) {
							$allDay = false;
							if( $item->get_meta( 'ps_time_pickup_select', true ) == '' ) {
								$time_pickup = '';
								$allDay = true;
							}
							$output_arrays[] = [
								'title' => 'Order # ' . $order->get_id(),
								'descripton' => $item->get_name(),
								'start' => date("Y-m-d\TH:i", strtotime($date.' '.$time_pickup)),
								'url' => '',
								'classNames' => [],
								// 'extendedProps' => ['aaa' => 'aa', 'bbb' => 'bb'],
								'allDay' => $allDay
							];
						}
					}
				}
			}
		}

		echo json_encode($output_arrays);
		wp_die();
	}
}//
