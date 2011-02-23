<?php use_helper('Tip', 'Javascript') ?>
<td><?php echo $customer->getId() ?></td>
<td><?php echo $customer->getCreatedAt() ?></td>
<td><?php echo link_to($customer->getSurname() . ' ' . $customer->getName() . ' ' . image_tag('icons/customer_go.png'), 'customers/show?id='.$customer->getId()) ?></td>
<td>
  <span id="reservation-count-<?php echo $customer->getId() ?>">
    <?php echo count($customer->getReservations()) ?>
  </span>
  <?php echo link_to(image_tag('icons/weekly_calendar_view.png', array('onmouseover' => tip_usage('Show customer reservations.'), 'onmouseout' => untip())), 'customers/show?id='.$customer->getId().'#reservations') ?>
</td>
<td><a href="mailto:<?php echo $customer->getEmail() ?>"><?php echo $customer->getEmail() ?></a></td>
<td>
  <?php echo link_to(image_tag('icons/edit.png', array('onmouseover' => tip_usage('Edit customer.'), 'onmouseout' => untip())), 'customers/edit?id='.$customer->getId()) ?>
  <?php echo link_to(image_tag('icons/cross.png', array('onmouseover' => tip_usage('Delete customer.'), 'onmouseout' => untip())), 'customers/delete?id='.$customer->getId(), array('confirm' => 'This will remove any reservation associated with this customer, are you sure?')) ?>
  <?php echo link_to_remote(
    image_tag('icons/add_reservation.png', array(
          'onclick' => 'popup(\'add-reservation-popup-' . $customer->getId() . '\')',
          'onmouseover' => tip_usage('Add reservation.'),
          'onmouseout' => untip()
      )
    ),
    array(
          'update' => 'add-reservation-popup-' . $customer->getId(),
          'url' => 'dialogs/addReservation?id='.$customer->getId(),
          'script' => true
    )
  ) ?>
</td>
