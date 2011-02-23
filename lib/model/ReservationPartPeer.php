<?php
/**
 * Subclass for performing query and update operations on the 'reservation_part' table.
 *
 * @package lib.model
 */
class ReservationPartPeer extends BaseReservationPartPeer
{
  /**
   * Change the arrangement for an assigned room.
   * @param $part_id the unique id of the part to modify.
   * @param $arrangement_id the unique id of the new arrangement.
   * @return <type>
   */
  public static function changeArrangement($reservation_part_id, $arrangement_id)
  {
    $reservation_part = self::retrieveByPk($reservation_part_id);
    if ($reservation_part)
    {
      if (array_key_exists($arrangement_id, $reservation_part->getArrangementsForSelect()))
      {
        $reservation_part->setArrangementFromSelect($arrangement_id);
        $reservation_part->save();
        return $reservation_part;
      }
      throw new rsException('Selected arrangement does not exist.');
    }
    throw new rsException('Selected part does not exist.');
  }

  public static function getAssignedPerRoom($start_day, $end_day, $room_id)
  {
    $c = self::getTimeIntervalCriteria($start_day, $end_day);
    $c->add(self::ROOM, $room_id);
    return self::doSelect($c);
  }

  public static function getCandidates($start_day, $end_day)
  {
    $c = self::getTimeIntervalCriteria($start_day, $end_day, false);
    $c->add(self::ROOM, null, Criteria::ISNULL);
    $c->addDescendingOrderByColumn(self::RESERVATION_ID);
    $c->addDescendingOrderByColumn(self::DAY_FROM);
    return self::doSelect($c);
  }

  private static function getTimeIntervalCriteria($start_day, $end_day, $order = true)
  {
    $c = new Criteria();
    $criterion_from = $c->getNewCriterion(self::DAY_FROM, $start_day, Criteria::GREATER_EQUAL);
    $criterion_from->addAnd($c->getNewCriterion(self::DAY_FROM, $end_day, Criteria::LESS_THAN));
    $criterion_to = $c->getNewCriterion(self::DAY_TO, $start_day, Criteria::GREATER_THAN);
    $criterion_to->addAnd($c->getNewCriterion(self::DAY_TO, $end_day, Criteria::LESS_EQUAL));
    //    if ($strict)
    //    {
    //      $criterion_from->addAnd($criterion_to);
    //    }
    $criterion_mid = $c->getNewCriterion(self::DAY_FROM, $start_day, Criteria::LESS_EQUAL);
    $criterion_mid->addAnd($c->getNewCriterion(self::DAY_TO, $end_day, Criteria::GREATER_EQUAL));
    $criterion_from->addOr($criterion_mid);
    $criterion_from->addOr($criterion_to);
    $c->add($criterion_from);
    if ($order) {
      $c->addAscendingOrderByColumn(self::DAY_FROM);
    }
    return $c;
  }



}
