<?php use_helper('Date', 'Javascript', 'Tip') ?>

<h2>Reservations view</h2>

<span id="reservations-view">
  <?php include_partial('reservationsTable', array('pager' => $pager, 'room_types' => $room_types, )) ?>
</span>