<?php
/**
 * Reservation actions. The public part of the system used by a customer
 * to send his reservation request.
 * @package    bnb
 * @subpackage reservation
 * @author     Michele Comignano <mc@libersoft.it>
 */
class reservationActions extends sfActions
{
  /**
   * To speed-up similar-properties fields.
   * @var array many reservation and customer field names.
   */
  private $session_fields = array(
    'reservation' => array('date_from', 'date_to', 'arrival_time'),
    'personal' => array('name', 'surname', 'email', 'phone', 'mobile', 'fax', 'address', 'zip', 'city', 'country', 'state', 'comments'),
    'confirmation' => array('cc_type', 'cc_number', 'cc_securcode', 'cc_expire_month', 'cc_expire_year', 'address', 'zip', 'city', 'country')
  );

  /**
   * Set specific "per environment" template if available.
   */
  public function preExecute() {
    $env = $this->getContext()->getConfiguration()->getEnvironment();
    if (file_exists(dirname(__FILE__) . '/../../../templates/' . $env . '.php')) {
      $this->setLayout($env);
    }
  }

  /**
   * Execute index action showing the form to choice reservation details.
   */
  public function executeIndex(sfRequest $request)
  {
    $this->getUser()->getAttributeHolder()->clear();
    $supported_langs = array_keys(sfConfig::get('app_languages_available'));
    $this->getUser()->setCulture($request->getPreferredCulture($supported_langs));
    $this->forward($this->getModuleName(), 'gatherReservationData');
  }

  /**
   * Execute gatherReservationData action to gather reservation data (rooms, dates).
   */
  public function executeGatherReservationData(sfRequest $request)
  {
    $room_types = sfConfig::get('app_room_types', array());
    $room_types_options = array();
    $tmp_room_arrangement_options = array();
    foreach ($room_types as $id => $room_type)
    {
      $room_types_options[] = $room_type['name'];
      $arrangement_options = array();
      foreach ($room_type['arrangements'] as $key => $val)
      {
        array_push($arrangement_options, $room_type['arrangements'][$key]['description']);
      }
      $tmp_room_arrangement_options[$id] = $arrangement_options;
    }
    foreach ($this->session_fields['reservation'] as $field)
    {
      $this->$field = $request->getParameter($field, '');
    }
    $this->header = sfConfig::get('app_policy_' . $this->getUser()->getCulture(), '');
    $this->rooms = $this->getUser()->getReadableRooms();
    $this->room_types = $room_types_options;
    $this->room_arrangements = $tmp_room_arrangement_options;
    $this->room_count_options = array(1 => 1, 2, 3, 4, 5, 6, 7, 8, 9, 10);// TODO questo farlo con config
    $this->arrival_time_options = rsCommon::getArrivalTimesNamesForSelect();
    return sfView::SUCCESS;
  }

  /**
   * Execute removeRoom action to remove room(s) from selected ones
   * @return <type>
   */
  public function executeRemoveRoom(sfRequest $request)
  {
    $this->forward404Unless($request->isXmlHttpRequest());
    $user_rooms = $this->getUser()->getAttribute('user_rooms', array());
    if ($this->hasRequestParameter('id'))
    {
      $id = (int)$request->getParameter('id') - 1;
      if (array_key_exists($id, $user_rooms))
      {
        unset($user_rooms[$id]);
        $this->getUser()->setAttribute('user_rooms', $user_rooms);
      }
    }
    $rooms = $this->getUser()->getReadableRooms($user_rooms);
    return $this->renderPartial($this->getModuleName() . '/selectedRooms', array('rooms' => $rooms, 'editable' => true));
  }

