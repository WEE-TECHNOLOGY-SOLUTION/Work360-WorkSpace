<?php

namespace Workdo\EInvoice\Http\Controllers;

use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\View;
use App\Models\Invoice;
use Workdo\Account\Entities\Customer;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use App\Models\WorkSpace;
use App\Models\Setting;

class EInvoiceController extends Controller
{
    /**
     * Display a listing of the resource.
     * @return Renderable
     */
    public function index()
    {
        return view('einvoice::index');
    }

    /**
     * Show the form for creating a new resource.
     * @return Renderable
     */
    public function create()
    {
        return view('einvoice::create');
    }

    /**
     * Store a newly created resource in storage.
     * @param Request $request
     * @return Renderable
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Show the specified resource.
     * @param int $id
     * @return Renderable
     */
    public function show($id)
    {
        return view('einvoice::show');
    }

    /**
     * Show the form for editing the specified resource.
     * @param int $id
     * @return Renderable
     */
    public function edit($id)
    {
        return view('einvoice::edit');
    }

    /**
     * Update the specified resource in storage.
     * @param Request $request
     * @param int $id
     * @return Renderable
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     * @param int $id
     * @return Renderable
     */
    public function destroy($id)
    {
        //
    }
    public function download($id){
        $invoice = Invoice::find($id);
        $customer = Customer::find($invoice->customer_id);
        $items = $invoice->items;
        $totalTaxPrice = 0;
        $TaxPrice_array = [];
        $totalTaxRate = 0;
        $workspace = WorkSpace::find(getActiveWorkSpace(Auth::user()->id));
        foreach($items as $item){
            if(!empty($item->tax)){
                $taxes = Invoice::tax($item->tax);
                foreach ($taxes as $taxe) {
                    $taxDataPrice = Invoice::taxRate($taxe->rate, $item->price, $item->quantity, $item->discount);
                    $totalTaxRate += $taxe->rate;
                }
                $data = 0;
                foreach ($taxes as $tax){
                    $taxPrice = Invoice::taxRate($tax->rate, $item->price, $item->quantity, $item->discount);
                    $totalTaxPrice += $taxPrice;
                    $data += $taxPrice;
                }
            }
        }
        $productname = $item->product()->name ?? '';
        if(empty($customer->electronic_address) || empty($customer->electronic_address_scheme)){
            return redirect()->back()->with('error',__('Please set the proper setting in System Settings >> E-Invoice Settings.'));
        }
        $xml = View::make('einvoice::xml.xml',compact('invoice','customer','totalTaxPrice','totalTaxRate','workspace','productname'))->render();
        $invoice_number = Invoice::invoiceNumberFormat($invoice->invoice_id);
        $xmlFileName = 'uploads/'.$invoice_number.'.xml';
        file_put_contents($xmlFileName,$xml);
        return response()->download($xmlFileName)->deleteFileAfterSend(true);
    }
    public function setting(Request $request){
        if(Auth::user()->isAbleTo('einvoice manage'))
        {
            $validator = Validator::make($request->all(), [
                'electronic_address' => 'required',
                'company_id' => 'required',
                'electronic_address_schema' => 'required',
                'company_id_schema' => 'required',
            ]);
            if($validator->fails()){
                $messages = $validator->getMessageBag();
                return redirect()->back()->with('error', $messages->first());
            }
            $getActiveWorkSpace = getActiveWorkSpace();
            $creatorId = creatorId();
            $post = $request->all();
            unset($post['_token']);
            foreach ($post as $key => $value) {
                $data = [
                    'key' => $key,
                    'workspace' => $getActiveWorkSpace,
                    'created_by' => $creatorId,
                ];
                Setting::updateOrInsert($data, ['value' => $value]);
            }

            // Settings Cache forget
            AdminSettingCacheForget();
            comapnySettingCacheForget();
            return redirect()->back()->with('success',__('E-invoice setting has been saved sucessfully.'));
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }
}
