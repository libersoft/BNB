<?php
##IP_CHECK##
require_once(dirname(__FILE__).'/../config/ProjectConfiguration.class.php');

$configuration = ProjectConfiguration::getApplicationConfiguration('frontend', 'bnb1', false);
sfContext::createInstance($configuration)->dispatch();
