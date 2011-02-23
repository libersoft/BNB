<?php use_helper('DateForm', 'Partial', 'Validation') ?>


<?php if ($sf_request->hasErrors()): ?>
<?php include_partial('errorsFound') ?>
<?php endif ?>

<?php echo form_tag('/reservation/savePersonalData') ?>

<h2><?php echo __('Personal data') ?></h2>
<div class="container">
  <?php echo form_error('name') ?>
  <?php echo label_for('name', __('Name') . ': *') ?><?php echo input_tag('name') ?>
</div>
<div class="container">
  <?php echo form_error('surname') ?>
  <?php echo label_for('surname', __('Surname') . ': *') ?><?php echo input_tag('surname') ?>
</div>
<div class="container">
  <?php echo form_error('email') ?>
  <?php echo label_for('email', __('E-mail address') . ': *') ?><?php echo input_tag('email') ?>
</div>
<div class="container">
  <?php echo form_error('address') ?>
  <?php echo label_for('address', __('Address') . ': **') ?><?php echo input_tag('address') ?>
</div>
<div class="container">
  <?php echo form_error('zip') ?>
  <?php echo label_for('zip', __('Postal code') . ': **') ?><?php echo input_tag('zip') ?>
</div>
<div class="container">
  <?php echo form_error('city') ?>
  <?php echo label_for('city', __('City') . ': **') ?><?php echo input_tag('city') ?>
</div>
<div class="container">
  <?php echo form_error('country') ?>
  <?php echo label_for('country', __('Country') . ': **') ?><?php echo select_country_tag('country', NULL, array('include_blank' => true)) ?>
</div>
<div class="container">
  <?php echo form_error('state') ?>
  <?php echo label_for('state', __('State (if any)') . ':') ?><?php echo input_tag('state') ?>
</div>
<div class="container">
  <?php echo label_for('phone', __('Home telephone') . ':') ?><?php echo input_tag('phone') ?>
</div>
<div class="container">
  <?php echo label_for('mobile', __('Mobile telephone (possibly bring with you during your trip)') . ':') ?><?php echo input_tag('mobile') ?>
</div>
<div class="container">
  <?php echo label_for('fax', __('FAX number') . ':') ?><?php echo input_tag('fax') ?>
</div>
<p class="container">*&nbsp;<?php echo __('marked fields are mandatory') . '.' ?></p>
<p class="container">**&nbsp;<?php echo __('marked fields and credit card information are necessary only in case of confirmation') . '.' ?></p>
<h2><?php echo __('Credit card informations') ?></h2>
<p class="container">
  Only guarantee with credit card information when you want to confirm.<br />In this case, please make sure that you send the name and billing address of the card holder.
</p>
<div class="container">
  <?php echo label_for('cc_type', __('Credit card type') . ': **') ?><?php echo select_tag('cc_type', options_for_select($cc_type_options)) ?>
</div>

<div class="container">
  <?php echo label_for('cc_expire_month', __('Credit card expiry date') . ': **') ?><?php echo select_month_tag('cc_expire_month', date('m', time()), array('use_month_numbers' => true)) ?>/<?php echo select_year_tag('cc_expire_year', null, array('year_start' => date('Y', time()), 'year_end' => date('Y', time()) + 20)) ?>
</div>

<div class="container">
  <?php echo form_error('cc_number') ?>
  <?php echo label_for('cc_number', __('Credit card number') . ': **') ?><?php echo input_tag('cc_number', null, array('size' => 16, 'maxlength' => 16)) ?>
</div>

<div class="container">
  <?php echo form_error('cc_securcode') ?>
  <?php echo label_for('cc_securcode', __('Card Verification Value') . ': **') ?><?php echo input_tag('cc_securcode', null, array('size' => 4, 'maxlength' => 4)) ?>
</div>

<h2><?php echo __('Comments') ?></h2>
<div class="container">
  <?php echo textarea_tag('comments', '', array('rows' => 10, 'cols' => 50)) ?>
</div>

<h2><?php echo __('Finalize') ?></h2>
<div class="container">
  <?php echo label_for('commit', '&nbsp;') ?>
  <?php echo submit_tag(__('Proceed')) ?>
</div>

</form>