<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

use Carbon\Carbon;
use Carbon\CarbonInterval;

/**
 * PS_SelectTimeRange
 * */
class PS_SelectTimeRange {
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

	public function initAjaxGetTimeRange()
	{

		add_action( 'wp_ajax_ps_select_time_pickup_range', [$this, 'ajaxSelectTimeRangePickup'] );
		add_action( 'wp_ajax_nopriv_ps_select_time_pickup_range', [$this, 'ajaxSelectTimeRangePickup'] );
	}

	//pickup
	public function ajaxSelectTimeRangePickup()
	{
		$currentTime = Carbon::now(wp_timezone_string());
		$available_select_time = [];
		$post_id = $_POST['post_id'];

		$interval_time = $this->getIntervalTime($post_id);
		$endTime = $this->getSelectEndTime($post_id);
		$available_time_end = $this->getSelectEndTime($post_id)->format('H:i');
		$isWithinTimeRange = $this->isWithinTimeRange(['post_id' => $post_id]);

		if ( $_POST['date_selected'] > $currentTime->format('Y/m/d') ) {
			$available_time_start = $this->getSelectStartTime($post_id)->format('H:i');
		} else {
			$available_time_start = $currentTime->format('H:i');
			if ( !$isWithinTimeRange ) {
				$available_time_start = $this->getSelectStartTime($post_id)->format('H:i');
			}
		}

		$available_select_time = $this->getPickupTime([
			'post_id' => $post_id,
			'available_time_start' => $available_time_start,
			'available_time_end' => $available_time_end,
			'date_selected' => $_POST['date_selected'] ?? false
		]);

		echo wp_send_json($available_select_time);
		wp_die();
	}

    public function isWithinTimeRange(array $args = null)
    {
        $post_id = $args['post_id'];

        $currentTime = Carbon::now(wp_timezone_string());

        $startTime = $this->getSelectStartTime($post_id);
        $endTime = $this->getSelectEndTime($post_id);

        if($currentTime->between($startTime, $endTime, true)){
            return true;
        }

        return false;
    }

	public function getSelectStartTime($post_id)
	{
		$option = new PS_Options;
        $globalStartTime = $option->getStartPickupTime();

        $metaStartTime = $this->getStartTime($post_id);
        if(!$metaStartTime) {
            $metaStartTime = Carbon::createFromFormat('H:i:s', $globalStartTime, wp_timezone_string())->format('H:i');
        }

		return Carbon::createFromFormat('H:i', $metaStartTime, wp_timezone_string());
	}

	public function getSelectEndTime($post_id)
	{
		$option = new PS_Options;
        $globalEndTime = $option->getEndPickupTime();

        $metaEndTime = $this->getEndTime($post_id);
        if(!$metaEndTime) {
            $metaEndTime = Carbon::createFromFormat('H:i:s', $globalEndTime, wp_timezone_string())->format('H:i');
        }

        return Carbon::createFromFormat('H:i', $metaEndTime, wp_timezone_string());
	}

	public function getIntervalPrepareTimeForHuman(array $args = null)
	{
		$post_id = $args['post_id'];
		$startToNowTime = $args['start_to_current_now_time'] ?? false;
		$format = $args['date_format'] ?? 'H:i';
		$now = Carbon::now(wp_timezone_string());
		$option = new PS_Options;

		$available_time_start = $args['available_time_start'];
		$isWithinTimeRange = $this->isWithinTimeRange(['post_id'=>$post_id]);

		$time_interval = $args['time_interval'] ?? null;

		if ( !$time_interval ) {
			$time_interval = $this->getIntervalPrepareTime($post_id);
		}

		$intervalParts = explode(':', $time_interval);

		$makeHour = '0 hour';
		$makeMinute = '0 minutes';
		$makeString = $makeHour.' and '.$makeMinute;

		$arrayIntervalTime = [];

		if (
			count($intervalParts) == 2
		) {
			if( isset($intervalParts[0]) && $intervalParts[0] > 0 ){
				$makeHour = $intervalParts[0].' hour';
			}
			if( isset($intervalParts[1]) && $intervalParts[1] > 0 ){
				$makeMinute = $intervalParts[1].' minutes';
			}
		}
		$makeString = $makeHour.' and '.$makeMinute;
		$interval_time = CarbonInterval::make($makeString);

		if( $startToNowTime && $isWithinTimeRange && $now->timestamp <= Carbon::createFromTimeString($args['available_time_start'])->timestamp ) {
			$available_time_start = $now->add($interval_time)->format('H:i');
		}

		$available_time_end = $args['available_time_end'];
		$intervals = CarbonInterval::make($makeString)->toPeriod($available_time_start, $available_time_end);

		foreach ($intervals as $date) {
			$dateTime = $date->format($format);
			$arrayIntervalTime[$dateTime] = $dateTime;
		}

		return $arrayIntervalTime;
	}

