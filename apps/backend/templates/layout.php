<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
  <head>
    <?php include_http_metas() ?>
    <?php include_metas() ?>
    <?php include_title() ?>
    <link rel="shortcut icon" href="/favicon.ico" />
  </head>
  <body>
    <?php echo javascript_include_tag('wz_tooltip') ?>
    <!--script type="text/javascript">
      if (typeof document.onselectstart!="undefined") {
        document.onselectstart=new Function ("return false");
      }
      else{
        document.onmousedown=new Function ("return false");
        document.onmouseup=new Function ("return true");
      }
    </script-->
    <h1><?php echo link_to('BnB Control panel', sfConfig::get('app_default_view') . '/index') ?></h1>
    <ul id="main-menu" class="vertical-menu">
      <li><?php echo link_to(image_tag('icons/add_customer.png') . ' Add customer', 'customers/add') ?></li>
      <li><?php echo link_to(image_tag('icons/customers_view.png') . ' Customers view', 'customers/index') ?></li>
      <li><?php echo link_to(image_tag('icons/weekly_calendar_view.png') . ' Weekly calendar view', 'calendar/weekly') ?></li>
      <li><?php echo link_to(image_tag('icons/monthly_calendar_view.png') . ' Monthly calendar view', 'calendar/monthly') ?></li>
      <!--li><?php echo link_to(image_tag('icons/reservations_view.png') . ' Reservations view', 'reservations/index') ?></li-->
      <li><?php echo link_to(image_tag('icons/exit.png') . 'Logout',  'general/logout') ?></li>
    </ul>
    <div id="content">
      <?php echo $sf_content ?>
    </div>
  </body>
</html>
