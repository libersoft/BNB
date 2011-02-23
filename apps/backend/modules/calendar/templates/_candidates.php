<!-- Il corpo della parte di tabella che contiene le righe con le parti assegnabili -->
<?php foreach ($candidates as $id => $candidate): ?>
<tr id="unassigned-row-<?php echo $id ?>">
  <?php include_partial('row', array(
    'title' => $candidate['title'],
    'row_elements' => $candidate['cells']
    )
  ) ?>
</tr>
<?php endforeach ?>
