<div class="ps-select-date-time-schedule">
    <p>Select Date
    <input readonly="readonly" type="text" class="ps-select-date-range" name="ps-select-date-range" id="ps-select-date-range" style="width:100%;" autocomplete="off">
    </p>
    <?php //if ( $isWithinTimeRange ) : ?>
        <p>Select Time <br>
            <select name="ps-select-time-range" class="ps-select-time-range">
                <?php foreach($available_select_time as $key => $val ) : ?>
                    <option value="<?php echo $key;?>"><?php echo $val;?></option>
                <?php endforeach; ?>
            </select>
            <span class="ps-select-time-ajax"></span>
        </p>
    <?php //endif; ?>
<div class="ps-select-date-time-schedule">
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
            selectTimeRangeAjax(date);
        },
    });

    function selectTimeRangeAjax(date)
    {
        var data = {
    		'action': 'ps_select_time_range',
            'date_selected' : date,
            'post_id' : postId
    	};
        var ps_ajax_url = '<?php echo admin_url( 'admin-ajax.php' ); ?>';

        var selectTime = jQuery('.ps-select-time-range');
        var spanAjaxMsg = jQuery('.ps-select-time-ajax');

        selectTime.prop("disabled", true);
        spanAjaxMsg.html();
        spanAjaxMsg.html('getting time range please wait...');

        jQuery.ajax({
            method: "POST",
            url: ps_ajax_url,
            data: data
        })
        .done(function( response ) {
            selectTime.empty();

            jQuery.each(response, function(key, val){
                selectTime.append('<option value="' + key + '">' + val + '</option>');
            });
            selectTime.prop("disabled", false);
            spanAjaxMsg.html('');
        })
        .fail(function(response){
            selectTime.prop("disabled", false);
            spanAjaxMsg.html();
            spanAjaxMsg.html('something went wrong');
        });
    }

} );
</script>
