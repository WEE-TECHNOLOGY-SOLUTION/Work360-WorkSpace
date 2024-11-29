<?php

namespace Workdo\PayHere\Entities;

use Workdo\PayHere\Entities\Authorize;
use Workdo\PayHere\Entities\Capture;
use Workdo\PayHere\Entities\Charge;
use Workdo\PayHere\Entities\Checkout;
use Workdo\PayHere\Entities\PreApproval;
use Workdo\PayHere\Entities\Recurring;
use Workdo\PayHere\Entities\Refund;
use Workdo\PayHere\Entities\Retrieve;
use Workdo\PayHere\Entities\Subscription;

class PayHere
{
    public static function checkOut(): Checkout
    {
        return new Checkout();
    }

    public static function recurring(): Recurring
    {
        return new Recurring();
    }

    public static function preapproval(): PreApproval
    {
        return new PreApproval();
    }

    public static function charge(): Charge
    {
        return new Charge();
    }

    public static function retrieve(): Retrieve
    {
        return new Retrieve();
    }

    public static function subscription(): Subscription
    {
        return new Subscription();
    }

    public static function refund(): Refund
    {
        return new Refund();
    }

    public static function authorize(): Authorize
    {
        return new Authorize();
    }

    public static function capture(): Capture
    {
        return new Capture();
    }
}
