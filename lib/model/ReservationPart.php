<?php
/**
 * Subclass for representing a row from the 'reservation_part' table.
 *
 *
 *
 * @package lib.model
 */
class ReservationPart extends BaseReservationPart
{
  public function getRoomName()
  {
    $rooms = sfConfig::get('app_rooms');
    return  $rooms[$this->getRoom()]['name'];
  }

  private function setRoomSimple($room_id)
  {
    return parent::setRoom($room_id);
  }

  public function isSplittable()
  {
    return ($this->getDayTo() - $this->getDayFrom()) > 1;
  }

  /**
   * Divide this part on day $splite_day if possible.
   */
  public function split($split_day)
  {
    if ($split_day < 1 || $split_day > $this->getDayTo() - $this->getDayFrom())
    {
      throw new rsException("Selected split day out of permitted bounds.");
    }
    // Modifico la vecchia per assegnarla
    $new_part = new ReservationPart();
    $new_part->setRoomSimple($this->getRoom());
    $new_part->setDayFrom($this->getDayFrom()+$split_day);
    $new_part->setDayTo($this->getDayTo());
    $new_part->setReservationId($this->getReservationId());
    $new_part->setType($this->getType());
    $new_part->setArrangement($this->getArrangement());
    $new_part->setCustomer($this->getCustomer());
    $new_part->save();
    $this->setDayTo($this->getDayFrom() + $split_day);
  }


  public function getAvailableSplitDays() {
    $split_days = array('-- select to split --');
    for ($i = 1; $i < $this->getDayTo() - $this->getDayFrom(); $i++) {
      $split_days[] = $i;
    }
    return $split_days;
  }


  /**
   * Qui si fa il grosso del lavoro: data una stanza a cui assegnare la prenotazione,
   * si disassegna, assegna o spezza opportunamente a seconda del contesto.
   * @param int $room_id identificatore numerico della stanza o NULL per rimuovere
   * l'assegnazione.
   * @param boolean $parent evita controlli di consistenza e assegna senza troppe domande.
   */
  public function setRoom($room_id, $strict = false)
  {
    /*
     * Si sta rimuovendo l'assegnazione, per cui controlliamo se ci sono parti
     * di prenotazione non assegnate e adiacenti per unirle.
     */
    if ($room_id === NULL)
    {
      parent::setRoom(NULL);
      $this->checkAdjacent();
      return;
    }
    /**
     * Si sta facendo un'assegnazione, quindi vediamo se la camera sta tutta dentro
     * oppure la prenotazione va spezzata.
     */
    // Caso 1, c'è spazio per tutta la parte di prenotazione
    else if ($this->isAssignable($room_id))
    {
      $new_type = rsCommon::getTypeIdFromRoom($room_id);
      if ($this->getType() != $new_type)
      {
        $this->setArrangement(NULL);
        $this->setType($new_type);
      }
      parent::setRoom($room_id);
      $this->checkAdjacent();
      return;
    }
    // Caso 2: c'è spazio solo per una parte iniziale di prenotazione
    // FIXME di tanto in tanto sbarella, => DISATTIVATO
    /*else if (($cut_day = $this->getCutDay($room_id)) !== NULL)
    {
      $new_part = new ReservationPart();
      $new_part->setRoomSimple($this->getRoom());
      $new_part->setDayFrom($cut_day);
      $new_part->setDayTo($this->getDayTo());
      $new_part->setReservationId($this->getReservationId());
      $new_part->setType($this->getType());
      $new_part->setArrangement($this->getArrangement());
      $new_part->setCustomer($this->getCustomer());
      $new_part->save();
      // Modifico la vecchia per assegnarla
      $new_type = rsCommon::getTypeIdFromRoom($room_id);
      if ($this->getType() != $new_type)
      {
        parent::setArrangement(NULL);
        $this->setType($new_type);
      }
      $this->setDayTo($cut_day);
      parent::setRoom($room_id);
      $this->checkAdjacent();
      return;
    }*/
    // Caso 3: non c'è spazio per la prenotazione
    throw new rsException('Selected room is not compatible with this reservation.');
  }

