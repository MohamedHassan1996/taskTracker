<?php

namespace Database\Seeders\Roles;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RolesAndPermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {

        // premissions
        $permissions = [
            'all_users',
            'create_user',
            'edit_user',
            'update_user',
            'delete_user',
            'change_user_status',

            'all_roles',
            'create_role',
            'edit_role',
            'update_role',
            'delete_role',

            'all_service_categories',
            'create_service_category',
            'edit_service_category',
            'update_service_category',
            'delete_service_category',

            'all_tasks',
            'create_task',
            'edit_task',
            'update_task',
            'delete_task',

            'all_admin_tasks',

            'all_task_time_logs',
            'create_task_time_log',
            'edit_task_time_log',
            'update_task_time_log',
            //'delete_task_time_log',

            'all_parameters',
            'create_parameter',
            'edit_parameter',
            'update_parameter',
            'delete_parameter',

            'all_active_tasks',
            'update_active_task',

            'all_reports',


        ];

        foreach ($permissions as $permission) {
            Permission::updateOrCreate(['name' => $permission], [
                'name' => $permission,
                'guard_name' => 'api',
            ]);
        }

        // roles
        $superAdmin = Role::create(['name' => 'superAdmin']);
        $superAdmin->givePermissionTo(Permission::get());

        $accountant = Role::create(['name' => 'admin']);
        $accountant->givePermissionTo([
            'all_users',
            'create_user',
            'edit_user',
            'update_user',
            'delete_user',
            'change_user_status',

            'all_roles',
            'create_role',
            'edit_role',
            'update_role',
            'delete_role',

            'all_service_categories',
            'create_service_category',
            'edit_service_category',
            'update_service_category',
            'delete_service_category',

            'all_tasks',
            'create_task',
            'edit_task',
            'update_task',
            'delete_task',

            'all_task_time_logs',
            'create_task_time_log',
            'edit_task_time_log',
            'update_task_time_log',
            //'delete_task_time_log',

            'all_parameters',
            'create_parameter',
            'edit_parameter',
            'update_parameter',
            'delete_parameter',

            'all_active_tasks',
            'update_active_task',

            'all_reports',

        ]);



    }
}
