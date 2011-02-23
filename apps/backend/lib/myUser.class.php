<?php

class myUser extends sfBasicSecurityUser
{
  /**
   * Obtain a descriptive list of user selected rooms.
   * @param int $customer_id user identifier.
   * @return array descriptive representation of user selected rooms.
   */
  public function getReadableRooms($customer_id)
  {
    $user_rooms = $this->getAttribute($customer_id . '_rooms', array());
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
      $tmp_rooms[$id] = array(
        'type' => $room_types[$room['type']],
        'arrangement' => $room_arrangements[$room['type']][$room['arrangement']]['description'],
        'count' => $room['count']
      );
    }
    return $tmp_rooms;
  }

  public function setAuthenticated($authenticated) {
    global $configuration;
    $env = $configuration->getEnvironment();
    $this->setAttribute($env, $authenticated);
  }

  public function isAuthenticated() {
    global $configuration;
    $env = $configuration->getEnvironment();
    return $this->getAttribute($env) === true;
  }
}
