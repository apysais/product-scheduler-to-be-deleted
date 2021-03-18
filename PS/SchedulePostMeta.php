<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
/**
 *
 * */
class PS_SchedulePostMeta {
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

	public function ps_select_day( $args = [] ) {
		$prefix = 'ps_select_day';
		if ( isset ( $args['post_id'] ) ) {
			$defaults = array(
				'single'  => false,
				'action'  => 'r',
				'value'   => '',
				'prefix'  => $prefix
			);
			$args = wp_parse_args( $args, $defaults );
			switch( $args['action'] ) {
				case 'd':
					delete_post_meta( $args['post_id'], $args['prefix'], $args['value'] );
				break;
				case 'u':
					update_post_meta( $args['post_id'], $args['prefix'], $args['value'] );
				break;
				case 'r':
					return get_post_meta( $args['post_id'], $args['prefix'], $args['single'] );
				break;
			}
		}
	}

	public function ps_available_as_date_range( $args = [] ) {
		$prefix = 'ps_available_as_date_range';
		if ( isset ( $args['post_id'] ) ) {
			$defaults = array(
				'single'  => false,
				'action'  => 'r',
				'value'   => '',
				'prefix'  => $prefix
			);
			$args = wp_parse_args( $args, $defaults );
			switch( $args['action'] ) {
				case 'd':
					delete_post_meta( $args['post_id'], $args['prefix'], $args['value'] );
				break;
				case 'u':
					update_post_meta( $args['post_id'], $args['prefix'], $args['value'] );
				break;
				case 'r':
					return get_post_meta( $args['post_id'], $args['prefix'], $args['single'] );
				break;
			}
		}
	}

	public function ps_select_date_range_start_month( $args = [] ) {
		$prefix = 'ps_select_date_range_start_month';
		if ( isset ( $args['post_id'] ) ) {
			$defaults = array(
				'single'  => false,
				'action'  => 'r',
				'value'   => '',
				'prefix'  => $prefix
			);
			$args = wp_parse_args( $args, $defaults );
			switch( $args['action'] ) {
				case 'd':
					delete_post_meta( $args['post_id'], $args['prefix'], $args['value'] );
				break;
				case 'u':
					update_post_meta( $args['post_id'], $args['prefix'], $args['value'] );
				break;
				case 'r':
					return get_post_meta( $args['post_id'], $args['prefix'], $args['single'] );
				break;
			}
		}
	}

	public function ps_select_date_range_start_day( $args = [] ) {
		$prefix = 'ps_select_date_range_start_day';
		if ( isset ( $args['post_id'] ) ) {
			$defaults = array(
				'single'  => false,
				'action'  => 'r',
				'value'   => '',
				'prefix'  => $prefix
			);
			$args = wp_parse_args( $args, $defaults );
			switch( $args['action'] ) {
				case 'd':
					delete_post_meta( $args['post_id'], $args['prefix'], $args['value'] );
				break;
				case 'u':
					update_post_meta( $args['post_id'], $args['prefix'], $args['value'] );
				break;
				case 'r':
					return get_post_meta( $args['post_id'], $args['prefix'], $args['single'] );
				break;
			}
		}
	}

	public function ps_select_date_range_end_month( $args = [] ) {
		$prefix = 'ps_select_date_range_end_month';
		if ( isset ( $args['post_id'] ) ) {
			$defaults = array(
				'single'  => false,
				'action'  => 'r',
				'value'   => '',
				'prefix'  => $prefix
			);
			$args = wp_parse_args( $args, $defaults );
			switch( $args['action'] ) {
				case 'd':
					delete_post_meta( $args['post_id'], $args['prefix'], $args['value'] );
				break;
				case 'u':
					update_post_meta( $args['post_id'], $args['prefix'], $args['value'] );
				break;
				case 'r':
					return get_post_meta( $args['post_id'], $args['prefix'], $args['single'] );
				break;
			}
		}
	}

	public function ps_select_date_range_end_day( $args = [] ) {
		$prefix = 'ps_select_date_range_end_day';
		if ( isset ( $args['post_id'] ) ) {
			$defaults = array(
			'single'  => false,
			'action'  => 'r',
			'value'   => '',
			'prefix'  => $prefix
			);
			$args = wp_parse_args( $args, $defaults );
			switch( $args['action'] ) {
				case 'd':
					delete_post_meta( $args['post_id'], $args['prefix'], $args['value'] );
				break;
				case 'u':
					update_post_meta( $args['post_id'], $args['prefix'], $args['value'] );
				break;
				case 'r':
					return get_post_meta( $args['post_id'], $args['prefix'], $args['single'] );
				break;
			}
		}
	}

	public function ps_select_start_time_available( $args = [] ) {
		$prefix = 'ps_select_start_time_available';
		if ( isset ( $args['post_id'] ) ) {
			$defaults = array(
				'single'  => false,
				'action'  => 'r',
				'value'   => '',
				'prefix'  => $prefix
			);
			$args = wp_parse_args( $args, $defaults );
			switch( $args['action'] ) {
				case 'd':
					delete_post_meta( $args['post_id'], $args['prefix'], $args['value'] );
				break;
				case 'u':
					update_post_meta( $args['post_id'], $args['prefix'], $args['value'] );
				break;
				case 'r':
					return get_post_meta( $args['post_id'], $args['prefix'], $args['single'] );
				break;
			}
		}
	}

	public function ps_select_end_time_available( $args = [] ) {
		$prefix = 'ps_select_end_time_available';
		if ( isset ( $args['post_id'] ) ) {
			$defaults = array(
				'single'  => false,
				'action'  => 'r',
				'value'   => '',
				'prefix'  => $prefix
			);
			$args = wp_parse_args( $args, $defaults );
			switch( $args['action'] ) {
				case 'd':
					delete_post_meta( $args['post_id'], $args['prefix'], $args['value'] );
				break;
				case 'u':
					update_post_meta( $args['post_id'], $args['prefix'], $args['value'] );
				break;
				case 'r':
					return get_post_meta( $args['post_id'], $args['prefix'], $args['single'] );
				break;
			}
		}
	}

	public function ps_interval_time( $args = [] ) {
		$prefix = 'ps_interval_time';
		if ( isset ( $args['post_id'] ) ) {
			$defaults = array(
				'single'  => false,
				'action'  => 'r',
				'value'   => '',
				'prefix'  => $prefix
			);
			$args = wp_parse_args( $args, $defaults );
			switch( $args['action'] ) {
				case 'd':
					delete_post_meta( $args['post_id'], $args['prefix'], $args['value'] );
				break;
				case 'u':
					update_post_meta( $args['post_id'], $args['prefix'], $args['value'] );
				break;
				case 'r':
					return get_post_meta( $args['post_id'], $args['prefix'], $args['single'] );
				break;
			}
		}
	}

	public function ps_enable_schedule( $args = [] ) {
		$prefix = 'ps_enable_schedule';
		if ( isset ( $args['post_id'] ) ) {
			$defaults = array(
				'single'  => false,
				'action'  => 'r',
				'value'   => '',
				'prefix'  => $prefix
			);
			$args = wp_parse_args( $args, $defaults );
			switch( $args['action'] ) {
				case 'd':
					delete_post_meta( $args['post_id'], $args['prefix'], $args['value'] );
				break;
				case 'u':
					update_post_meta( $args['post_id'], $args['prefix'], $args['value'] );
				break;
				case 'r':
					return get_post_meta( $args['post_id'], $args['prefix'], $args['single'] );
				break;
			}
		}
	}
}//
