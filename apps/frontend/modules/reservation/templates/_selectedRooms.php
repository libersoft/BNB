<?php use_helper('Javascript') ?>
<?php if (count($rooms)): ?>

<table class="rooms" border="0" cellspacing="0">
  <tr>
    <th>
      <?php echo __('Room type') ?>
    </th>
    <th>
      <?php echo __('Room arrangement') ?>
    </th>
    <th>
      <?php echo __('Room count') ?>
    </th>
    <?php if (isset($editable) && $editable == true): ?>
    <th>
      &nbsp;
    </th>
    <?php endif ?>
  </tr>
  <?php foreach ($rooms as $id => $room): ?>
  <tr>
    <td>
      <?php echo $room['type'] ?>
    </td>
    <td>
      <?php echo $room['arrangement']['description'] ?>
    </td>
    <td>
      <?php echo $room['count'] ?>
    </td>
    <?php if (isset($editable) && $editable == true): ?>
    <td>
      <?php echo link_to_remote(__(' Remove this room(s) ') . image_tag('icons/cross.png'), array('update' => 'selected_rooms', 'url' => 'reservation/removeRoom?id=' . ($id + 1))) ?>
    </td>
    <?php endif ?>
  </tr>
  <?php endforeach ?>
</table>

<?php else: ?>

<p class="container"><?php echo __('Please use the upper form to populate your reservation with rooms.') ?></p>

<?php endif ?>
