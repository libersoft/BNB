<?php
/**
 * The calendar module manage the calendar view.
 * This file contain calendar actions.
 *
 * @package reservations
 * @subpackage calendar
 * @author Michele Comignano <mc@libersoft.it>
 */
class calendarActions extends sfActions
{
  /**
   * Executes index action displaying the calendar for current state.
   */
  public function executeIndex()
  {
    $this->initCalendar();
    return sfView::SUCCESS;
  }

  /**
   * Switch calendar to monthly view.
   */
  public function executeMonthly()
  {
    $this->setLength(30);
    $this->initCalendar();
    return $this->setTemplate('index');
  }

  /**
   * Switch calendar to weekly view.
   */
  public function executeWeekly()
  {
    $this->setLength(7);
    $this->initCalendar();
    return $this->setTemplate('index');
  }

  /**
   * Point calendar on the specified day if valid.
   */
  public function executeGoto(sfRequest $request)
  {
    $this->forward404Unless($request->hasParameter('when'));
    $this->setStartDay(rsCommon::getDaysFromTimestamp(
        sfContext::getInstance()->getI18N()->getTimestampForCulture(
          $request->getParameter('when'),
          $this->getUser()->getCulture())
      ) - 1
    );
    $this->forwardIf(
      $request->isXmlHttpRequest(),
      $this->getModuleName(),
      'calendarView'
    );
    $this->initCalendar();
    return $this->setTemplate('index');
  }

  /**
   * Executes all the necessary tasks to instantiate a calendar.
   */
  private function initCalendar()
  {
    $this->setStartDay($start_day = $this->getStartDay());
    $length = $this->getLength();
    $this->setLength($length);
    rsCalendar::$compact = $this->getLength() > sfConfig::get('app_calendar_view_full_limit');
    $days = array();
    $day = $start_day;
    for ($i = 0; $i < $length; $i++)
    {
      $days[] = rsCommon::getTimestampFromDays($day);
      $day++;
    }
    $this->length = $length;
    $this->min_length = sfConfig::get('app_calendar_view_default_length');
    $this->calendar_vars = array(
      'days' => $days,
      'rooms' => sfConfig::get('app_rooms'),
      'candidate_rows' => $this->getCandidateRows(),
      'assigned_rows' => $this->getRoomsRows()
    );
  }

  private function getRoomRow($id, $start_day, $end_day)
  {
    $row = array();
    $last_day = $start_day;
    foreach (ReservationPartPeer::getAssignedPerRoom($start_day, $end_day, $id) as $reservation_part)
    {
      $day_from = $reservation_part->getDayFrom();
      $day_to = $reservation_part->getDayTo();
      if ($day_from > $last_day)
      {
        $row[] = array(
          'reservation_part' => NULL,
          'cells_count' => $day_from - $last_day
        );
        $last_day = $day_from;
      }
      if ($day_to < $end_day)
      {
        $row[] = array(
          'cells_count' => $day_to - $last_day,
          'reservation_part' => $reservation_part,
          'classes' => rsCalendar::getPartClasses($reservation_part, $this->getStartDay(), $this->getEndDay())
        );
        $last_day = $day_to;
      }
      else
      {
        $row[] = array(
          'cells_count' => $end_day - $last_day,
          'reservation_part' => $reservation_part,
          'classes' => rsCalendar::getPartClasses($reservation_part, $this->getStartDay(), $this->getEndDay())
        );
        $last_day = $end_day;
      }
    }
    if ($last_day < $end_day)
    {
      $row[] = array(
        'reservation_part' => NULL,
        'cells_count' => $end_day - $last_day
      );
    }
    return $row;
  }


  /**
   * Calculate an array about days and reservations to represent a calendar row.
   * @return array the representation of the calendar row.
   */
  private function getRoomsRows()
  {
    $start_day = $this->getStartDay();
    $end_day = $this->getEndDay();
    $rooms = sfConfig::get('app_rooms');
    $rows = array();
    foreach (array_keys($rooms) as $id)
    {
      $rows[] = $this->getRoomRow($id, $start_day, $end_day);
    }
    return $rows;
  }

