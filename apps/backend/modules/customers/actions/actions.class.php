<?php
/**
 * Customers module is used to manage customers and their reservation directly
 * from the backend bypassing the public interface.
 * This file impements customers actions.
 *
 * @package    reservations
 * @subpackage customers
 * @author     Michele Comignano <mc@libersoft.it>
 */
class customersActions extends sfActions
{
    /**
     * Symply redirect to the customers list.
     */
    public function executeIndex(sfRequest $request)
    {
        $this->forward('customers', 'list');
    }

    /**
     * Offers the pportunity to add a new customer to the system.
     * @see ../lib/form/AddCustomerForm
     */
    public function executeAdd()
    {
        $this->form = new AddCustomerForm();
        $this->setTemplate('edit');
    }

    /**
     * Save a new customer into the system.
     * @see ../lib/form/AddCustomerForm.class.php
     */
    public function executeSave(sfRequest $request)
    {
        $this->forward404Unless($request->isMethod('post'));
        $form = new AddCustomerForm(CustomerPeer::retrieveByPk($request->getParameter('id')));
        $form->bind($request->getParameter('customer'));
        if ($form->isValid())
        {
            $form->save();
            $this->redirect($this->getModuleName() . '/show?id='.$form->getObject()->getId());
        }
        else
        {
            $this->form = $form;
            $this->setTemplate('edit');
        }
    }


    public function executeEdit(sfRequest $request)
    {
        $customer = CustomerPeer::retrieveByPK($request->getParameter('id'));
        $this->forward404Unless($customer);
        $this->form = new AddCustomerForm($customer);
        $this->customer = $customer;
        return sfView::SUCCESS;
    }

    public function executeShow(sfRequest $request)
    {
        $customer = CustomerPeer::retrieveByPk((int)$request->getParameter('id'));
        $this->forward404Unless($customer);
        $this->customer = $customer;
        $c = new Criteria();
        $c->add(ReservationPeer::CUSTOMER_ID, $customer->getId(), Criteria::EQUAL);
        $c->addDescendingOrderByColumn(ReservationPeer::ID);
        $this->reservations = ReservationPeer::doSelect($c);
        return sfView::SUCCESS;
    }

    public function executeReservationsList(sfRequest $request)
    {
        $customer = CustomerPeer::retrieveByPk((int)$request->getParameter('customer_id'));
        $this->forward404Unless($request->isXmlHttpRequest() && $customer);
        $c = new Criteria();
        $c->add(ReservationPeer::CUSTOMER_ID, $customer->getId(), Criteria::EQUAL);
        $c->addDescendingOrderByColumn(ReservationPeer::ID);
        return $this->renderPartial('reservationsList', array('reservations' => ReservationPeer::doSelect($c)));
    }

    public function executeDeleteReservation(sfRequest $request)
    {
        $this->forward404Unless($request->isXmlHttpRequest());
        $c = new Criteria();
        $c->add(ReservationPeer::ID, $request->getParameter('id'), Criteria::EQUAL);
        $c->add(ReservationPeer::CUSTOMER_ID, $request->getParameter('customer_id'), Criteria::EQUAL);
        $this->forward404Unless(ReservationPeer::doDelete($c));
        $this->forward($this->getModuleName(), 'reservationsList');
    }

    public function executeDelete(sfRequest $request)
    {
        $customer = CustomerPeer::retrieveByPK($request->getParameter('id'));
        $this->forward404Unless($customer);
        $customer->delete();
        $this->forward('customers', 'list');
    }

    public function executeList(sfRequest $request)
    {
        $pager = new sfPropelPager('Customer', $this->getUser()->getAttribute('rows_per_page', sfConfig::get('app_reservations_view_rows_per_page')));
        $c = new Criteria();
        $c->addDescendingOrderByColumn(CustomerPeer::ID);
        $pager->setCriteria($c);
        $last_page = $this->getUser()->getAttribute('last_customers_list_page', 1);
        $pager->setPage($request->getParameter('page', $last_page));
        $this->getUser()->setAttribute('last_customers_list_page', $pager->getPage());
        $pager->init();
        $this->pager = $pager;
        return sfView::SUCCESS;
    }

}

