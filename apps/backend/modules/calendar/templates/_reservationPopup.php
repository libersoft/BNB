<?php
use_helper('Form', 'Javascript', 'Date');
$dom_id = ($reservation_part->getRoom() === NULL) ? 'calendar-view' : 'room-'.$row_id;
$row_id_param = $row_id ? 'row_id=' . $row_id . '&' : '';
?>

<!-- Decorazione della finestra del popup -->
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
<!-- Breve avviso per spiegare la mancanza di opzioni se la parte non è iniziale -->
<div class="container">
  Please refer to the first part of the reservation to modify global preferences such as notes or
  arrival time.
</div>
<?php endif ?>

<!-- Modifica la tipologia stanza -->
<div class="container">
  <label>Room type:</label>
  <?php echo $reservation_part->getTypeName() ?> (<?php echo $reservation_part->getReservation()->getTypeOrigName() ?>)
</div>

<!-- Modifica la sistemazione stanza -->
<div class="container">
  <label>Room arrangement:</label>
  <?php echo select_tag('arrangement-' . $reservation_part->getId(),
    options_for_select($reservation_part->getArrangementsForSelect(),
    $reservation_part->getArrangementForSelect()),
    array(
      'onchange' => remote_function(array(
        'update' => 'reservation-buttons-' . $reservation_part->getId(),
        'url' => 'calendar/changeArrangement?id=' . $reservation_part->getId(),
        'with' => '"arrangement="+$("arrangement-' . $reservation_part->getId() . '").value',
        'script' => true
        )
      )
    )) ?> (<?php echo $reservation_part->getReservation()->getArrangementOrigName() ?>)
</div>

<!-- Spezza la parte -->
<?php if ($reservation_part->isSplittable()): ?>
<div class="container">
  <label>Split part after:</label>
  <?php echo select_tag('split-day-' . $reservation_part->getId(),
    options_for_select($reservation_part->getAvailableSplitDays()),
    array(
      'onchange' => remote_function(array(
      'update' => 'calendar-view',
      'url' => 'calendar/split?part_id=' . $reservation_part->getId(),
      'with' => '"split_day="+$("split-day-' . $reservation_part->getId() . '").value',
      'script' => true
      )
    )
  )) ?> day(s)
</div>
<?php endif ?>

<!-- Modifica la durata all'inizio -->
<?php if ($reservation_part->isInitial()): ?>
<div class="container">
  <label>From:</label>
  <?php if ($reservation_part->getReservation()->canIncreaseDurationBefore(1)): ?>
  <?php echo link_to_remote(image_tag('icons/arrow_left.png', array(
    'onmouseover'=>tip_usage('Increase duration by one day at the beginning.'),
    'onmouseout'=>untip())), array(
      'url' => 'calendar/changeDuration?' . $row_id_param . 'what=increase&when=Before&id=' . $reservation_part->getId(),
      'update'=> $dom_id,
      'script'=>true
    )
  ) ?>
  <?php endif ?>

  <?php echo format_date($reservation_part->getReservation()->getTimeFrom()) ?>
  
  <?php if ($reservation_part->getReservation()->canDecreaseDuration(1)): ?>
  <?php $durat = $reservation_part->getDuration();
  echo link_to_remote(image_tag('icons/arrow_right.png', array(
    'onmouseover'=>tip_usage('Decrease duration by one day at the beginning.'),
    'onmouseout'=>untip())), array(
      /*
       * Per le parti lunghe 1 che andranno eleminate, aggiorniamo tutto
       * il calendario per evitare righe non aggiornate nella visualizzazione.
       * Inoltre row_id < 0 significa "non ho idea di quali righe siano coinvolte,
       * dammi tutto".
       */
      'url' => 'calendar/changeDuration?' . $row_id_param . 'id=' . $reservation_part->getId() . '&what=decrease&when=Before',
      'update' => $durat == 1 ? 'calendar-view' : $dom_id,
      'script' => true
    )
  ) ?>
  <?php endif ?>

  (<?php echo format_date($reservation_part->getReservation()->getTimeFromOrig()) ?>)
</div>
<?php endif ?>

<!-- Modifica la durata alla fine -->
<?php if ($reservation_part->isFinal()): ?>
<div class="container">
  <label>To:</label>
  <?php if ($reservation_part->getReservation()->canDecreaseDuration(1)): ?>
  <?php $durat = $reservation_part->getDuration();
  echo link_to_remote(image_tag('icons/arrow_left.png', array(
    'onmouseover'=>tip_usage('Decrease duration by one day at the end.'),
    'onmouseout'=>untip())), array(
      'url' => 'calendar/changeDuration?' . $row_id_param . 'id=' . $reservation_part->getId() . '&what=decrease&when=After',
      'update'=> $durat == 1 ? 'calendar-view' : $dom_id,
      'script'=>true
    )
  );

//  echo '!!!!!calendar/changeDuration?id=' . $reservation_part->getId() . ($row_id ? '&row_id=' . $row_id : '') . '&what=decrease&when=After';

  ?>
  <?php endif ?>

  <?php echo format_date($reservation_part->getReservation()->getTimeTo()) ?>
  
  <?php if ($reservation_part->getReservation()->canIncreaseDurationAfter(1)): ?>
  <?php echo link_to_remote(image_tag('icons/arrow_right.png', array(
    'onmouseover'=>tip_usage('Increase duration by one day at the end.'),
    'onmouseout'=>untip())), array(
      /*
       * Per le parti lunghe 1 che andranno eleminate, aggiorniamo tutto
       * il calendario per evitare righe non aggiornate nella visualizzazione.
       */
      'url' => 'calendar/changeDuration?'.$row_id_param.'what=increase&when=After&id=' . $reservation_part->getId(),
      'update'=> $dom_id,
      'script'=>true
    )
  ) ?>
  <?php endif ?>
  
  (<?php echo format_date($reservation_part->getReservation()->getTimeToOrig()) ?>)
</div>
<?php endif ?>

<!-- Modifica l'orario di arrivo previsto -->
<?php if ($reservation_part->isInitial()): ?>
<div class="container">
  <label>Arrival time:</label>
  <?php echo select_tag('arrival-time-' . $reservation_part->getId(),  options_for_select(rsCommon::getArrivalTimesNamesForSelect(true), $reservation_part->getReservation()->getArrivalTime()), array(
    'onchange' => remote_function(array(
      'update' => 'reservation-buttons-' . $reservation_part->getId(),
      'url' => 'calendar/changeArrivalTime?id=' . $reservation_part->getId(),
      'with' => '"arrival_time="+$("arrival-time-' . $reservation_part->getId() . '").value',
      'script' => true
      )
    )
  )) ?>
  (<?php echo $reservation_part->getReservation()->getArrivalTimeOrigName() ?>)
</div>

<!-- Modifica le note -->
<div class="container" style="text-align: center">
  Notes (editable, click on the disk to save)
  <?php echo textarea_tag('notes-' . $reservation_part->getId(), $reservation_part->getReservation()->getNotes(), array(
    'style' => 'float:right',
    'rows' => 6,
    'style' => 'width: 90%;',
    )
  ) ?>
  <ul class="vertical-menu" style="width: 95%">
    <li>
      <a href="#"><?php echo image_tag('icons/save.png', array(
      'onclick' => remote_function(array(
        'update' => 'reservation-buttons-' . $reservation_part->getId(),
        'url' => 'calendar/saveNotes?id=' . $reservation_part->getId(),
        'with' => '"notes="+$("notes-' . $reservation_part->getId() . '").value',
        'script' => true,
        ))
      )
    ) ?></a>
    </li>
  </ul>
</div>
<?php endif ?>