  private function getCandidateRow($reservation_id)
  {
    $start_day = $this->getStartDay();
    $end_day = $this->getEndDay();
    $parts = ReservationPartPeer::getCandidates($start_day, $end_day);
  }

  /**
   * Generate the matrix (days * reservation) of unassigned reservation requests
   * falling in the considered calendar interval.
   * @return array the matrix of unassigned rows where each line is indexed by the
   * unique id of the reservation and contains the parts falling in the considered
   * interval.
   */
  private function getCandidateRows()
  {
    // ATTENZIONE guardare l'ordine...
    $start_day = $this->getStartDay();
    $end_day = $this->getEndDay();
    $parts = ReservationPartPeer::getCandidates($start_day, $end_day);
    $rows = array();
    if (!$parts){
      return $rows;
    }
    $active_reservation_id = $parts[0]->getReservationId();
    $rows[$active_reservation_id]['title'] = array(
            'name'=>$parts[0]->getCustomer()->getSurname(),
            'type'=>$parts[0]->getTypeName());
    $last_seen_day = $start_day;
    foreach ($parts as $key => $part)
    {
      if ($active_reservation_id != $part->getReservationId())
      {// Se si comincia una nuova riga
        // Parte finale
        if ($last_seen_day < $end_day) {
          $rows[$active_reservation_id]['cells'][]=array(
                        'cells_count' => $end_day - $last_seen_day,
                        'reservation_part' => NULL);
        }
        $last_seen_day = $start_day;
        $active_reservation_id = $part->getReservationId();
        $rows[$active_reservation_id]['title'] = array(
                    'name'=>$part->getCustomer()->getSurname(),
                    'type'=>$part->getTypeName());
      }
      // spazio iniziale
      if ($part->getDayFrom() > $last_seen_day) {
        $rows[$active_reservation_id]['cells'][]=array(
                    'cells_count' => $part->getDayFrom() - $last_seen_day,
                    'reservation_part' => NULL);
      }
      // parte
      $rows[$active_reservation_id]['cells'][]=array(
                'cells_count' => min($part->getDayTo(), $end_day) - max($start_day, $part->getDayFrom()),
                'reservation_part' => $part,
                'classes' => rsCalendar::getPartClasses($part, $start_day, $end_day));
      $last_seen_day = min($part->getDayTo(), $end_day);
    }
    if ($last_seen_day < $end_day) {// FIne eventuale dell'ultima riga
      $rows[$active_reservation_id]['cells'][]=array(
                        'cells_count' => $end_day - $last_seen_day,
                        'reservation_part' => NULL);
    }
    return $rows;
  }

  /**
   * Retrieve the first day shown in the calendar.
   * @return int the sequence number of the first day shown in the calendar.
   */
  private function getStartDay()
  {
    return $this->getUser()->getAttribute('calendar_start_day', rsCommon::getDaysFromTimestamp(time()) - 2);
  }

  private function setStartDay($start_day)
  {
    $this->getUser()->setAttribute('calendar_start_day', $start_day);
  }

  private function getEndDay()
  {
    return $this->getStartDay() + $this->getLength();
  }

  private function getLength()
  {
    return $this->getUser()->getAttribute('calendar_length', sfConfig::get('app_calendar_view_default_length'));
  }

  private function setLength($length)
  {
    if ($length >= 7)
    {
      $this->getUser()->setAttribute('calendar_length', $length);
    }
  }

  /**
   * Point calendar on today.
   */
  public function executeToday(sfRequest $request)
  {
    $this->forward404Unless($request->isXmlHttpRequest());
    $this->setStartDay(rsCommon::getDaysFromTimestamp(time()) - 2);
    $this->forward($this->getModuleName(), 'calendarView');
  }

  /**
   * Point calendar on the day set by offset from today.
   * @param int $offset the offset of the new date to point to.
   */
  private function setStartDayByOffest($offset)
  {
    $this->getUser()->setAttribute('calendar_start_day', $this->getUser()->getAttribute('calendar_start_day') + $offset);
  }

