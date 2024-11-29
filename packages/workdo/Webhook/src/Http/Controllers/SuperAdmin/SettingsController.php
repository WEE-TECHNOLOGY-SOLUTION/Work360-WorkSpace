<?php
// This file use for handle super admin setting page

namespace Workdo\Webhook\Http\Controllers\SuperAdmin;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Workdo\Webhook\Entities\Webhook;

class SettingsController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index($settings)
    {
        $webhook_module = Webhook::where('created_by',creatorId())->where('workspace',getActiveWorkSpace())->get();
        return view('webhook::super-admin.settings.index',compact('webhook_module'));
    }


    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }
}
