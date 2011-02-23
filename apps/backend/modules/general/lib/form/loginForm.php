<?php
/**
 * Add customer form.
 *
 * @package    form
 * @subpackage customer
 */
class loginForm extends sfForm
{
    public function configure()
    {
        $this->widgetSchema['username'] = new sfWidgetFormInput();
        $this->widgetSchema['password'] = new sfWidgetFormInputPassword();

        $this->validatorSchema['username'] = new sfValidatorString();
        $this->validatorSchema['password'] = new sfValidatorString();

        $this->widgetSchema->setLabels(array(
            'username'=> 'User name:',
            'password' => 'User password:'
            ));
    }
}
