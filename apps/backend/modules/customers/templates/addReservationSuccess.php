<?php use_helper('Form', 'Javascript', 'Date', 'Partial', 'Validation') ?>


<div class="popup-title">
    <ul class="horizontal-menu">
        <li style="text-align: left; float: left">
            Add reservation to "<?php echo $customer->getName() ?> <?php echo $customer->getSurname() ?>"
        </li>
        <li>
            <a href="#"><?php echo image_tag('icons/cross.png', array('onclick' => '$(\'add-reservation-popup-' . $customer->getId() . '\').style.display = \'none\'')) ?></a>
        </li>
    </ul>
</div>

<?php
$js_hide_arrangements = '';
for ($i = 0; $i < count($room_types); $i++)
{
    $js_hide_arrangements.= '$("' . $customer->getId() . '_' . $i . '_arrangement").style.display = "none";'."\n";
}
?>

<?php echo form_remote_tag(array(
    'update' => 'selected-rooms-' . $customer->getId(),
    'url' => 'customers/addRoom?customer_id=' . $customer->getId(),
    'script' => true)) ?>
<table class="table-form" border="0" cellpadding="0" cellspacing="0">
    <?php echo $room_form['room_type']->renderRow(array(
                    'onchange' => $js_hide_arrangements . '$(' . $customer->getId() . '+ "_" + this.value + "_arrangement").style.display = "block"'//'updateVisibleArrangements' . $customer->getId() . '(this.value)'
        )) ?>
    <tr>
        <th>
            <label>Arrangement:</label>
        </th>
        <td>
            <?php foreach ($room_types as $id => $time): ?>
            <?php echo $room_form[$id . '_arrangement']->render(array(
            'style' => 'display: ' . ($id == 0 ? 'block' : 'none'),
            'id' => $customer->getId() . '_' . $id . '_arrangement'
                )) ?>
            <?php endforeach ?>
        </td>
    </tr>
    <?php echo $room_form['room_count']->renderRow() ?>
    <tr>
        <td />
        <td align="left">
            <input type="image" src="<?php echo image_path('icons/add_reservation.png', true) ?>" />
        </td>
    </tr>
</table>
</form>

<span id="selected-rooms-<?php echo $customer->getId() ?>">
    <?php include_partial('selectedRooms', array('rooms' => $rooms, 'customer' => $customer, 'errors' => isset($room_error) ? $room_error : false)) ?>
</span>


<?php echo form_remote_tag(array(
  'complete' => 'refreshInterface(' . $customer->getId() . ', json)',
  'url' => 'customers/saveReservation?mode=' . $mode . '&id=' . $customer->getId(),
  'script' => true
    ))
?>
<table class="table-form" border="0" cellpadding="0" cellspacing="0">
    <?php echo $reservation_form ?>
    <tr>
        <td />
        <td align="left">
            <input type="image" src="<?php echo image_path('icons/save.png', true) ?>" />
        </td>
    </tr>
</table>
</form>
