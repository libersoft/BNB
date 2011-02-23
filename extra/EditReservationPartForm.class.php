<?php
class AddReservationForm extends sfForm {

    public function configure(ReservationPart $reservation_part)
    {
        $this->setWidgets(array(
            'arrangement' => new sfWidgetFormSelect(array('choices' => $reservation_part->getArrangementsForSelect())),
            'arrival_time' => new sfWidgetFormSelect(array('choices' => rsCommon::getArrivalTimesNamesForSelect(true))),
            'notes' => new sfWidgetFormTextarea()
            ));

        $this->setDefaults(array(
            'arrangement' => time(),
            'arrival_time' => $reservation_part->getReservation()->getArrivalTime(),
            'notes' => $reservation_part->getReservation()->getNotes()
            ));

        $this->widgetSchema->setLabels(array(
            'arrangement'    => 'New arrangement:',
            'arrival_time'   => 'New arrival time:',
            'notes' => 'Notes:',
            ));

        $this->setValidators(array(
      'date_from' => new sfValidatorDate(array('required' => true)),
      'date_to' => new sfValidatorDate(array('required' => true)),
      'arrival_time' => new sfValidatorChoice(array('choices' => array_keys(rsCommon::getArrivalTimesNamesForSelect())))
            ));

        $this->widgetSchema->setNameFormat('reservation[%s]');
    }
}
