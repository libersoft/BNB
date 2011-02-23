<?php use_helper('Javascript', 'Form') ?>
<?php echo select_tag('arrangement-' . $reservation->getId(),  options_for_select($reservation->getArrangementsForSelect(), $reservation->getArrangementForSelect()), array('onchange' => remote_function(array(
          'update'   => 'arrangement-td-' . $reservation->getId(),
          'url'      => 'reservations/changeArrangement?id=' . $reservation->getId(),
          'with' => '"arrangement="+$("arrangement-' . $reservation->getId() . '").value',
          'script' => true)))) ?>