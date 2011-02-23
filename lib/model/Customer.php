<?php

/**
 * Subclass for representing a row from the 'customers' table.
 *
 *
 *
 * @package lib.model
 */
class Customer extends BaseCustomer
{
  public function getFullName()
  {
    return $this->getName() . ' ' . $this->getSurname();
  }
  public function getCcTypeName()
  {
    $cc_types = sfConfig::get('app_credit_cards_accepted');
    return $cc_types[$this->getCcType()];
  }
  public function getCcExpiry()
  {
    return $this->getCcExpireMonth() . '/' . $this->getCcExpireYear();
  }
}
