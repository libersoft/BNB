<?php echo __('Thank you for Your Reservation Request!') ?>


<?php echo __('Name') . ':' ?> <?php echo $name ?>

<?php echo __('Surname') . ':' ?> <?php echo $surname ?>


<?php echo __('From') . ':' ?> <?php echo $date_from ?>

<?php echo __('To') . ':' ?> <?php echo $date_to ?>


<?php echo __('Rooms') . ':' ?>
<?php foreach ($rooms as $room): ?>
<?php endforeach ?>


---
<?php echo __('This is an automated email sent to you from the BnB reservatin system, please do not reply. You will be contacted soon by an our staff member to finalize your reservation request') . '.' ?>