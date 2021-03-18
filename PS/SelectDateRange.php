<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

use Carbon\Carbon;

/**
 * PS_SelectDateRange
 * */
class PS_SelectDateRange {
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

	public function isAvailableDateRange($post_id = null)
	{
		$startMonth = $this->getDateRangeStartMonth($post_id);
		$startDay = $this->getDateRangeStartDay($post_id);
		$endMonth = $this->getDateRangeEndMonth($post_id);
		$endDay = $this->getDateRangeEndDay($post_id);
		$arr = [
			'start_month' => $startMonth,
			'start_day' => $startDay,
			'end_month' => $endMonth,
			'end_day' => $endDay,
		];
		$today = Carbon::today()->format('md');

		if(
			strlen($startMonth) > 1
			&& strlen($endMonth) > 1
		) {
			return true;
		}

		return false;
	}

	/**
	* date range
	* if only start month then it would be the whole month 1st to end of start month
	* if only start month with day meaning available on that date only
	* if with start month and end month only meaning within that start month selected to end of that month selected
	* if with start month and date and end month only, then start month with date and end of month selected
	* if with start month and date and end month and date, then within range of start month - date and end month - date
	** if there is a select date available meaning it is only available on that date only
	**/
	public function isWithinDateRange(array $args = null)
	{
		$postId = $args['post_id'];
		$isDateRange = $this->isDateRangeChecked($postId);
		$startMonth = $this->getDateRangeStartMonth($postId);
		$startDay = $this->getDateRangeStartDay($postId);
		$endMonth = $this->getDateRangeEndMonth($postId);
		$endDay = $this->getDateRangeEndDay($postId);
		$arr = [
			'is_date_range_checked' => $isDateRange,
			'start_month' => $startMonth,
			'start_day' => $startDay,
			'end_month' => $endMonth,
			'end_day' => $endDay,
		];
		$today = Carbon::today()->format('md');

		//check if is in start month range
		if(
			$startMonth
			&& strlen($startMonth) > 1
			&& strlen($endMonth) == 1
		) {
			if ($startDay == 0){
				$startDay = 1;
			}
			$startDate = Carbon::parse("{$startMonth} {$startDay}")->format('md');
			if ($today >= $startDate) {
				return true;
			}
		}

		//check if is in end month
		if(
			$endMonth
			&& strlen($startMonth) == 1
			&& strlen($endMonth) > 1
		) {
			if ($endDay == 0){
				$endDay = 1;
			}
			$endDate = Carbon::parse("{$endMonth} {$endDay}")->format('md');
			if ($today <= $endDate) {
				return true;
			}
		}

		//check if is in start month and end month only
		if(
			$startMonth
			&& strlen($startMonth) > 1
			&& strlen($endMonth) > 1
		) {
			if ($startDay == 0){
				$startDay = 1;
			}
			$startDate = Carbon::parse("{$startMonth} {$startDay}")->format('md');

			if ($endDay == 0){
				$endDay = Carbon::parse("{$endMonth}")->daysInMonth;
			}
			$endDate = Carbon::parse("{$endMonth} {$endDay}")->format('md');

			if (($today >= $startDate) && ($today <= $endDate)){
				return true;
			}
		}

		return false;
	}

	public function isDateRangeChecked($post_id = null)
	{
		$ret = PS_SchedulePostMeta::get_instance()->ps_available_as_date_range([
			'post_id' => $post_id,
			'action' => 'r',
			'single' => true
		]);

		return ($ret == 'yes') ? true : false;
	}

	public function getDateRangeStartMonth($post_id = null)
	{
		return PS_SchedulePostMeta::get_instance()->ps_select_date_range_start_month([
			'post_id' => $post_id,
			'action' => 'r',
			'single' => true
		]);
	}

	public function getDateRangeStartDay($post_id = null)
	{
		return PS_SchedulePostMeta::get_instance()->ps_select_date_range_start_day([
			'post_id' => $post_id,
			'action' => 'r',
			'single' => true
		]);
	}

	public function getDateRangeEndMonth($post_id = null)
	{
		return PS_SchedulePostMeta::get_instance()->ps_select_date_range_end_month([
			'post_id' => $post_id,
			'action' => 'r',
			'single' => true
		]);
	}

	public function getDateRangeEndDay($post_id = null)
	{
		return PS_SchedulePostMeta::get_instance()->ps_select_date_range_end_day([
			'post_id' => $post_id,
			'action' => 'r',
			'single' => true
		]);
	}

	public function getDateRangeStartEnd($post_id = null)
	{
		$startMonth = $this->getDateRangeStartMonth($post_id);
		$startDay = $this->getDateRangeStartDay($post_id);
		$endMonth = $this->getDateRangeEndMonth($post_id);
		$endDay = $this->getDateRangeEndDay($post_id);

		$today = Carbon::today()->format('Y-m-d');
		$startDate = null;
		$endDate = null;
		$returnDateRange = [
			'start_date' => null,
			'end_date' => null,
		];

		//check if is in start month range
		if(
			$startMonth
			&& strlen($startMonth) > 1
			&& strlen($endMonth) == 1
		) {
			if ($startDay == 0){
				$startDay = 1;
			}
			$startDate = Carbon::parse("{$startMonth} {$startDay}")->format('Y-m-d');
			$endDate = '';
		}

		//check if is in end month
		if(
			$endMonth
			&& strlen($startMonth) == 1
			&& strlen($endMonth) > 1
		) {
			if ($endDay == 0){
				$endDay = Carbon::parse("{$endMonth}")->daysInMonth;
			}
			$startDate = Carbon::today()->startOfMonth()->format('Y-m-d');
			$endDate = Carbon::parse("{$endMonth} {$endDay}")->format('Y-m-d');
		}

		//check if is in start month and end month only
		if(
			$startMonth
			&& strlen($startMonth) > 1
			&& strlen($endMonth) > 1
		) {
			if ($startDay == 0){
				$startDay = 1;
			}
			$startDate = Carbon::parse("{$startMonth} {$startDay}")->format('Y-m-d');

			if ($endDay == 0){
				$endDay = Carbon::parse("{$endMonth}")->daysInMonth;
			}
			$endDate = Carbon::parse("{$endMonth} {$endDay}")->format('Y-m-d');
		}

		return $returnDateRange = [
			'start_date' => $startDate,
			'end_date' => $endDate,
		];
	}

	public function getSelectMonth()
	{
		return [
			'january' => 'January',
			'february' => 'February',
			'march' => 'March',
			'april' => 'April',
			'may' => 'May',
			'june' => 'June',
			'july' => 'July',
			'august' => 'August',
			'september' => 'September',
			'october' => 'October',
			'november' => 'November',
			'december' => 'December',
		];
	}

	public function getSelectDate()
	{
		$day = range(1,31);

		$ret = array_map( function( $day ) {
			return str_pad( $day, 2, '0', STR_PAD_LEFT );
		}, range(1, 31) );

		return $ret;
	}
}//
