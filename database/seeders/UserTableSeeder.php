<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class UserTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // create Admin role and assign to user
        // $admin = User::create(['first_name' => 'admin', 'username' => 'admin', 'is_default' => 1, 'password' => bcrypt('123456')]);
        // $role = Role::create(['name' => 'admin', 'guard_name' => 'web', 'is_default' => 1])->givePermissionTo(Permission::all());
        // $admin->assignRole($role->name);

        // //Create Cashier role for a new business
        // $kasir = User::create(['first_name' => 'cashier', 'username' => 'cashier', 'password' => bcrypt('123456')]);
        // $cashier_role = Role::create(['name' => 'cashier', 'guard_name' => 'web']);
        // $cashier_role->syncPermissions(['user.view']);
        // $kasir->assignRole($cashier_role->name);
    }
}