  /**
   * Ci dice con un booleano se la stanza è assegnabile senza spezzarla.
   */
  public function isAssignable($room_id, $strict = false)
  {
    $day_from = $this->getDayFrom();
    $day_to = $this->getDayTo();
    $c = new Criteria();
    /* Qui cerco tutte le prenotazioni con una camera assegnata che cadano nell'intervallo
     * di incompatibilità di questa prenotazione.
     */
    $c_from = $c->getNewCriterion(ReservationPartPeer::DAY_FROM, $day_from, Criteria::GREATER_EQUAL);
    $c_from->addAnd($c->getNewCriterion(ReservationPartPeer::DAY_FROM, $day_to, Criteria::LESS_THAN));
    $c_to = $c->getNewCriterion(ReservationPartPeer::DAY_TO, $day_from, Criteria::GREATER_THAN);
    $c_to->addAnd($c->getNewCriterion(ReservationPartPeer::DAY_TO, $day_to, Criteria::LESS_THAN));
    $c_mid = $c->getNewCriterion(ReservationPartPeer::DAY_FROM, $day_from, Criteria::LESS_THAN);
    $c_mid->addAnd($c->getNewCriterion(ReservationPartPeer::DAY_TO, $day_to, Criteria::GREATER_EQUAL));
    $c_to->addOr($c_mid);
    $c_to->addOr($c_from);
    $c->add($c_to);
    $c->addAnd(ReservationPartPeer::ROOM, $room_id, Criteria::EQUAL);
    if ($strict)
    {
      $c->add(ReservationPartPeer::TYPE, $this->getType());
    }
    $va = ReservationPartPeer::doSelect($c);
    return count($va) == 0;
  }

  /**
   * Questo controlla se ci sono parti adiacenti sulla stessa riga ed eventualmente
   * le unisce in una sola parte.
   */
  private function checkAdjacent()
  {
    $c = new Criteria();
    $c->add(ReservationPartPeer::RESERVATION_ID, $this->getReservation()->getId(), Criteria::EQUAL);
    $c->add(ReservationPartPeer::ROOM, $this->getRoom(), Criteria::EQUAL);
    $c_from = $c->getNewCriterion(ReservationPartPeer::DAY_TO, $this->getDayFrom(), Criteria::EQUAL);
    $c_to = $c->getNewCriterion(ReservationPartPeer::DAY_FROM, $this->getDayTo(), Criteria::EQUAL);
    $c_to->addOr($c_from);
    $c->addAnd($c_to);
    $adjacent_parts = ReservationPartPeer::doSelect($c);
    foreach ($adjacent_parts as $part)
    {
      $day_from = $part->getDayFrom();
      if ($day_from < $this->getDayFrom())
      {
        $this->setDayFrom($day_from);
      }
      $day_to = $part->getDayTo();
      if ($day_to > $this->getDayTo())
      {
        $this->setDayTo($day_to);
      }
      $part->delete();
    }
  }

  public function getTypeName()
  {
    $room_types = sfConfig::get('app_room_types');
    return $room_types[$this->getType()]['name'];
  }

  public function setArrangement($arrangement_id)
  {
    if ($arrangement_id === NULL)
    {
      return parent::setArrangement(NULL);
    }
    $room_types = sfConfig::get('app_room_types');
    if (key_exists($arrangement_id, $room_types[$this->getType()]['arrangements']))
    {
      return parent::setArrangement($arrangement_id);
    }
    throw new rsException('Selected arrangement is not valid for this room type.');
  }

  public function getArrangementName()
  {
    return $this->getArrangementInfo('name');
  }

  public function getArrangementDescription()
  {
    return $this->getArrangementInfo('description');
  }

  private function getArrangementInfo($field)
  {
    if ($this->getArrangement() === NULL)
    {
      return '-- not specified --';
    }
    $tmp = sfConfig::get('app_room_types');
    return $tmp[$this->getType()]['arrangements'][$this->getArrangement()][$field];
  }


  public function getArrangementsForSelect()
  {
    $tmp = sfConfig::get('app_room_types');
    $arrangements = array('- not spec. -');
    foreach ($tmp[$this->getType()]['arrangements'] as $arrangement)
    {
      array_push($arrangements, $arrangement['name']);
    }
    return $arrangements;
  }

  public function getArrangementForSelect()
  {
    return ($arrangement = $this->getArrangement()) === NULL ? 0 : $arrangement + 1;
  }

