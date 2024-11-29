<?php

namespace Workdo\Webhook\Listeners;

use App\Models\User;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Workdo\LegalCaseManagement\Entities\Cases;
use Workdo\LegalCaseManagement\Events\CreateExpense;
use Workdo\Webhook\Entities\SendWebhook;

class CreateExpenseLis
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
    public function handle(CreateExpense $event)
    {
        if (module_is_active('Webhook')) {
            $request = $event->request;
            $expense = $event->expense;

            $case = Cases::find($expense->case_id);
            $advocate = User::find($expense->member);

            $web_array = [
                'Case Title' => $case->title,
                'Advocate' => $advocate->name,
                'Date' => $expense->date,
                'Particulars' => $expense->particulars,
                'Money' => $expense->money,
                'Method' => $expense->method,
                'Notes' => $expense->notes
            ];

            $action = 'New Expense';
            $module = 'LegalCaseManagement';
            SendWebhook::SendWebhookCall($module, $web_array, $action);
        }
    }
}
