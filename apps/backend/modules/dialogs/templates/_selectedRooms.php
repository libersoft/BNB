<?php use_helper('Javascript') ?>

<?php if (count($rooms)): ?>

<table class="table-form" border="0" cellspacing="0">
    <tr>
        <th>
            Room type
        </th>
        <th>
            Room arrangement
        </th>
        <th>
            Room count
        </th>
        <th>
            &nbsp;
        </th>
    </tr>
    <?php foreach ($rooms as $id => $room): ?>
    <tr>
        <td>
            <?php echo $room['type'] ?>
        </td>
        <td>
            <?php echo $room['arrangement'] ?>
        </td>
        <td>
            <?php echo $room['count'] ?>
        </td>
        <td>
            <?php echo link_to_remote(' Remove this room(s) ' . image_tag('icons/cross.png'), array(
                                        'update' => 'selected-rooms-' . $customer->getId(),
                                        'url' => 'dialogs/removeRoom?customer_id=' . $customer->getId() . '&room_id=' . ($id + 1))) ?>
        </td>
    </tr>
    <?php endforeach ?>
</table>

<?php else: ?>

<p class="container"<?php if ($errors): ?> style="color: red"<?php endif ?>>Use upper form to select new user's reservations.</p>

<?php endif ?>