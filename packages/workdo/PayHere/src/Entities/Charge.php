<?php

namespace Workdo\PayHere\Entities;

use Workdo\PayHere\Entities\PayHereRestClient;

class Charge extends PayHereRestClient
{
    protected $url = "merchant/v1/payment/charge";

    public function byToken($token)
    {
        $this->form_data['customer_token'] = $token;

        return $this;
    }
}
