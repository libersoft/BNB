<!-- Il rettangolo verde con la parte -->
<div onmouseout="reservation_out('.reservation-<?php echo $element['reservation_part']->getReservation()->getId() ?>')" onmouseover="reservation_over('.reservation-<?php echo $element['reservation_part']->getReservation()->getId() ?>');" class="reservation-<?php echo $element['reservation_part']->getReservation()->getId() ?> calendar-reservation<?php echo $element['classes'] ?>" id="reservat-<?php echo $element['reservation_part']->getId() ?>">
  <ul class="horizontal-menu" id="reservation-buttons-<?php echo $element['reservation_part']->getId() ?>">
    <?php include_partial('reservationMenu', array('candidate' => isset($candidate) ? $candidate : false, 'reservation_part' => $element['reservation_part'])) ?>
  </ul>
</div>
<?php echo draggable_element('reservat-' . $element['reservation_part']->getId(), array('scroll' => 'window', 'revert' => true, 'constraint' => '\'vertical\'')) ?>

<!-- Il popup della cella con la parte -->
<div class="popup" id="popup-reservation-<?php echo $element['reservation_part']->getId() ?>">
  <?php include_partial('reservationPopup', array(
    'row_id' => $element['reservation_part']->getRoom(),
    'reservation_part' => $element['reservation_part']
    )
  ) ?>
</div>
<?php echo draggable_element('popup-reservation-' . $element['reservation_part']->getId(),
  array(
    'starteffect' => false,
    'endeffect' => false,
  )
) ?>
