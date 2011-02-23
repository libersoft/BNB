<?php

class AddRoomForm extends sfForm
{
    public function configure()
    {
        $room_types = sfConfig::get('app_room_types');
        $this->widgetSchema['room_type'] = new sfWidgetFormSelect(array(
            'choices' => rsCommon::getRoomTypesForSelect()
            ));
        $this->widgetSchema->setLabel('room_type', 'Room type:');
        foreach ($room_types as $id => $type)
        {
            $this->widgetSchema[$id . '_arrangement'] = new sfWidgetFormSelect(array(
                'choices' => rsCommon::getArrangementsForSelect($id)
                ));
        }
        $this->widgetSchema['room_count'] = new sfWidgetFormSelect(array(
            'choices' => array(1 => 1, 2, 3, 4, 5, 6, 7, 8, 9, 10)
            ));
        $this->widgetSchema->setLabel('count', 'Quantity');
    }
}