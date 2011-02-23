<?php use_helper('Partial', 'Javascript') ?>

<h2><?php echo __('Confirmation required') ?></h2>
  <div id="confirm_box">
    <?php include_partial('confirmForm') ?>
  </div>

<h2><?php echo __('Summary') ?></h2>
<h3><?php echo __('Your reservation details') ?></h3>
  <?php include_partial('selectedRooms', array('rooms' => $rooms, 'editable' => false)) ?>
  <div class="container">
    <span class="summary-label"><?php echo __('From:') ?></span><?php echo $date_from ?>
  </div>
  <div class="container">
    <span class="summary-label"><?php echo __('To:') ?></span><?php echo $date_to ?>
  </div>
  <div class="container">
    <span class="summary-label"><?php echo __('Total stay:') ?></span><?php echo $total_stay ?> <?php echo __('night(s)') ?>
  </div>
  <div class="container">
    <span class="summary-label"><?php echo __('Expected arrival time:') ?></span><?php echo $arrival_time ?>
  </div>
<h3><?php echo __('Your personal details') ?></h3>
  <div class="container">
    <span class="summary-label"><?php echo __('Name:') ?></span><?php echo $name ?>
  </div>
  <div class="container">
    <span class="summary-label"><?php echo __('Surname:') ?></span><?php echo $surname ?>
  </div>
  <div class="container">
    <span class="summary-label"><?php echo __('E-mail:') ?></span><?php echo $email ?>
  </div>
  <?php if (!empty($address)): ?><div class="container">
    <span class="summary-label"><?php echo __('Address:') ?></span><?php echo $address ?>
  </div><?php endif ?>
  <?php if (!empty($zip)): ?><div class="container">
    <span class="summary-label"><?php echo __('Postal code:') ?></span><?php echo $zip ?>
  </div><?php endif ?>
  <?php if (!empty($city)): ?><div class="container">
    <span class="summary-label"><?php echo __('City:') ?></span><?php echo $city ?>
  </div><?php endif ?>
  <?php if (!empty($country)): ?><div class="container">
    <span class="summary-label"><?php echo __('Country:') ?></span><?php echo format_country($country) ?>
  </div><?php endif ?>
  <?php if (!empty($state)): ?><div class="container">
    <span class="summary-label"><?php echo __('State:') ?></span><?php echo $state ?>
  </div><?php endif ?>
  <?php if (!empty($phone)): ?><div class="container">
    <span class="summary-label"><?php echo __('Home telephone:') ?></span><?php echo $phone ?>
  </div><?php endif ?>
  <?php if (!empty($mobile)): ?><div class="container">
    <span class="summary-label"><?php echo __('Mobile telephone:') ?></span><?php echo $mobile ?>
  </div><?php endif ?>
  <?php if (!empty($fax)): ?><div class="container">
    <span class="summary-label"><?php echo __('FAX number:') ?></span><?php echo $fax ?>
  </div><?php endif ?>

<?php if (!empty($cc_number)): ?>
<h3><?php echo __('Credit card informations') ?></h3>
  <div class="container">
    <span class="summary-label"><?php echo __('Credit card type:') ?></span><?php echo $cc_type ?>
  </div>
  <div class="container">
    <span class="summary-label"><?php echo __('Credit card expiry date:') ?></span><?php echo $cc_expire_month ?>/<?php echo $cc_expire_year ?>
  </div>
  <div class="container">
    <span class="summary-label"><?php echo __('Credit card number:') ?></span>************<?php echo $cc_number ?>
  </div>
  <div class="container">
    <span class="summary-label"><?php echo __('Card verification value:') ?></span><?php echo $cc_securcode ?>
  </div>
<?php endif ?>

<?php if (!empty($comments)): ?>
<h3><?php echo __('Comments') ?></h3>
  <p class="container"><?php echo $comments ?></p>
<?php endif ?>