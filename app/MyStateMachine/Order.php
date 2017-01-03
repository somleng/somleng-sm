<?php
/**
 * Created by PhpStorm.
 * User: phyrum
 * Date: 12/30/16
 * Time: 10:16 AM
 */

namespace App\MyStateMachine;


class Order
{
    // First state graph, payment status
    const PAYMENT_PENDING  = 'pending';
    const PAYMENT_ACCEPTED = 'accepted';
    const PAYMENT_REFUSED  = 'refused';

    // second state graph, shipping status
    const SHIPPING_PENDING = 'pending';
    const SHIPPING_PARTIAL = 'partial';
    const SHIPPING_SHIPPED = 'shipped';

    private $paymentStatus;
    private $shippingStatus;

    public function setPaymentStatus($paymentStatus)
    {
        $this->paymentStatus = $paymentStatus;
    }

    public function getPaymentStatus()
    {
        return $this->paymentStatus;
    }

    public function setShippingStatus($shippingStatus)
    {
        $this->shippingStatus = $shippingStatus;
    }

    public function getShippingStatus()
    {
        return $this->shippingStatus;
    }


}