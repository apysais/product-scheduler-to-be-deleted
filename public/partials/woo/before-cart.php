<p>Select Date
<input type="text" class="ps-select-date-range" name="ps-select-date-range" id="ps-select-date-range" style="width:100%;" autocomplete="off">
</p>
<?php if ( $isWithinTimeRange ) : ?>
    <p>Select Time<br>
    <select name="ps-select-time-range" class="ps-select-time-range">
        <?php foreach($available_select_time as $key => $val ) : ?>
            <option value="<?php echo $key;?>"><?php echo $val;?></option>
        <?php endforeach; ?>
    </select>
    </p>
<?php endif; ?>
<script>
jQuery( function() {
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
    console.log(setMinDate);
    console.log(setMaxDate);
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
        }
    });
} );
</script>
