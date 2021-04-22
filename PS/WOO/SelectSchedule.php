<?php
if ( ! defined( 'ABSPATH' ) ) {
exit; // Exit if accessed directly
}

use Carbon\Carbon;

/**
* WP menu
* */
class PS_WOO_SelectSchedule {
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
        add_action('woocommerce_before_add_to_cart_button', [$this, 'select'], 100);
        add_filter('woocommerce_loop_add_to_cart_link', [$this, 'psScheduleRedirectToProduct'], 100, 2 );
        add_filter('woocommerce_add_to_cart_validation', [$this, 'psValidateCustomField'], 100, 3 );
        add_action('woocommerce_checkout_create_order_line_item', [$this, 'psAddCustomDataToOrder'], 100, 4 );
        add_filter('woocommerce_add_cart_item_data', [$this, 'psAddCustomFieldItemData'], 100, 4 );
        add_filter('woocommerce_cart_item_name', [$this, 'psCartItemName'], 100, 3 );
        add_filter('woocommerce_order_item_display_meta_key', [$this, 'psChangeOrderItemMetaTitle'], 200, 3 );
        add_action('wp_enqueue_scripts', [$this, 'load_scripts']);
    }

    public function load_scripts()
    {
        wp_enqueue_style( 'jquery-ui-datepicker-style' , '//ajax.googleapis.com/ajax/libs/jqueryui/1.10.4/themes/smoothness/jquery-ui.css');
        wp_enqueue_script( 'jquery-ui-datepicker' );
    }

    /**
     * Changing a meta title
     * @param  string        $key  The meta key
     * @param  WC_Meta_Data  $meta The meta object
     * @param  WC_Order_Item $item The order item object
     * @return string        The title
     */
    public function psChangeOrderItemMetaTitle( $key, $meta, $item ) {

        // By using $meta-key we are sure we have the correct one.
        if ( 'ps_date_range_select' === $meta->key ) { $key = 'Afhentningsdato'; }
        if ( 'ps_time_select' === $meta->key ) { $key = 'Prepare Time'; }
        if ( 'ps_time_pickup_select' === $meta->key ) { $key = 'Afhentningstidspunkt'; }

        return $key;
    }

    public function psCartItemName( $name, $cart_item, $cart_item_key ) {
        if( isset( $cart_item['ps_date_range_select'] ) ) {
            $name .= sprintf(
            '<p>Afhentningsdato: %s</p>',
            esc_html( $cart_item['ps_date_range_select'] )
            );
        }
        if( isset( $cart_item['ps_time_select'] ) ) {
            $name .= sprintf(
            '<p>Prepare Time: %s</p>',
            esc_html( $cart_item['ps_time_select'] )
            );
        }
        if( isset( $cart_item['ps_time_pickup_select'] ) ) {
            $name .= sprintf(
            '<p>Afhentningstidspunkt: %s</p>',
            esc_html( $cart_item['ps_time_pickup_select'] )
            );
        }
        return $name;
    }

    public function psAddCustomFieldItemData( $cart_item_data, $product_id, $variation_id, $quantity )
    {
        $isScheduleEnable = ps_enable_schedule([
            'post_id' => $product_id,
        ]);
        if( $isScheduleEnable && ! empty( $_POST['ps-select-date-range'] ) ) {
            // Add the item data
            $cart_item_data['ps_date_range_select'] = $_POST['ps-select-date-range'];
        }
        if( $isScheduleEnable && ! empty( $_POST['ps_time_select'] ) ) {
            // Add the item data, prepare
            $cart_item_data['ps_time_select'] = $_POST['ps_time_select'];
        }
        if( $isScheduleEnable && ! empty( $_POST['ps-select-time-range-pickup'] ) ) {
            // Add the item data, pickup
            $cart_item_data['ps_time_pickup_select'] = $_POST['ps-select-time-range-pickup'];
        }
        return $cart_item_data;
    }

    public function psAddCustomDataToOrder( $item, $cart_item_key, $values, $order )
    {
        foreach( $item as $cart_item_key=>$values ) {
            if( isset( $values['ps_date_range_select'] ) ) {
                $item->add_meta_data( 'ps_date_range_select', $values['ps_date_range_select'], true );
            }
            //prepare
            if( isset( $values['ps_time_select'] ) ) {
                $item->add_meta_data( 'ps_time_select', $values['ps_time_select'], true );
            }
            //pickup
            if( isset( $values['ps_time_pickup_select'] ) ) {
                $item->add_meta_data( 'ps_time_pickup_select', $values['ps_time_pickup_select'], true );
            }
        }
    }

    public function psValidateCustomField( $passed, $product_id, $quantity )
    {
        $isScheduleEnable = ps_enable_schedule([
            'post_id' => $product_id,
        ]);
        if( $isScheduleEnable && empty( $_POST['ps-select-date-range'] ) ) {
            // Fails validation
            $passed = false;
            wc_add_notice( __( 'Please select a date into the date schedule', 'woocommerce' ), 'error' );
        }
        if( $isScheduleEnable && empty( $_POST['ps-select-time-range-pickup'] ) ) {
            // Fails validation
            $passed = false;
            wc_add_notice( __( 'Please select a time pickup', 'woocommerce' ), 'error' );
        }
        return $passed;
    }

    public function psScheduleRedirectToProduct($button, $product)
    {
        if (is_product_category() || is_shop()) {
            $post_id = $product->get_id();
            $isScheduleEnable = ps_enable_schedule([
    			'post_id' => $post_id,
    		]);
            if( $isScheduleEnable ) {
                $button_text = __("Select Schedule", "woocommerce");
        		$button_link = $product->get_permalink();
        		$button = '<a class="button" href="' . $button_link . '">' . $button_text . '</a>';
            }
    	}
        return $button;
    }

    public function select()
    {
        global $post;
        $data = [];
        $available = [];

        $post_id = $post->ID;
        $now = Carbon::now(wp_timezone_string());

        $isScheduleEnable = ps_enable_schedule([
			'post_id' => $post_id,
		]);


        if ( $isScheduleEnable ) {
            $available = PS_Available::get_instance()->status(['post_id'=>$post_id]);

            $isWithinDateRange = PS_SelectDateRange::get_instance()->isWithinDateRange([
    			'post_id'=>$post_id
    		]);

            $isAvailableToday = PS_SelectDay::get_instance()->isAvailableToday(['post_id'=>$post_id]);
            $data['available_select_day'] = PS_SelectDay::get_instance()->convertDayToNumericKey($available['available_select_day']);

            $isWithinTimeRange = PS_SelectTimeRange::get_instance()->isWithinTimeRange(['post_id'=>$post_id]);
            $prepareTime = PS_SelectTimeRange::get_instance()->getIntervalPrepareTime($post_id);

            $available_time_pickup = PS_SelectTimeRange::get_instance()->getIntervalTimeForHuman([
                'post_id' => $post_id,
                'available_time_start' => $available['available_time_start'],
                'available_time_end' => $available['available_time_end'],
                'start_to_current_now_time' => true
            ]);
            //ps_dd($available_time_prepare);
            $data['available'] = $available;
            $data['isScheduleEnable'] = $isScheduleEnable;
            $data['isWithinTimeRange'] = $isWithinTimeRange;
            $data['isWithinDateRange'] = $isWithinDateRange;
            $data['prepare_time'] = $prepareTime;
            $data['available_select_time_pickup'] = $available_time_pickup;
            $data['available_select_date_day'] = PS_SelectDay::get_instance()->convertDayToJqueryDatePicker($available['available_day']);
            $data['post_id'] = $post_id;
            //ps_dd($data);
            PS_View::get_instance()->public_partials( 'woo/before-cart.php', $data );
        }
    }

}//
