<?php

namespace Workdo\PayHere\Entities;

use Workdo\PayHere\Entities\PayHereClient;

class Authorize extends PayHereClient
{
    protected $url = "pay/authorize";
}