  public function setArrangementFromSelect($arrangement_id)
  {
    if ($arrangement_id == 0)
    {
      return $this->setArrangement(NULL);
    }
    return $this->setArrangement($arrangement_id - 1);
  }

  public function getAssignableRoomsForSelect()
  {
    $rooms = array();
    foreach ($this->getAssignableRooms() as $id => $room)
    {
      $rooms[$id] = $room['name'] . ' (' . $room['type'] . ')';
    }
    return $rooms;
  }

  /**
   * Return an array of the free rooms in the specified period.
   * @param boolean $strict if ture get assignable rooms only for the same room type.
   * @return array an array of assignable rooms.
   */
  public function getAssignableRooms($strict = false)
  {
    $day_from = $this->getDayFrom();
    $day_to = $this->getDayTo();
    $c = new Criteria();
    /* Qui cerco tutte le prenotazioni con una camera assegnata che cadano nell'intervallo
     * di incompatibilità di questa prenotazione.
     */
    $c_from = $c->getNewCriterion(ReservationPartPeer::DAY_FROM, $day_from, Criteria::GREATER_EQUAL);
    $c_from->addAnd($c->getNewCriterion(ReservationPartPeer::DAY_FROM, $day_to, Criteria::LESS_THAN));
    $c_to = $c->getNewCriterion(ReservationPartPeer::DAY_TO, $day_from, Criteria::GREATER_THAN);
    $c_to->addAnd($c->getNewCriterion(ReservationPartPeer::DAY_TO, $day_to, Criteria::LESS_THAN));
    $c_mid = $c->getNewCriterion(ReservationPartPeer::DAY_FROM, $day_from, Criteria::LESS_THAN);
    $c_mid->addAnd($c->getNewCriterion(ReservationPartPeer::DAY_TO, $day_to, Criteria::GREATER_EQUAL));
    $c_to->addOr($c_mid);
    $c_to->addOr($c_from);
    $c->add($c_to);
    $c->add(ReservationPartPeer::ROOM, null, Criteria::ISNOTNULL);
    if ($strict)
    {
      $c->add(ReservationPartPeer::TYPE, $this->getType());
    }
    $c->addGroupByColumn(ReservationPartPeer::ROOM);
    $rooms = sfConfig::get('app_rooms');
    foreach (ReservationPartPeer::doSelect($c) as $part)
    {
      unset($rooms[$part->getRoom()]);
    }
    return $rooms;
  }


  public function getCutDay($room_id)
  {
    $c = new Criteria();
    $c->add(ReservationPartPeer::ROOM, $room_id, Criteria::EQUAL);
    $c->add(ReservationPartPeer::DAY_FROM, $this->getDayFrom(), Criteria::LESS_EQUAL);
    $c->add(ReservationPartPeer::DAY_TO, $this->getDayFrom(), Criteria::GREATER_THAN);
    $result = ReservationPartPeer::doSelectOne($c);
    if ($result)
    {
      return NULL;
    }
    $c = new Criteria();
    $c->add(ReservationPartPeer::ROOM, $room_id, Criteria::EQUAL);
    $c->add(ReservationPartPeer::DAY_FROM, $this->getDayTo(), Criteria::LESS_THAN);
    $c->add(ReservationPartPeer::DAY_FROM, $this->getDayFrom(), Criteria::GREATER_THAN);
    $result = ReservationPartPeer::doSelectOne($c);
    return $result ? $result->getDayFrom() : NULL;
  }

  public function getDuration() {
    return $this->getDayTo() - $this->getDayFrom();
  }

  public function getTimeFrom()
  {
    return rsCommon::getTimestampFromDays($this->getDayFrom());
  }

  public function getTimeTo()
  {
    return rsCommon::getTimestampFromDays($this->getDayTo());
  }

  public function isInitial()
  {
    return $this->getDayFrom() == $this->getReservation()->getDayFrom();
  }

  public function isFinal()
  {
    return $this->getDayTo() == $this->getReservation()->getDayTo();
  }

  public function isMedian()
  {
    return $this->getDayTo() < $this->getReservation()->getDayTo() && $this->getDayFrom() > $this->getReservation()->getDayFrom();
  }

}
