
<tr class="header">
    <td>Id</td>
    <td>From</td>
    <td>To</td>
    <td>Parts</td>
    <td>Completely assigned</td>
    <td>Notes</td>
    <td>Operations</td>
</tr>

<?php if (count($reservations)): ?>
<?php foreach ($reservations as $reservation): ?>
<?php include_partial('reservationRow', array('reservation' => $reservation)) ?>
<?php endforeach ?>

<?php else: ?>
<tr class="<?php echo rsCommon::getTrClass() ?>">
    <td colspan="7">
        <p style="text-align: center; padding: 1em">This customer has no reservations yet. You can add some clicking on the "plus" button.</p>
    </td>
</tr>
<?php endif ?>
