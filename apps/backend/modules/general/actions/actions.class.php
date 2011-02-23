<?php

/**
 * General module implements generic backend operations such as login, logout
 * and 404 error gesture.
 * This file implements general module actions.
 *
 * @package    reservations
 * @subpackage general
 * @author     Michele Comignano <mc@libersoft.it>
 */
class generalActions extends sfActions
{
  private function isUserAgentSupported($user_agent)
  {
    // FIXME
    //return strstr($user_agent, 'Firefox') || strstr($user_agent, 'Iceweasel') || strstr($user_agent, 'GranParadiso') || strstr($user_agent, 'Opera');
    return true;
  }

    /**
     * Simply redirect to the login form.
     */
  public function executeIndex()
  {
    $this->redirect($this->getModuleName() . '/login');
  }

  public function executeLogin(sfRequest $request)
  {
    if (!$this->isUserAgentSupported($request->getHttpHeader('User-Agent')))
    {
      return $this->renderText('Your browser is probably not supported. Get <a href="http://www.mozilla.com/">Firefox</a>');
    }
    $this->redirectIf($this->getUser()->isAuthenticated(), sfConfig::get('app_default_view') . '/index');
    if ($request->getMethod() == sfRequest::POST && $this->hasRequestParameter('username') && $this->hasRequestParameter('password'))
    {
      if ($this->getRequestParameter('username') == sfConfig::get('app_username') && md5($this->getRequestParameter('password')) == sfConfig::get('app_password'))
      {
        $this->getUser()->setAuthenticated(true);
        $this->redirect(sfConfig::get('app_default_view') . '/index');
      }
      else
      {
        $this->errors = true;
      }
    }
    $this->form = new loginForm();
    return sfView::SUCCESS;
  }

  public function executeLogout()
  {
    if ($this->getUser()->isAuthenticated())
    {
      $this->getUser()->setAuthenticated(false);
    }
    return sfView::SUCCESS;
  }

  public function executeError404()
  {
    return sfView::SUCCESS;
  }
}
