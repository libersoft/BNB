<?php

/**
 * format actions.
 *
 * @package    project
 * @subpackage format
 * @author     Fabien Potencier <fabien.potencier@symfony-project.com>
 * @version    SVN: $Id: actions.class.php 8460 2008-04-14 23:17:55Z fabien $
 */
class formatActions extends sfActions
{
  public function executeIndex($request)
  {
    if ('xml' == $request->getRequestFormat())
    {
      $this->setLayout('layout');
    }
  }

  public function executeForTheIPhone($request)
  {
    $this->setTemplate('index');
  }

  public function executeJs($request)
  {
    $request->setRequestFormat('js');
  }

  public function executeJsWithAccept()
  {
    $this->setTemplate('index');
  }
}