  /**
   * Execute addRoom action to process AJAX requests from the room selection page.
   */
  public function executeAddRoom($request)
  {
    $this->forward404Unless($request->isXmlHttpRequest());
    $user_rooms = $this->getUser()->getAttribute('user_rooms', array());
    if ($request->hasParameter('room_type') && $request->hasParameter('room_count'))
    {
      $room_type = $request->getParameter('room_type');
      if ($request->hasParameter($room_type . '_arrangement_options'))
      {
        array_push($user_rooms, array('type' => $room_type, 'arrangement' => $request->getParameter($room_type . '_arrangement_options'), 'count' => $request->getParameter('room_count')));
        $this->getUser()->setAttribute('user_rooms', $user_rooms);
      }
    }
    $rooms = $this->getUser()->getReadableRooms($user_rooms);
    return $this->renderPartial($this->getModuleName() . '/selectedRooms', array('rooms' => $rooms, 'editable' => true));
  }

  /**
   * Validate saveReservationData action request to be sure about data correctness.
   */
  public function validateSaveReservationData()
  {
    $return = true;
    $errors = $this->getRequest()->getErrors();
    $time_from;
    $time_to;
    if (!array_key_exists('date_from', $errors))
    {
      $time_from = sfContext::getInstance()->getI18N()->getTimestampForCulture($this->getRequest()->getParameter('date_from'), $this->getUser()->getCulture());
      if ($time_from <= time())
      {
        $this->getRequest()->setError('date_from', 'Arrival date could not be in the past or today');
        $return = false;
      }
      else if (!array_key_exists('date_to', $errors))
      {
        $time_to = sfContext::getInstance()->getI18N()->getTimestampForCulture($this->getRequest()->getParameter('date_to'), $this->getUser()->getCulture());
        if ($time_from >= $time_to)
        {
          $this->getRequest()->setError('date_to', 'Check-out date could not be the same day or before the arrival date');
          $return = false;
        }
        else if ((($time_to - $time_from) / 86400 < sfConfig::get('app_minimum_stay')))
        {
          $this->getRequest()->setError('date_to', 'Minimum stay not respected');
          $return = false;
        }
      }
    }
    if (count($this->getUser()->getAttribute('user_rooms', array())) == 0)
    {
      $this->getRequest()->setError('rooms', 'You should "Add" at least one room. Please use the form above');
      $return = false;
    }
    return $return;
  }

  /**
   * Handle saveReservationData request in case of errors.
   */
  public function handleErrorSaveReservationData()
  {
    $this->forward($this->getModuleName(), 'gatherReservationData');
  }

  /**
   * Execute saveReservationData action to save reservation data within the user session.
   */
  public function executeSaveReservationData($request)
  {
    $this->forward404Unless($request->getMethod() == sfRequest::POST);
    foreach ($this->session_fields['reservation'] as $field)
    {
      $this->getUser()->setAttribute($field, $request->getParameter($field));
    }
    $this->getUser()->setGatherReservationDataDone();
    $this->forward($this->getModuleName(), '/gatherPersonalData');
  }

  /**
   * Execute gatherPersonalData action to gather personal data.
   */
  public function executeGatherPersonalData()
  {
    $this->forward404Unless($this->getUser()->isGatherReservationDataDone());
    $this->cc_type_options = sfConfig::get('app_credit_cards_accepted', array());
    $this->action_name = $this->getActionName();
    return sfView::SUCCESS;
  }

  /**
   * Handle savePersonalData action errors.
   */
  public function handleErrorSavePersonalData()
  {
    $this->forward($this->getModuleName(), 'gatherPersonalData');
  }

  /**
   * Executes savePersonalData action to definitely create a new reservation.
   */
  public function executeSavePersonalData($request)
  {
    $this->forward404Unless($this->getUser()->isGatherReservationDataDone());
    foreach ($this->session_fields['personal'] as $field_name)
    {
      $this->getUser()->setAttribute($field_name, $request->getParameter($field_name));
    }
    foreach ($this->session_fields['confirmation'] as $field_name)
    {
      $this->getUser()->setAttribute($field_name, $request->getParameter($field_name));
    }
    $this->getUser()->setGatherPersonalDataDone();
    $this->forward($this->getModuleName(), 'summary');
  }

  /**
   * Validates savePersonalData action to be sure the user entered correct data.
   */
  public function validateSavePersonalData()
  {
    $return = true;
    if ($this->getRequest()->getParameter('cc_number', '') != '')
    {
      foreach ($this->session_fields['confirmation'] as $field)
      {
        if ($this->getRequest()->getParameter($field, '') == '')
        {
          $return = false;
          $this->getRequest()->setError($field, 'Field required if you specify a credit card number for confirmation');
        }
      }
    }
    return $return;
  }

