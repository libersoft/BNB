<?php

class rsCalendar {
    public static $compact = false;

    public static function getPartClasses($reservation_part, $start_day, $end_day)
    {
        $day_from = $reservation_part->getDayFrom();
        $day_to = $reservation_part->getDayTo();
        $classes = '';
        if ($day_from >= $start_day)
        {
            if ($reservation_part->isInitial())
            {
                $classes.= ' calendar-reservation-left';
            }
            else
            {
                $classes.= ' calendar-reservation-left-broken';
            }
        }
        if ($day_to <= $end_day)
        {
            if ($reservation_part->isFinal())
            {
                $classes.= ' calendar-reservation-right';
            }
            else
            {
                $classes.= ' calendar-reservation-right-broken';
            }
        }
        if ($day_to < rsCommon::getDaysFromTimestamp(time()))
        {
            $classes.= ' calendar-reservation-past';
        }
        $c = new Criteria();
        $c->add(ReservationPartPeer::ROOM, null, Criteria::ISNULL);
        $c->add(ReservationPartPeer::RESERVATION_ID, $reservation_part->getReservation()->getId(), Criteria::EQUAL);
        if (ReservationPartPeer::doSelect($c))
        {
            $classes.= ' calendar-reservation-uncomplete';
        }
        return $classes;
    }

    public static function isCompact()
    {
        return self::$compact;;
    }

}