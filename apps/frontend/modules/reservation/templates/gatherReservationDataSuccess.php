<?php use_helper('DateForm', 'Partial', 'Javascript', 'Validation') ?>

<?php if ($sf_request->hasErrors()): ?>
<?php include_partial('errorsFound') ?>
<?php endif ?>

<?php if ($header): ?>
<h2><?php echo __('Reservation policy') ?></h2>
<p class="container">
  <?php echo $header ?>
</p>
<?php endif ?>

<?php
$js_row = '';
for ($i = 0; $i < count($room_arrangements); $i++)
{
  $js_row.= '$("' . $i . '_arrangement").style.display = \'none\';';
};
echo javascript_tag('
function updateVisibleArrangements(room_type)
{
  ' . $js_row . '
  $($("room_type")[room_type].value + "_arrangement").style.display = \'block\';
}

function updateDateTo()
{
if ($("date_to").value == "")
  $("date_to").value = $("date_from").value;
}

'); ?>


<?php echo form_remote_tag(array('update' => 'selected_rooms', 'url' => '/reservation/addRoom')) ?>
<h2><?php echo __('Rooms details') ?>:</h2>
<h3><?php echo __('Select room') ?>:</h3>
<div class="container">
  <?php echo label_for('room_type', __('Room type') . ':') ?><?php echo select_tag('room_type', options_for_select($room_types), array('onchange' => 'updateVisibleArrangements(this.selectedIndex);')) ?>
</div>
<?php foreach ($room_arrangements as $key => $options): ?>
<div class="container" id="<?php echo $key . '_arrangement' ?>" <?php echo ($key != 0) ? 'style="display: none"' : '' ?>>
  <?php echo label_for($key . '_arrangement_options', __('Room arrangement') . ':') ?><?php echo select_tag($key . '_arrangement_options', $room_arrangements[$key]) ?>
</div>
<?php endforeach ?>
<div id="div_room_count" class="container">
  <?php echo label_for('room_count', __('Room count') . ':') ?>
  <?php echo select_tag('room_count', $room_count_options) ?>
</div>
<div class="container">
  <?php echo form_error('rooms') ?>
  <?php echo label_for('commit', '&nbsp;') ?>
  <?php echo submit_tag(__('Add')) ?>
</div>
<h3><?php echo __('Selected rooms') ?>:</h3>
<div id="selected_rooms"><?php include_partial('selectedRooms', array('rooms' => $rooms, 'editable' => true)) ?></div>
</form>

<?php echo form_tag('/reservation/saveReservationData', array('name' => 'main')) ?>
<h2><?php echo __('Stay details') ?></h2>
<div class="container">
  <?php echo form_error('date_from') ?>
  <?php echo label_for('date_from', __('From') . ':') ?>
  <?php echo input_date_tag('date_from', null, array('value' => $date_from, 'onchange' => 'updateDateTo()', 'rich' => true, 'readonly' => true, 'calendar_button_img' => 'icons/calendar.png')) ?>
</div>
<div class="container">
  <?php echo form_error('date_to') ?>
  <?php echo label_for('date_to', __('To') . ':') ?>
  <?php echo input_date_tag('date_to', null, array('value' => $date_to, 'rich' => true, 'readonly' => true, 'calendar_button_img' => 'icons/calendar.png')) ?>
</div>
<div class="container">
  <?php echo label_for('arrival_time', __('Expected arrival time') . ':') ?>
  <?php echo select_tag('arrival_time', options_for_select($arrival_time_options, $arrival_time)) ?>
</div>
<div class="container">
  <?php echo label_for('commit', '&nbsp;') ?>
  <?php echo submit_tag(__('Proceed to insert personal data')) ?>
</div>
</form>
