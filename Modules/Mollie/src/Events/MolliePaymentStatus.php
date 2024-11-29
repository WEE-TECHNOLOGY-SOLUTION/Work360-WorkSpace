<?php

namespace Workdo\Mollie\Events;

use Illuminate\Queue\SerializesModels;

class MolliePaymentStatus
{
    use SerializesModels;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public $data;
    public $type;
    public $payment;

    public function __construct($data,$type,$payment)
    {
        $this->data = $data;
        $this->type = $type;
        $this->payment = $payment;
    }
}
