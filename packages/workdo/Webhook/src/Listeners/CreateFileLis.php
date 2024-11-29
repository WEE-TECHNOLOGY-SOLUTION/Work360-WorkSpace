<?php

namespace Workdo\Webhook\Listeners;

use App\Models\User;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Workdo\FileSharing\Events\CreateFile;
use Workdo\Webhook\Entities\SendWebhook;

class CreateFileLis
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
    public function handle(CreateFile $event)
    {
        if (module_is_active('Webhook')) {
            $file = $event->file;

            $users = explode(',', $file->user_id);

            $usersData = User::whereIn('id', $users)->get();

            $usersInfo = [];
            foreach ($usersData as $user) {
                $usersInfo[] = [
                    'Name' => $user->name,
                    'Email' => $user->email,
                ];
            }

            $web_array = [
                'File Sharing Type' => $file->filesharing_type,
                'Email' => !empty($file->email) ? $file->email : '',
                'Auto Destroy' => $file->auto_destroy,
                'Users' => $usersInfo,
                'Description' => $file->description,
            ];

            $action = 'New File';
            $module = 'FileSharing';
            SendWebhook::SendWebhookCall($module, $web_array, $action);
        }
    }
}