  /**
   * Executes summary action showing a summary about the reservation done.
   */
  public function executeSummary()
  {
    $this->forward404Unless($this->getUser()->isGatherReservationDataDone() && $this->getUser()->isGatherPersonalDataDone());
    $this->initSummaryVars(true);
    return sfView::SUCCESS;
  }

  /**
   * Execute confirmReservation action to save reservation data on the database.
   */
  public function executeConfirmReservation($request)
  {
    $this->forward404Unless($request->isXmlHttpRequest() && $this->getUser()->isGatherReservationDataDone() && $this->getUser()->isGatherPersonalDataDone());
    $customer = new Customer();
    $customer->fromArray(array(
      'Name' => $this->getUser()->getAttribute('name'),
      'Surname' => $this->getUser()->getAttribute('surname'),
      'Email' => $this->getUser()->getAttribute('email'),
      'Address' => $this->getUser()->getAttribute('address'),
      'Zip' => $this->getUser()->getAttribute('zip'),
      'City' => $this->getUser()->getAttribute('city'),
      'Country' => $this->getUser()->getAttribute('country'),
      'State' => $this->getUser()->getAttribute('state'),
      'Phone' => $this->getUser()->getAttribute('phone'),
      'Mobile' => $this->getUser()->getAttribute('mobile'),
      'Fax' => $this->getUser()->getAttribute('fax'),
      'CcType' => $this->getUser()->getAttribute('cc_type'),
      'CcExpireMonth' => $this->getUser()->getAttribute('cc_expire_month'),
      'CcExpireYear' => $this->getUser()->getAttribute('cc_expire_year'),
      'CcNumber' => $this->getUser()->getAttribute('cc_number'),
      'CcSecurcode' => $this->getUser()->getAttribute('cc_securcode'),
      'Comments' => $this->getUser()->getAttribute('comments'),
      'Language' => substr($this->getUser()->getCulture(), 0, 2),
      'Ip' => $_SERVER['REMOTE_ADDR'],
      ));
    $customer->save();
    foreach ($this->getUser()->getAttribute('user_rooms') as $room)
    {
      for ($i = 0; $i < $room['count']; $i++)
      {
        $reservation = new Reservation();
        $reservation->fromArray(array(
          'CustomerId' => $customer->getId(),
          'ArrivalTime' => $this->getUser()->getAttribute('arrival_time'),
          'DayFrom' => rsCommon::getDaysFromTimestamp(sfContext::getInstance()->getI18N()->getTimestampForCulture($this->getUser()->getAttribute('date_from'), $this->getUser()->getCulture()), true),
          'DayTo' => rsCommon::getDaysFromTimestamp(sfContext::getInstance()->getI18N()->getTimestampForCulture($this->getUser()->getAttribute('date_to'), $this->getUser()->getCulture()), true),
          'Notes' => '',
          'ArrivalTimeOrig' => $this->getUser()->getAttribute('arrival_time'),
          'DayFromOrig' => rsCommon::getDaysFromTimestamp(sfContext::getInstance()->getI18N()->getTimestampForCulture($this->getUser()->getAttribute('date_from'), $this->getUser()->getCulture()), true),
          'DayToOrig' => rsCommon::getDaysFromTimestamp(sfContext::getInstance()->getI18N()->getTimestampForCulture($this->getUser()->getAttribute('date_to'), $this->getUser()->getCulture()), true),
          'TypeOrig' => $room['type'],
          'ArrangementOrig' => $room['arrangement']));
        $reservation->save();
        $reservation_part = new ReservationPart();
        $reservation_part->fromArray(array(
          'ReservationId' => $reservation->getId(),
          'CustomerId' => $customer->getId(),
          'Type' => $room['type'],
          'Arrangement' => $room['arrangement'],
          'DayFrom' => rsCommon::getDaysFromTimestamp(sfContext::getInstance()->getI18N()->getTimestampForCulture($this->getUser()->getAttribute('date_from'), $this->getUser()->getCulture()), true),
          'DayTo' => rsCommon::getDaysFromTimestamp(sfContext::getInstance()->getI18N()->getTimestampForCulture($this->getUser()->getAttribute('date_to'), $this->getUser()->getCulture()), true),
          'Room' => NULL));
        $reservation_part->save();
      }
    }
    $admin_mail = $this->getController()->getPresentationFor($this->getModuleName(), 'sendAdminEmail');
    $this->logMessage($admin_mail, 'debug');
    try
    {
      $mailer = new Swift(new Swift_Connection_NativeMail());
      $message = new Swift_Message(sfConfig::get('app_admin_email_subject') . ' ' . $this->getUser()->getAttribute('surname'), $admin_mail);
      $message->setReplyTo($this->getUser()->getAttribute('email'));
      $sender = sfConfig::get('app_admin_email_sender_address');
      $recipients = new Swift_RecipientList();
      foreach (sfConfig::get('app_admin_email_receivers') as $recipient)
      {
        $recipients->addCc($recipient);
      }
      $mailer->send($message, $recipients, $sender);
      $mailer->disconnect();
    }
    catch (Exception $e)
    {
      $this->logMessage('Unable to send email: ' . $e->getMessage(), 'debug');
      $mailer->disconnect();
    }
    $this->getUser()->getAttributeHolder()->clear();
    return sfView::SUCCESS;
  }

