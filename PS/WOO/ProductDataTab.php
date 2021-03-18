<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

use Carbon\Carbon;

/**
 * WP menu
 * */
class PS_WOO_ProductDataTab {
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
		add_filter('woocommerce_product_data_tabs', [$this, 'productTab']);
		add_action( 'woocommerce_product_data_panels', [$this, 'productTabContent'] );
		add_action( 'woocommerce_process_product_meta', [$this, 'productSaveSchedule'], 10, 2 );
	}

	public function productSaveSchedule($id, $post)
	{
		$ps_select_day = $_POST['ps_select_day'];
		PS_SchedulePostMeta::get_instance()->ps_select_day([
			'post_id' => $id,
			'action' => 'u',
			'value' => $ps_select_day
		]);

		$ps_enable_schedule = isset($_POST['ps_enable_schedule']) ? 'yes' : 'no';
		PS_SchedulePostMeta::get_instance()->ps_enable_schedule([
			'post_id' => $id,
			'action' => 'u',
			'value' => $ps_enable_schedule
		]);

		$ps_available_as_date_range = isset($_POST['ps_available_as_date_range']) ? 'yes' : 'no';
		PS_SchedulePostMeta::get_instance()->ps_available_as_date_range([
			'post_id' => $id,
			'action' => 'u',
			'value' => $ps_available_as_date_range
		]);

		$ps_select_date_range_start_month = $_POST['ps_select_date_range_start_month'];
		PS_SchedulePostMeta::get_instance()->ps_select_date_range_start_month([
			'post_id' => $id,
			'action' => 'u',
			'value' => $ps_select_date_range_start_month
		]);

		$ps_select_date_range_start_day = $_POST['ps_select_date_range_start_day'];
		PS_SchedulePostMeta::get_instance()->ps_select_date_range_start_day([
			'post_id' => $id,
			'action' => 'u',
			'value' => $ps_select_date_range_start_day
		]);

		$ps_select_date_range_end_month = $_POST['ps_select_date_range_end_month'];
		PS_SchedulePostMeta::get_instance()->ps_select_date_range_end_month([
			'post_id' => $id,
			'action' => 'u',
			'value' => $ps_select_date_range_end_month
		]);

		$ps_select_date_range_end_day = $_POST['ps_select_date_range_end_day'];
		PS_SchedulePostMeta::get_instance()->ps_select_date_range_end_day([
			'post_id' => $id,
			'action' => 'u',
			'value' => $ps_select_date_range_end_day
		]);

		$ps_select_start_time_available = $_POST['ps_select_start_time_available'];
		PS_SchedulePostMeta::get_instance()->ps_select_start_time_available([
			'post_id' => $id,
			'action' => 'u',
			'value' => $ps_select_start_time_available
		]);

		$ps_select_end_time_available = $_POST['ps_select_end_time_available'];
		PS_SchedulePostMeta::get_instance()->ps_select_end_time_available([
			'post_id' => $id,
			'action' => 'u',
			'value' => $ps_select_end_time_available
		]);

		$ps_interval_time = $_POST['ps_interval_time'];
		PS_SchedulePostMeta::get_instance()->ps_interval_time([
			'post_id' => $id,
			'action' => 'u',
			'value' => $ps_interval_time
		]);

	}

	public function productTab($tabs)
	{
		// $checkScreen = psCheckCurrentScreen('product');
		//
		// if ( ! $checkScreen ) {
		//   return false;
		// }

		$tabs['schedule'] = array(
			'label' => __( 'Schedule', 'woocommerce' ),
			'target' => 'schedule_product_data',
			'class' => array( 'xshow_if_simple', 'xshow_if_variable'  ),
			'priority' => 21,
		);

		return $tabs;
	}

	public function productTabContent()
	{
		echo '<div id="schedule_product_data" class="panel woocommerce_options_panel hidden">';

			echo '<div class="options_group">';
				woocommerce_wp_checkbox( array(
					'id'                => 'ps_enable_schedule',
					'value'             => get_post_meta( get_the_ID(), 'ps_enable_schedule', true ),
					'label'             => 'Enable Schedule?',
					'description'       => 'Is this product available on a date time only?'
				) );
			echo '</div>';

			echo '<div class="options_group">';
				woocommerce_wp_select( array(
					'id'   => 'ps_select_day',
					'name'  => 'ps_select_day[]',
					'value' => get_post_meta( get_the_ID(), 'ps_select_day', true ) ? get_post_meta( get_the_ID(), 'ps_select_day', true ) : 'all',
					'label' => 'Select Day',
					'options' => $this->getCalendarDayName(),
					'custom_attributes' => ['multiple' => 'multiple'],
					'desc_tip' => 'Select Day, default is All Day.'
				) );
			echo '</div>';

			echo '<div class="options_group">';
				// woocommerce_wp_checkbox( array(
				// 	'id'                => 'ps_available_as_date_range',
				// 	'value'             => get_post_meta( get_the_ID(), 'ps_available_as_date_range', true ),
				// 	'label'             => 'Date range.',
				// 	'description'       => 'Optional, Available as date range.'
				// ) );

				echo '<div class="show_date_range">';
					woocommerce_wp_select( array(
						'id'          => 'ps_select_date_range_start_month',
						'value'       => get_post_meta( get_the_ID(), 'ps_select_date_range_start_month', true ),
						'label'       => 'Select start month.',
						'options' => array_merge_recursive(['Select'], $this->getCalendarMonth()),
					) );

					woocommerce_wp_select( array(
						'id'          => 'ps_select_date_range_start_day',
						'value'       => get_post_meta( get_the_ID(), 'ps_select_date_range_start_day', true ),
						'label'       => 'Select start day.',
						'options' => array_merge_recursive(['Select'], $this->getCalenderDay()),
					) );

					woocommerce_wp_select( array(
						'id'          => 'ps_select_date_range_end_month',
						'value'       => get_post_meta( get_the_ID(), 'ps_select_date_range_end_month', true ),
						'label'       => 'Select end month.',
						'options' => array_merge_recursive(['Select'], $this->getCalendarMonth()),
					) );

					woocommerce_wp_select( array(
						'id'          => 'ps_select_date_range_end_day',
						'value'       => get_post_meta( get_the_ID(), 'ps_select_date_range_end_day', true ),
						'label'       => 'Select end day.',
						'options' => array_merge_recursive(['Select'], $this->getCalenderDay()),
					) );
				echo '</div>';

			echo '</div>';

			echo '<div class="options_group">';
				woocommerce_wp_select( array(
					'id'          => 'ps_select_start_time_available',
					'value'       => get_post_meta( get_the_ID(), 'ps_select_start_time_available', true ),
					'label'       => 'Select start time available',
					'options' => array_merge_recursive(['select'], $this->getHoursRange()),
					'desc_tip' => 'Select start time.'
				) );
				woocommerce_wp_select( array(
					'id'          => 'ps_select_end_time_available',
					'value'       => get_post_meta( get_the_ID(), 'ps_select_end_time_available', true ),
					'label'       => 'Select end time available',
					'options' => array_merge_recursive(['select'], $this->getHoursRange()),
					'desc_tip' => 'Select end time.'
				) );
			echo '</div>';

			echo '<div class="options_group">';
				echo '<p>Available Time interval </p>';
				echo '<p>input, manually type hour or minute format. </p>';
				echo '<p>example: 1:00 for hour, or 0:30 for minute. </p>';
				woocommerce_wp_text_input( array(
					'id'          => 'ps_interval_time',
					'value'       => get_post_meta( get_the_ID(), 'ps_interval_time', true ),
					'label'       => 'Interval time.',
				) );
			echo '</div>';

		echo '</div>';
	}

	protected function getCalenderDay()
	{
		return PS_SelectDateRange::get_instance()->getSelectDate();
	}

	protected function getCalendarMonth()
	{
		return PS_SelectDateRange::get_instance()->getSelectMonth();
	}

	protected function getCalendarDayName()
	{
		return PS_SelectDay::get_instance()->getDay();
	}

	protected function getHoursRange( $start = 0, $end = 86400, $step = 3600, $format = 'g:i a' ) {
		return PS_SelectTimeRange::get_instance()->getHoursRange($start, $end, $step, $format);
	}
}//