	//pickup
	public function getPickupTime(array $args = null)
	{
		$post_id = $args['post_id'];
		$format = $args['date_format'] ?? 'H:i';
		$startToNowTime = $args['start_to_current_now_time'] ?? false;
		$selectedDate = $args['date_selected'];

		$now = Carbon::now(wp_timezone_string());
		$option = new PS_Options;

		$isWithinTimeRange = $this->isWithinTimeRange(['post_id'=>$post_id]);
		$userSelectedDate = Carbon::make($selectedDate)->format('Ymd');
		//prepare
		$time_prepare_interval = $args['time_prepare_interval'] ?? null;
		if ( !$time_prepare_interval ) {
			$time_prepare_interval = $this->getIntervalPrepareTime($post_id);
		}
		$intervalPrepareParts = explode(':', $time_prepare_interval);
		$makePrepareHour = '0 hour';
		$makePrepareMinute = '0 minutes';
		$makePrepareString = $makePrepareHour.' and '.$makePrepareMinute;

		if (
			count($intervalPrepareParts) == 2
		) {
			if( isset($intervalPrepareParts[0]) && $intervalPrepareParts[0] > 0 ){
				$makePrepareHour = $intervalPrepareParts[0].' hour';
			}
			if( isset($intervalPrepareParts[1]) && $intervalPrepareParts[1] > 0 ){
				$makePrepareMinute = $intervalPrepareParts[1].' minutes';
			}
		}
		$makePrepareString = $makePrepareHour.' and '.$makePrepareMinute;

		//pickup
		$time_pickup_interval = $args['time_interval'] ?? null;
		if ( !$time_pickup_interval ) {
			$time_pickup_interval = $this->getIntervalTime($post_id);
		}
		$intervalPickupParts = explode(':', $time_pickup_interval);
		$makePickupHour = '0 hour';
		$makePickupMinute = '0 minutes';
		$makePickupString = $makePickupHour.' and '.$makePickupMinute;

		if (
			count($intervalPickupParts) == 2
		) {
			if( isset($intervalPickupParts[0]) && $intervalPickupParts[0] > 0 ){
				$makePickupHour = $intervalPickupParts[0].' hour';
			}
			if( isset($intervalPickupParts[1]) && $intervalPickupParts[1] > 0 ){
				$makePickupMinute = $intervalPickupParts[1].' minutes';
			}
		}
		$makePickupString = $makePickupHour.' and '.$makePickupMinute;
		$interval_pickup_time = CarbonInterval::make($makePickupString);
		
		if ( $userSelectedDate > $now->format('Ymd') || $args['available_time_start'] > $now->format('H:i')  ) {
		//if ( !$isWithinTimeRange ) {
			$available_time_start = Carbon::createFromFormat('H:i', $args['available_time_start'], wp_timezone_string())->format('H:i');
		} else {
			$available_time_start = Carbon::createFromFormat('H:i', $args['available_time_start'], wp_timezone_string())->add($makePrepareString)->format('H:i');
		}

		$available_time_end = $args['available_time_end'];
		$intervals = CarbonInterval::make($makePickupString)->toPeriod($available_time_start, $available_time_end);

		$arrayIntervalTime = [];

		foreach ($intervals as $date) {
			$dateTime = $date->format($format);
			$arrayIntervalTime[$dateTime] = $dateTime;
		}

		return $arrayIntervalTime;
	}
	//pickup

