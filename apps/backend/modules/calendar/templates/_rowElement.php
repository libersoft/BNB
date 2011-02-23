<?php use_helper('Tip', 'Javascript', 'Form') ?>
<?php if ($element['reservation_part']): ?>
<td class="calendar-cell" colspan="<?php echo $element['cells_count'] ?>" id="reservation-block-<?php echo $element['reservation_part']->getId() ?>">
    <?php include_partial('reservationCell', array('element' => $element, 'candidate' => isset($candidate) ? $candidate : false)) ?>
</td>
<?php else: ?>
<?php for ($i = 0; $i < $element['cells_count']; $i++): ?>
<td class="calendar-cell"></td>
<?php endfor ?>
<?php endif ?>