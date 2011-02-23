<?php
use_helper('Tip', 'Javascript', 'Date');
$compact = rsCalendar::isCompact()
?>

<li><?php $text = '';
    $text = 'Name: ' . $reservation_part->getCustomer()->getName() . "\n";
    $text.= 'Surname: ' . $reservation_part->getCustomer()->getSurname() . "\n";
    $text.= 'Room arrangement: ' . $reservation_part->getArrangementDescription() . "\n";
    $text.= 'Room type: ' . $reservation_part->getTypeName() . "\n";
    $text.= 'Arrival time: ' . $reservation_part->getReservation()->getArrivalTimeDescription() . "\n";
    $text.= 'Reservation from: ' . format_date($reservation_part->getReservation()->getTimeFrom()) . "\n";
    $text.= 'Reservation to: ' . format_date($reservation_part->getReservation()->getTimeTo()) . "\n";
    $text.= 'Fragment from: ' . format_date($reservation_part->getTimeFrom()) . "\n";
    $text.= 'Fragment to: ' . format_date($reservation_part->getTimeTo()) . "\n" ?>
    <?php echo image_tag('icons/info.png', array('onmouseover' => tip_info($text), 'onmouseout' => untip())) ?>
</li>
<li>
    <?php echo $reservation_part->getCustomer()->getSurname() ?>
    <?php if (!$compact && $reservation_part->getArrangement() !== NULL) echo ' &bull; ' . $reservation_part->getArrangementName() ?>
    <?php if (!$compact && $reservation_part->isInitial()): ?>
    <?php if ($reservation_part->isInitial()) echo ' &bull; ' . $reservation_part->getReservation()->getArrivalTimeName() ?>
    <?php if ($notes = $reservation_part->getReservation()->getNotes()): ?> &bull;
    <span <?php if (strlen($notes) > sfConfig::get('app_calendar_view_note_preview_length')): ?>onmouseover="<?php echo tip_note($reservation_part->getReservation()->getNotes()) ?>" onmouseout="<?php echo untip() ?>" <?php endif ?>class="note-inline">
        <?php echo substr($notes, 0, sfConfig::get('app_calendar_view_note_preview_length')) ?>
        <?php if (strlen($notes) > sfConfig::get('app_calendar_view_note_preview_length')): ?>
        ...
        <?php endif ?>
    </span>
    <?php endif ?>
    <?php endif ?>
</li>
<?php if (!$compact): ?>
<li>
    <a href="#"><?php echo image_tag('icons/edit.png', array('onclick' => 'popup(\'popup-reservation-' . $reservation_part->getId() . '\');')) ?></a>
</li>
<?php if ($reservation_part->getRoom() !== NULL): ?>
<li>
    <?php echo link_to_remote(image_tag('icons/unassign.png', array('onmouseover' => tip_usage('Remove room association'), 'onmouseout' => untip())), array(
                                                  'update' => 'calendar-view',
                                                  'url' => 'calendar/unassign?id=' . $reservation_part->getId(),
                                                  'script' => true)) ?>
</li>
<?php endif ?>

<?php if ($reservation_part->isFinal()): ?>
<li>
    <?php echo link_to_remote(image_tag('icons/cross.png', array('onmouseover' => tip_usage('Delete reservation'), 'onmouseout' => untip())), array(
                                                  'update' => 'calendar-view',
                                                  'url' => 'calendar/delete?id=' . $reservation_part->getReservation()->getId(),
                                                  'script' => true), array('confirm' => 'Reservation data will be lost for ever, are you sure?')) ?>
</li>
<?php endif ?>
<?php endif ?>
