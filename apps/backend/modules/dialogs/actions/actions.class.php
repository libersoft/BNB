<?php

/**
 * dialogs actions.
 *
 * @package    reservations
 * @subpackage dialogs
 * @author     Your name here
 * @version    SVN: $Id: actions.class.php 9301 2008-05-27 01:08:46Z dwhittle $
 */
class dialogsActions extends sfActions
{
  public function executeAddReservation(sfRequest $request)
  {
    $this->forward404Unless($request->isXmlHttpRequest());
    $customer = CustomerPeer::retrieveByPK($request->getParameter('id'));
    $this->forward404Unless($customer);
    $this->reservation_form = new AddReservationForm();
    $this->room_form = new AddRoomForm();
    $this->customer = $customer;
    $this->mode = $request->getParameter('mode', 'customers-list');
    $this->room_types = sfConfig::get('app_room_types');
    $this->rooms = $this->getUser()->getReadableRooms($customer->getId());
    return sfView::SUCCESS;
  }

  public function executeSaveReservation(sfRequest $request)
  {
    $this->forward404Unless($request->isMethod('post') && $request->isXmlHttpRequest());
    $customer = CustomerPeer::retrieveByPK($request->getParameter('id'));
    $this->getResponse()->setHttpHeader('Content-Type','application/json; charset=utf-8');
    $form = new AddReservationForm();
    $form->bind($request->getParameter('reservation'));
    $rooms = $this->getUser()->getAttribute($customer->getId() . '_rooms', array());
    if (!$form->isValid() || !count($rooms))
    {
      if ($form->isValid()) {
        $json = json_encode(array('new' => '0'));
      }
      else $json = json_encode(array('new' => '-1'));
      $this->getResponse()->setHttpHeader('X-JSON', $json);
      return $this->renderText($json);
    }
    $day_from = rsCommon::getDaysFromTimestamp(sfContext::getInstance()->getI18N()->getTimestampForCulture($form->getValue('date_from'), $this->getUser()->getCulture()), true);
    $day_to = rsCommon::getDaysFromTimestamp(sfContext::getInstance()->getI18N()->getTimestampForCulture($form->getValue('date_to'), $this->getUser()->getCulture()), true);
    $json_response = array();
    $json_response['new'] = 0;
    foreach ($this->getUser()->getAttribute($customer->getId() . '_rooms') as $room)
    {
      for ($j = 0; $j < $room['count']; $j++)
      {
        $reservation = new Reservation();
        $reservation->fromArray(array(
                      'CustomerId' => $customer->getId(),
                      'ArrivalTime' => $form->getValue('arrival_time'),
                      'DayFrom' => $day_from,
                      'DayTo' => $day_to,
                      'Notes' => '',
                      'ArrivalTimeOrig' => $form->getValue('arrival_time'),
                      'DayFromOrig' => $day_from,
                      'DayToOrig' => $day_to,
                      'TypeOrig' => $form->getValue('type'),
                      'ArrangementOrig' => $room['arrangement']
          ));
        $reservation->save();
        $reservation_part = new ReservationPart();
        $reservation_part->fromArray(array(
                      'ReservationId' => $reservation->getId(),
                      'CustomerId' => $customer->getId(),
                      'Type' => $room['type'],
                      'Arrangement' => $room['arrangement'],
                      'DayFrom' => $day_from,
                      'DayTo' => $day_to,
                      'Room' => NULL
          ));
        $reservation_part->save();
        $json_response['new']++;
      }
    }
    $this->getUser()->setAttribute($customer->getId() . '_rooms', array());
    $json = json_encode($json_response);
    $this->getResponse()->setHttpHeader('X-JSON', $json);
    return $this->renderText($json);
  }

  /**
   * Execute removeRoom action to remove room(s) for an user.
   * @return sfView::NONE
   */
  public function executeRemoveRoom($request)
  {
    $customer = CustomerPeer::retrieveByPk((int)$request->getParameter('customer_id'));
    $this->forward404Unless($request->isXmlHttpRequest() && $customer);
    $user_rooms = $this->getUser()->getAttribute($customer->getId() . '_rooms', array());
    if ($this->hasRequestParameter('room_id'))
    {
      $id = (int)$request->getParameter('room_id') - 1;// BAD HACK
      if (array_key_exists($id, $user_rooms))
      {
        unset($user_rooms[$id]);
        $this->getUser()->setAttribute($customer->getId() . '_rooms', $user_rooms);
      }
    }
    return $this->renderPartial('selectedRooms', array(
                'rooms' => $this->getUser()->getReadableRooms($customer->getId()),
                'customer' => $customer,
                'errors' => false
      ));
  }

  /**
   * Execute addRoom action to process AJAX requests from the room selection dialog.
   * @return sfView::NONE
   */
  public function executeAddRoom(sfRequest $request)
  {
    $customer = CustomerPeer::retrieveByPk((int)$request->getParameter('customer_id'));
    $this->forward404Unless($request->isXmlHttpRequest() && $customer);
    $user_rooms = $this->getUser()->getAttribute($customer->getId() . '_rooms', array());
    if ($request->hasParameter('room_type') && $request->hasParameter('room_count'))
    {
      $room_type = $request->getParameter('room_type');
      if ($request->hasParameter($room_type . '_arrangement'))
      {
        array_push($user_rooms, array('type' => $room_type, 'arrangement' => $request->getParameter($room_type . '_arrangement'), 'count' => $request->getParameter('room_count')));
        $this->getUser()->setAttribute($customer->getId() . '_rooms', $user_rooms);
      }
    }
    return $this->renderPartial('selectedRooms', array(
                'rooms' => $this->getUser()->getReadableRooms($customer->getId()),
                'customer' => $customer,
                'errors' => false
      ));
  }

  public function executeEditReservationPart(sfRequest $request)
  {

  }
}
