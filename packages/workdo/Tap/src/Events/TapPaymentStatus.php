<?php

namespace Workdo\Tap\Events;

use Illuminate\Queue\SerializesModels;

class TapPaymentStatus
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

    public function __construct($data, $type, $payment)
    {
        $this->data = $data;
        $this->type = $type;
        $this->payment = $payment;
    }

    /**
     * Get the channels the event should be broadcast on.
     *
     * @return array
     */
    public function broadcastOn()
    {
        return [];
    }
}
