<?php
/**
 * Created by PhpStorm.
 * User: lahiru
 * Date: 11/26/21
 * Time: 3:09 PM
 */

namespace Workdo\PayHere\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Workdo\PayHere\Entities\Authorize;
use Workdo\PayHere\Entities\Checkout;
use Workdo\PayHere\Entities\PreApproval;
use Workdo\PayHere\Entities\Recurring;
use Workdo\PayHere\Events\AuthorizeCallbackEvent;
use Workdo\PayHere\Events\CheckoutCallbackEvent;
use Workdo\PayHere\Events\PreapprovalCallbackEvent;
use Workdo\PayHere\Events\RecurringCallbackEvent;

class CallbackController extends Controller
{
    public function handle($type, Request $request)
    {
        switch ($type) {
            case Authorize::getCallbackKey():
                event(new AuthorizeCallbackEvent($request->all()));
                break;

            case Checkout::getCallbackKey():
                event(new CheckoutCallbackEvent($request->all()));
                break;

            case Recurring::getCallbackKey():
                event(new RecurringCallbackEvent($request->all()));
                break;

            case PreApproval::getCallbackKey():
                event(new PreapprovalCallbackEvent($request->all()));
                break;

            case "test":
                break;
        }
    }
}
