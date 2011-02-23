<?php
##IP_CHECK##
require_once(dirname(__FILE__).'/../config/ProjectConfiguration.class.php');

$configuration = ProjectConfiguration::getApplicationConfiguration('backend', 'bnb1', false);
sfContext::createInstance($configuration)->dispatch();
