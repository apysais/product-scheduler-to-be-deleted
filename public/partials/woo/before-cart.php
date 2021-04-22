<div class="ps-select-date-time-schedule">
    <p>Afhentningsdato
        <input readonly="readonly" type="text" class="ps-select-date-range" name="ps-select-date-range" id="ps-select-date-range" style="width:100%;" autocomplete="off">
    </p>
    <?php //if ( $isWithinTimeRange ) : ?>
        <p>Afhentningstidspunkt <br>
            <select name="ps-select-time-range-pickup" class="ps-select-time-range-pickup">
                <?php foreach($available_select_time_pickup as $key => $val ) : ?>
                    <option value="<?php echo $key;?>"><?php echo $val;?></option>
                <?php endforeach; ?>
            </select>

        </p>
    <?php //endif; ?>
    <span class="ps-select-time-ajax"></span>
    <input type="hidden" name="ps_time_select" value="<?php echo $prepare_time;?>">
</div>
<script>
jQuery( function() {
    var postId = <?php echo $post_id; ?>;
    var dateToday = new Date();
    var selectAvailabeDay = <?php echo json_encode(array_keys($available_select_date_day)); ?>;
    //1 monday
    //2 tuesday
    //3 wednesday
    //4 thursday
    //5 friday
    //6 saturday
    //0 sunday

    var dbEndMonth = '<?php echo $available['date_range_select']['end_date']; ?>';
    var dbStartMonth = '<?php echo $available['date_range_select']['start_date']; ?>';

    var setMaxDate = '';
    if(dbEndMonth != '') {
        var setMaxDate = new Date(dbEndMonth);
    }

    var setMinDate = dateToday;
    if(dbStartMonth != '') {
        if( new Date(dbStartMonth) >= dateToday) {
            var setMinDate = new Date(dbStartMonth);
        }
    }

    function disableSelectTime(disable = 0)
    {
        var selectTimePickup = jQuery('.ps-select-time-range-pickup');
        if( disable == 1 ) {
            selectTimePickup.prop('disabled', true);
            selectTimePickup.addClass('disabled');
        } else {
            selectTimePickup.removeClass('disabled');
            selectTimePickup.prop('disabled', false);
        }
    }
    disableSelectTime(1);

    function wooAddToCartDisable(disable = 0) {
        var wooAddToCart = jQuery('.single_add_to_cart_button');
        if( disable == 1 ) {
            wooAddToCart.prop('disabled', true);
            wooAddToCart.addClass('disabled');
        } else {
            wooAddToCart.removeClass('disabled');
            wooAddToCart.prop('disabled', false);
        }
    }

    jQuery( "#ps-select-date-range" ).datepicker({
        dateFormat:'yy/mm/dd',
        changeMonth: false,
        numberOfMonths: 1,
        minDate: setMinDate,
        maxDate: setMaxDate,
        beforeShowDay: function (date) {
            var day = date.getDay();

            if ( selectAvailabeDay.length == 0 ) {
                return [true];
            }

            if ( jQuery.inArray(day, selectAvailabeDay) != -1 ) {
                return [true];
            }
            return [false];
        },
        onSelect: function(date) {
            var dateToday = jQuery.datepicker.formatDate('yy/mm/dd', new Date());
            jQuery('.single_add_to_cart_button').attr("disabled", true);
            selectTimePickupAjax(date);
        },
    });

    function selectTimePickupAjax(date)
    {
        var ps_ajax_url = '<?php echo admin_url( 'admin-ajax.php' ); ?>';
        var spanAjaxMsg = jQuery('.ps-select-time-ajax');
        var selectTimePickup = jQuery('.ps-select-time-range-pickup');


        var data = {
    		'action': 'ps_select_time_pickup_range',
            'date_selected' : date,
            'post_id' : postId
    	};

        selectTimePickup.prop("disabled", true);
        wooAddToCartDisable(1);

        spanAjaxMsg.html();
        spanAjaxMsg.html('Finder tidspunkter. Vent venligst...');

        jQuery.ajax({
            method: "POST",
            url: ps_ajax_url,
            data: data
        })
        .done(function( response ) {
            selectTimePickup.empty();

            jQuery.each(response, function(key, val){
                selectTimePickup.append('<option value="' + key + '">' + val + '</option>');
            });

            selectTimePickup.prop("disabled", false);
            wooAddToCartDisable();
            spanAjaxMsg.html('');
        })
        .fail(function(response){
            selectTimePickup.prop("disabled", false);
            wooAddToCartDisable();
            spanAjaxMsg.html();
            spanAjaxMsg.html('something went wrong');
        });
    }

} );
</script>
