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

	public function getIntervalTimeForHuman(array $args = [])
	{
		$post_id = $args['post_id'];
		$startToNowTime = $args['start_to_current_now_time'] ?? false;

		$now = Carbon::now(wp_timezone_string());
		$option = new PS_Options;

		$available_time_start = $args['available_time_start'];
		if( $startToNowTime && $now->format('h:i a') <= Carbon::createFromTimeString($args['available_time_start'])->format('h:i a') ) {
		 	$available_time_start = $now->format('h:i a');
		}

		$available_time_end = $args['available_time_end'];
		$time_interval = $args['time_interval'] ?? null;

		if ( !$time_interval ) {
			$time_interval = $this->getIntervalTime($post_id);
			if(!$time_interval){
				$time_interval = $option->getGlobalTimeInterval();
			}
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

			$makeString = $makeHour.' and '.$makeMinute;

			$intervals = CarbonInterval::make($makeString)->toPeriod($available_time_start, $available_time_end);

	        foreach ($intervals as $date) {
				$dateTime = $date->format('h:i a');
				$arrayIntervalTime[$dateTime] = $dateTime;
	        }
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