  /**
   * Point calendar one month in the past.
   */
  public function executeMonthBack(sfRequest $request)
  {
    $this->forward404Unless($request->isXmlHttpRequest());
    $this->setStartDayByOffest(-30);
    $this->forward($this->getModuleName(), 'calendarView');
  }

  /**
   * Point calendar one month in the future.
   */
  public function executeMonthForward(sfRequest $request)
  {
    $this->forward404Unless($request->isXmlHttpRequest());
    $this->setStartDayByOffest(30);
    $this->forward($this->getModuleName(), 'calendarView');
  }

  /**
   * Point calendar one week in the past.
   */
  public function executeWeekBack(sfRequest $request)
  {
    $this->forward404Unless($request->isXmlHttpRequest());
    $this->setStartDayByOffest(-7);
    $this->forward($this->getModuleName(), 'calendarView');
  }

  /**
   * Point calendar one week in the future.
   */
  public function executeWeekForward(sfRequest $request)
  {
    $this->forward404Unless($request->isXmlHttpRequest());
    $this->setStartDayByOffest(7);
    $this->forward($this->getModuleName(), 'calendarView');
  }

  /**
   * Add one more day in calendar.
   */
  public function executeOneMoreDay(sfRequest $request)
  {
    $this->forward404Unless($request->isXmlHttpRequest());
    $this->setLength($this->getLength() + 1);
    $this->forward($this->getModuleName(), 'calendarView');
  }

  /**
   * Remove the last day from calendar if possible.
   */
  public function executeOneDayLess(sfRequest $request)
  {
    $this->forward404Unless($request->isXmlHttpRequest());
    $this->setLength($this->getLength() -1);
    $this->forward($this->getModuleName(), 'calendarView');
  }

  public function executeSplit(sfRequest $request)
  {
    $this->forward404Unless($request->isXmlHttpRequest());
    $reservation_part = ReservationPartPeer::retrieveByPk($request->getParameter('part_id'));
    $this->forward404Unless($reservation_part);
    try
    {
      $reservation_part->split($request->getParameter('split_day'));
      $reservation_part->save();
    }
    catch (rsException $e)
    {
      echo 'errore';
    }
    $this->forward($this->getModuleName(), 'calendarView');
  }


  /**
   * Ajax action to change assigned room to a reservation.
   */
  public function executeMove(sfRequest $request)
  {
    $this->forward404Unless($request->isXmlHttpRequest());
    $reservation_part = ReservationPartPeer::retrieveByPk(substr($request->getParameter('id'), 9));
    try
    {
      $reservation_part->setRoom($request->getParameter('room_id'));
      $reservation_part->save();
    }
    catch (rsException $e)
    {
    }
    $this->forward($this->getModuleName(), 'calendarView');
  }

  /**
   * Ajax action to remove association between one room and one reservation.
   */
  public function executeUnassign(sfRequest $request)
  {
    $this->forward404Unless($request->isXmlHttpRequest());
    $id = $request->getParameter('id');
    if (!is_numeric($id))
    {
      $id = substr($id, 9);
    }
    $reservation_part = ReservationPartPeer::retrieveByPk($id);
    $reservation_part->setRoom(NULL);
    // TODO farlo alla parte e unire semmai
    $reservation_part->save();
    $this->forward($this->getModuleName(), 'calendarView');
  }

  /**
   * Ajax action to delete a reservation.
   */
  public function executeDelete(sfRequest $request)
  {
    $this->forward404Unless($request->isXmlHttpRequest());
    $id = $request->getParameter('id');
    if (!is_numeric($id))
    {
      $id = substr($id, 9);
    }
    $reservation = ReservationPeer::retrieveByPK($id);
    $this->forward404Unless($reservation);
    $reservation->delete();
    $this->forward($this->getModuleName(), 'calendarView');
  }

