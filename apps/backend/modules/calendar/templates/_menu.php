<?php use_helper('Form', 'Javascript', 'Tip') ?>

<ul class="horizontal-menu" id="calendar-menu">
  <li>
    <?php echo link_to_remote(image_tag('icons/one_month_back.png', array('onmouseover' => tip_usage('Go one month back'), 'onmouseout' => untip())), array('url' => 'calendar/monthBack', 'update' => 'calendar-view', 'script' => true)) ?>
  </li>
  <li>
    <?php echo link_to_remote(image_tag('icons/one_week_back.png', array('onmouseover' => tip_usage('Go one week back'), 'onmouseout' => untip())), array('url' => 'calendar/weekBack', 'update' => 'calendar-view', 'script' => true)) ?>
  </li>
  <li>
    <?php echo link_to_remote(image_tag('icons/goto_today.png', array('onmouseover' => tip_usage('Go to today'), 'onmouseout' => untip())), array('url' => 'calendar/today', 'update' => 'calendar-view', 'script' => true)) ?>
  </li>
  <li>
    <?php echo link_to_remote(image_tag('icons/one_week_forward.png', array('onmouseover' => tip_usage('Go one week forward'), 'onmouseout' => untip())), array('url' => 'calendar/weekForward', 'update' => 'calendar-view', 'script' => true)) ?>
  </li>
  <li>
    <?php echo link_to_remote(image_tag('icons/one_month_forward.png', array('onmouseover' => tip_usage('Go one month forward'), 'onmouseout' => untip())), array('url' => 'calendar/monthForward', 'update' => 'calendar-view', 'script' => true)) ?>
  </li>
  <li>
    <?php echo link_to_remote(image_tag('icons/one_more_day.png', array('onmouseover' => tip_usage('Show one more day'), 'onmouseout' => untip())), array('url' => 'calendar/oneMoreDay', 'update' => 'calendar-view', 'script' => true)) ?>
  </li>
  <?php if ($length > $min_length): ?>
  <li>
    <?php echo link_to_remote(image_tag('icons/one_day_less.png', array('onmouseover' => tip_usage('Show one day less'), 'onmouseout' => untip())), array('url' => 'calendar/oneDayLess', 'update' => 'calendar-view', 'script' => true)) ?>
  </li>
  <?php endif ?>
</ul>
<ul class="horizontal-menu" id="calendar-menu">
  <li>
    <?php echo input_date_tag('point_to', null, array('id' => 'goto-select',
                                                      'rich' => true,
        'onmouseover' => tip_usage('Click on the calendar icon right here to select a new date'),
        'onmouseout' => untip(),
                                                      'readonly' => true,
                                                      'calendar_button_img' => 'icons/calendar.png',
                                                      //'style' => 'display: none',
                                                      )) ?>
                                                    
    <?php echo link_to_remote(image_tag('icons/arrow_right.png'), array('update' => 'calendar-view',
                                    'url' => 'calendar/goto',
        'onmouseover' => tip_usage('Clock to point calendar on selected date'),
        'onmouseout' => untip(),
                                    'with' => '"when="+$("goto-select").value',
                                    'script' => true)) ?>
  </li>
</ul>
