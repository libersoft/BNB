ID: <?php echo $id ?><?php echo $date ?>

E-mail: <?php echo $email ?>

Provide credit card: <?php echo $cc_number ? 'yes' : 'no' ?>


<?php echo $name ?> <?php echo $surname ?>

<?php if ($address) echo $address ?>

<?php if ($zip) echo $zip ?> <?php if ($city) echo $city ?> <?php if ($state) echo $state ?> <?php if ($country) echo format_country($country, 'en') ?>

<?php if ($phone): ?>Home telephone: <?php echo $phone ?><?php endif ?>

<?php if ($mobile): ?>Mobile telephone: <?php echo $mobile ?><?php endif ?>

<?php if ($fax): ?>Fax: <?php echo $fax ?><?php endif ?>


--

- Request details:

From: <?php echo $date_from ?>

To: <?php echo $date_to ?>

Arrival time: <?php echo $arrival_time ?>


- Selected rooms:

<?php foreach ($rooms as $room): ?>
<?php echo $room['count'] ?> <?php echo $room['type'] ?> (<?php echo $room['arrangement']['description'] ?>)
<?php endforeach ?>

<?php if (!empty($comments)): ?>- Comments:
<?php echo $comments ?><?php endif ?>


Please check it on < <?php echo $panel_url ?> > as soon as possibile.
This is an automated e-mail sent to you by the BnB booking system.
