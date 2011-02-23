<?php use_helper('Form', 'Tip') ?>

<?php $customer = $form->getObject() ?>

<h2>
  <?php if ($customer->isNew()): ?>
  Add new customer
  <?php else: ?>
  Edit customer "<?php echo $customer->getName() ?> <?php echo $customer->getSurname() ?>"
  <?php echo link_to(image_tag('icons/customer_go.png', array(
            'onmouseover' => tip_usage('Switch to detailed view'),
            'onmouseout' => untip())), 'customers/show?id=' . $customer->getId()) ?>
  <?php endif ?>
</h2>

<?php echo form_tag('customers/save' . ($customer->isNew() ? '' : '?id='.$customer->getId())) ?>
<table>
  <?php echo $form ?>
  <tr>
    <td></td>
    <td>
      <input type="image" src="<?php echo image_path('icons/save.png', true) ?>" onmouseover="<?php echo tip_usage($customer->isNew() ? 'Add new customer' : 'Update this customer') ?>" onmouseout="<?php echo untip() ?>" />&nbsp;&nbsp;&nbsp;&nbsp;
      <?php if (!$customer->isNew()): ?>
      <?php echo link_to(image_tag('icons/cross.png', array(
            'onmouseover' => tip_usage('Delete current customer and all his reservations'),
            'onmouseout' => untip(),
            'style' => 'vertical-align: top'
      )), 'customers/delete?id=' . $customer->getId(), array('post' => true, 'confirm' => 'This will completely remove the customer and all its reservations, are you sure?')) ?>
      <?php endif; ?>
    </td>
  </tr>
</table>
</form>
