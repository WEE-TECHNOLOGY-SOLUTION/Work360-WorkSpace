<?php

namespace Workdo\Webhook\Database\Seeders;

use App\Models\Role;
use App\Models\Permission;
use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Artisan;

class PermissionTableSeeder extends Seeder
{
     public function run()
    {
        Model::unguard();
        Artisan::call('cache:clear');
        $permissions  = [
            'webhook manage',
            'webhook create',
            'webhook edit',
            'webhook delete'
        ];

        $company_role = Role::where('name','company')->first();
        $superadmin_role = Role::where('name','super admin')->first();

        foreach ($permissions as $key => $value)
        {
            $table = Permission::where('name',$value)->where('module','Webhook')->exists();
            if(!$table)
            {
                $permission = Permission::create(
                    [
                        'name' => $value,
                        'guard_name' => 'web',
                        'module' => 'Webhook',
                        'created_by' => 0,
                        "created_at" => date('Y-m-d H:i:s'),
                        "updated_at" => date('Y-m-d H:i:s')
                    ]
                );
                if(!$company_role->hasPermission($value) && !$superadmin_role->hasPermission($value)){
                    $company_role->givePermission($permission);
                    $superadmin_role->givePermission($permission);
                }
            }
        }
    }
}
