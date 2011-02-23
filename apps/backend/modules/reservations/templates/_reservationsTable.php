<?php echo use_helper('Tip', 'Form') ?>

<div style="width: 50%; clear:both">
  <strong><?php echo $pager->getNbResults() ?></strong> reservations found.<br />
  Displaying reservations <strong><?php echo $pager->getFirstIndice() ?></strong> to <strong><?php echo $pager->getLastIndice() ?></strong>.<br /><br />
</div>


<!--<div style="width: 50%; float:left; clear: right; text-align: right;">
<?php echo label_for('reservations-filter', 'Add filter filter:') ?> <?php echo select_tag('reservations-filter',
  options_for_select($available_filters, array('include_custom' => '-- select filter di add --')), array('onchange' =>'')) ?>
</div>-->

<?php include_partial('pager', array('pager' => $pager)) ?>

<?php if ($pager->getNbResults() > 0): ?>

<table width="100%" cellspacing="0">
  <tr class="header">
    <td>Id</td>
    <td>Customer</td>
    <td>From</td>
    <td>To</td>
    <td>Room type</td>
    <td>Room arrangement</td>
    <td>Assigned room</td>
    <td>Change room</td>
    <td>Notes</td>
    <td>Operations</td>
  </tr>
  <?php foreach ($pager->getResults() as $reservation): ?>
  <tr class="<?php echo rsCommon::getTrClass() ?>">
    <td><?php echo $reservation->getId() ?></td>
    <td><?php echo link_to($reservation->getCustomer()->getName(). ' ' . $reservation->getCustomer()->getSurname(), 'customers/show?id=' . $reservation->getCustomer()->getId()) ?></td>
    <td><?php echo link_to($tmp = format_date($reservation->getTimeFrom()), 'calendar/goto?when=' . $tmp) ?></td>
    <td><?php echo link_to($tmp = format_date($reservation->getTimeTo()), 'calendar/goto?when=' . $tmp) ?></td>
    <td><?php echo $reservation->getTypeName() ?></td>
    <td id="arrangement-td-<?php echo $reservation->getId() ?>">
      <?php include_partial('arrangementSelect', array('reservation' => $reservation)) ?>
    </td>
    <td><?php echo $reservation->getRoom() === NULL ? '<span style="color: red">-- none --</span>': $reservation->getRoomName() ?></td>
    <td>
      <?php $assignable = $reservation->getAssignableRoomsForSelect();
      echo select_tag('room-' . $reservation->getId(),  options_for_select($assignable, NULL, array('include_custom' => count($assignable) > 0 ? '-- select to change --' : '-- none available --')), array('onchange' => remote_function(array(
                                        'update'   => 'reservations-view',
                                        'url'      => 'reservations/assign?page=' . $pager->getPage() . '&id=' . $reservation->getId(),
                                        'with' => '"room="+$("room-' . $reservation->getId() . '").value',
                                        'script' => true)))) ?>
    </td>
    <td>
      <?php if (strlen($reservation->getNotes()) != 0) echo image_tag('icons/notes.png', array('onmouseover' => tip_usage($reservation->getNotes()), 'onmouseout' => untip())) ?>
    </td>
    <td>
      <?php echo link_to(image_tag('icons/edit.png', array('onmouseover' => tip_usage('Edit reservation.'), 'onmouseout' => untip())), 'reservations/edit?id='.$reservation->getId()) ?>
      <?php echo link_to_remote(image_tag('icons/cross.png', array('onmouseover' => tip_usage('Remove reservation.'), 'onmouseout' => untip())), array(
                                            'update' => 'reservations-view',
                                            'url' => 'reservations/delete?page=' . $pager->getPage() . '&id=' . $reservation->getId(),
                                            'script' => true),
        array('confirm' => 'Reservation data is going to be lost forever, are you sure?')) ?>
      <?php if ($reservation->getRoom() !== NULL) echo ' ' . link_to_remote(image_tag('icons/unassign.png', array('onmouseover' => tip_usage('Remove room association.'), 'onmouseout' => untip())), array('update' => 'reservations-view',
                        'url' => 'reservations/unassign?page=' . $pager->getPage() . '&id=' . $reservation->getId(),
                        'script' => true)) ?>
    </td>
  </tr>
  <?php endforeach ?>
</table>

<?php endif ?>

<?php include_partial('pager', array('pager' => $pager)) ?>
