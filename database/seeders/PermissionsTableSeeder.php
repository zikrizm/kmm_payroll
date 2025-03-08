<?php

namespace Database\Seeders;

use Illuminate\Support\Carbon;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class PermissionsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $data = [
            ['name' => 'user.view'],
            ['name' => 'user.create'],
            ['name' => 'user.update'],
            ['name' => 'user.delete'],
            ['name' => 'role.view'],
            ['name' => 'role.create'],
            ['name' => 'role.update'],
            ['name' => 'role.delete'],

            ['name' => 'employee.view'],
            ['name' => 'employee.create'],
            ['name' => 'employee.update'],
            ['name' => 'employee.delete'],
            ['name' => 'employee-photo.view'],
            ['name' => 'employee-photo.create'],
            ['name' => 'kasbon.view'],
            ['name' => 'kasbon.create'],
            ['name' => 'kasbon.update'],
            ['name' => 'kasbon.delete'],
            ['name' => 'resign.view'],
            ['name' => 'resign.create'],
            ['name' => 'resign.update'],
            ['name' => 'resign.delete'],
            ['name' => 'attendance-manual.view'],
            ['name' => 'attendance-manual.create'],
            ['name' => 'attendance-manual.delete'],

            ['name' => 'shift.view'],
            ['name' => 'shift.create'],
            ['name' => 'shift.update'],
            ['name' => 'shift.delete'],
            ['name' => 'break-time.view'],
            ['name' => 'break-time.create'],
            ['name' => 'break-time.update'],
            ['name' => 'break-time.delete'],
            ['name' => 'timetable.view'],
            ['name' => 'timetable.create'],
            ['name' => 'timetable.update'],
            ['name' => 'timetable.delete'],
            ['name' => 'holiday.view'],
            ['name' => 'holiday.create'],
            ['name' => 'holiday.update'],
            ['name' => 'holiday.delete'],
            ['name' => 'department.view'],
            ['name' => 'department.create'],
            ['name' => 'department.update'],
            ['name' => 'department.delete'],
            ['name' => 'position.view'],
            ['name' => 'position.create'],
            ['name' => 'position.update'],
            ['name' => 'position.delete'],
            ['name' => 'area.view'],
            ['name' => 'area.create'],
            ['name' => 'area.update'],
            ['name' => 'area.delete'],

            ['name' => 'device.view'],
            ['name' => 'device.create'],
            ['name' => 'device.update'],
            ['name' => 'device.delete'],
            ['name' => 'attendance-report.view'],
            ['name' => 'attendance-report.create'],
            ['name' => 'attendance-report.update'],
            ['name' => 'attendance-report.delete'],
            ['name' => 'attendance-card.view'],
            ['name' => 'attendance-card.create'],
            ['name' => 'attendance-card.update'],
            ['name' => 'attendance-card.delete'],
            ['name' => 'attendance-operational.view'],
            ['name' => 'attendance-operational.create'],
            ['name' => 'attendance-operational.update'],
            ['name' => 'attendance-operational.delete'],
            ['name' => 'payroll-report.view'],
            ['name' => 'payroll-report.calculate'],


            ['name' => 'operational.view'],
            ['name' => 'operational.create'],
            ['name' => 'operational.update'],
            ['name' => 'operational.delete'],

            ['name' => 'attendance-tso.view'],
            ['name' => 'attendance-tso.approved'],

            ['name' => 'attendance-lb.set-status'],

            ['name' => 'request-task.view'],
            ['name' => 'request-task.create'],
            ['name' => 'request-task.update'],
            ['name' => 'request-task.delete'],

            ['name' => 'salary-archive.view'],
            ['name' => 'salary-archive.re-calculate'],
            
            ['name' => 'food-archive.view'],
            ['name' => 'business_settings.access'],

            ['name' => 'business_settings.access'],

            ['name' => 'report.working.view'],
            ['name' => 'report.break-time.view'],
        ];

        // $insert_data = [];
        // $time_stamp = Carbon::now()->toDateTimeString();
        // foreach ($data as $d) {
        //     $d['guard_name'] = 'web';
        //     $d['created_at'] = $time_stamp;
        //     $insert_data[] = $d;
        // }
        // Permission::insert($insert_data);

        foreach ($data as $permission) {
            Permission::firstOrCreate([
                'name' => $permission['name'],
                'guard_name' => 'web'
            ]);
        }
    }
}
