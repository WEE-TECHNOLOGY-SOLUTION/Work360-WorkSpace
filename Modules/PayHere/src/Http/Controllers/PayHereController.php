<?php

namespace Workdo\PayHere\Http\Controllers;

use App\Models\Order;
use App\Models\Plan;
use App\Models\Setting;
use App\Models\User;
use Exception;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use Workdo\PayHere\Entities\PayHere;
use Workdo\PayHere\Events\PayHerePaymentStatus;

class PayHereController extends Controller
{
    protected $invoiceData;
    public $currancy;


    /**
     * Display a listing of the resource.
     * @return Renderable
     */

    public function setting(Request $request)
    {
        if (Auth::user()->isAbleTo('payhere manage')) {
            if ($request->has('payhere_is_on')) {
                $validator = Validator::make(
                    $request->all(),
                    [
                        'company_payhere_mode'      => 'required|string',
                        'payhere_merchant_id'       => 'required|string',
                        'payhere_merchant_secret'   => 'required|string',
                        'payhere_app_id'            => 'required|string',
                        'payhere_app_secret'        => 'required|string',
                    ]
                );
                if ($validator->fails()) {
                    $messages = $validator->getMessageBag();

                    return redirect()->back()->with('error', $messages->first());
                }
            }
            $post = $request->all();
            unset($post['_token']);
            unset($post['_method']);
            if ($request->has('payhere_is_on')) {
                foreach ($post as $key => $value) {
                    $data = [
                        'key'           => $key,
                        'workspace'     => getActiveWorkSpace(),
                        'created_by'    => creatorId(),
                    ];

                    Setting::updateOrInsert($data, ['value' => $value]);
                }
            } else {
                $data = [
                    'key'           => 'payhere_is_on',
                    'workspace'     => getActiveWorkSpace(),
                    'created_by'    => creatorId(),
                ];

                Setting::updateOrInsert($data, ['value' => 'off']);
            }

            AdminSettingCacheForget();
            comapnySettingCacheForget();
            return redirect()->back()->with('success', __('PayHere Setting save successfully'));
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    public function planPayWithPayHere(Request $request)
    {

        $user               = User::find(Auth::user()->id);
        $plan               = Plan::find($request->plan_id);
        $admin_settings     = getAdminAllSetting();
        $admin_currancy     = !empty($admin_settings['defult_currancy']) ? $admin_settings['defult_currancy'] : 'LKR';
        $authuser           = Auth::user();
        $user_counter       = !empty($request->user_counter_input) ? $request->user_counter_input : 0;
        $workspace_counter  = !empty($request->workspace_counter_input) ? $request->workspace_counter_input : 0;
        $user_module        = !empty($request->user_module_input) ? $request->user_module_input : '';
        $duration           = !empty($request->time_period) ? $request->time_period : 'Month';
        $user_module_price  = 0;
        if (!empty($user_module) && $plan->custom_plan == 1) {
            $user_module_array = explode(',', $user_module);
            foreach ($user_module_array as $key => $value) {
                $temp = ($duration == 'Year') ? ModulePriceByName($value)['yearly_price'] : ModulePriceByName($value)['monthly_price'];
                $user_module_price = $user_module_price + $temp;
            }
        }
        $user_price = 0;
        if ($user_counter > 0) {
            $temp       = ($duration == 'Year') ? $plan->price_per_user_yearly : $plan->price_per_user_monthly;
            $user_price = $user_counter * $temp;
        }
        $workspace_price = 0;
        if ($workspace_counter > 0) {
            $temp               = ($duration == 'Year') ? $plan->price_per_workspace_yearly : $plan->price_per_workspace_monthly;
            $workspace_price    = $workspace_counter * $temp;
        }
        $plan_price = ($duration == 'Year') ? $plan->package_price_yearly : $plan->package_price_monthly;
        $counter = [
            'user_counter'      => $user_counter,
            'workspace_counter' => $workspace_counter,
        ];
        $stripe_session = '';
        $orderID = strtoupper(str_replace('.', '', uniqid('', true)));
        if ($plan) {
            /* Check for code usage */
            $plan->discounted_price = false;
            $payment_frequency      = $plan->duration;
            if ($request->coupon_code) {
                $plan_price = CheckCoupon($request->coupon_code, $plan_price,$plan->id);
            }
            $price = $plan_price + $user_module_price + $user_price + $workspace_price;
            if ($price <= 0) {
                $assignPlan = DirectAssignPlan($plan->id, $duration, $user_module, $counter, 'STRIPE', $request->coupon_code);
                if ($assignPlan['is_success']) {
                    return redirect()->route('plans.index')->with('success', __('Plan activated Successfully!'));
                } else {
                    return redirect()->route('plans.index')->with('error', __('Something went wrong, Please try again,'));
                }
            }

            try {

                $config = [
                    'payhere.api_endpoint' => $admin_settings['company_payhere_mode'] === 'sandbox'
                        ? 'https://sandbox.payhere.lk/'
                        : 'https://www.payhere.lk/',
                ];

                $config['payhere.merchant_id']      = $admin_settings['payhere_merchant_id'] ?? '';
                $config['payhere.merchant_secret']  = $admin_settings['payhere_merchant_secret'] ?? '';
                $config['payhere.app_secret']       = $admin_settings['payhere_app_secret'] ?? '';
                $config['payhere.app_id']           = $admin_settings['payhere_app_id'] ?? '';
                config($config);

                $hash = strtoupper(
                    md5(
                        $admin_settings['payhere_merchant_id'] .
                            $orderID .
                            number_format($price, 2, '.', '') .
                            'LKR' .
                            strtoupper(md5($admin_settings['payhere_merchant_secret']))
                    )
                );

                $data = [
                    'first_name'    => $user->name,
                    'last_name'     => '',
                    'email'         => $user->email,
                    'phone'         => $user->mobile_no ?? '',
                    'address'       => 'Main Rd',
                    'city'          => 'Anuradhapura',
                    'country'       => 'Sri lanka',
                    'order_id'      => $orderID,
                    'items'         => $plan->name ?? 'Add-on',
                    'currency'      => 'LKR',
                    'amount'        => $price,
                    'hash'          => $hash,
                ];


                return PayHere::checkOut()
                    ->data($data)
                    ->successUrl(route('plan.get.payhere.status', [
                        $plan->id,
                        'amount'        => $price,
                        'user_module'   => $user_module,
                        'counter'       => $counter,
                        'duration'      => $duration,
                        'coupon_code'   => $request->coupon_code,
                    ]))
                    ->failUrl(route('plan.get.payhere.status', [
                        $plan->id,
                        'amount'        => $price,
                        'user_module'   => $user_module,
                        'counter'       => $counter,
                        'duration'      => $duration,
                        'coupon_code'   => $request->coupon_code,
                    ]))
                    ->renderView();
            } catch (\Exception $e) {
                return redirect()->route('plans.index')->with('error', $e->getMessage());
            }
        } else {
            return redirect()->route('plans.index')->with('error', __('Plan is deleted.'));
        }
    }

    public function planGetPayHereStatus(Request $request, $plan_id)
    {
        $user = Auth::user();
        $plan = Plan::find($plan_id);
        if ($plan) {

            $orderID = strtoupper(str_replace('.', '', uniqid('', true)));
            try {
                $order = Order::create(
                    [
                        'order_id'          => $orderID,
                        'name'              => null,
                        'email'             => null,
                        'card_number'       => null,
                        'card_exp_month'    => null,
                        'card_exp_year'     => null,
                        'plan_name'         => !empty($plan->name) ? $plan->name : 'Basic Package',
                        'plan_id'           => $plan->id,
                        'price'             => !empty($request->amount) ? $request->amount : 0,
                        'price_currency'    => admin_setting('defult_currancy'),
                        'txn_id'            => '',
                        'payment_type'      => __('PayHere'),
                        'payment_status'    => 'succeeded',
                        'receipt'           => null,
                        'user_id'           => $user->id,
                    ]
                );
                $type       = 'Subscription';
                $user       = User::find($user->id);
                $assignPlan = $user->assignPlan($plan->id, $request->duration, $request->user_module, $request->counter);
                if ($request->coupon_code) {

                    UserCoupon($request->coupon_code, $orderID);
                }
                event(new PayHerePaymentStatus($plan, $type, $order));

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

    public function invoicePayWithPayHere(Request $request)
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
        if ($type == 'invoice') {
            $invoice    = \App\Models\Invoice::find($invoice_id);
            $user_id    = $invoice->created_by;
            $workspace  = $invoice->workspace;
            $payment_id = $invoice->id;
        } elseif ($type == 'retainer') {

            $invoice = \Workdo\Retainer\Entities\Retainer::find($invoice_id);
            $user_id = $invoice->created_by;
            $workspace = $invoice->workspace;
            $payment_id = $invoice->id;
        }

        $this->invoiceData = $invoice;
        $company_settings = getCompanyAllSetting($user_id, $workspace);
        $user = User::find($user_id);

        $config = [
            'payhere.api_endpoint' => $company_settings['company_payhere_mode'] === 'sandbox'
                ? 'https://sandbox.payhere.lk/'
                : 'https://www.payhere.lk/',
        ];

        $config['payhere.merchant_id']      = $company_settings['payhere_merchant_id'] ?? '';
        $config['payhere.merchant_secret']  = $company_settings['payhere_merchant_secret'] ?? '';
        $config['payhere.app_secret']       = $company_settings['payhere_app_secret'] ?? '';
        $config['payhere.app_id']           = $company_settings['payhere_app_id'] ?? '';
        config($config);

        $get_amount = $request->amount;

        if ($invoice) {
            if ($get_amount > $invoice->getDue()) {
                return redirect()->back()->with('error', __('Invalid amount.'));
            } else {
                $orderID = strtoupper(str_replace('.', '', uniqid('', true)));
                try {
                    $hash = strtoupper(
                        md5(
                            $company_settings['payhere_merchant_id'] .
                                $orderID .
                                number_format($get_amount, 2, '.', '') .
                                'LKR' .
                                strtoupper(md5($company_settings['payhere_merchant_secret']))
                        )
                    );

                    $data = [
                        'first_name'    => $user->name,
                        'last_name'     => '',
                        'email'         => $user->email,
                        'phone'         => $user->mobile_no ?? '94761234567',
                        'address'       => 'Main Rd',
                        'city'          => 'Anuradhapura',
                        'country'       => 'Sri lanka',
                        'order_id'      => $orderID,
                        'items'         => 'Invoice',
                        'currency'      => 'LKR',
                        'amount'        => $get_amount,
                        'hash'          => $hash,
                    ];

                    return PayHere::checkOut()
                        ->data($data)
                        ->successUrl(route('invoice.payhere', [$payment_id, $get_amount, $type]))
                        ->failUrl(route('invoice.payhere', [$payment_id, $get_amount, $type]))
                        ->renderView();

                } catch (Exception $e) {
                    if ($request->type == 'invoice') {
                        return redirect()->route('invoice.show', $invoice_id)->with('error', $e->getMessage() ?? 'Something went wrong.');
                    } elseif ($request->type == 'retainer') {
                        return redirect()->route('retainer.show', $invoice_id)->with('error', $e->getMessage() ?? 'Something went wrong.');
                    }
                }

                return redirect()->back()->with('error', __('Unknown error occurred'));
            }
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    public function getInvoicePaymentStatus(Request $request, $invoice_id, $amount, $type)
    {

        if ($type == 'invoice') {
            $invoice = \App\Models\Invoice::find($invoice_id);

            $company_settings = getCompanyAllSetting($invoice->created_by, $invoice->workspace);

            $this->currancy = isset($company_settings['defult_currancy']) ? $company_settings['defult_currancy'] : '$';
            $this->invoiceData = $invoice;

            if ($invoice) {
                if (empty($request->PayerID || empty($request->token))) {
                    return redirect()->route('invoice.show', $invoice_id)->with('error', __('Payment failed'));
                }
                $orderID = strtoupper(str_replace('.', '', uniqid('', true)));
                try {
                    $invoice_payment                    = new \App\Models\InvoicePayment();
                    $invoice_payment->invoice_id        = $invoice_id;
                    $invoice_payment->date              = Date('Y-m-d');
                    $invoice_payment->account_id        = 0;
                    $invoice_payment->payment_method    = 0;
                    $invoice_payment->amount            = $amount;
                    $invoice_payment->order_id          = $orderID;
                    $invoice_payment->currency          = $this->currancy;
                    $invoice_payment->payment_type      = 'PayHere';
                    $invoice_payment->save();

                    $due = $invoice->getDue();
                    if ($due <= 0) {
                        $invoice->status = 4;
                        $invoice->save();
                    } else {
                        $invoice->status = 3;
                        $invoice->save();
                    }
                    if (module_is_active('Account')) {
                        //for customer balance update
                        \Workdo\Account\Entities\AccountUtility::updateUserBalance('customer', $invoice->customer_id, $invoice_payment->amount, 'debit');
                    }
                    event(new PayHerePaymentStatus($invoice, $type, $invoice_payment));


                    return redirect()->route('pay.invoice', \Illuminate\Support\Facades\Crypt::encrypt($invoice_id))->with('success', __('Invoice paid Successfully!'));

                } catch (\Exception $e) {
                    return redirect()->route('pay.invoice', \Illuminate\Support\Facades\Crypt::encrypt($invoice_id))->with('success', $e->getMessage());
                }
            } else {
                return redirect()->route('pay.invoice', \Illuminate\Support\Facades\Crypt::encrypt($invoice_id))->with('success', __('Invoice not found.'));
            }

        } elseif ($type == 'retainer') {
            $retainer = \Workdo\Retainer\Entities\Retainer::find($invoice_id);
            $company_settings = getCompanyAllSetting($retainer->created_by, $retainer->workspace);

            $this->currancy = isset($company_settings['defult_currancy']) ? $company_settings['defult_currancy'] : '$';

            $this->invoiceData = $retainer;
            if ($retainer) {
                $orderID = strtoupper(str_replace('.', '', uniqid('', true)));

                if (empty($request->PayerID || empty($request->token))) {
                    return redirect()->route('retainer.show', $invoice_id)->with('error', __('Payment failed'));
                }

                try {
                    $retainer_payment                   = new \Workdo\Retainer\Entities\RetainerPayment();
                    $retainer_payment->retainer_id      = $invoice_id;
                    $retainer_payment->date             = Date('Y-m-d');
                    $retainer_payment->account_id       = 0;
                    $retainer_payment->payment_method   = 0;
                    $retainer_payment->amount           = $amount;
                    $retainer_payment->order_id         = $orderID;
                    $retainer_payment->currency         = $this->currancy;
                    $retainer_payment->payment_type     = 'PayHere';
                    $retainer_payment->save();
                    $due = $retainer->getDue();

                    if ($due <= 0) {
                        $retainer->status = 4;
                        $retainer->save();
                    } else {
                        $retainer->status = 2;
                        $retainer->save();
                    }
                    //for customer balance update
                    \Workdo\Retainer\Entities\RetainerUtility::updateUserBalance('customer', $retainer->customer_id, $retainer_payment->amount, 'debit');
                    event(new PayHerePaymentStatus($retainer, $type, $retainer_payment));


                    return redirect()->route('pay.retainer', \Illuminate\Support\Facades\Crypt::encrypt($invoice_id))->with('success', __('Retainer paid Successfully!'));

                } catch (\Exception $e) {
                    return redirect()->route('pay.retainer', \Illuminate\Support\Facades\Crypt::encrypt($invoice_id))->with('success', $e->getMessage());
                }
            } else {

                return redirect()->route('pay.retainer', \Illuminate\Support\Facades\Crypt::encrypt($invoice_id))->with('success', __('Retainer not found.'));
            }
        }
    }
}
