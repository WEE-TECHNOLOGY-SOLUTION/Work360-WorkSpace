<?php

namespace Workdo\Webhook\Listeners;

use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Workdo\Webhook\Entities\SendWebhook;
use Workdo\Hrm\Events\CreateMonthlyPayslip;

class CreateMonthlyPayslipLis
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  object  $event
     * @return void
     */
    public function handle(CreateMonthlyPayslip $event)
    {
        if(module_is_active('Webhook')){
            $payslipEmployee = $event->payslipEmployee;
            $employee = \Workdo\Hrm\Entities\Employee::where('id',$payslipEmployee->employee_id)->first();
            if(!empty($employee))
            {
                // $payslipEmployee->employee_id = $employee->name;
            }
            $action = 'New Monthly Payslip';
            $module = 'Hrm';
            SendWebhook::SendWebhookCall($module ,$payslipEmployee,$action);
        }
    }
}