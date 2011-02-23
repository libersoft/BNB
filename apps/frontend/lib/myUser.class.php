<?php

class myUser extends sfBasicSecurityUser
{
  /**
   * Obtain a descriptive list of user selected rooms.
   * @return array an array with descriptive representation of user selected rooms.
   */
  public function getReadableRooms()
  {
    $user_rooms = $this->getAttribute('user_rooms', array());
    $room_arrangements = array();
    $room_types = array();
    foreach (sfConfig::get('app_room_types', array()) as $key => $room)
    {
      $room_types[$key] = $room['name'];
      $room_arrangements[$key] = $room['arrangements'];
    }
    $tmp_rooms = array();
    foreach ($user_rooms as $id => $room)
    {
      $tmp_rooms[$id] = array('type' => $room_types[$room['type']], 'arrangement' => $room_arrangements[$room['type']][$room['arrangement']], 'count' => $user_rooms[$id]['count']);
    }
    return $tmp_rooms;
  }

  /**
   * Set the flag used to determine if the first phase has been completed.
   */
  public function setGatherReservationDataDone()
  {
    $this->setAttribute('gatherReservationData_done', true);
  }
  
  public function isGatherReservationDataDone()
  {
    return $this->getAttribute('gatherReservationData_done', false);
  }
  
  public function setGatherPersonalDataDone()
  {
    $this->setAttribute('gatherPersonalData_done', true);
  }
  
  public function isGatherPersonalDataDone()
  {
    return $this->getAttribute('gatherPersonalData_done', false);
  }
}
