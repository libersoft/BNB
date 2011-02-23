<?php
/**
 * Subclass for representing a row from the 'reservations' table.
 *
 * @package lib.model
 */
class Reservation extends BaseReservation
{
  public function setArrivalTime($arrival_time)
  {
    $arrival_time = (int)$arrival_time;
    if (array_key_exists($arrival_time, sfConfig::get('app_arrival_times_available')))
    {
      return parent::setArrivalTime($arrival_time);
    }
    throw new rsException('Specified arrival time is not valid.');
  }

  public function getTypeOrigName()
  {
    $room_types = sfConfig::get('app_room_types');
    return $room_types[$this->getTypeOrig()]['name'];
  }

  public function isCompletelyAssigned()
  {
    foreach ($this->getReservationParts() as $part)
    {
      if ($part->getRoom() == NULL)
      {
        return false;
      }
    }
    return true;
  }

  public function getInitialPart()
  {
    $c=new Criteria();
    $c->add(ReservationPartPeer::RESERVATION_ID, $this->getId(), Criteria::EQUAL);
    $c->add(ReservationPartPeer::DAY_FROM, $this->getDayFrom(), Criteria::EQUAL);
    $results = ReservationPartPeer::doSelect($c);
    if (count($results) == 1)
    {
      return $results[0];
    }
    throw new rsException('Missing reservation final part.');
  }

  public function getFinalPart()
  {
    $c=new Criteria();
    $c->add(ReservationPartPeer::RESERVATION_ID, $this->getId(), Criteria::EQUAL);
    $c->add(ReservationPartPeer::DAY_TO, $this->getDayTo(), Criteria::EQUAL);
    $results = ReservationPartPeer::doSelect($c);
    if (count($results) == 1)
    {
      return $results[0];
    }
    throw new rsException('Missing reservation final part.');
  }

  public function decreaseDurationAfter($delta)
  {
    $delta = (int)$delta;
    if ($this->canDecreaseDuration($delta))
    {
      $day_to = $this->getDayTo() - $delta;
      $final_part = $this->getFinalPart();
      // Elimino la parte lunga 1 se all'estremo da accorciare
      if ($final_part->getDayTo() - $final_part->getDayFrom() <= 1)
      {
        $final_part->delete();
      }
      else
      {
        $final_part->setDayTo($day_to);
        $final_part->save();
      }
      return $this->setDayTo($day_to);
    }
    throw new rsException('Cannot decrease duration otherwise we\'ll have a 0-day reservation.');
  }


  public function decreaseDurationBefore($delta)
  {
    $delta = (int)$delta;
    if ($this->canDecreaseDuration($delta))
    {
      $day_from = $this->getDayFrom() + $delta;
      $initial_part = $this->getInitialPart();
      if ($initial_part->getDayTo() - $initial_part->getDayFrom() <= 1)
      {
        $initial_part->delete();
      }
      else
      {
        $initial_part->setDayFrom($day_from);
        $initial_part->save();
      }
      return $this->setDayFrom($day_from);
    }
    throw new rsException('Cannot decrease duration otherwise we\'ll have a 0-day reservation.');
  }

  public function canDecreaseDuration($delta)
  {
    return ($this->getDayTo() - $this->getDayFrom() - (int)$delta) > 0;
  }

  public function increaseDurationAfter($delta)
  {
    $delta = (int)$delta;
    if ($this->canIncreaseDurationAfter($delta))
    {
      $day_to = $this->getDayTo() + $delta;
      $final_part = $this->getFinalPart();
      $final_part->setDayTo($day_to);
      $final_part->save();
      return $this->setDayTo($day_to);
    }
    throw new rsException('Cannot increase reservation duration after.');
  }

  public function increaseDurationBefore($delta)
  {
    $delta = (int)$delta;
    if ($this->canIncreaseDurationBefore($delta))
    {
      $day_from = $this->getDayFrom() - $delta;
      $initial_part = $this->getInitialPart();
      $initial_part->setDayFrom($day_from);
      $initial_part->save();
      return $this->setDayFrom($day_from);
    }
    throw new rsException('Cannot increase reservation duration after.');
  }

  public function canIncreaseDurationAfter($delta)
  {
    $delta = (int)$delta;
    $final_part_room = $this->getFinalPart()->getRoom();
    if ($final_part_room === NULL)
    {
      return true;
    }
    $c = new Criteria();
    $c->add(ReservationPartPeer::ROOM, $final_part_room, Criteria::EQUAL);
    $c->add(ReservationPartPeer::DAY_TO, $this->getDayTo(), Criteria::GREATER_THAN);
    $c->add(ReservationPartPeer::DAY_FROM, $this->getDayTo() + $delta, Criteria::LESS_THAN);
    $c->add(ReservationPartPeer::RESERVATION_ID, $this->getId(), Criteria::NOT_EQUAL);
    $result = ReservationPartPeer::doSelect($c);
    return count($result) == 0;
  }

  public function canIncreaseDurationBefore($delta)
  {
    $delta = (int)$delta;
    $initial_part_room = $this->getInitialPart()->getRoom();
    if ($initial_part_room === NULL)
    {
      return true;
    }
    $c = new Criteria();
    $c->add(ReservationPartPeer::ROOM, $initial_part_room, Criteria::EQUAL);
    $c->add(ReservationPartPeer::DAY_FROM, $this->getDayFrom() - $delta, Criteria::LESS_THAN);
    $c->add(ReservationPartPeer::DAY_TO, $this->getDayFrom() - $delta, Criteria::GREATER_THAN);
    $c->add(ReservationPartPeer::RESERVATION_ID, $this->getId(), Criteria::NOT_EQUAL);
    $result = ReservationPartPeer::doSelect($c);
    return count($result) == 0;
  }

  public function getTimeFrom()
  {
    return rsCommon::getTimestampFromDays($this->getDayFrom());
  }

  public function getTimeTo()
  {
    return rsCommon::getTimestampFromDays($this->getDayTo());
  }

  public function getTimeFromOrig()
  {
    return rsCommon::getTimestampFromDays($this->getDayFromOrig());
  }

  public function getTimeToOrig()
  {
    return rsCommon::getTimestampFromDays($this->getDayToOrig());
  }

  public function getArrivalTimeOrigDescription()
  {
    $tmp = sfConfig::get('app_arrival_times_available');
    return $tmp[$this->getArrivalTimeOrig()]['description'];
  }
  public function getArrivalTimeDescription()
  {
    $tmp = sfConfig::get('app_arrival_times_available');
    return $tmp[$this->getArrivalTime()]['description'];
  }

  public function getArrivalTimeName()
  {
    $tmp = sfConfig::get('app_arrival_times_available');
    return $tmp[$this->getArrivalTime()]['name'];
  }

  public function getArrivalTimeOrigName()
  {
    $tmp = sfConfig::get('app_arrival_times_available');
    return $tmp[$this->getArrivalTimeOrig()]['name'];
  }

  public function getArrangementOrigName()
  {
    $tmp = sfConfig::get('app_room_types');
    return $tmp[$this->getTypeOrig()]['arrangements'][$this->getArrangementOrig()]['name'];
  }
}
