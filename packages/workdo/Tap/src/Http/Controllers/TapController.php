<?php

namespace Workdo\Tap\Http\Controllers;

use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use App\Models\Setting;
use App\Models\EmailTemplate;
use App\Models\Plan;
use App\Models\Order;
use Workdo\Tap\Package\Payment;
use App\Models\User;
use Illuminate\Support\Facades\Session;
use Workdo\Tap\Events\TapPaymentStatus;
use Workdo\Holidayz\Entities\Hotels;
use Workdo\Holidayz\Entities\RoomBookingCart;
use Workdo\Holidayz\Entities\BookingCoupons;
use Workdo\Holidayz\Entities\HotelCustomer;
use Workdo\Holidayz\Entities\RoomBooking;
use Workdo\Holidayz\Entities\RoomBookingOrder;
use Workdo\Holidayz\Entities\UsedBookingCoupons;
use Workdo\Holidayz\Events\CreateRoomBooking;
use Illuminate\Support\Facades\Cookie;

class TapController extends Controller
{
    public $invoiceData;
    public $company_tap_secret_key;
    public $currancy;
    public function setting(Request $request)
    {
        if(Auth::user()->isAbleTo('tap manage'))
        {
            if ($request->has('tap_payment_is_on')) {
                $validator = Validator::make($request->all(),
                [
                    'company_tap_secret_key' => 'required|string',
                ]);
                if ($validator->fails()) {
                    $messages = $validator->getMessageBag();

                    return redirect()->back()->with('error', $messages->first());
                }
            }
            $post = $request->all();
            unset($post['_token']);

            if($request->has('tap_payment_is_on')) {
                foreach ($post as $key => $value) {
                    $data = [
                        'key' => $key,
                        'workspace' => getActiveWorkSpace(),
                        'created_by' => creatorId(),
                    ];
                    Setting::updateOrInsert($data, ['value' => $value]);
                }
            }
            else
            {
                $data = [
                    'key' => 'tap_payment_is_on',
                    'workspace' => getActiveWorkSpace(),
                    'created_by' => creatorId(),
                ];
                Setting::updateOrInsert($data, ['value' => 'off']);
            }

            AdminSettingCacheForget();
            comapnySettingCacheForget();
            return redirect()->back()->with('success', __('Tap Setting save successfully.'));
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    public function planPayWithTap(Request $request)
    {
        $plan = Plan::find($request->plan_id);
        $user_counter = !empty($request->user_counter_input) ? $request->user_counter_input : 0;
        $workspace_counter = !empty($request->workspace_counter_input) ? $request->workspace_counter_input : 0;
        $user_module = !empty($request->user_module_input) ? $request->user_module_input : '0';
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
        $plan_price = ($duration == 'Year') ? $plan->package_price_yearly : $plan->package_price_monthly;
        $counter = [
            'user_counter'=>$user_counter,
            'workspace_counter'=>$workspace_counter,
        ];
        $admin_settings = getAdminAllSetting();
        $company_tap_secret_key = isset($admin_settings['company_tap_secret_key']) ? $admin_settings['company_tap_secret_key'] : '';
        $currency               = isset($admin_settings['defult_currancy']) ? $admin_settings['defult_currancy'] : '';

        if ($plan) {
            try {
                if($request->coupon_code)
                {
                    $plan_price = CheckCoupon($request->coupon_code,$plan_price,$plan->id);
                }
                $price = $plan_price + $user_module_price + $user_price + $workspace_price;

                if($price <= 0){
                    $assignPlan = DirectAssignPlan($plan->id,$duration,$user_module,$counter,'Tap',$request->coupon_code);
                    if($assignPlan['is_success']) {
                       return redirect()->route('plans.index')->with('success', __('Plan activated Successfully!'));
                    } else {
                       return redirect()->route('plans.index')->with('error', __('Something went wrong, Please try again.'));
                    }
                }

                $TapPay = new Payment(['company_tap_secret_key'=> $company_tap_secret_key]);

                    return $TapPay->charge([
                        'amount' => $price,
                        'currency' => $currency,
                        'threeDSecure' => 'true',
                        'description' => 'test description',
                        'statement_descriptor' => 'sample',
                        'customer' => [
                           'first_name' => Auth::user()->name,
                           'email' => Auth::user()->email,
                        ],
                        'source' => [
                          'id' => 'src_card'
                        ],
                        'post' => [
                           'url' => null
                        ],
                        'redirect' => [
                           'url' => route('plan.get.tap.status', [ $plan->id,
                           'amount' => $price,
                           'user_module' => $user_module,
                           'counter' => $counter,
                           'duration' => $duration,
                           'coupon_code' => $request->coupon_code,
                            ])
                        ]
                    ],true);

            } catch (\Exception $e) {
                return redirect()->route('plans.index')->with('error', __('Something went wrong!'));
            }
        } else {
            return redirect()->route('plans.index')->with('error', __('Plan is deleted.'));
        }
    }

    public function planGetTapStatus(Request $request, $plan_id)
    {
        $user = Auth::user();
        $plan = Plan::find($plan_id);
        if ($plan)
        {
            $admin_settings = getAdminAllSetting();
            $company_tap_secret_key = isset($admin_settings['company_tap_secret_key']) ? $admin_settings['company_tap_secret_key'] : '';
            $currency               = isset($admin_settings['defult_currancy']) ? $admin_settings['defult_currancy'] : '';

            $orderID = strtoupper(str_replace('.', '', uniqid('', true)));
            $TapPay = new Payment(['company_tap_secret_key'=> $company_tap_secret_key]);
            try {

                $order = Order::create(
                    [
                        'order_id' => $orderID,
                        'name' => null,
                        'email' => null,
                        'card_number' => null,
                        'card_exp_month' => null,
                        'card_exp_year' => null,
                        'plan_name' =>  !empty($plan->name) ? $plan->name :'Basic Package',
                        'plan_id' => $plan->id,
                        'price' => !empty($request->amount)?$request->amount:0,
                        'price_currency' => admin_setting('defult_currancy'),
                        'txn_id' => '',
                        'payment_type' => __('Tap'),
                        'payment_status' =>'succeeded',
                        'receipt' => null,
                        'user_id' => $user->id,
                    ]
                );
                $type = 'Subscription';
                $user = User::find($user->id);
                $assignPlan = $user->assignPlan($plan->id,$request->duration,$request->user_module,$request->counter);
                if($request->coupon_code){

                    UserCoupon($request->coupon_code,$orderID);
                }
                $value = Session::get('user-module-selection');

                event(new TapPaymentStatus($plan,$type,$order));

                if(!empty($value))
                {
                    Session::forget('user-module-selection');
                }

                if ($assignPlan['is_success']) {
                    return redirect()->route('plans.index')->with('success', __('Plan activated Successfully.'));
                } else {
                    return redirect()->route('plans.index')->with('error', __($assignPlan['error']));
                }

            } catch (\Exception $e) {
                return redirect()->route('plans.index')->with('error', __('Transaction has been failed.'));
            }

        } else {
            return redirect()->route('plans.index')->with('error', __('Plan is deleted.'));
        }
    }

    public function invoicePayWithTap(Request $request)
    {

        $validator = Validator::make(
            $request->all(),
            ['amount' => 'required|numeric', 'invoice_id' => 'required']
        );
        if ($validator->fails()) {
            return redirect()->back()->with('error', $validator->errors()->first());
        }
        $invoice_id = $request->input('invoice_id');
        $type = $request->type;
        if($type == 'invoice')
        {
            $invoice = \App\Models\Invoice::find($invoice_id);
            $user_id = $invoice->created_by;
            $workspace = $invoice->workspace;
            $payment_id = $invoice->id;
        }
        elseif($type == 'retainer') {

            $invoice = \Workdo\Retainer\Entities\Retainer::find($invoice_id);
            $user_id = $invoice->created_by;
            $workspace = $invoice->workspace;
            $payment_id = $invoice->id;
        }

        $this->invoiceData  = $invoice;
        $this->payment_setting($user_id,$workspace);
        $get_amount = $request->amount;
        $users = User::where('id', $user_id)->first();

        if ($invoice) {
            $TapPay = new Payment(['company_tap_secret_key'=> $this->company_tap_secret_key]);

            return $TapPay->charge([
                'amount' => $get_amount,
                'currency' => $this->currancy,
                'threeDSecure' => 'true',
                'description' => 'test description',
                'statement_descriptor' => 'sample',
                'customer' => [
                   'first_name' => $users->name,
                   'email' => $users->email,
                ],
                'source' => [
                  'id' => 'src_card'
                ],
                'post' => [
                   'url' => null
                ],
                'redirect' => [
                   'url' => route('invoice.tap', [$payment_id,$get_amount, $type])
                ]
            ],true);
            }
         else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    public function getInvoicePaymentStatus(Request $request, $invoice_id, $amount,$type)
    {
        if($type == 'invoice')
        {
            $invoice = \App\Models\Invoice::find($invoice_id);
            $this->payment_setting($invoice->created_by,$invoice->workspace);
            $this->invoiceData  = $invoice;

            if ($invoice) {
                $TapPay = new Payment(['company_tap_secret_key'=> $this->company_tap_secret_key]);

                $orderID = strtoupper(str_replace('.', '', uniqid('', true)));
                try {
                    $invoice_payment                 = new \App\Models\InvoicePayment();
                    $invoice_payment->invoice_id     = $invoice_id;
                    $invoice_payment->date           = Date('Y-m-d');
                    $invoice_payment->account_id     = 0;
                    $invoice_payment->payment_method = 0;
                    $invoice_payment->amount         = $amount;
                    $invoice_payment->order_id       = $orderID;
                    $invoice_payment->currency       = $this->currancy;
                    $invoice_payment->payment_type = 'Tap';
                    $invoice_payment->save();

                    $due     = $invoice->getDue();
                    if ($due <= 0) {
                        $invoice->status = 4;
                        $invoice->save();
                    } else {
                        $invoice->status = 3;
                        $invoice->save();
                    }
                    if(module_is_active('Account'))
                    {
                        \Workdo\Account\Entities\AccountUtility::updateUserBalance('customer', $invoice->customer_id, $invoice_payment->amount, 'debit');
                    }
                    event(new TapPaymentStatus($invoice,$type,$invoice_payment));

                    return redirect()->route('pay.invoice',\Illuminate\Support\Facades\Crypt::encrypt($invoice_id))->with('success', __('Invoice paid Successfully!'));

                } catch (\Exception $e) {
                    return redirect()->route('pay.invoice', \Illuminate\Support\Facades\Crypt::encrypt($invoice_id))->with('error', __('Something went wrong!'));
                }
            } else {
                return redirect()->route('pay.invoice', \Illuminate\Support\Facades\Crypt::encrypt($invoice_id))->with('error', __('Invoice not found.'));
            }

        }
        elseif($type == 'retainer')
        {
            $retainer = \Workdo\Retainer\Entities\Retainer::find($invoice_id);
            $this->payment_setting($retainer->created_by,$retainer->workspace);

            $this->invoiceData  = $retainer;
            if ($retainer)
            {
                $orderID = strtoupper(str_replace('.', '', uniqid('', true)));
                $TapPay = new Payment(['company_tap_secret_key'=> $this->company_tap_secret_key]);

                try {
                    $retainer_payment                 = new \Workdo\Retainer\Entities\RetainerPayment();
                    $retainer_payment->retainer_id     = $invoice_id;
                    $retainer_payment->date           = Date('Y-m-d');
                    $retainer_payment->account_id     = 0;
                    $retainer_payment->payment_method = 0;
                    $retainer_payment->amount         = $amount;
                    $retainer_payment->order_id       = $orderID;
                    $retainer_payment->currency       = $this->currancy;
                    $retainer_payment->payment_type   = 'Tap';
                    $retainer_payment->save();
                    $due     = $retainer->getDue();
                    if ($due <= 0) {
                        $retainer->status = 4;
                        $retainer->save();
                    } else {
                        $retainer->status = 2;
                        $retainer->save();
                    }
                    //for customer balance update
                    \Workdo\Retainer\Entities\RetainerUtility::updateUserBalance('customer', $retainer->customer_id, $retainer_payment->amount, 'debit');

                    event(new TapPaymentStatus($retainer,$type,$retainer_payment));

                    return redirect()->route('pay.retainer', \Illuminate\Support\Facades\Crypt::encrypt($invoice_id))->with('success', __('Retainer paid Successfully!'));

                } catch (\Exception $e) {
                    return redirect()->route('pay.retainer',  \Illuminate\Support\Facades\Crypt::encrypt($invoice_id))->with('error', __('Something went wrong!'));
                }
            } else {

                return redirect()->route('pay.retainer',  \Illuminate\Support\Facades\Crypt::encrypt($invoice_id))->with('error', __('Retainer not found.'));
            }
        }
    }

    public function coursePayWithTap(Request $request, $slug)
    {
        $cart     = session()->get($slug);
        $products = $cart['products'];

        $store = \Workdo\LMS\Entities\Store::where('slug', $slug)->first();

        $this->payment_setting($store->created_by,$store->workspace_id);

        $objUser = Auth::user();

        $total_price    = 0;
        $sub_totalprice = 0;
        $product_name   = [];
        $product_id     = [];

        foreach ($products as $key => $product) {
            $product_name[] = $product['product_name'];
            $product_id[]   = $product['id'];
            $sub_totalprice += $product['price'];
            $total_price    += $product['price'];
        }
        if ($products) {
            try {
                $coupon_id = null;
                if (isset($cart['coupon']) && isset($cart['coupon'])) {
                    if ($cart['coupon']['coupon']['enable_flat'] == 'off') {
                        $discount_value = ($sub_totalprice / 100) * $cart['coupon']['coupon']['discount'];
                        $total_price    = $sub_totalprice - $discount_value;
                    } else {
                        $discount_value = $cart['coupon']['coupon']['flat_discount'];
                        $total_price    = $sub_totalprice - $discount_value;
                    }
                }
                if($total_price <= 0){
                    $assignCourse= \Workdo\LMS\Entities\LmsUtility::DirectAssignCourse($store,'Coingate');
                    if($assignCourse['is_success']){
                        return redirect()->route(
                            'store-complete.complete',
                            [
                                $store->slug,
                                \Illuminate\Support\Facades\Crypt::encrypt($assignCourse['courseorder_id']),
                            ]
                        )->with('success', __('Transaction has been success.'));
                    }else{
                       return redirect()->route('store.cart',$store->slug)->with('error', __('Something went wrong!'));
                    }
                }
                $student = Auth::guard('students')->user();
                $TapPay = new Payment(['company_tap_secret_key'=> $this->company_tap_secret_key]);

                return $TapPay->charge([
                    'amount' => $total_price,
                    'currency' => $this->currancy,
                    'threeDSecure' => 'true',
                    'description' => 'test description',
                    'statement_descriptor' => 'sample',
                    'customer' => [
                       'first_name' => $student->name,
                       'email' => $student->email,
                    ],
                    'source' => [
                      'id' => 'src_card'
                    ],
                    'post' => [
                       'url' => null
                    ],
                    'redirect' => [
                       'url' => route('course.get.tap', [$store->slug])
                    ]
                ],true);
            } catch (\Exception $e) {
                return redirect()->back()->with('error', __('Unknown error occurred.'));
            }
        } else {
            return redirect()->back()->with('error', __('is deleted.'));
        }
    }

    public function getCoursePaymentStatus(Request $request, $slug)
    {
        $store = \Workdo\LMS\Entities\Store::where('slug', $slug)->first();

        $cart = session()->get($slug);
        if (isset($cart['coupon'])) {
            $coupon = $cart['coupon']['coupon'];
        }
        $products       = $cart['products'];
        $sub_totalprice = 0;
        $totalprice     = 0;
        $product_name   = [];
        $product_id     = [];

        foreach ($products as $key => $product) {
            $product_name[] = $product['product_name'];
            $product_id[]   = $product['id'];
            $sub_totalprice += $product['price'];
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
            $this->payment_setting($store->created_by,$store->workspace_id);
            $TapPay = new Payment(['company_tap_secret_key'=> $this->company_tap_secret_key]);

            try {
                $order       = new \Workdo\LMS\Entities\CourseOrder();
                $latestOrder = \Workdo\LMS\Entities\CourseOrder::orderBy('created_at', 'DESC')->first();
                if (!empty($latestOrder)) {
                    $order->order_nr = '#' . str_pad($latestOrder->id + 1, 4, "100", STR_PAD_LEFT);
                } else {
                    $order->order_nr = '#' . str_pad(1, 4, "100", STR_PAD_LEFT);
                }

                    $student                      = Auth::guard('students')->user();
                    $course_order                 = new \Workdo\LMS\Entities\CourseOrder();
                    $course_order->order_id       =  '#' .time();
                    $course_order->name           = $student->name;
                    $course_order->card_number    = '';
                    $course_order->card_exp_month = '';
                    $course_order->card_exp_year  = '';
                    $course_order->student_id     = $student->id;
                    $course_order->course         = json_encode($products);
                    $course_order->price          = $totalprice;
                    $course_order->coupon         = !empty($cart['coupon']['coupon']['id']) ? $cart['coupon']['coupon']['id'] : '';
                    $course_order->coupon_json    = json_encode(!empty($coupon) ? $coupon : '');
                    $course_order->discount_price = !empty($cart['coupon']['discount_price']) ? $cart['coupon']['discount_price'] : '';
                    $course_order->price_currency = !empty(company_setting('defult_currancy',$store->created_by,$store->workspace_id)) ? company_setting('defult_currancy',$store->created_by,$store->workspace_id) : 'USD';
                    $course_order->txn_id         = '';
                    $course_order->payment_type   = __('Tap');
                    $course_order->payment_status = 'success';
                    $course_order->receipt        = '';
                    $course_order->store_id       = $store['id'];
                    $course_order->save();

                    foreach ($products as $course_id) {
                        $purchased_course = new \Workdo\LMS\Entities\PurchasedCourse();
                        $purchased_course->course_id  = $course_id['product_id'];
                        $purchased_course->student_id = $student->id;
                        $purchased_course->order_id   = $course_order->id;
                        $purchased_course->save();

                        $student = \Workdo\LMS\Entities\Student::where('id', $purchased_course->student_id)->first();
                        $student->courses_id = $purchased_course->course_id;
                        $student->save();
                    }

                    $type = 'coursepayment';

                    if (!empty(company_setting('New Course Order',$store->created_by,$store->workspace_id)) && company_setting('New Course Order',$store->created_by,$store->workspace_id)  == true) {
                        $course = \Workdo\LMS\Entities\Course::whereIn('id',$product_id)->get()->pluck('title');
                        $course_name = implode(', ', $course->toArray());
                        $user = User::where('id',$store->created_by)->where('workspace_id',$store->workspace_id)->first();
                        $uArr    = [
                            'student_name' => $student->name,
                            'course_name' => $course_name,
                            'store_name' => $store->name,
                            'order_url' => route('user.order',[$store->slug,\Illuminate\Support\Facades\Crypt::encrypt($course_order->id),]),
                        ];
                        try
                        {
                            $resp = EmailTemplate::sendEmailTemplate('New Course Order', [$user->id => $user->email], $uArr,$store->created_by);
                        }
                        catch(\Exception $e)
                        {
                            $resp['error'] = $e->getMessage();
                        }
                    }

                    event(new TapPaymentStatus($store,$type,$course_order));
                    session()->forget($slug);

                    return redirect()->route(
                        'store-complete.complete',
                        [
                            $store->slug,
                            \Illuminate\Support\Facades\Crypt::encrypt($course_order->id),
                        ]
                    )->with('success', __('Transaction has been success.'));
            } catch (\Exception $e) {
                return redirect()->back()->with('error', __('Transaction has been failed.'));
            }
        } else {
            return redirect()->back()->with('error', __('is deleted.'));
        }
    }

    public function bookingPayWithTap(Request $request, $slug)
    {
        $hotel = Hotels::where('slug', $slug)->first();

        if ($hotel) {
            $grandTotal = 0;
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

            try {
                $this->payment_setting($hotel->created_by,$hotel->workspace);

                $coupon_id = null;
                $get_amount     = $grandTotal;
                if (!empty($request->coupon)) {
                    $coupons = BookingCoupons::where('code', strtoupper($request->coupon))->where('is_active', '1')->first();
                    if (!empty($coupons)) {
                        $usedCoupun     = $coupons->used_coupon();
                        if ($coupons->limit == $usedCoupun) {
                            return redirect()->back()->with('error', __('This coupon code has expired.'));
                        }
                        $discount_value = ($get_amount / 100) * $coupons->discount;
                        $get_amount         = $get_amount - $discount_value;
                        $coupon_id = $coupons->id;
                    } else {
                        return redirect()->back()->with('error', __('This coupon code is invalid or has expired.'));
                    }
                }
                $get_amount = number_format((float)$get_amount, 2, '.', '');
                session()->put('guestInfo', $request->only(['firstname','email','address','country','lastname','phone','city','zipcode']));
                if ($get_amount <= 0) {
                    return $this->GetBookingPaymentStatus($request, $slug, $get_amount, $coupon_id);
                }


                $data = [$slug, $get_amount, 0];
                if ($coupon_id) {
                    $data = [$slug, $get_amount, $coupon_id];
                }
                $customer = Auth::guard('holiday')->user();
                $TapPay = new Payment(['company_tap_secret_key'=> $this->company_tap_secret_key]);

                return $TapPay->charge([
                    'amount' => $get_amount,
                    'currency' => $this->currancy,
                    'threeDSecure' => 'true',
                    'description' => 'test description',
                    'statement_descriptor' => 'sample',
                    'customer' => [
                       'first_name' => isset($customer->name) ? $customer->name : $request->firstname,
                       'email' => isset($customer->email) ? $customer->email : $request->email,
                    ],
                    'source' => [
                      'id' => 'src_card'
                    ],
                    'post' => [
                       'url' => null
                    ],
                    'redirect' => [
                       'url' => route('booking.tap', $data)
                    ]
                ],true);
            } catch (\Exception $e) {
                return redirect()->back()->with('error', __('Something went wrong!'));
            }
        } else {
            return redirect()->back()->with('error', __('Hotel Not found.'));
        }
    }

    public function getBookingPaymentStatus(Request $request, $slug, $price, $coupon_id = 0)
    {
        $hotel = Hotels::where('slug', $slug)->first();
        if ($hotel) {
            $this->payment_setting($hotel->created_by,$hotel->workspace);
            $TapPay = new Payment(['company_tap_secret_key'=> $this->company_tap_secret_key]);
            if ($coupon_id != '') {
                $coupons = BookingCoupons::find($coupon_id);
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
                    'payment_method' => __('Tap'),
                    'payment_status' => 1,
                    'invoice' => null,
                    'workspace' => $hotel->workspace,
                    'created_by' => $hotel->created_by,
                    'total' => isset($price) ? $price : 0,
                    'coupon_id' => ($coupon_id) ? $coupon_id : 0,
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
                    'payment_method' => __('Tap'),
                    'payment_status' => 1,
                    'invoice' => null,
                    'workspace' => $hotel->workspace,
                    'created_by' => $hotel->created_by,
                    'total' => isset($price) ? $price : 0,
                    'coupon_id' => ($coupon_id) ? $coupon_id : 0,
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
            event(new TapPaymentStatus($hotel,$type,$booking));

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
            return redirect()->route('hotel.home', $slug)->with('success', __('Booking successfully.'));

        }else{
            return redirect()->back()->with('error', __('Hotel not found.'));
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
        $this->company_tap_secret_key = !empty($company_settings['company_tap_secret_key']) ? $company_settings['company_tap_secret_key'] : '';
        $this->currancy        = !empty($company_settings['defult_currancy']) ? $company_settings['defult_currancy'] : 'AED';

    }
}
