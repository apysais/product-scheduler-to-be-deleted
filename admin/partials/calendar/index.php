<link href='<?php echo plugins_url('/product-scheduler/assets/fullcalendar/lib/main.min.css'); ?>' rel='stylesheet' />
<script src='<?php echo plugins_url('/product-scheduler/assets/moment/moment.min.js'); ?>'></script>
<script src='<?php echo plugins_url('/product-scheduler/assets/fullcalendar/lib/main.min.js'); ?>'></script>
<script src='<?php echo plugins_url('/product-scheduler/assets/fullcalendar/lib/main.global.min.js'); ?>'></script>
<script src='<?php echo plugins_url('/product-scheduler/assets/popper.min.js'); ?>'></script>
<script src='<?php echo plugins_url('/product-scheduler/assets/tooltip.min.js'); ?>'></script>

<link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>

<script>
  document.addEventListener('DOMContentLoaded', function() {
    var calendarEl = document.getElementById('calendar');

    var calendar = new FullCalendar.Calendar(calendarEl, {
      headerToolbar: {
        left: 'prev,next today',
        center: 'title',
        // right: 'dayGridMonth,timeGridWeek,timeGridDay,listWeek'
        right: 'listDay,listWeek,listMonth'
      },
      initialView: 'listMonth',
      views: {
          listDay: { buttonText: 'list day' },
          listWeek: { buttonText: 'list week' },
          listMonth: { buttonText: 'list month' }
      },
      editable: true,
      navLinks: true, // can click day/week names to navigate views
      dayMaxEvents: true, // allow "more" link when too many events
      selectable: true,
      selectMirror: true,
      eventDidMount: function(info) {
        var tooltip = new Tooltip(info.el, {
            title: info.event.extendedProps['descripton'],
            placement: 'top',
            trigger: 'hover',
            container: 'body'
        });
      },
      eventClick: function(arg) {
        //jQuery( "#dialog" ).dialog();
        // console.log(arg.event);
        // console.log(arg.event.meta);
      },
      eventSources: [
        {
          url: ajaxurl,
          method: 'POST',
          extraParams: {
            action: 'ps_fullcalendar_events',
          },
          failure: function() {
            alert('there was an error while fetching events!');
          },
          // color: 'gray',   // a non-ajax option
          // textColor: 'white' // a non-ajax option
        }
      ],

      loading: function(bool) {
        document.getElementById('loading').style.display =
          bool ? 'block' : 'none';
      }
    });

    calendar.render();
  });

</script>
<style>

  #script-warning {
    display: none;
    background: #eee;
    border-bottom: 1px solid #ddd;
    padding: 0 10px;
    line-height: 40px;
    text-align: center;
    font-weight: bold;
    font-size: 12px;
    color: red;
  }

  #loading {
    display: none;
    position: absolute;
    top: 10px;
    right: 50%;
  }

  #calendar {
    max-width: 1100px;
    margin: 40px auto;
    padding: 0 10px;
  }

  .popper,
.tooltip {
  position: absolute;
  z-index: 9999;
  background: #FFC107;
  color: black;
  width: 150px;
  border-radius: 3px;
  box-shadow: 0 0 2px rgba(0,0,0,0.5);
  padding: 10px;
  text-align: center;
}
.style5 .tooltip {
  background: #1E252B;
  color: #FFFFFF;
  max-width: 200px;
  width: auto;
  font-size: .8rem;
  padding: .5em 1em;
}
.popper .popper__arrow,
.tooltip .tooltip-arrow {
  width: 0;
  height: 0;
  border-style: solid;
  position: absolute;
  margin: 5px;
}

.tooltip .tooltip-arrow,
.popper .popper__arrow {
  border-color: #FFC107;
}
.style5 .tooltip .tooltip-arrow {
  border-color: #1E252B;
}
.popper[x-placement^="top"],
.tooltip[x-placement^="top"] {
  margin-bottom: 5px;
}
.popper[x-placement^="top"] .popper__arrow,
.tooltip[x-placement^="top"] .tooltip-arrow {
  border-width: 5px 5px 0 5px;
  border-left-color: transparent;
  border-right-color: transparent;
  border-bottom-color: transparent;
  bottom: -5px;
  left: calc(50% - 5px);
  margin-top: 0;
  margin-bottom: 0;
}
.popper[x-placement^="bottom"],
.tooltip[x-placement^="bottom"] {
  margin-top: 5px;
}
.tooltip[x-placement^="bottom"] .tooltip-arrow,
.popper[x-placement^="bottom"] .popper__arrow {
  border-width: 0 5px 5px 5px;
  border-left-color: transparent;
  border-right-color: transparent;
  border-top-color: transparent;
  top: -5px;
  left: calc(50% - 5px);
  margin-top: 0;
  margin-bottom: 0;
}
.tooltip[x-placement^="right"],
.popper[x-placement^="right"] {
  margin-left: 5px;
}
.popper[x-placement^="right"] .popper__arrow,
.tooltip[x-placement^="right"] .tooltip-arrow {
  border-width: 5px 5px 5px 0;
  border-left-color: transparent;
  border-top-color: transparent;
  border-bottom-color: transparent;
  left: -5px;
  top: calc(50% - 5px);
  margin-left: 0;
  margin-right: 0;
}
.popper[x-placement^="left"],
.tooltip[x-placement^="left"] {
  margin-right: 5px;
}
.popper[x-placement^="left"] .popper__arrow,
.tooltip[x-placement^="left"] .tooltip-arrow {
  border-width: 5px 0 5px 5px;
  border-top-color: transparent;
  border-right-color: transparent;
  border-bottom-color: transparent;
  right: -5px;
  top: calc(50% - 5px);
  margin-left: 0;
  margin-right: 0;
}

</style>
<div class="wrap">
  <div class="bootstrap-iso">
    <div class="container-fluid">
      <h1>Calendar</h1>

      <div class="dashboard-container">

        <div class="dashboard-top-container-full-width">
          <div class="row">
            <div class="col-sm-12 col-md-12">
              <div class="wa-top-dashboard-full-width">

                <div id='loading'>loading...</div>

                <div id='calendar'></div>
                <div id="dialog" title="Basic dialog">
                  <div id="dialog-msg"></div>
                </div>
              </div>
            </div>
          </div>
        </div><!-- .dashboard-top-container-full-width -->

      </div><!-- .dashboard-container -->
    </div><!-- .container-fluid -->
  </div><!-- .bootstrap-iso -->
</div><!-- .wrap -->
<script>
</script>
