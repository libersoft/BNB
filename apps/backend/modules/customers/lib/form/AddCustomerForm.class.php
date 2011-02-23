<?php
/**
 * Add customer form.
 *
 * @package    form
 * @subpackage customer
 */
class AddCustomerForm extends CustomerForm {
    public function configure() {
        unset($this->validatorSchema['created_at']);
        unset($this->widgetSchema['created_at']);
        
        /**
         * Creo un array con valori dall'anno corrente all'anno corrente + 20.
         * Uso l'array_combine perché ci seve essere corrispondenza 1:1 tra
         * chiave e valore dell'array, in modo da creare una select in cui
         * valore ed etichetta corrispondano.
         **/
        $year_range = range(date('Y', time()), date('Y', time()) + 20);        
        $this->widgetSchema['cc_expire_year'] = new sfWidgetFormSelect(array(
            'choices' => array_combine($year_range, $year_range)
        ));
        
        $month_range = range(1, 12);        
        $this->widgetSchema['cc_expire_month'] = new sfWidgetFormSelect(array(
            'choices' => array_combine($month_range, $month_range)
        ));
        
        $this->widgetSchema['country'] = new sfWidgetFormI18nSelectCountry(array(
            'culture' => 'en'));
        $this->widgetSchema['language'] = new sfWidgetFormI18nSelectLanguage(array(
            'culture' => 'en',
            'languages' => array(
            'en',
            'it')
            )
        );
        $this->widgetSchema['cc_type'] = new sfWidgetFormSelect(array(
            'choices' => sfConfig::get('app_credit_cards_accepted')
        ));
        $this->widgetSchema['comments']->setAttributes(array(
            'rows' => 10,
            'cols' => 40));

        $this->validatorSchema['email'] = new sfValidatorAnd(
            array(
            $this->validatorSchema['email'],
            new sfValidatorEmail()),
            array('required' => false)
        );
        // Va bene perchè il valore viene da combo preriempita.
        $this->validatorSchema['cc_expire_month'] = new sfValidatorPass();
        $this->validatorSchema['cc_expire_year'] = new sfValidatorPass();
        $this->validatorSchema['cc_number'] = new sfValidatorAnd(array(
            new sfValidatorString(array(
               'min_length' => 16,
               'max_length' => 16)),
            //      new sfValidatorInteger()
               ),
               array('required' => false)
           );
        $this->validatorSchema['cc_securcode'] = new sfValidatorAnd(array(
            new sfValidatorString(array(
                'min_length' => 3,
                'max_length' => 4)
            ),
            new sfValidatorInteger()),
            array('required' => false)
        );
        $this->widgetSchema->setLabels(array(
            'language'    => 'Spoken language:',
            'cc_securcode'   => 'Credit card security code:',
            'cc_number' => 'Credit card number:',
            'cc_expire_year' => 'Credit card expiry year:',
            'cc_expire_month' => 'Credit card expiry month:',
            'comments' => 'Comments/notes:'
        ));
    }
}
