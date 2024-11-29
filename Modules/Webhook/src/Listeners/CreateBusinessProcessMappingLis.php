<?php

namespace Workdo\Webhook\Listeners;

use App\Models\User;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Workdo\BusinessProcessMapping\Entities\Related;
use Workdo\BusinessProcessMapping\Events\CreateBusinessProcessMapping;
use Workdo\Contract\Entities\Contract;
use Workdo\Lead\Entities\Deal;
use Workdo\Lead\Entities\Lead;
use Workdo\PropertyManagement\Entities\Property;
use Workdo\Taskly\Entities\Project;
use Workdo\Taskly\Entities\Task;
use Workdo\Webhook\Entities\SendWebhook;

class CreateBusinessProcessMappingLis
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
    public function handle(CreateBusinessProcessMapping $event)
    {
        if (module_is_active('Webhook')) {
            $request = $event->request;
            $businessProcess = $event->businessProcess;

            $related = Related::find($businessProcess->related_to);

            $web_array = [];
            $project_data = [];
            $task_data = [];
            $lead_data = [];
            $deal_data = [];
            $property_data = [];
            $contract_data = [];

            if (!empty ($related) && $related->related == 'Project') {
                $project_id = explode(',', $businessProcess->related_assign);

                $projects = Project::WhereIn('id', $project_id)->get();
                foreach ($projects as $project) {
                    $project_data[] = [
                        'Project name' => $project->name,
                        'Project Status' => $project->status,
                        'Project Start Date' => $project->start_date,
                        'Project End Date' => $project->end_date,
                        'Project Description' => $project->description
                    ];
                }

                $web_array = [
                    'Business Processing Mapping Title' => $businessProcess->title,
                    'Business Processing Mapping Description' => $businessProcess->description,
                    'Related To' => $related->related,
                    'Projects' => $project_data
                ];
            }

            if (!empty ($related) && $related->related == 'Task') {
                $task_id = explode(',', $businessProcess->related_assign);

                $tasks = Task::whereIn('id', $task_id)->get();
                $project_ids = array_unique(array_column($tasks->toArray(), 'project_id'));
                $projectNames = Project::whereIn('id', $project_ids)->pluck('name', 'id');

                foreach ($tasks as $task) {
                    $task_data[] = [
                        'Task Title' => $task->title,
                        'Project' => $projectNames[$task['project_id']],
                        'Task Priority' => $task->priority,
                        'Task Start Date' => $task->start_date,
                        'Task End Date' => $task->due_date,
                        'Task Description' => $task->description,
                    ];
                }

                $web_array = [
                    'Business Processing Mapping Title' => $businessProcess->title,
                    'Business Processing Mapping Description' => $businessProcess->description,
                    'Related To' => $related->related,
                    'Projects' => $task_data
                ];
            }

            if (!empty ($related) && $related->related == 'Lead') {
                $lead_id = explode(',', $businessProcess->related_assign);

                $leads = Lead::whereIn('id', $lead_id)->get();
                $userIds = $leads->pluck('user_id')->unique()->toArray();
                $users = User::whereIn('id', $userIds)->get();

                foreach ($leads as $lead) {
                    $user = $users->where('id', $lead->user_id)->first();

                    $lead_data[] = [
                        'Lead Title' => $lead->name,
                        'Lead Email' => $lead->email,
                        'Lead Subject' => $lead->subject,
                        'Lead User Name' => $user->name,
                        'Lead User Email' => $user->email,
                        'Phone Number' => $lead->phone,
                        'Date' => $lead->date,
                        'Follow Up Date' => $lead->follow_up_date,
                    ];

                    $web_array = [
                        'Business Processing Mapping Title' => $businessProcess->title,
                        'Business Processing Mapping Description' => $businessProcess->description,
                        'Related To' => $related->related,
                        'Leads' => $lead_data
                    ];
                }
            }

            if (!empty ($related) && $related->related == 'Deal') {
                $deal_id = explode(',', $businessProcess->related_assign);

                $deals = Deal::whereIn('id', $deal_id)->get();

                foreach ($deals as $deal) {
                    $deal_data[] = [
                        'Deal Title' => $deal->name,
                        'Deal Price' => $deal->price,
                        'Deal Status' => $deal->status,
                    ];
                }

                $web_array = [
                    'Business Processing Mapping Title' => $businessProcess->title,
                    'Business Processing Mapping Description' => $businessProcess->description,
                    'Related To' => $related->related,
                    'Deals' => $deal_data
                ];
            }

            if (!empty ($related) && $related->related == 'Property') {
                $property_id = explode(',', $businessProcess->related_assign);

                $propeeties = Property::whereIn('id', $property_id)->get();

                foreach ($propeeties as $property) {
                    $property_data[] = [
                        'Property Title' => $property->name,
                        'Property Address' => $property->address,
                        'Property Country' => $property->country,
                        'Property State' => $property->state,
                        'Property City' => $property->city,
                        'Property Pincode' => $property->pincode,
                        'Property Latitude' => $property->latitude,
                        'Property Longitude' => $property->longitude,
                        'Property Description' => $property->description,
                        'Security Deposite' => !empty ($property->security_deposit) ? $property->security_deposit : '',
                        'Maintenance Charge' => !empty ($property->maintenance_charge) ? $property->maintenance : '',
                    ];
                }

                $web_array = [
                    'Business Processing Mapping Title' => $businessProcess->title,
                    'Business Processing Mapping Description' => $businessProcess->description,
                    'Related To' => $related->related,
                    'Properties' => $property_data
                ];
            }

            if (!empty ($related) && $related->related == 'Contract') {
                $contract_id = explode(',', $businessProcess->related_assign);

                $contracts = Contract::whereIn('id', $contract_id)->get();

                foreach ($contracts as $contract) {
                    $contract_data[] = [
                        'Contract Subject' => $contract->subject,
                        'Contract Status' => $contract->status,
                        'Contract Type' => $contract->contract_type,
                        'Contract Description' => $contract->description,
                    ];
                }

                $web_array = [
                    'Business Processing Mapping Title' => $businessProcess->title,
                    'Business Processing Mapping Description' => $businessProcess->title,
                    'Related To' => $related->related,
                    'Contracts' => $contract_data,
                ];
            }

            $action = 'New Business Process Mapping';
            $module = 'BusinessProcessMapping';
            SendWebhook::SendWebhookCall($module, $web_array, $action);
        }
    }
}
