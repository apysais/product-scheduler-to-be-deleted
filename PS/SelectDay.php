<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

use Carbon\Carbon;

/**
 * WP menu
 * */
class PS_SelectDay {
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

	}

	public function isAllDay($postId)
	{
		$availableDays = $this->get($postId);
		return in_array('all', $availableDays) ? true : false;
	}

	public function isAvailableToday(array $args = null)
	{
		$post_id = $args['post_id'];
		$availableDays = $this->get($post_id);

		$today = strtolower(Carbon::now()->isoFormat('dddd'));

		if ( $this->isAllDay($post_id) ) {
			return true;
		}

		if ( in_array($today, $availableDays) ) {
			return true;
		}

		return false;
	}

	public function get($post_id = null)
	{
		return PS_SchedulePostMeta::get_instance()->ps_select_day([
			'post_id' => $post_id,
			'action' => 'r',
			'single' => true
		]);
	}

	public function getAvailableDay(array $args = null)
	{
		$post_id = $args['post_id'];
		$getDay = $this->getDay();

		$getDaySelected = $this->get($post_id);
		if(!$getDaySelected) {
			$getDaySelected = 'all';
		}

		$arrAvailableDay = null;

		if ( $this->isAllDay($post_id) ) {
			unset($getDay['all']);
			return $this->convertDayToNumericKey(array_keys($getDay));
		}
		return $this->convertDayToNumericKey($getDaySelected);
	}

	public function getAvailableSelecDay(array $args = null)
	{
		$dt = Carbon::now();
		$currentTodayDay = $dt->format('N');
		$post_id = $args['post_id'];
		$getAvailableDay = $this->getAvailableDay(['post_id' => $post_id]);
		$getAvailableSelectDay = [];

		foreach($getAvailableDay as $k => $v) {
			if($currentTodayDay <= $k) {
				$getAvailableSelectDay[] = $v;
			}
		}
		return $getAvailableSelectDay;
	}

	public function convertDayToNumericKey(array $day = null)
	{
		$newDay = [];
		$dayCount = 1;
		$arrDayDBcount = 0;

		foreach($day as $k => $v) {
			if( $v == 'monday' ) {
				$newDay[1] = 'monday';
			}
			if( $v == 'tuesday' ) {
				$newDay[2] = 'tuesday';
			}
			if( $v == 'wednesday' ) {
				$newDay[3] = 'wednesday';
			}
			if( $v == 'thursday' ) {
				$newDay[4] = 'thursday';
			}
			if( $v == 'friday' ) {
				$newDay[5] = 'friday';
			}
			if( $v == 'saturday' ) {
				$newDay[6] = 'saturday';
			}
			if( $v == 'sunday' ) {
				$newDay[7] = 'sunday';
			}
		}

		return $newDay;
	}

	public function convertDayToJqueryDatePicker(array $day = null)
	{
		$newDay = [];
		$dayCount = 1;
		$arrDayDBcount = 0;

		foreach($day as $k => $v) {
			if( $v == 'monday' ) {
				$newDay[1] = 'monday';
			}
			if( $v == 'tuesday' ) {
				$newDay[2] = 'tuesday';
			}
			if( $v == 'wednesday' ) {
				$newDay[3] = 'wednesday';
			}
			if( $v == 'thursday' ) {
				$newDay[4] = 'thursday';
			}
			if( $v == 'friday' ) {
				$newDay[5] = 'friday';
			}
			if( $v == 'saturday' ) {
				$newDay[6] = 'saturday';
			}
			if( $v == 'sunday' ) {
				$newDay[0] = 'sunday';
			}
		}

		return $newDay;
	}

	public function getDay()
	{
		return [
			'all' => 'All Day',
			'monday' => 'Monday',
			'tuesday' => 'Tuesday',
			'wednesday' => 'Wednesday',
			'thursday' => 'Thursday',
			'friday' => 'Friday',
			'saturday' => 'Saturday',
			'sunday' => 'Sunday'
		];
	}
}//
