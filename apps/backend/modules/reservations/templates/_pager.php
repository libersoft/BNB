
<?php if ($pager->haveToPaginate()): ?>
  <div class="pager">
    <?php if ($pager->getPage() != $pager->getFirstPage()): ?>
      <?php echo link_to_remote('&laquo; first', array('script' => true, 'update' => 'reservations-view', 'url' => 'reservations/table?page=' . $pager->getFirstPage())) ?>
      <?php echo link_to_remote(' &lsaquo; previous', array('script' => true, 'update' => 'reservations-view', 'url' => 'reservations/table?page='.$pager->getPreviousPage())) ?>
    <?php endif ?>
    <?php $links = $pager->getLinks(); foreach ($links as $page): ?>
      <?php echo ($page == $pager->getPage()) ? '<span>' . $page . '</span>' : link_to_remote($page, array('script' => true, 'update' => 'reservations-view', 'url' => 'reservations/table?page=' . $page)) ?>
    <?php endforeach ?>
    <?php if ($pager->getPage() != $pager->getLastPage()): ?>
      <?php echo link_to_remote('next &rsaquo;', array('script' => true, 'update' => 'reservations-view', 'url' => 'reservations/table?page='.$pager->getNextPage())) ?>
      <?php echo link_to_remote('last &raquo;', array('script' => true, 'update' => 'reservations-view', 'url' => 'reservations/table?page='.$pager->getLastPage())) ?>
    <?php endif ?>
  </div>
<?php endif ?>
