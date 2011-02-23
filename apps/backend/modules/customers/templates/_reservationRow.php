<?php use_helper('Date', 'Javascript', 'Tip') ?>

<tr class="<?php echo rsCommon::getTrClass() ?>">
    <td>
        <?php echo $reservation->getId() ?>
    </td>
    <td>
        <?php echo link_to($tmp = format_date($reservation->getTimeFrom()), 'calendar/goto?when=' . $tmp) ?>
    </td>
    <td>
        <?php echo link_to($tmp = format_date($reservation->getTimeTo()), 'calendar/goto?when=' . $tmp) ?>
    </td>
    <td>
        <?php echo count($reservation->getReservationParts()) ?>
    </td>
    <td>
        <?php echo $reservation->isCompletelyAssigned() ? 'yes' : 'no' ?>
    </td>
    <td>
        <?php if (strlen($reservation->getNotes()) != 0) echo image_tag('icons/notes.png', array('onmouseover' => tip_usage($reservation->getNotes()), 'onmouseout' => untip())) ?>
    </td>
    <td>
        <?php echo link_to_remote(
            image_tag(
                        'icons/cross.png',
                array(
                            'onmouseover' => tip_usage('Delete this reservation'),
                            'onmouseout' => untip())),
            array(
                         'url' => 'customers/deleteReservation?customer_id=' . $reservation->getCustomerId() . '&id=' . $reservation->getId(),
                         'update' => 'reservations-list',
                         'confirm' => 'Are you sure? All data concerning this reservation are going to be lost for ever!',
                         'script' => true
            )) ?>
    </td>
</tr>