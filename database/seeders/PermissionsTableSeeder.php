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
            // ['name' => 'work-section.view'],
            // ['name' => 'work-section.create'],
            // ['name' => 'work-section.update'],
            // ['name' => 'work-section.delete'],
            // ['name' => 'group.view'],
            // ['name' => 'group.create'],
            // ['name' => 'group.update'],
            // ['name' => 'group.delete'],


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
            ['name' => 'transaction.view'],
            ['name' => 'transaction.create'],
            ['name' => 'transaction.update'],
            ['name' => 'transaction.delete'],
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
            ['name' => 'attendance-card'],
            ['name' => 'payroll-report.view'],
            ['name' => 'payroll-report.create'],
            ['name' => 'payroll-report.update'],
            ['name' => 'payroll-report.delete'],
            ['name' => 'overtime-rice-report.view'],
            ['name' => 'overtime-rice-report.create'],
            ['name' => 'overtime-rice-report.update'],
            ['name' => 'overtime-rice-report.delete'],

            ['name' => 'operational.view'],
            ['name' => 'operational.create'],
            ['name' => 'operational.update'],
            ['name' => 'operational.delete'],
            ['name' => 'request-help.view'],
            ['name' => 'request-help.create'],
            ['name' => 'request-help.update'],
            ['name' => 'request-help.delete'],
            ['name' => 'request-task.view'],
            ['name' => 'request-task.create'],
            ['name' => 'request-task.update'],
            ['name' => 'request-task.delete'],

            ['name' => 'business_settings.access'],
        ];

        $insert_data = [];
        $time_stamp = Carbon::now()->toDateTimeString();
        foreach ($data as $d) {
            $d['guard_name'] = 'web';
            $d['created_at'] = $time_stamp;
            $insert_data[] = $d;
        }
        Permission::insert($insert_data);
    }
}
