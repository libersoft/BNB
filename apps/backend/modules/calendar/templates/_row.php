<?php use_helper('Partial') ?>

<td class="calendar-room">
    <?php echo $title['name'] ?> (<?php echo $title['type'] ?>)
</td>
<?php foreach ($row_elements as $element): ?>
<?php include_partial('rowElement', array('element' => $element)) ?>
<?php endforeach ?>
