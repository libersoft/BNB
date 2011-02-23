<?php

/**
 * reservations actions.
 *
 * @package    reservations
 * @subpackage reservations
 * @author     Michele Comignano <mc@libersoft.it>
 */
class reservationsActions extends sfActions
{
  public function executeIndex($request)
  {
    $this->initTable($request);
    return sfView::SUCCESS;
  }

  public function executeTable($request)
  {
    $this->initTable($request);
    return sfView::SUCCESS;
  }

  public function executeDelete($request)
  {
    $this->forward404Unless($request->isXmlHttpRequest());
    $reservation = ReservationPeer::retrieveByPk($request->getParameter('id'));
    if ($reservation)
    {
      $reservation->delete();
    }
    $this->initTable($request);
    return $this->setTemplate('table');
  }

  public function executeChangeArrangement($request)
  {
    $this->forward404Unless($request->isXmlHttpRequest());
    $reservation = ReservationPeer::changeArrangement($request->getParameter('id'), $request->getParameter('arrangement'));
    return $reservation ? $this->renderPartial('arrangementSelect', array('reservation' => $reservation)) : sfView::NONE;
  }


  public function executeUnassign($request)
  {
    $this->forward404Unless($request->isXmlHttpRequest());
    $reservation = ReservationPeer::retrieveByPk($request->getParameter('id'));
    $reservation->setRoom(NULL);
    $reservation->save();
    $this->initTable($request);
    $this->setTemplate('table');
  }

  public function executeAssign($request)
  {
    $this->forward404Unless($request->isXmlHttpRequest());
    $reservation = ReservationPeer::retrieveByPk($request->getParameter('id'));
    if ($reservation)
    {
      try
      {
        $reservation->setRoom($request->getParameter('room'));
        $reservation->save();
      }
      catch (rsException $e)
      {
      }
    }
    $this->initTable($request);
    $this->setTemplate('table');
  }

  private function initTable($request)
  {
    $this->available_filters = array();/*$this->getAvailableFilters();array(
            'all' => 'Show all reservations',
            'new' => 'Show only reservations to be processed',
            'begin-future' => 'Show only reservations beginning in the future',
            'end-future' => 'Show only reservations ending in the future',
            'assigned' => 'Show only assigned reservations',
            'unassigned' => 'Show only unassigned reservarions',
            'today' => 'Show only reservations falling today');*/
    $this->room_types = sfConfig::get('app_room_types');
    $this->rooms = sfConfig::get('app_rooms');
    $pager = new sfPropelPager('Reservation', $this->getUser()->getAttribute('rows_per_page', sfConfig::get('app_reservations_view_rows_per_page')));
    $pager->setCriteria($this->getCriteria());
    $pager->setPage($request->getParameter('page', 1));
    $pager->init();
    $this->pager = $pager;
  }

  private function getCriteria()
  {
//     if ($this->hasRequestParameter('add_filter'))
//     {
//       $c = new Criteria();
//       switch ($request->getParameter('filter', 'all'))
//       {
//         case 'today':
//           $c->add(ReservationPeer::DAY_FROM, $today, Criteria::LESS_EQUAL);
//           $c->addAnd(ReservationPeer::DAY_TO, $today, Criteria::GREATER_THAN);
//           break;
//         case 'all':
//           break;
//         default:
//           return $this->getUser()->getAttribute('reservations_table_criteria');
//       }
//     $this->getUser()->setAttribute('reservations_table_criteria', $c);
    return new Criteria();//$c;
  }
}