  /**
   * Ajax action to save notes about reservations.
   */
  public function executeSaveNotes(sfRequest $request)
  {
    $this->forward404Unless($request->isXmlHttpRequest());
    $reservation_part = ReservationPartPeer::retrieveByPk($request->getParameter('id'));
    if ($reservation_part)
    {
      $reservation_part->getReservation()->setNotes($request->getParameter('notes'));
      $reservation_part->save();
      return $this->renderPartial('calendar/reservationMenu', array(
                                    'reservation_part' => $reservation_part,
                                    'candidate' => $reservation_part->getRoom() === NULL));
    }
    return sfView::ERROR;
  }

  /**
   * Ajax action to generate only the calendar table part within the calendar view.
   * To be used when only the calendar table change following an operation.
   * @return <type>
   */
  public function executeCalendar(sfRequest $request)
  {
    $this->forward404Unless($request->isXmlHttpRequest());
    $this->initCalendar();
    return sfView::SUCCESS;
  }

  /**
   * Ajax action the generate the whole calendar view within the layout. To be used
   * when the candidate reservations list change following an operation.
   * @return <type>
   */
  public function executeCalendarView(sfRequest $request)
  {
    $this->forward404Unless($request->isXmlHttpRequest());
    $this->initCalendar();
    return sfView::SUCCESS;
  }

  /**
   * Ajax action following the change of the arrangements combo in reservation
   * popup within the calendar.
   * @return <type>
   */
  public function executeChangeArrangement(sfRequest $request)
  {
    $this->forward404Unless($request->isXmlHttpRequest());
    $reservation_part = ReservationPartPeer::changeArrangement($request->getParameter('id'), $request->getParameter('arrangement'));
    return $this->renderPartial('reservationMenu', array(
      'reservation_part' => $reservation_part,
      'candidate' => $reservation_part->getRoom() === NULL,
      'compact' => false
      )
    );
  }

  public function executeChangeDuration(sfRequest $request)
  {
    $this->forward404Unless($request->isXmlHttpRequest());
    //var_dump($request->getParameter('id'));
    //var_dump($request->getParameterHolder());

    $part = ReservationPartPeer::retrieveByPk($request->getParameter('id'));
    $this->forward404Unless($part);
    $row_id = $request->getParameter('row_id');
    $reservation = $part->getReservation();
    try
    {
      $what = $request->getParameter('what');
      $when = $request->getParameter('when');
      if (!in_array($what, array('increase', 'decrease')) ||
        !in_array($when, array('Before', 'After')))
      {
        throw new rsException('Requested operation not available.');
      }
      $method = $what . 'Duration' . $when;
      $reservation->$method(1);
      $reservation->save();
    }
    catch (rsException $e)
    {
      return sfView::ERROR;
    }
    $title;
    $row_elements;
    if (!$row_id) {
      $this->forward($this->getModuleName(), 'calendarView');
      /* E qui esce */
    }
    else if ($part->getRoom()) {
      $rooms = sfConfig::get('app_rooms');
      $title = $rooms[$part->getRoom()];
      $row_elements = $this->getRoomRow($row_id, $this->getStartDay(), $this->getEndDay());
    }
    else
    {
      $title = array(
        'name' => $reservation->getCustomer()->getName(),
        'type' => 'bella',
      );
      $row_elements = $this->getCandidateRow($reservation->getId());
    }
    return $this->renderPartial('row', array(
      'title' => $title,
      'row_elements' => $row_elements,
      )
    );
  }

  /**
   * Ajax action following value change within the arival time combo inside the
   * reservation details popup.
   * @return <type>
   */
  public function executeChangeArrivalTime(sfRequest $request)
  {
    $this->forward404Unless($request->isXmlHttpRequest());
    $reservation_part = ReservationPartPeer::retrieveByPk($request->getParameter('id'));
    if ($reservation_part)
    {
      try
      {
        $reservation_part->getReservation()->setArrivalTime($request->getParameter('arrival_time'));
        $reservation_part->save();
      }
      catch (rsException $e)
      {
        return sfView::ERROR;
      }
      return $this->renderPartial('reservationMenu', array(
                  'reservation_part' => $reservation_part,
                  'candidate' => $reservation_part->getRoom() === NULL,
                  'compact' => false));
    }
    return sfView::ERROR;
  }
}