  //  public function executeSendCustomerEmail()
  //  {
  //    $this->initSummaryVars();
  //  }

  /**
   * This generate the message for the admin email.
   */
  public function executeSendAdminEmail()
  {
    $this->initSummaryVars();
    $this->panel_url = sfConfig::get('app_panel_url');
    $this->setLayout(false);
  }

  /**
   * Executes error404 action when a page not found error is occours.
   */
  public function executeError404()
  {
    $this->getUser()->getAttributeHolder()->clear();
    return sfView::SUCCESS;
  }

  /**
   * Initialize reservation summary variables in use within generated emails and summary page.
   */
  private function initSummaryVars($secure_html = false)
  {
    $this->rooms = $this->getUser()->getReadableRooms($this->getUser()->getAttribute('user_rooms', array()));
    $tmp_from = sfContext::getInstance()->getI18N()->getTimestampForCulture($this->getUser()->getAttribute('date_from'), $this->getUser()->getCulture());
    $tmp_to = sfContext::getInstance()->getI18N()->getTimestampForCulture($this->getUser()->getAttribute('date_to'), $this->getUser()->getCulture());
    $this->total_stay = round(($tmp_to - $tmp_from) / (60 * 60 * 24));
    foreach ($this->session_fields['reservation'] as $field_name)
    {
      $this->$field_name = $this->getUser()->getAttribute($field_name);
    }
    $arrival_time_options = sfConfig::get('app_arrival_times_available');
    $this->arrival_time = $arrival_time_options[$this->getUser()->getAttribute('arrival_time')]['description'];
    foreach ($this->session_fields['personal'] as $field_name)
    {
      $this->$field_name = $this->getUser()->getAttribute($field_name);
    }
    if ($secure_html)
    {
      $this->comments = htmlspecialchars($this->comments);
    }
    foreach ($this->session_fields['confirmation'] as $field_name)
    {
      $this->$field_name = $this->getUser()->getAttribute($field_name);
    }
    $cc_type_options = sfConfig::get('app_credit_cards_accepted', array());
    $this->cc_type = $cc_type_options[$this->getUser()->getAttribute('cc_type')];
    $tmp_cc_number = $this->getUser()->getAttribute('cc_number', '');
    if (!empty($tmp_cc_number))
    {
      $this->cc_number = substr($tmp_cc_number, -4);
    }
    $tmp_securcode = '***';
    if (strlen($this->getUser()->getAttribute('cc_securcode', '')) > 3)
    {
      $tmp_securcode.= '*';
    }
    $this->cc_securcode = $tmp_securcode;
  }
}
