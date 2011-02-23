<?php

define('DAY_LENGTH', 60 * 60 * 24);

/**
 * This class defines mainly static methods useful all arround.
 */
class rsCommon
{
  /**
   * Calculate the number of entire days elapsed from the unix epoch time.
   * @param int $seconds the timestamp
   * @param boolean $tolerant determine if the result should be calculated even if
   * the timestamp don't represent exactly a day. If false and the timestamp
   * don't represent exactly a day throws an exception. Default to true.
   * @return int days elapsed from the unix epoch time.
   */
  public static function getDaysFromTimestamp($seconds, $tolerant = true)
  {
    if (!$tolerant && !is_int($seconds / DAY_LENGTH))
    {
      throw new Exception($seconds . ' does not represent exactly a day');
    }
    return ($seconds - ($seconds % DAY_LENGTH)) / DAY_LENGTH + 1;
  }

  public static function getTimestampFromDays($days)
  {
    return $days * DAY_LENGTH;
  }


  public static function getTypeIdFromRoom($room_id)
  {
    $types = sfConfig::get('app_room_types');
    $rooms = sfConfig::get('app_rooms');
    $my_room_type = $rooms[$room_id]['type'];
    foreach ($types as $key => $type) {
      if ($type['name'] == $my_room_type)
      {
        return $key;
      }
    }
    throw new Exception('Room type not found!');
  }

  /**
   * Return true if the destination room (id) is free in the interval.
   */
  public static function isBusyRoom($day_from, $day_to, $destination)
  {

  }

  private static $tr_class = 'odd';
  public static function getTrClass($first_time = false)
  {
    if ($first_time)
    {
      return self::$tr_class = 'odd';
    }
    return self::$tr_class = (self::$tr_class == 'odd' ? 'even' : 'odd');
  }

  public static function getArrangementsForSelect($id, $brief = false)
  {
    $tmp = sfConfig::get('app_room_types');
    $tmp = $tmp[$id]['arrangements'];
    $ret = array();
    foreach ($tmp as $key => $val)
    {
      $ret[$key] = $val[$brief? 'name' : 'description'];
    }
    return $ret;
  }

  public static function getRoomTypesForSelect($brief = false)
  {
    $tmp = sfConfig::get('app_room_types');
    $ret = array();
    foreach ($tmp as $key => $val)
    {
      $ret[$key] = $val[$brief? 'name' : 'description'];
    }
    return $ret;
  }

  public static function getArrivalTimesNamesForSelect($brief = false)
  {
    $tmp = sfConfig::get('app_arrival_times_available');
    $ret = array();
    foreach ($tmp as $key => $val)
    {
      $ret[$key] = $val[$brief ? 'name' : 'description'];
    }
    return $ret;
  }
}
