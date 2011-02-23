<?php
use_helper('Date', 'Tip');
$month_to = strftime('%B %Y', $days[count($days) -1]);
$month_from = strftime('%B %Y', $days[0])
?>

<table width="100%" cellpadding="0" cellspacing="0" border="1">
  <thead id="assigned-header">
    <tr>
      <th id="calendar-cross"><?php echo $month_from ?>
        <?php if ($month_from != $month_to): ?>
        <br />&darr;<br /><?php echo $month_to ?>
        <?php endif ?>
      </th>
      <?php foreach ($days as $id => $day): ?>
      <th onmouseover="<?php echo tip_usage(strftime('%A', $day) . "\n" . format_date($day)) ?>" onmouseout="<?php echo untip() ?>" class="calendar-day" id="d-<?php echo $id ?>">
        <?php echo rsCalendar::isCompact() ? strftime('%d', $day) : strftime('%A', $day) . '<br />' . format_date($day) ?>
      </th>
      <?php endforeach ?>
    </tr>
  </thead>
  <!-- Le righe delle camere dove si vedono le parti assegnate -->
  <tbody id="assigned-body">
    <?php foreach($rooms as $id => $room): ?>
    <tr id="room-<?php echo $id ?>">
      <?php include_partial('row', array(
        'title' => $room,
        'row_elements' => $assigned_rows[$id]
        )
      ); ?>
    </tr>
    <?php echo drop_receiving_element('room-' . $id, array(
      'update' => array(
        'success' => 'calendar-view',
        'failure' => 'calendar-error'
        ),
      'url' => 'calendar/move?room_id=' . $id,
      'script' => true,
      'accept' => 'calendar-reservation'
      )
    ) ?>
    <?php endforeach ?>
  </tbody>
  <thead id="candidates-header">
    <tr>
      <th colspan="<?php echo count($days) + 1 ?>">
        Unassigned reservations within considered time interval, one per row. <a href="#"><?php echo image_tag('icons/help.png', array('onmouseover' => tip_usage("Drag assigned reservations on this row to unassign.\nDrag unassigned reservations on desired room row to assign.\nSe non è libero tutto lo spazio richiesto, la prenotazione sarà spezzata."), 'onmouseout' => untip())) ?></a>
      </th>
    </tr>
  </thead>
  <?php echo drop_receiving_element('candidates-header', array(
    'update' => 'calendar-view',
    'url' => 'calendar/unassign',
    'script' => true,
    'accept' => 'calendar-reservation'
    )
  ) ?>
  <tbody id="candidates-body">
    <?php include_partial('candidates', array(
      'candidates' => $candidate_rows
      )
    ) ?>
  </tbody>
  <?php echo drop_receiving_element('candidates-body', array(
    'update' => 'calendar-view',
    'url' => 'calendar/unassign',
    'script' => true,
    'accept' => 'calendar-reservation'
    )
  ) ?>
</table>