	//not use
	public function getIntervalTimeForHuman(array $args = [])
	{
		$post_id = $args['post_id'];
		$startToNowTime = $args['start_to_current_now_time'] ?? false;
		$format = $args['date_format'] ?? 'H:i';
		$now = Carbon::now(wp_timezone_string());
		$option = new PS_Options;

		$available_time_start = $args['available_time_start'];
		$isWithinTimeRange = $this->isWithinTimeRange(['post_id'=>$post_id]);

		$time_prepare_interval = $args['time_prepare_interval'] ?? null;
		if ( !$time_prepare_interval ) {
			$time_prepare_interval = $this->getIntervalPrepareTime($post_id);
		}
		$intervalPrepareParts = explode(':', $time_prepare_interval);
		$makePrepareHour = '0 hour';
		$makePrepareMinute = '0 minutes';
		$makePrepareString = $makePrepareHour.' and '.$makePrepareMinute;

		if (
			count($intervalPrepareParts) == 2
		) {
			if( isset($intervalPrepareParts[0]) && $intervalPrepareParts[0] > 0 ){
				$makePrepareHour = $intervalPrepareParts[0].' hour';
			}
			if( isset($intervalPrepareParts[1]) && $intervalPrepareParts[1] > 0 ){
				$makePrepareMinute = $intervalPrepareParts[1].' minutes';
			}
		}
		$makePrepareString = $makePrepareHour.' and '.$makePrepareMinute;
		$interval_prepare_time = CarbonInterval::make($makePrepareString);
		//pickup
		$time_pickup_interval = $args['time_interval'] ?? null;
		if ( !$time_pickup_interval ) {
			$time_pickup_interval = $this->getIntervalTime($post_id);
		}
		$intervalPickupParts = explode(':', $time_pickup_interval);
		$makePickupHour = '0 hour';
		$makePickupMinute = '0 minutes';
		$makePickupString = $makePickupHour.' and '.$makePickupMinute;

		if (
			count($intervalPickupParts) == 2
		) {
			if( isset($intervalPickupParts[0]) && $intervalPickupParts[0] > 0 ){
				$makePickupHour = $intervalPickupParts[0].' hour';
			}
			if( isset($intervalPickupParts[1]) && $intervalPickupParts[1] > 0 ){
				$makePickupMinute = $intervalPickupParts[1].' minutes';
			}
		}
		$makePickupString = $makePickupHour.' and '.$makePickupMinute;
		$interval_pickup_time = CarbonInterval::make($makePickupString);

		if( $startToNowTime && $isWithinTimeRange && $now->timestamp <= Carbon::createFromTimeString($args['available_time_start'])->timestamp ) {
		//if( $startToNowTime && $isWithinTimeRange && $now->timestamp >= Carbon::createFromTimeString($args['available_time_start'])->timestamp ) {
			$available_time_start = $now->add($interval_prepare_time)->format('H:i');
		}

		$available_time_end = $args['available_time_end'];
		$intervals = CarbonInterval::make($makePickupString)->toPeriod($available_time_start, $available_time_end);

		$arrayIntervalTime = [];

		foreach ($intervals as $date) {
			$dateTime = $date->format($format);
			$arrayIntervalTime[$dateTime] = $dateTime;
		}

		return $arrayIntervalTime;
	}

    public function getStartTime($post_id)
    {
        return PS_SchedulePostMeta::get_instance()->ps_select_start_time_available([
			'post_id' => $post_id,
			'action' => 'r',
			'single' => true
		]);
    }

    public function getEndTime($post_id)
    {
        return PS_SchedulePostMeta::get_instance()->ps_select_end_time_available([
			'post_id' => $post_id,
			'action' => 'r',
			'single' => true
		]);
    }

    public function getIntervalTime($post_id)
    {
        $ret = PS_SchedulePostMeta::get_instance()->ps_interval_time([
			'post_id' => $post_id,
			'action' => 'r',
			'single' => true
		]);
		if ( !$ret ) {
			$option = new PS_Options;
			return $option->getGlobalTimeInterval();
		}
		return $ret;
    }

    public function getIntervalPrepareTime($post_id)
    {
        $ret = PS_SchedulePostMeta::get_instance()->ps_interval_time_preperations([
			'post_id' => $post_id,
			'action' => 'r',
			'single' => true
		]);
		if ( !$ret ) {
			$option = new PS_Options;
			return $option->getGlobalPrepareTimeInterval();
		}
		return $ret;
    }

	public function getHoursRange( $start = 0, $end = 86400, $step = 3600, $format = 'g:i a' ) {
		$times = array();
		foreach ( range( $start, $end, $step ) as $timestamp ) {
			$hour_mins = gmdate( 'H:i', $timestamp );
			if ( ! empty( $format ) )
			$times[$hour_mins] = gmdate( $format, $timestamp );
			else $times[$hour_mins] = $hour_mins;
		}
		return $times;
	}

	/**
	* create time range by CodexWorld
	* https://www.codexworld.com/create-time-range-array-php/
	* @param mixed $start start time, e.g., 7:30am or 7:30
	* @param mixed $end   end time, e.g., 8:30pm or 20:30
	* @param string $interval time intervals, 1 hour, 1 mins, 1 secs, etc.
	* @param string $format time format, e.g., 12 or 24
	*/
	protected function createTimeRange($start, $end, $interval = '30 mins', $format = '12') {
	  $startTime = strtotime($start);
	  $endTime   = strtotime($end);
	  $returnTimeFormat = ($format == '12')?'g:i:s A':'G:i:s';

	  $current   = time();
	  $addTime   = strtotime('+'.$interval, $current);
	  $diff      = $addTime - $current;

	  $times = array();
	  while ($startTime < $endTime) {
	      $times[] = date($returnTimeFormat, $startTime);
	      $startTime += $diff;
	  }
	  $times[] = date($returnTimeFormat, $startTime);
	  return $times;
	}

}//
