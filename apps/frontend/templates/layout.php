<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
  <head>
    <?php include_http_metas() ?>
    <?php include_metas() ?>
    <?php include_title() ?>
    <link rel="shortcut icon" href="/favicon.png" />
  </head>
  <body>
    <div class="layout-container" id="logo">
      <?php if (($action_name = $sf_context->getActionName()) == 'gatherReservationData'): ?>
      <?php include_partial('languageSwitch', array('available_languages' => sfConfig::get('app_languages_available'), 'action_name' => $action_name)) ?>
      <?php endif ?>
    </div>
    <div class="layout-container" id="content">
      <?php echo $sf_content ?>
    </div>
  </body>
</html>
