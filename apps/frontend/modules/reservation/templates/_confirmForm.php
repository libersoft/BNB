<?php use_helper('Validation', 'Javascript') ?>

<p class="container">
  <?php echo __('Please review your data below. Click on the confirm button to submit us your reservation request.') ?>
</p>
<?php echo form_remote_tag(array('update' => 'confirm_box', 'url' => 'reservation/confirmReservation')) ?>
  <div class="container">
    <?php echo label_for('commit', __('Click to confirm') . ':') ?>
    <?php echo submit_tag(__('Confirm')) ?>
  </div>
</form>