<?php use_helper('Form', 'Javascript', 'Date') ?>

  <div class="popup-title">
    <ul class="horizontal-menu">
      <li style="text-align: left; float: left">
        <?php echo link_to($reservation_part->getCustomer()->getFullName(), 'customers/show?id=' . $reservation_part->getCustomer()->getId()) ?> <?php echo image_tag('flags/' . $reservation_part->getCustomer()->getLanguage()) ?>
      </li>
      <li>
        <a href="#"><?php echo image_tag('icons/help.png', array('onmouseover' => tip_usage("Changing the combos has immediate effect on saving data.\nTo save the notes write your text and then press the green button."), 'onmouseout' => untip())) ?></a>
        <a href="#"><?php echo image_tag('icons/cross.png', array('onclick' => '$(\'popup-reservation-' . $reservation_part->getId() . '\').style.display = \'none\'')) ?></a>
      </li>
    </ul>
  </div>


  <?php if (!$reservation_part->isInitial()): ?>
  <div class="container">
  Please refer to the first part of the reservation to modify global preferences such as notes, arrival time,
  arriving day and departing day.
  </div>
  <?php endif ?>

  <div class="container">
    <label>Room arrangement:</label>
    <?php echo select_tag('arrangement-' . $reservation_part->getId(),  options_for_select($reservation_part->getArrangementsForSelect(), $reservation_part->getArrangementForSelect()), array('onchange' => remote_function(array(
                                                            'update' => 'reservation-buttons-' . $reservation_part->getId(),
                                                            'url'      => 'calendar/changeArrangement?id=' . $reservation_part->getId(),
                                                            'with' => '"arrangement="+$("arrangement-' . $reservation_part->getId() . '").value',
                                                            'script' => true)))) ?> (<?php echo $reservation_part->getReservation()->getArrangementOrigName() ?>)
  </div>
  <?php if ($reservation_part->isInitial()): ?>
  <div class="container">
    <label>From:</label>
    <?php if ($reservation_part->getReservation()->canIncreaseDurationBefore(1)) echo link_to_remote(image_tag('icons/arrow_left.png', array(
            'onmouseover'=>tip_usage('Increase duration by one day at the beginning.'),
            'onmouseout'=>untip())), array('url' => 'calendar/changeDuration?what=increase&when=Before&id=' . $reservation_part->getReservation()->getId(),
                                           'update'=>'calendar-view',
                                           'script'=>true)) ?>
    <?php echo format_date($reservation_part->getReservation()->getTimeFrom()) ?>
    <?php if ($reservation_part->getReservation()->canDecreaseDuration(1)) echo link_to_remote(image_tag('icons/arrow_right.png', array(
            'onmouseover'=>tip_usage('Decrease duration by one day at the beginning.'),
            'onmouseout'=>untip())), array('url' => 'calendar/changeDuration?what=decrease&when=Before&id=' . $reservation_part->getReservation()->getId(),
                                           'update'=>'calendar-view',
                                           'script'=>true)) ?>
    (<?php echo format_date($reservation_part->getReservation()->getTimeFromOrig()) ?>)
  </div>
  <div class="container">
    <label>To:</label>
    <?php if ($reservation_part->getReservation()->canDecreaseDuration(1)) echo link_to_remote(image_tag('icons/arrow_left.png', array(
            'onmouseover'=>tip_usage('Decrease duration by one day at the end.'),
            'onmouseout'=>untip())), array('url' => 'calendar/changeDuration?what=decrease&when=After&id=' . $reservation_part->getReservation()->getId(),
                                           'update'=>'calendar-view',
                                           'script'=>true)) ?>
    <?php echo format_date($reservation_part->getReservation()->getTimeTo()) ?>
    <?php if ($reservation_part->getReservation()->canIncreaseDurationAfter(1)) echo link_to_remote(image_tag('icons/arrow_right.png', array(
            'onmouseover'=>tip_usage('Increase duration by one day at the end.'),
            'onmouseout'=>untip())), array('url' => 'calendar/changeDuration?what=increase&when=After&id=' . $reservation_part->getReservation()->getId(),
                                           'update'=>'calendar-view',
                                           'script'=>true)) ?>
    (<?php echo format_date($reservation_part->getReservation()->getTimeToOrig()) ?>)
  </div>
  <div class="container">
    <label>Arrival time:</label>
    <?php echo select_tag('arrival-time-' . $reservation_part->getId(),  options_for_select(rsCommon::getArrivalTimesNamesForSelect(true), $reservation_part->getReservation()->getArrivalTime()), array(
                        'onchange' => remote_function(array(
          'update' => 'reservation-buttons-' . $reservation_part->getId(),
          'url' => 'calendar/changeArrivalTime?id=' . $reservation_part->getId(),
          'with' => '"arrival_time="+$("arrival-time-' . $reservation_part->getId() . '").value',
          'script' => true)))) ?>
    (<?php echo $reservation_part->getReservation()->getArrivalTimeOrigName() ?>)
  </div>
  <div class="container" style="text-align: center">
    Notes (editable, click on the disk to save)

    <?php echo textarea_tag('notes-' . $reservation_part->getId(), $reservation_part->getReservation()->getNotes(), array('style' => 'float:right', 'rows' => 6, 'style' => 'width: 90%;')) ?>

    <ul class="vertical-menu" style="width: 95%">
      <li>
        <a href="#"><?php echo image_tag('icons/save.png', array('onclick' => remote_function(array(
          'update' => 'reservation-buttons-' . $reservation_part->getId(),
          'url' => 'calendar/saveNotes?id=' . $reservation_part->getId(),
          'with' => '"notes="+$("notes-' . $reservation_part->getId() . '").value',
          'script' => true)))) ?></a>
      </li>
    </ul>
  </div>
  <?php endif ?>