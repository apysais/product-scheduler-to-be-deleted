<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
/**
 * Orders Controller
 * */
class PS_Calendar_Controller extends PS_Base {
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

	public function product_scheduler() {
		$data = [];

		PS_View::get_instance()->admin_partials( 'calendar/index.php', $data );
	}

	public function test()
	{
		echo 'here';
		//start: 2021-02-28T00:00:00+08:00
		//end: 2021-04-11T00:00:00+08:00
		$start = '2021-02-28T00:00:00+08:00';
		$end = '2021-04-11T00:00:00+08:00';

		$date_start = date_create($start);
		$date_start_format = date_format( $date_start, "Y-m-d");

		$date_end = date_create($end);
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
					$time = $item->get_meta( 'ps_time_select', true );

					if ( $date != '' ) {
						$allDay = false;
						if( $item->get_meta( 'ps_time_select', true ) == '' ) {
							$time = '';
							$allDay = true;
						}
						$output_arrays[] = [
							'title' => 'Order # ' . $order->get_id(),
							'descripton' => $item->get_name(),
							'start' => date("Y-m-d\TH:i", strtotime($date.' '.$time)),
							'url' => '',
							'classNames' => [],
							'extendedProps' => ['aaa' => 'aa', 'bbb' => 'bb'],
							'allDay' => $allDay
						];
					}
				}
			}
			ps_dd($output_arrays);
		}
	}

	/**
	 * Controller
	 *
	 * @param	$action		string | empty
	 * @parem	$arg		array
	 * 						optional, pass data for controller
	 * @return mix
	 * */
	public function controller($action = '', $arg = array()){
		$this->call_method($this, $action);
	}

	public function __construct(){}

}
