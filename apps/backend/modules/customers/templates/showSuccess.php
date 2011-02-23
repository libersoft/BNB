<?php use_helper('Date', 'Tip', 'Javascript', 'I18N') ?>

<h2>Detailed view for customer "<?php echo $customer->getName() ?> <?php echo $customer->getSurname() ?>" 
  <?php echo link_to(image_tag('icons/edit.png', array(
                  'onmouseover' => tip_usage('Edit customer'),
                  'onmouseout' => untip())), 'customers/edit?id=' . $customer->getId()) ?></h2>

  <?php echo javascript_tag("
      function refreshInterface(customer_id, json)
      {
          if (json.new > 0){
              Element.update('selected-rooms-' + customer_id, 'Added ' + json.new + ' reservation(s). You can add other reservations without closing this window.');
      " . remote_function(array(
          'url' => 'customers/reservationsList?customer_id=' . $customer->getId(),
          'update' => 'reservations-list',
          'script' => true
      )) . "
      }
      else
      {
          if (json.new == 0){
              alert('Unable to add reservations: you should add at least one room.');
          }
          else
          {
              alert('Unable to add reservations: please check arrive and departing date.');
          }
      }
      }
      ") ?>

<table class="form-table">
  <tr>
    <th>Id:</th>
    <td><?php echo $customer->getId() ?></td>
  </tr>
  <tr>
    <th>Created:</th>
    <td><?php echo $customer->getCreatedAt() ?></td>
  </tr>
  <tr>
    <th>Name:</th>
    <td><?php echo $customer->getName() ?></td>
  </tr>
  <tr>
    <th>Surname:</th>
    <td><?php echo $customer->getSurname() ?></td>
  </tr>
  <tr>
    <th>
      E-mail:
    </th>
    <td>
      <?php if ($customer->getEmail()): ?>
      <a href="mailto:<?php echo $customer->getEmail() ?>"><?php echo $customer->getEmail() ?></a>
      <?php else: ?>
      <span style="color: red">-- not provided --</span>
      <?php endif ?>
    </td>
  </tr>
  <tr>
    <th>Origin:</th>
    <td>
      <?php if ($customer->getIp()): ?>
      remote request from <?php echo $customer->getIp() ?>
      <?php else: ?>
      added by an operator
      <?php endif ?>
    </td>
  </tr>
  <tr>
    <th>
      Address:
    </th>
    <td>
      <?php if ($customer->getAddress()): ?>
      <?php echo $customer->getAddress() ?>
      <?php else: ?>
      <span style="color: red">-- not provided --</span>
      <?php endif ?>
    </td>
  </tr>
  <tr>
    <th>
      Postal code:
    </th>
    <td>
      <?php if ($customer->getZip()): ?>
      <?php echo $customer->getZip() ?>
      <?php else: ?>
      <span style="color: red">-- not provided --</span>
      <?php endif ?>
    </td>
  </tr>
  <tr>
    <th>
      City:
    </th>
    <td>
      <?php if ($customer->getCity()): ?>
      <?php echo $customer->getCity() ?>
      <?php else: ?>
      <span style="color: red">-- not provided --</span>
      <?php endif ?>
    </td>
  </tr>
  <tr>
    <th>
      Country:
    </th>
    <td>
      <?php if ($customer->getCountry()): ?>
      <?php echo format_country($customer->getCountry()) ?>
      <?php else: ?>
      <span style="color: red">-- not provided --</span>
      <?php endif ?>
    </td>
  </tr>
  <tr>
    <th>
      State:
    </th>
    <td>
      <?php if ($customer->getState()): ?>
      <?php echo $customer->getState() ?>
      <?php else: ?>
      <span style="color: red">-- not provided --</span>
      <?php endif ?>
    </td>
  </tr>
  <tr>
    <th>
      Phone number:
    </th>
    <td>
      <?php if ($customer->getPhone()): ?>
      <?php echo $customer->getPhone() ?>
      <?php else: ?>
      <span style="color: red">-- not provided --</span>
      <?php endif ?>
    </td>
  </tr>
  <tr>
    <th>
      Mobile number:
    </th>
    <td>
      <?php if ($customer->getMobile()): ?>
      <?php echo $customer->getMobile() ?>
      <?php else: ?>
      <span style="color: red">-- not provided --</span>
      <?php endif ?>
    </td>
  </tr>
  <tr>
    <th>
      Fax number:
    </th>
    <td>
      <?php if ($customer->getFax()): ?>
      <?php echo $customer->getFax() ?>
      <?php else: ?>
      <span style="color: red">-- not provided --</span>
      <?php endif ?>
    </td>
  </tr>
  <?php if ($customer->getCcNumber()): ?>
  <tr>
    <th>
      Credit card type:
    </th>
    <td>
      <?php echo $customer->getCcTypeName() ?>
    </td>
  </tr>
  <tr>
    <th>
      Credit card number:
    </th>
    <td>
      <?php echo $customer->getCcNumber() ?>
    </td>
  </tr>
  <tr>
    <th>
      Credit card expiry:
    </th>
    <td>
      <?php echo $customer->getCcExpiry() ?>
    </td>
  </tr>
  <tr>
    <th>
      Credit card secore code:
    </th>
    <td>
      <?php echo $customer->getCcSecurcode() ?>
    </td>
  </tr>
  <?php else: ?>
  <tr>
    <th>
      Credit card:
    </th>
    <td>
      <span style="color: red">-- not provided --</span>
    </td>
  </tr>
  <?php endif ?>
  <tr>
    <th>
      Comments:
    </th>
    <td>
      <?php if ($customer->getComments()): ?>
      <?php echo $customer->getComments() ?>
      <?php else: ?>
      <span style="color: red">-- not provided --</span>
      <?php endif ?>
    </td>
  </tr>
</table>
<h2>Customer's reservations:
  <?php echo link_to_remote(
    image_tag('icons/add_reservation.png', array(
                                                                  'onclick' => 'popup(\'add-reservation-popup-' . $customer->getId() . '\')',
                                                                  'onmouseover' => tip_usage('Add new reservation(s)'),
                                                                  'onmouseout' => untip())),
    array('update' => 'add-reservation-popup-' . $customer->getId(),
                                                                  'url' => 'dialogs/addReservation?id='.$customer->getId(),
                                                                  'script' => true)) ?></h2>

<a name="reservations" />
<table id="reservations-list" width="100%" cellspacing="0">
  <?php include_partial('reservationsList', array('reservations' => $reservations)) ?>
</table>




<div class="popup" id="add-reservation-popup-<?php echo $customer->getId() ?>">
</div>
<?php echo draggable_element('add-reservation-popup-' . $customer->getId()) ?>
