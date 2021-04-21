<?php
if ( ! defined( 'ABSPATH' ) ) {
exit; // Exit if accessed directly
}

use Carbon\Carbon;
use Carbon\CarbonInterval;

/**
* PS_Availabe
* */
class PS_Available {
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

    public function status(array $args = null)
    {
        $post_id = $args['post_id'];
        $counterAvailabe = 0;

        $defaults = [
            'status_date_range' => false,
            'status_today' => false,
            'status_time' => false,
            'enable_schedule' => true,
            'is_available' => false
        ];

        $args = wp_parse_args( $args, $defaults );

        $isScheduleEnable = ps_enable_schedule([
			'post_id' => $post_id,
		]);
        if (!$isScheduleEnable) {
            $args['enable_schedule'] = false;
            return $args;
        }

        $dateRange = PS_SelectDateRange::get_instance()->isWithinDateRange([
			'post_id'=>$post_id
		]);
        $args['date_range_select'] = PS_SelectDateRange::get_instance()->getDateRangeStartEnd($post_id);
        if ($dateRange) {
            $args['status_date_range'] = true;
            $counterAvailabe += 1;
        }

        $isAvailableToday = PS_SelectDay::get_instance()->isAvailableToday(['post_id'=>$post_id]);
        $args['available_day'] = PS_SelectDay::get_instance()->getAvailableDay(['post_id'=>$post_id]);
        $args['available_select_day'] = PS_SelectDay::get_instance()->getAvailableSelecDay(['post_id'=>$post_id]);
        if ($isAvailableToday) {
            $args['status_today'] = true;
            $counterAvailabe += 1;
        }

        $timeRange = PS_SelectTimeRange::get_instance()->isWithinTimeRange(['post_id'=>$post_id]);
        $args['available_time_start'] = PS_SelectTimeRange::get_instance()->getSelectStartTime($post_id)->format('H:i');
        $args['available_time_end'] = PS_SelectTimeRange::get_instance()->getSelectEndTime($post_id)->format('H:i');
        $args['available_select_time'] = PS_SelectTimeRange::get_instance()->getIntervalTimeForHuman([
            'post_id' => $post_id,
            'available_time_start' => $args['available_time_start'],
            'available_time_end' => $args['available_time_end'],
            'start_to_current_now_time' => true
        ]);
        $args['interval_time'] = PS_SelectTimeRange::get_instance()->getIntervalTime($post_id);
        if ($timeRange) {
            $args['status_time'] = true;
            $counterAvailabe += 1;
        }

        if($counterAvailabe > 0) {
            $args['is_available'] = true;
        }

        return $args;
    }

}//
