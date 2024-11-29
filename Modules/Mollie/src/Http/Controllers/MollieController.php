<?php

namespace Workdo\Mollie\Http\Controllers;

use App\Models\EmailTemplate;
use App\Models\Order;
use App\Models\Plan;
use App\Models\Setting;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use Workdo\Mollie\Events\MolliePaymentStatus;
use Illuminate\Support\Facades\Cookie;
use Workdo\Holidayz\Entities\Hotels;
use Workdo\Holidayz\Entities\RoomBookingCart;
use Workdo\Holidayz\Entities\BookingCoupons;
use Workdo\Holidayz\Entities\HotelCustomer;
use Workdo\Holidayz\Entities\RoomBooking;
use Workdo\Holidayz\Entities\RoomBookingOrder;
use Workdo\Holidayz\Entities\UsedBookingCoupons;
use Workdo\Holidayz\Events\CreateRoomBooking;

class MollieController extends Controller
{

    public $api_key;
    public $profile_id;
    public $partner_id;
    public $is_enabled;
    public $currancy;

    public function setting(Request $request)
    {
        if(Auth::user()->isAbleTo('mollie payment manage'))
        {
            if($request->has('mollie_payment_is_on'))
            {
                $validator = Validator::make($request->all(), [
                    'company_mollie_api_key' => 'required|string',
                    'company_mollie_profile_id' => 'required|string',
                    'company_mollie_partner_id' => 'required|string'
                ]);
                if($validator->fails()){
                    $messages = $validator->getMessageBag();

                    return redirect()->back()->with('error', $messages->first());
                }
            }
            $getActiveWorkSpace = getActiveWorkSpace();
            $creatorId = creatorId();
            if($request->has('mollie_payment_is_on'))
            {
                $post = $request->all();
                unset($post['_token']);
                foreach ($post as $key => $value) {
                    // Define the data to be updated or inserted
                    $data = [
                        'key' => $key,
                        'workspace' => $getActiveWorkSpace,
                        'created_by' => $creatorId,
                    ];

                    // Check if the record exists, and update or insert accordingly
                    Setting::updateOrInsert($data, ['value' => $value]);
                }
            }else{
                $data = [
                    'key' => 'mollie_payment_is_on',
                    'workspace' => $getActiveWorkSpace,
                    'created_by' => $creatorId,
                ];
                // Check if the record exists, and update or insert accordingly
                Setting::updateOrInsert($data, ['value' => 'off']);
            }
            // Settings Cache forget
            AdminSettingCacheForget();
            comapnySettingCacheForget();
            return redirect()->back()->with('success','Mollie setting save sucessfully.');
        }
        else
        {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    public function payment_setting($id = Null, $wokspace = Null)
    {
        if (!empty($id) && empty($wokspace)) {
            $company_settings = getCompanyAllSetting($id);
        } elseif (!empty($id) && !empty($wokspace)) {
            $company_settings = getCompanyAllSetting($id, $wokspace);
        } else {
            $company_settings = getCompanyAllSetting();
        }
        $this->currancy      = !empty($company_settings['defult_currancy']) ? $company_settings['defult_currancy'] : '$';
        $this->api_key       = !empty($company_settings['company_mollie_api_key']) ? $company_settings['company_mollie_api_key'] : '';
        $this->profile_id    = !empty($company_settings['company_mollie_profile_id']) ? $company_settings['company_mollie_profile_id'] : '';
        $this->partner_id    = !empty($company_settings['company_mollie_partner_id']) ? $company_settings['company_mollie_partner_id'] : '';
        $this->is_enabled    = !empty($company_settings['mollie_payment_is_on']) ? $company_settings['mollie_payment_is_on'] : 'off';
    }

    public function invoicePayWithMollie(Request $request)
    {
        if ($request->type == "invoice") {
            $invoice      = \App\Models\Invoice::find($request->invoice_id);
            $user_id      = $invoice->created_by;
            $wokspace        = $invoice->workspace;
        } elseif ($request->type == "salesinvoice") {
            $invoice      = \Workdo\Sales\Entities\SalesInvoice::find($request->invoice_id);
            $user_id      = $invoice->created_by;
            $wokspace        = $invoice->workspace;
        }
        elseif ($request->type == "retainer") {
            $invoice      = \Workdo\Retainer\Entities\Retainer::find($request->invoice_id);
            $user_id      = $invoice->created_by;
            $wokspace        = $invoice->workspace;
        }
        self::payment_setting($user_id, $wokspace);
        if ($invoice) {
            try {
                $price = $request->amount;
                $type = $request->type;
                if ($price > 0) {
                    $mollie = new \Mollie\Api\MollieApiClient();
                    $mollie->setApiKey($this->api_key);

                    $payment = $mollie->payments->create(
                        [
                            "amount" => [
                                "currency" =>  $this->currancy,
                                "value" => number_format($price, 2),
                            ],
                            "description" => "payment for product",
                            "redirectUrl" => route(
                                'invoice.mollie',
                                [
                                    $request->invoice_id,
                                    $price,
                                    $type,
                                ]
                            ),
                        ]
                    );

                    session()->put('mollie_payment_id', $payment->id);

                    return redirect($payment->getCheckoutUrl())->with('payment_id', $payment->id);
                } else {
                    $res['msg']  = __("Enter valid amount.");
                    $res['flag'] = 2;

                    return $res;
                }
            } catch (\Exception $e) {
                return redirect()->back()->with('error', $e->getMessage());
            }
        } else {
            return redirect()->back()->with('error', 'Oops something went wrong.');
        }
    }

    public function getInvoicePaymentStatus($invoice_id, $amount, $type, Request $request)
    {
        if (!empty($invoice_id) && !empty($amount) && !empty($type))
        {
            if ($type == "invoice") {
                $mollie = new \Mollie\Api\MollieApiClient();
                $invoice    =  \App\Models\Invoice::find($invoice_id);
                $user_id = $invoice->created_by;
                $wokspace        = $invoice->workspace;
                self::payment_setting($user_id, $wokspace);
                $mollie->setApiKey($this->api_key);

                if ($invoice && session()->has('mollie_payment_id')) {
                    try {
                        $payment = $mollie->payments->get(session()->get('mollie_payment_id'));
                        $orderID = strtoupper(str_replace('.', '', uniqid('', true)));

                        if ($payment->isPaid()) {
                            $invoice_payment                       = new \App\Models\InvoicePayment();
                            $invoice_payment->invoice_id           = $invoice_id;
                            $invoice_payment->date                 = date('Y-m-d');
                            $invoice_payment->amount               = isset($amount) ? $amount : 0;
                            $invoice_payment->account_id           = 0;
                            $invoice_payment->payment_method       = 0;
                            $invoice_payment->order_id             = $orderID;
                            $invoice_payment->currency             = isset($payment->amount->currency) ? $payment->amount->currency : 'USD';
                            $invoice_payment->payment_type         = __('MOLLIE');
                            $invoice_payment->save();
                            $due     = $invoice->getDue();
                            if ($due <= 0) {
                                $invoice->status = 4;
                                $invoice->save();
                            } else {
                                $invoice->status = 3;
                                $invoice->save();
                            }
                            if (($invoice->getDue() - $invoice_payment->amount) == 0) {
                                $invoice->status = 3;
                                $invoice->save();
                            }
                            if(module_is_active('Account'))
                            {
                                //for customer balance update
                                \Workdo\Account\Entities\AccountUtility::updateUserBalance('customer', $invoice->customer_id, $invoice_payment->amount, 'debit');
                            }
                            event(new MolliePaymentStatus($invoice,$type,$invoice_payment));
                            return redirect()->route('pay.invoice', encrypt($invoice_id))->with('success', __('Invoice paid Successfully!'));
                        } else {
                            return redirect()->route('pay.invoice', encrypt($invoice_id))->with('success', __('Transaction fail'));
                        }
                    } catch (\Exception $e) {

                        return redirect()->route('pay.invoice', encrypt($invoice_id))->with('success', $e->getMessage());
                    }
                } else {

                    return redirect()->route('pay.invoice', encrypt($invoice_id))->with('success', __('Invoice not found.'));
                }
            }
            elseif ($type == "salesinvoice") {
                $mollie = new \Mollie\Api\MollieApiClient();
                $invoice    =  \Workdo\Sales\Entities\SalesInvoice::find($invoice_id);
                $user_id = $invoice->created_by;
                $wokspace        = $invoice->workspace;
                self::payment_setting($user_id, $wokspace);
                $mollie->setApiKey($this->api_key);

                if ($invoice && session()->has('mollie_payment_id')) {
                    try {
                        $payment = $mollie->payments->get(session()->get('mollie_payment_id'));
                        $orderID = strtoupper(str_replace('.', '', uniqid('', true)));

                        if ($payment->isPaid()) {
                            $salesinvoice_payment                     = new \Workdo\Sales\Entities\SalesInvoicePayment();
                            $salesinvoice_payment->transaction_id     = $invoice_id;
                            $salesinvoice_payment->client_id          = 0;
                            $salesinvoice_payment->invoice_id         = $invoice_id;
                            $salesinvoice_payment->amount             = isset($amount) ? $amount : 0;
                            $salesinvoice_payment->date               = date('Y-m-d');
                            $salesinvoice_payment->payment_type       = __('MOLLIE');
                            $salesinvoice_payment->notes              = '';
                            $salesinvoice_payment->save();
                            $due     = $invoice->getDue();
                            if ($due <= 0) {
                                $invoice->status = 3;
                                $invoice->save();
                            } else {
                                $invoice->status = 2;
                                $invoice->save();
                            }
                            if (($invoice->getDue() - $salesinvoice_payment->amount) == 0) {
                                $invoice->status = 3;
                                $invoice->save();
                            }
                            event(new MolliePaymentStatus($invoice,$type,$salesinvoice_payment));

                            return redirect()->route('pay.salesinvoice', encrypt($invoice_id))->with('success', __('Invoice paid Successfully!'));
                        } else {

                            return redirect()->route('pay.salesinvoice', encrypt($invoice_id))->with('success', __('Transaction fail'));
                        }
                    } catch (\Exception $e) {

                        return redirect()->route('pay.salesinvoice', encrypt($invoice_id))->with('success', $e->getMessage());
                    }
                } else {

                    return redirect()->route('pay.salesinvoice', encrypt($invoice_id))->with('success', __('Invoice not found.'));
                }
            }

            elseif ($type == "retainer") {
                $mollie = new \Mollie\Api\MollieApiClient();
                $retainer    =  \Workdo\Retainer\Entities\Retainer::find($invoice_id);
                $user_id = $retainer->created_by;
                $wokspace        = $retainer->workspace;
                self::payment_setting($user_id, $wokspace);
                $mollie->setApiKey($this->api_key);

                if ($retainer && session()->has('mollie_payment_id')) {
                    try {
                        $payment = $mollie->payments->get(session()->get('mollie_payment_id'));
                        $orderID = strtoupper(str_replace('.', '', uniqid('', true)));

                        if ($payment->isPaid()) {
                            $retainer_payment                       = new \Workdo\Retainer\Entities\RetainerPayment();
                            $retainer_payment->retainer_id           = $invoice_id;
                            $retainer_payment->date                 = date('Y-m-d');
                            $retainer_payment->amount               = isset($amount) ? $amount : 0;
                            $retainer_payment->account_id           = 0;
                            $retainer_payment->payment_method       = 0;
                            $retainer_payment->order_id             = $orderID;
                            $retainer_payment->currency             = isset($payment->amount->currency) ? $payment->amount->currency : 'USD';
                            $retainer_payment->payment_type         = __('MOLLIE');
                            $retainer_payment->save();
                            $due     = $retainer->getDue();
                            if ($due <= 0) {
                                $retainer->status = 4;
                                $retainer->save();
                            } else {
                                $retainer->status = 2;
                                $retainer->save();
                            }
                            if (($retainer->getDue() - $retainer_payment->amount) == 0) {
                                $retainer->status = 3;
                                $retainer->save();
                            }
                            //for customer balance update
                            \Workdo\Retainer\Entities\RetainerUtility::updateUserBalance('customer', $retainer->customer_id, $retainer_payment->amount, 'debit');
                            event(new MolliePaymentStatus($retainer,$type,$retainer_payment));

                            return redirect()->route('pay.retainer', encrypt($invoice_id))->with('success', __('Retainer paid Successfully!'));
                        } else {

                            return redirect()->route('pay.retainer', encrypt($invoice_id))->with('success', __('Transaction fail'));
                        }
                    } catch (\Exception $e) {

                        return redirect()->route('pay.retainer', encrypt($invoice_id))->with('success', $e->getMessage());
                    }
                } else {

                    return redirect()->route('pay.retainer', encrypt($invoice_id))->with('success', __('Retainer not found.'));
                }
            }
            else {

                return redirect()->back()->with('error', __('Oops something went wrong.'));
            }
        } else {

            return redirect()->back()->with('error', __('Oops something went wrong.'));
        }
    }


    public function planPayWithMollie(Request $request)
    {
            $plan = Plan::find($request->plan_id);
            $user_counter = !empty($request->user_counter_input) ? $request->user_counter_input : 0;
            $user_module = !empty($request->user_module_input) ? $request->user_module_input : '';
            $workspace_counter = !empty($request->workspace_counter_input) ? $request->workspace_counter_input : 0;
            $duration = !empty($request->time_period) ? $request->time_period : 'Month';
            $user_module_price = 0;
            if(!empty($user_module) && $plan->custom_plan == 1)
            {
                $user_module_array =    explode(',',$user_module);
                foreach ($user_module_array as $key => $value)
                {
                    $temp = ($duration == 'Year') ? ModulePriceByName($value)['yearly_price'] : ModulePriceByName($value)['monthly_price'];
                    $user_module_price = $user_module_price + $temp;
                }
            }
            $user_price = 0;
            if($user_counter > 0)
            {
                $temp = ($duration == 'Year') ? $plan->price_per_user_yearly : $plan->price_per_user_monthly;
                $user_price = $user_counter * $temp;
            }
            $workspace_price = 0;
            if($workspace_counter > 0)
            {
                $temp = ($duration == 'Year') ? $plan->price_per_workspace_yearly : $plan->price_per_workspace_monthly;
                $workspace_price = $workspace_counter * $temp;
            }
            $counter = [
                'user_counter'=>$user_counter,
                'workspace_counter'=>$workspace_counter,
            ];
            $plan_price = ($duration == 'Year') ? $plan->package_price_yearly : $plan->package_price_monthly;
            if($request->coupon_code)
            {
                $plan_price = CheckCoupon($request->coupon_code,$plan_price,$plan->id);
            }
            if($plan)
            {
            $price                  = $plan_price + $user_module_price + $user_price + $workspace_price;
            if($price <= 0){
                $assignPlan= DirectAssignPlan($plan->id,$duration,$user_module,$counter,'MOLLIE');
                if($assignPlan['is_success']){
                    return redirect()->route('plans.index')->with('success', __('Plan activated Successfully!'));
                }else{
                    return redirect()->route('plans.index')->with('error', __('Something went wrong, Please try again,'));
                }
            }
            try {
                $mollie = new \Mollie\Api\MollieApiClient();
                $mollie->setApiKey(admin_setting('company_mollie_api_key'));

                $payment = $mollie->payments->create(
                    [
                        "amount" => [
                            "currency" => admin_setting('defult_currancy'),
                            "value" => number_format($price, 2),
                        ],
                        "description" => "payment for product",
                        "redirectUrl" => route(
                            'plan.get.mollie.status', [
                                            $plan->id,
                                            'user_module'=>$user_module,
                                            'duration'=>$duration,
                                            'counter'=>$counter,
                                            'price'=>$price,
                                            'coupon_code'=>$request->coupon_code,
                                         ]
                        ),
                    ]
                );

                session()->put('mollie_payment_id', $payment->id);
                return redirect($payment->getCheckoutUrl())->with('payment_id', $payment->id);

            }
            catch (\Throwable $th)
            {
                return redirect()->back()->with('error', $th->getMessage());
            }
        }
        else
        {
            return redirect()->back()->with('error', 'Plan is deleted.');
        }

    }

    public function planGetMollieStatus(Request $request, $plan_id)
    {
        $user = User::find(\Auth::user()->id);
        $plan = Plan::find($plan_id);
        $orderID = strtoupper(str_replace('.', '', uniqid('', true)));
        if($plan)
        {
            try
            {
                $mollie = new \Mollie\Api\MollieApiClient();
                $mollie->setApiKey(admin_setting('company_mollie_api_key'));

                if(session()->has('mollie_payment_id'))
                {
                    $payment = $mollie->payments->get(session()->get('mollie_payment_id'));
                    if($payment->isPaid())
                    {
                        $product = !empty($plan->name) ? $plan->name :'Basic Package';
                        $order= Order::create(
                            [
                                'order_id' => $orderID,
                                'name' => null,
                                'email' => null,
                                'card_number' => null,
                                'card_exp_month' => null,
                                'card_exp_year' => null,
                                'plan_name' => $product,
                                'plan_id' => $plan->id,
                                'price' => !empty($request->price)?$request->price:0,
                                'price_currency' => admin_setting('defult_currancy'),
                                'txn_id' => '',
                                'payment_type' => __('MOLLIE'),
                                'payment_status' => 'succeeded',
                                'receipt' => null,
                                'user_id' => $user->id,
                            ]
                        );

                        $assignPlan = $user->assignPlan($plan->id,$request->duration,$request->user_module,$request->counter);
                        $type = 'Subscription';
                        if($request->coupon_code){

                            UserCoupon($request->coupon_code,$orderID);
                        }
                        event(new MolliePaymentStatus($plan,$type,$order));
                        $value = Session::get('user-module-selection');
                        if(!empty($value))
                        {
                            Session::forget('user-module-selection');
                        }
                        if($assignPlan['is_success'])
                        {
                            return redirect()->route('plans.index')->with('success', __('Plan activated Successfully!'));
                        }
                        else
                        {
                            return redirect()->route('plans.index')->with('error', __($assignPlan['error']));
                        }
                    }
                    else
                    {
                        return redirect()->route('plans.index')->with('error', __('Transaction has been failed! '));
                    }
                }
                else
                {
                    return redirect()->route('plans.index')->with('error', __('Transaction has been failed! '));
                }
            }
            catch(\Exception $e)
            {
                return redirect()->route('plans.index')->with('error', __('Plan not found!'));
            }
        }
    }

    // holidayz

    public function bookingPayWithMollie(Request $request,$slug)
    {
        $hotel = Hotels::where('slug', $slug)->first();
        if ($hotel) {
            $payment    = self::payment_setting($hotel->created_by,$hotel->workspace);
            $grandTotal = $couponsId = 0;
            if (!auth()->guard('holiday')->user()) {
                $Carts = Cookie::get('cart');
                $Carts = json_decode($Carts, true);
                foreach ($Carts as $key => $value) {
                    //
                    $toDate = \Carbon\Carbon::parse($value['check_in']);
                    $fromDate = \Carbon\Carbon::parse($value['check_out']);

                    $days = $toDate->diffInDays($fromDate);
                    //
                    $grandTotal += $value['price'] * $value['room'] * $days;
                    $grandTotal += ($value['serviceCharge']) ? $value['serviceCharge'] : 0;
                }

            } else {
                $Carts = RoomBookingCart::where(['customer_id' => auth()->guard('holiday')->user()->id])->get();
                foreach ($Carts as $key => $value) {
                    $grandTotal += $value->price;   // * $value->room
                    $grandTotal += ($value->service_charge) ? $value->service_charge : 0;
                }
            }

            $price = $grandTotal;
            $coupons_id = 0;
            if (!empty($request->coupon)) {
                $coupons = BookingCoupons::where('code', strtoupper($request->coupon))->where('is_active', '1')->first();
                if (!empty($coupons)) {
                    $usedCoupun     = $coupons->used_coupon();
                    $discount_value = ($price / 100) * $coupons->discount;
                    $price          = $price - $discount_value;
                    $coupons_id = $coupons->id;
                    if ($coupons->limit == $usedCoupun) {
                        return redirect()->back()->with('error', __('This coupon code has expired.'));
                    }
                } else {
                    return redirect()->back()->with('error', __('This coupon code is invalid or has expired.'));
                }
            }

            if ($price <= 0) {
                if (!empty($coupons_id != 0)) {
                    $userCoupon         = new UsedBookingCoupons();
                    $userCoupon->customer_id   = (auth()->guard('holiday')->user()) ? auth()->guard('holiday')->user()->id : 0;
                    $userCoupon->coupon_id = $coupons->id;
                    $userCoupon->save();
                    $coupons_id = $coupons->id;
                    $usedCoupun = $coupons->used_coupon();
                    if ($coupons->limit <= $usedCoupun) {
                        $coupons->is_active = 0;
                        $coupons->save();
                    }
                }


                if (!auth()->guard('holiday')->user()) {
                    $booking_number = \Workdo\Holidayz\Entities\Utility::getLastId('room_booking', 'booking_number');
                    $booking = RoomBooking::create([
                        'booking_number' => $booking_number,
                        'user_id' => isset(auth()->guard('holiday')->user()->id) ? auth()->guard('holiday')->user()->id : 0,
                        'payment_method' => __('Mollie'),
                        'payment_status' => 1,
                        'invoice' => null,
                        'workspace' => $hotel->workspace,
                        'created_by' => $hotel->created_by,
                        'total' => $price,
                        'coupon_id' => $coupons_id,
                        'first_name' => $request->firstname,
                        'last_name' => $request->lastname,
                        'email' =>  $request->email,
                        'phone' => $request->phone,
                        'address' => $request->address,
                        'city' => $request->city,
                        'country' => ($request->country) ? $request->country : 'india',
                        'zipcode' => $request->zipcode,
                    ]);
                    foreach($Carts as $key => $value){
                        //
                        $toDate = \Carbon\Carbon::parse($value['check_in']);
                        $fromDate = \Carbon\Carbon::parse($value['check_out']);

                        $days = $toDate->diffInDays($fromDate);
                        //
                        $bookingOrder = RoomBookingOrder::create([
                            'booking_id' => $booking->id,
                            'customer_id' => isset(auth()->guard('holiday')->user()->id) ? auth()->guard('holiday')->user()->id : 0,
                            'room_id' => $value['room_id'],
                            'workspace' => $value['workspace'],
                            'check_in' => $value['check_in'],
                            'check_out' => $value['check_out'],
                            'price' => $value['price'] * $value['room'] * $days,
                            'room' => $value['room'],
                            'service_charge' => $value['serviceCharge'],
                            'services' => $value['serviceIds'],
                        ]);
                        unset($Carts[$key]);
                    }
                    $cart_json = json_encode($Carts);
                    Cookie::queue('cart', $cart_json, 1440);

                }else{
                    $booking_number = \Workdo\Holidayz\Entities\Utility::getLastId('room_booking', 'booking_number');
                    $booking = RoomBooking::create([
                        'booking_number' => $booking_number,
                        'user_id' => auth()->guard('holiday')->user()->id,
                        'payment_method' => __('Mollie'),
                        'payment_status' => 1,
                        'total' => $price,
                        'coupon_id' => $coupons_id,
                        'invoice' => null,
                        'workspace' => $hotel->workspace,
                        'created_by' => $hotel->created_by,
                    ]);
                    foreach($Carts as $key => $value){
                        $bookingOrder = RoomBookingOrder::create([
                            'booking_id' => $booking->id,
                            'customer_id' => auth()->guard('holiday')->user()->id,
                            'room_id' => $value->room_id,
                            'workspace' => $value->workspace,
                            'check_in' => $value->check_in,
                            'check_out' => $value->check_out,
                            'price' => $value->price,   // * $value->room
                            'room' => $value->room,
                            'service_charge' => $value->service_charge,
                            'services' => $value->services,
                        ]);
                    }
                    RoomBookingCart::where(['customer_id' => auth()->guard('holiday')->user()->id])->delete();
                }

                event(new CreateRoomBooking($request,$booking));
                $type = "roombookinginvoice";
                event(new MolliePaymentStatus($hotel,$type,$booking));

                // Email notification
                if(!empty(company_setting('New Room Booking By Hotel Customer',$hotel->created_by,$hotel->workspace)) && company_setting('New Room Booking By Hotel Customer',$hotel->created_by,$hotel->workspace)  == true)
                {
                    $user = User::where('id',$hotel->created_by)->first();
                    $customer = HotelCustomer::find($booking->user_id);
                    $room = \Workdo\Holidayz\Entities\Rooms::find($bookingOrder->room_id);
                    $uArr = [
                        'hotel_customer_name' => isset($customer->name) ? $customer->name : $booking->first_name,
                        'invoice_number' => $booking->booking_number,
                        'check_in_date' => $bookingOrder->check_in,
                        'check_out_date' => $bookingOrder->check_out,
                        'room_type' => $room->type,
                        'hotel_name' => $hotel->name,
                    ];

                    try
                    {
                        $resp = EmailTemplate::sendEmailTemplate('New Room Booking By Hotel Customer', [$user->email],$uArr);
                    }
                    catch(\Exception $e)
                    {
                        $resp['error'] = $e->getMessage();
                    }

                    return redirect()->route('hotel.home', $slug)->with('success', __('Booking Successfully.') . ((isset($resp['error'])) ? '<br> <span class="text-danger" style="color:red">' . $resp['error'] . '</span>' : ''));
                }
                return redirect()->route('hotel.home', $slug)->with('success', 'Booking Successfully. email notification is off.');
                return redirect()->route('hotel.home',$slug)->with('success', __('Booking successfully.'));
            }

            $mollie = new \Mollie\Api\MollieApiClient();
            $mollie->setApiKey($this->api_key);
            $payment = $mollie->payments->create(
                [
                    "amount" => [
                        "currency" =>  ($this->currancy) ? $this->currancy : env('CURRENCY'),
                        "value" => number_format((float)$price, 2, '.', ''),
                    ],
                    "description" => "payment for product",
                    "redirectUrl" => route(
                        'booking.mollie',
                        [   $slug,
                            'coupon_id=' . $coupons_id . '&TXNAMOUNT=' . $price,
                        ]
                    ),
                ]
            );
            session()->put('guestInfo', $request->only(['firstname', 'email', 'address', 'country', 'lastname', 'phone', 'city', 'zipcode']));
            session()->put('booking_mollie_payment_id', $payment->id);
            return redirect($payment->getCheckoutUrl())->with('payment_id', $payment->id);
        }else{
            return redirect()->back()->with('error', __('Hotel not found'));
        }
    }

    public function getBookingPaymentStatus(Request $request,$slug)
    {
        $hotel = Hotels::where('slug', $slug)->first();
        if ($hotel) {
            // $payment    = $this->BookingPaymentConfig($hotel);
            $payment    = self::payment_setting($hotel->created_by,$hotel->workspace);
            $mollie = new \Mollie\Api\MollieApiClient();
            $mollie->setApiKey($this->api_key);
            if (session()->has('booking_mollie_payment_id')) {
                $payment = $mollie->payments->get(session()->get('booking_mollie_payment_id'));
                if ($payment->isPaid()) {
                    if ($request->has('coupon_id') && $request->coupon_id != '') {
                        $coupons = BookingCoupons::find($request->coupon_id);
                        if (!empty($coupons)) {
                            $userCoupon         = new UsedBookingCoupons();
                            $userCoupon->customer_id   = isset(auth()->guard('holiday')->user()->id) ? auth()->guard('holiday')->user()->id : 0;
                            $userCoupon->coupon_id = $coupons->id;
                            $userCoupon->save();
                            $usedCoupun = $coupons->used_coupon();
                            if ($coupons->limit <= $usedCoupun) {
                                $coupons->is_active = 0;
                                $coupons->save();
                            }
                        }
                    }

                    $guestDetails = session()->get('guestInfo');
                    if (!auth()->guard('holiday')->user()) {
                        $Carts = Cookie::get('cart');
                        $Carts = json_decode($Carts, true);
                        $booking_number = \Workdo\Holidayz\Entities\Utility::getLastId('room_booking', 'booking_number');
                        $booking = RoomBooking::create([
                            'booking_number' => $booking_number,
                            'user_id' => isset(auth()->guard('holiday')->user()->id) ? auth()->guard('holiday')->user()->id : 0,
                            'payment_method' => __('Mollie'),
                            'payment_status' => 1,
                            'invoice' => null,
                            'workspace' => $hotel->workspace,
                            'created_by' => $hotel->created_by,
                            'total' => isset($request->TXNAMOUNT) ? $request->TXNAMOUNT : 0,
                            'coupon_id' => ($request->coupon_id) ? $request->coupon_id : 0,
                            'first_name' => $guestDetails['firstname'],
                            'last_name' => $guestDetails['lastname'],
                            'email' =>  $guestDetails['email'],
                            'phone' => $guestDetails['phone'],
                            'address' => $guestDetails['address'],
                            'city' => $guestDetails['city'],
                            'country' => ($guestDetails['country']) ? $guestDetails['country'] : 'india',
                            'zipcode' => $guestDetails['zipcode'],
                        ]);
                        foreach ($Carts as $key => $value) {
                            //
                            $toDate = \Carbon\Carbon::parse($value['check_in']);
                            $fromDate = \Carbon\Carbon::parse($value['check_out']);

                            $days = $toDate->diffInDays($fromDate);
                            //
                            $bookingOrder = RoomBookingOrder::create([
                                'booking_id' => $booking->id,
                                'customer_id' => isset(auth()->guard('holiday')->user()->id) ? auth()->guard('holiday')->user()->id : 0,
                                'room_id' => $value['room_id'],
                                'workspace' => $value['workspace'],
                                'check_in' => $value['check_in'],
                                'check_out' => $value['check_out'],
                                'price' => $value['price'] * $value['room'] * $days,
                                'room' => $value['room'],
                                'service_charge' => $value['serviceCharge'],
                                'services' => $value['serviceIds'],
                            ]);
                            unset($Carts[$key]);
                        }
                        $cart_json = json_encode($Carts);
                        Cookie::queue('cart', $cart_json, 1440);

                    } else {
                        $Carts = RoomBookingCart::where(['customer_id' => auth()->guard('holiday')->user()->id])->get();
                        $booking_number = \Workdo\Holidayz\Entities\Utility::getLastId('room_booking', 'booking_number');
                        $booking = RoomBooking::create([
                            'booking_number' => $booking_number,
                            'user_id' => auth()->guard('holiday')->user()->id,
                            'payment_method' => __('Mollie'),
                            'payment_status' => 1,
                            'invoice' => null,
                            'workspace' => $hotel->workspace,
                            'created_by' => $hotel->created_by,
                            'total' => isset($request->TXNAMOUNT) ? $request->TXNAMOUNT : 0,
                            'coupon_id' => ($request->coupon_id) ? $request->coupon_id : 0,
                        ]);
                        foreach ($Carts as $key => $value) {
                            $bookingOrder = RoomBookingOrder::create([
                                'booking_id' => $booking->id,
                                'customer_id' => auth()->guard('holiday')->user()->id,
                                'room_id' => $value->room_id,
                                'workspace' => $value->workspace,
                                'check_in' => $value->check_in,
                                'check_out' => $value->check_out,
                                'price' => $value->price,   // * $value->room
                                'room' => $value->room,
                                'service_charge' => $value->service_charge,
                                'services' => $value->services,
                            ]);
                        }
                        RoomBookingCart::where(['customer_id' => auth()->guard('holiday')->user()->id])->delete();

                    }

                    event(new CreateRoomBooking($request,$booking));
                    $type = "roombookinginvoice";
                    event(new MolliePaymentStatus($hotel,$type,$booking));

                    //Email notification
                    if(!empty(company_setting('New Room Booking By Hotel Customer',$hotel->created_by,$hotel->workspace)) && company_setting('New Room Booking By Hotel Customer',$hotel->created_by,$hotel->workspace)  == true)
                    {
                        $user = User::where('id',$hotel->created_by)->first();
                        $customer = HotelCustomer::find($booking->user_id);
                        $room = \Workdo\Holidayz\Entities\Rooms::find($bookingOrder->room_id);
                        $uArr = [
                            'hotel_customer_name' => isset($customer->name) ? $customer->name : $booking->first_name,
                            'invoice_number' => $booking->booking_number,
                            'check_in_date' => $bookingOrder->check_in,
                            'check_out_date' => $bookingOrder->check_out,
                            'room_type' => $room->type,
                            'hotel_name' => $hotel->name,
                        ];

                        try
                        {
                            $resp = EmailTemplate::sendEmailTemplate('New Room Booking By Hotel Customer', [$user->email],$uArr);
                        }
                        catch(\Exception $e)
                        {
                            $resp['error'] = $e->getMessage();
                        }

                        return redirect()->route('hotel.home', $slug)->with('success', __('Booking Successfully.') . ((isset($resp['error'])) ? '<br> <span class="text-danger" style="color:red">' . $resp['error'] . '</span>' : ''));
                    }
                    return redirect()->route('hotel.home', $slug)->with('success', 'Booking Successfully. email notification is off.');
                    return redirect()->route('hotel.home', $slug)->with('success', __('Booking successfully'));
                }else{
                    return redirect()->back()->with('error', __('Payment Fail Please try again!'));
                }
            }
        }else{
            return redirect()->back()->with('error', __('Hotel not found'));
        }
    }

    //LMS

    //Mollie Prepare payment
    public function coursePayWithmollie($slug, Request $request)
    {
        $cart           = session()->get($slug);
        $products       = $cart['products'];
        $store          = \Workdo\LMS\Entities\Store::where('slug', $slug)->first();
        if (!empty($cart['coupon'])) {
            $coupon         = \Workdo\LMS\Entities\CourseCoupon::where('id', $cart['coupon']['data_id'])->first();
        } else {
            $coupon = '';
        }
        self::payment_setting($store->created_by, $store->workspace_id);
        $product_name   = [];
        $product_id     = [];
        $sub_totalprice = 0;
        $totalprice  = 0;

        foreach ($products as $key => $product) {
            $product_name[] = $product['product_name'];
            $product_id[]   = $product['id'];
            $sub_totalprice += $product['price'];
            $totalprice     += $product['price'];
        }
        if (!empty($coupon)) {
            if ($coupon['enable_flat'] == 'off') {
                $discount_value = ($sub_totalprice / 100) * $coupon['discount'];
                $totalprice     = $sub_totalprice - $discount_value;
            } else {
                $discount_value = $coupon['flat_discount'];
                $totalprice     = $sub_totalprice - $discount_value;
            }
        }


        if ($products) {
            if ($totalprice <= 0) {
                $assignCourse = \Workdo\LMS\Entities\LmsUtility::DirectAssignCourse($store, 'Coingate');
                if ($assignCourse['is_success']) {
                    return redirect()->route(
                        'store-complete.complete',
                        [
                            $store->slug,
                            \Illuminate\Support\Facades\Crypt::encrypt($assignCourse['courseorder_id']),
                        ]
                    )->with('success', __('Transaction has been success'));
                } else {
                    return redirect()->route('store.cart', $store->slug)->with('error', __('Something went wrong, Please try again,'));
                }
            }
            if (\Workdo\LMS\Entities\LmsUtility::StudentAuthCheck($slug)) {
                $student_data     = Auth::guard('students')->user();
                $pdata['phone']   = $student_data->phone_number;
                $pdata['email']   = $student_data->email;
                $pdata['user_id'] = $student_data->id;
            } else {
                $pdata['phone']   = '';
                $pdata['email']   = '';
                $pdata['user_id'] = '';
            }
            $request = $request->all();
            $mollie  = new \Mollie\Api\MollieApiClient();
            $mollie->setApiKey($this->api_key);
            //var_dump(intval($request['amount']));

            $payment = $mollie->payments->create(
                [
                    "amount" => [
                        "currency" => "$this->currancy",
                        "value" => str_replace(",", "", number_format($totalprice, 2)),
                    ],
                    "description" => "payment for product",
                    "redirectUrl" => route(
                        'course.mollie',
                        [
                            $store->slug,
                            $request['desc'],
                        ]
                    ),

                ]
            );

            session()->put('mollie_payment_id', $payment->id);

            return redirect($payment->getCheckoutUrl())->with('payment_id', $payment->id);
        } else {
            return redirect()->back()->with('error', __('is deleted.'));
        }
    }

    //Mollie Callback payment
    public function getCoursePaymentStatus($slug, $order_id, Request $request)
    {
        $store          = \Workdo\LMS\Entities\Store::where('slug', $slug)->first();
        $products       = '';
        $cart           = session()->get($slug);

        self::payment_setting($store->created_by, $store->workspace_id);
        if (!empty($cart)) {
            $products = $cart['products'];
        } else {
            return redirect()->back()->with('error', __('Please add to product into cart'));
        }

        if (isset($cart['coupon']['data_id'])) {
            $coupon         = \Workdo\LMS\Entities\CourseCoupon::where('id', $cart['coupon']['data_id'])->first();
        } else {
            $coupon = '';
        }
        $product_name   = [];
        $product_id     = [];
        $totalprice     = 0;
        $sub_totalprice = 0;
        foreach ($products as $key => $product) {
            $product_name[] = $product['product_name'];
            $product_id[]   = $product['id'];
            $sub_totalprice += $product['price'];
            $totalprice     += $product['price'];
        }
        if (!empty($coupon)) {
            if ($coupon['enable_flat'] == 'off') {
                $discount_value = ($sub_totalprice / 100) * $coupon['discount'];
                $totalprice     = $sub_totalprice - $discount_value;
            } else {
                $discount_value = $coupon['flat_discount'];
                $totalprice     = $sub_totalprice - $discount_value;
            }
        }

        if ($products) {
            $mollie = new \Mollie\Api\MollieApiClient();
            $mollie->setApiKey($this->api_key);

            if (session()->has('mollie_payment_id')) {
                $payment = $mollie->payments->get(session()->get('mollie_payment_id'));
                $orderID = strtoupper(str_replace('.', '', uniqid('', true)));
                if ($payment->isPaid()) {
                    $student_id            = Auth::guard('students')->user();
                    $course_order                 = new \Workdo\LMS\Entities\CourseOrder();
                    $course_order->order_id       = $orderID;
                    $course_order->name           = $student_id->name;
                    $course_order->card_number    = '';
                    $course_order->card_exp_month = '';
                    $course_order->card_exp_year  = '';
                    $course_order->student_id     = $student_id->id;
                    $course_order->course         = json_encode($products);
                    $course_order->price          = $totalprice;
                    $course_order->coupon         = isset($cart['coupon']['data_id']) ? $cart['coupon']['data_id'] : '';
                    $course_order->coupon_json    = json_encode($coupon);
                    $course_order->discount_price = isset($cart['coupon']['discount_price']) ? $cart['coupon']['discount_price'] : '';
                    $course_order->price_currency = $this->currancy;
                    $course_order->txn_id         = isset($pay_id) ? $pay_id : '';
                    $course_order->payment_type   = 'mollie';
                    $course_order->payment_status = 'success';
                    $course_order->receipt        = '';
                    $course_order->store_id       = $store['id'];
                    $course_order->save();

                    foreach ($products as $course_id) {
                        $purchased_course = new \Workdo\LMS\Entities\PurchasedCourse();
                        $purchased_course->course_id  = $course_id['product_id'];
                        $purchased_course->student_id = $student_id->id;
                        $purchased_course->order_id   = $course_order->id;
                        $purchased_course->save();

                        $student = \Workdo\LMS\Entities\Student::where('id', $purchased_course->student_id)->first();
                        $student->courses_id = $purchased_course->course_id;
                        $student->save();
                    }

                    if (!empty(company_setting('New Course Order', $store->created_by, $store->workspace_id)) && company_setting('New Course Order', $store->created_by, $store->workspace_id)  == true) {
                        $user = User::where('id', $store->created_by)->where('workspace_id', $store->workspace_id)->first();
                        $course = \Workdo\LMS\Entities\Course::whereIn('id', $product_id)->get()->pluck('title');
                        $course_name = implode(', ', $course->toArray());
                        $uArr    = [
                            'student_name' => $student->name,
                            'course_name' => $course_name,
                            'store_name' => $store->name,
                            'order_url' => route('user.order', [$store->slug, \Illuminate\Support\Facades\Crypt::encrypt($course_order->id),]),
                        ];
                        try {
                            // Send Email
                            $resp = EmailTemplate::sendEmailTemplate('New Course Order', [$user->id => $user->email], $uArr, $store->created_by);
                        } catch (\Exception $e) {
                            $resp['error'] = $e->getMessage();
                        }
                    }
                    $type = 'coursepayment';
                    event(new MolliePaymentStatus($store, $type, $course_order));

                    session()->forget($slug);
                    session()->forget('mollie_payment_id');

                    return redirect()->route(
                        'store-complete.complete',
                        [
                            $store->slug,
                            \Illuminate\Support\Facades\Crypt::encrypt($course_order->id),
                        ]
                    )->with('success', __('Transaction has been success'));
                } else {
                    return redirect()->back()->with('error', __('Transaction Unsuccesfull'));
                }
            } else {
                session()->flash('warning', 'Payment not made!');

                return redirect('/');
            }
        } else {
            return redirect()->back()->with('error', __('Plan is deleted.'));
        }
    }
}
