<?php
class AddReservationForm extends sfForm {
  public function configure()
  {
    $this->setWidgets(array(
      'date_from'    => new sfWidgetFormI18nDate(array(
                                'culture' => 'en',
                                )),
      'date_to'   => new sfWidgetFormI18nDate(array(
                                'culture' => 'en',
                                )),
      'arrival_time' => new sfWidgetFormSelect(array('choices' => rsCommon::getArrivalTimesNamesForSelect()))
      ));

    $this->setDefaults(array(
      'date_from' => time(),
      'date_to' => time() + 60 * 60 * 24 * 2
      ));

    $this->widgetSchema->setLabels(array(
      'date_from'    => 'Arrival date:',
      'date_to'   => 'Departing date:',
      'arrival_time' => 'Arrival time:',
      ));

    $this->setValidators(array(
      'date_from' => new sfValidatorDate(array('required' => true)),
      'date_to' => new sfValidatorDate(array('required' => true)),
      'arrival_time' => new sfValidatorChoice(array('choices' => array_keys(rsCommon::getArrivalTimesNamesForSelect())))
      ));
  

    $this->validatorSchema->setPostValidator(
      new sfValidatorSchemaCompare('date_from', sfValidatorSchemaCompare::LESS_THAN, 'date_to',
        array('throw_global_error' => true),
        array('invalid' => 'Arrival date should be before the departing date')
      )
    );
    $this->widgetSchema->setNameFormat('reservation[%s]');
  }
}
