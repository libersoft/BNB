<?php echo use_helper('Tip', 'Form', 'Javascript') ?>
    <div id="customers-view">
<h2>Customers list</h2>

<div style="width: 50%; clear:both">
    <strong><?php echo $pager->getNbResults() ?></strong> customers found.<br />
    <?php if ($pager->getNbResults() > 0): ?>
    Displaying customers <strong><?php echo $pager->getFirstIndice() ?></strong> to <strong><?php echo $pager->getLastIndice() ?></strong>.<br /><br />
    <?php endif ?>
</div>

<?php include_partial('pager', array('pager' => $pager)) ?>

<?php if ($pager->getNbResults() > 0): ?>


<?php echo javascript_tag("
function refreshInterface(customer_id, json)
{
    if (json.new > 0){
        Element.update('selected-rooms-' + customer_id, 'Added ' + json.new + ' reservation(s). You can ' + ' add other reservations without closing this window.');
        Element.update('reservation-count-' + customer_id, parseInt($('reservation-count-' + customer_id).innerHTML) + json.new);
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


  <table width="100%" cellspacing="0">
    <tr class="header">
        <td>Id</td>
        <td>Created</td>
        <td>Name</td>
        <td>Reservations</td>
        <td>Email</td>
        <td>Operations</td>
    </tr>
    <?php foreach ($pager->getResults() as $customer): ?>
    <tr class="<?php echo rsCommon::getTrClass() ?>">
        <?php include_partial('customerRow', array('customer' => $customer)) ?>
    </tr>
    <?php endforeach ?>
</table>
<?php foreach ($pager->getResults() as $customer): ?>

<div class="popup" id="add-reservation-popup-<?php echo $customer->getId() ?>">
</div>
<?php echo draggable_element('add-reservation-popup-' . $customer->getId()) ?>
<?php endforeach ?>


<?php endif ?>

<?php include_partial('pager', array('pager' => $pager)) ?>
</div>