<?php
/**
 * Created by PhpStorm.
 * User: lahiru
 * Date: 11/26/21
 * Time: 4:03 PM
 */

namespace Workdo\PayHere\Events;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class AuthorizeCallbackEvent
{
    use Dispatchable;
    use InteractsWithSockets;
    use SerializesModels;

    public $payload;

    public function __construct($payload)
    {
        $this->payload = $payload;
    }
}
