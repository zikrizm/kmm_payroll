<?php

namespace App\Utils;

use App\Models\AttendanceLb;
use App\Models\AttendanceTso;
use App\Models\User;
use App\Models\Shift;
use App\Models\Device;
use App\Models\Holiday;
use App\Models\Business;
use App\Models\Employee;
use App\Models\Position;
use App\Models\Timetable;
use App\Models\Department;
use App\Models\EmployeeTso;
use App\Models\EmployeeDebt;
use App\Models\Operational;
use App\Models\Transaction;
use App\Models\EmployeeStatusLb;
use App\Models\SalaryArchiveTh;
use App\Services\Api\ApiServices;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Carbon\CarbonPeriod;
use Illuminate\Support\Collection;
use Spatie\Permission\Models\Permission;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Log;
use App\Models\SalaryArchiveTd;
use App\Models\SalaryArchiveTdEmp;

class AttendanceUtil extends Util
{
    private $service;
    public $slug_week = ['Mgg', 'Sen', 'Sel', 'Rab', 'Kam', 'Jum', 'Sab'];

    /**
     * Initializes the Attendance.
     *
     * @return void
     */
    public function __construct(ApiServices $service)
    {
        $this->service = $service;
    }

    public function formatDuration($minutes) {
        if ($minutes <= 0) {
            return "0 menit";
        }
    
        $hours = floor($minutes / 60);
        $remainingMinutes = $minutes % 60;
    
        if ($hours > 0 && $remainingMinutes > 0) {
            return "{$hours} jam {$remainingMinutes} menit";
        } elseif ($hours > 0) {
            return "{$hours} jam";
        } else {
            return "{$remainingMinutes} menit";
        }
    }

    public function calculatePaymentPeriodDiff(string $payment_period, Carbon $start_date, Carbon $end_date)
    {
        switch ($payment_period) {
            case 'mounthly':
                return $start_date->diffInMonths($end_date);
                break;
            case 'weekly':
                return $start_date->diffInWeeks($end_date);
                break;
            case 'daily':
                return $start_date->diffInDays($end_date);
                break;
            default:
                return $start_date->diffInWeeks($end_date);
                break;
        }
    }

    public function getRangeDate(
        Carbon $start_date_work_day,
        Carbon $end_date_work_day,
        Carbon $start_date_overtime,
        Carbon $end_date_overtime
    ) {
        $start_date = min($start_date_work_day, $start_date_overtime)->subDay();
        $end_date = max($end_date_work_day, $end_date_overtime)->addDay();
    
        $business_id = Session::get('business_id');
    
        return collect(CarbonPeriod::create($start_date, $end_date))->map(function ($date) use (
            $start_date, $end_date, $start_date_work_day, $end_date_work_day, 
            $start_date_overtime, $end_date_overtime, $business_id
        ) {
            $dayOfWeek = $date->dayOfWeek;
            $holidays = Holiday::where('business_id', $business_id)
                ->whereDate('start_date', '<=', $date)
                ->whereDate('end_date', '>=', $date)
                ->first();
    
            return collect([
                'holiday' => [
                    'status' => $date->isSunday() || !empty($holidays),
                    'name' => !empty($holidays) ? $holidays->name: (($date->isSunday()) ? 'sunday' : null),
                    'start_date' => !empty($holidays) ? $holidays->start_date: null,
                    'end_date' => !empty($holidays) ? $holidays->end_date: null,
                ],
                'is_addition_date' => $date->isSameDay($start_date) || $date->isSameDay($end_date),
                'salary_included' => $date->between($start_date_work_day, $end_date_work_day),
                'overtime_included' => $date->between($start_date_overtime, $end_date_overtime),
                'd' => $date->format('d'),
                'date' => $date->format('Y-m-d'),
                'key' => $this->slug_week[$dayOfWeek] ?? null,
            ]);
        });
    }
    

    public function getMergeAttendance(
        Carbon $start_date_work_day,
        Carbon $end_date_work_day,
        Carbon $start_date_overtime,
        Carbon $end_date_overtime,
        array|null $list_emp_code,
        Collection $list_device = null,
        array|null $department
    ) {
        $start_date = min($start_date_work_day, $start_date_overtime)->copy()->subDay();
        $end_date = max($end_date_work_day, $end_date_overtime)->copy()->addDay();

        $list_attendance_db = Transaction::whereBetween('punch_time', [$start_date, $end_date])->orderBy('punch_time', 'ASC')->get();

        $start_date_new = $start_date->copy()->startOfDay();
        $end_date_new = $end_date->copy()->endOfDay();

        // $list_emp_code_string = "'" . implode("','", $list_emp_code) . "'";
        // $query = "
        //     SELECT * FROM iclock_transaction 
        //     WHERE punch_time BETWEEN '{$start_date_new->format('Y-m-d H:i:s')}' AND '{$end_date_new->format('Y-m-d H:i:s')}'
        //     AND emp_code IN ($list_emp_code_string)
        //     ORDER BY emp_code, punch_time
        // ";

        // $attendances = DB::connection('pgsql')->select($query);
        // $attendances = collect($attendances); // Konversi ke Collection

        // $list_attendance_bios = $attendances->map(function ($item) use ($list_device) {
        //     $device_db = $list_device->firstWhere('sn', $item->terminal_sn);
        
        //     return [
        //         "id" => $item->id,
        //         "emp_code" => $item->emp_code,
        //         "punch_time" => $item->punch_time,
        //         "area_alias" => $item->area_alias,
        //         "terminal_sn" => $item->terminal_sn,
        //         "terminal_alias" => $item->terminal_alias,
        //         "punch_type" => $device_db ? $device_db['punch_type'] ?? 'check-in-out' : 'check-in-out'
        //     ];
        // });

        if (!empty($department)) {
            $page_size = $this->service->get_transaction_reports(["start_date" =>  $start_date_new->format('Y-m-d'), "end_date" =>  $end_date_new->format('Y-m-d'), "departments" => $department['id']])['count'];
            $list_attendance_bios = collect($this->service->get_transaction_reports(array_merge(['page_size' => $page_size], [
                "start_date" => $start_date_new->format('Y-m-d'), "end_date" => $end_date_new->format('Y-m-d'), "departments" => $department['id'],
            ]))['data']);

            $list_attendance_bios = $list_attendance_bios->map(function ($item) {
                return [
                    "id" => $item['id'],
                    "emp_code" => $item['emp_code'],
                    "first_name" => $item['first_name'],
                    "last_name" => $item['last_name'],
                    "dept_code" => $item['dept_code'],
                    "dept_name" => $item['dept_name'],
                    "position_code" => $item['position_code'],
                    "position_name" => $item['position_name'],
                    "punch_time" => $item['att_date'] . " " . $item['punch_time'] . ":00",
                    "punch_state" => $item['punch_state'],
                    "verify_type" => $item['verify_type'],
                    "work_code" => $item['work_code'],
                    "area_alias" => null,
                    "terminal_alias" => null,
                    "source" => $item['source']
                ];
            });
        } else {
            $page_size = $this->service->get_transactions(["start_time" =>  $start_date_new->format('Y-m-d H:i:s'), "end_time" => $end_date_new->format('Y-m-d H:i:s')])['count'];
            $list_attendance_bios = collect($this->service->get_transactions(array_merge(['page_size' => $page_size], [
                "start_time" =>  $start_date_new->format('Y-m-d H:i:s'), "end_time" =>  $end_date_new->format('Y-m-d H:i:s'),
            ]))['data']);

            $list_attendance_bios = $list_attendance_bios->map(function ($item) {
                return [
                    "id" => $item['id'],
                    "emp_code" => $item['emp_code'],
                    "first_name" => $item['first_name'],
                    "last_name" => $item['last_name'],
                    "dept_code" => null,
                    "dept_name" => $item['department'],
                    "position_code" => null,
                    "position_name" => $item['position'],
                    "punch_time" => $item['punch_time'],
                    "punch_state" => $item['punch_state_display'],
                    "verify_type" => $item['verify_type_display'],
                    "work_code" => $item['work_code'],
                    "area_alias" => $item['area_alias'],
                    "terminal_alias" => $item['terminal_alias'],
                    "source" => null
                ];
            });
        }

        foreach ($list_attendance_db as $key => $item) {
            $list_attendance_bios->push($item->toArray());
        }

        return $list_attendance_bios->sortBy('punch_time');
    }

    public function groupingAttendance($attendances){
        $attendances = $attendances->groupBy([
            'emp_code',
            function ($item) {
                return Carbon::parse($item['punch_time'])->format('Y-m-d');
            }
        ]);

        return $attendances;
    }

    public function getShift(array|null $department) {
        $business_id = Session::get('business_id');

        $shifts = Shift::where('business_id', $business_id);
        if (!empty($department)) {
            $shifts = $shifts->where('dept_id', $department['id']);
        }

        $shifts = $shifts->with([
            'shiftdays' => fn ($query) => $query->select('id', 'shift_id', 'code_day'),
            'shiftdays.shiftday_has_timetables' => fn ($query) => 
                $query->select('id', 'shift_day_id', 'timetable_id'),
            'shiftdays.shiftday_has_timetables.timetable' => fn ($query) => 
                $query->select(
                    'id', 'name', 
                    'check_in', 'check_out', 
                    'check_in_min', 'check_in_plus', 
                    'check_out_min', 'check_out_plus', 
                    'check_in_plusmn', 'check_out_plusmn', 
                    'warning_check_in_min', 'warning_check_in_plus', 
                    'warning_check_out_min', 'warning_check_out_plus',  
                    'cross_day', 'work_time', 'is_without_break', 
                    'ot_roundone_hr', 'ot_roundhalf_hr', 'ot_period', 
                    'ot_pay', 'enable_extra_pay', 'extra_pay', 
                    'duration_count_one_shift', 'duration_ot_limit', 
                    'duration_rice_shift'
                ),
        ])->get();

        $shifts->each(function ($shift) {
            $shift->shiftdays->each(function ($shiftday) {
                $shiftday->shiftday_has_timetables = $shiftday->shiftday_has_timetables->sortBy('timetable.check_in')->values();
            });
        });

        return $shifts;
    }

    public function getOperationals(
        Carbon $start_date_work_day,
        Carbon $end_date_work_day,
        Carbon $start_date_overtime,
        Carbon $end_date_overtime,
        array|null $department
    ) {
        $start_date = min($start_date_work_day, $start_date_overtime)->subDay();
        $end_date = max($end_date_work_day, $end_date_overtime)->addDay();

        $business_id = Session::get('business_id');
        $operationals = Operational::where('business_id', $business_id)
            ->whereBetween('date', [$start_date, $end_date]);

        if (!empty($department)) {
            $operationals = $operationals->where('dept_id', $department['id']);
        }

        $operationals->get();

        return $operationals;
    }

    public function getPosition() {
        $page_size = $this->service->get_positions([])["count"];
        $list_position_bios = collect($this->service->get_positions(["page_size" => $page_size])['data']);

        $list_position_db = Position::select('id as position_id', 'position_id as id', 'position_name', 'must_attend', 'permanently', 'extra_pay', 'enable_extra_break_time', 'extra_break_time')->get();

        $positions = $list_position_bios->map(function ($item) use ($list_position_db) {
            $position_db = $list_position_db->firstWhere('id', $item['id']);
            if ($position_db) {
                return array_merge($item, $position_db->toArray());
            }

            return $item;
        });

        return $positions;
    }

    public function getDevice() {
        $page_size = $this->service->get_devices([])["count"];
        $list_device_bios = collect($this->service->get_devices(["page_size" => $page_size])['data']);

        $list_device_db = Device::select('id as device_id', 'device_id as id', 'punch_type')->get();

        $devices = $list_device_bios->map(function ($item) use ($list_device_db) {
            $device_db = $list_device_db->firstWhere('id', $item['id']);
            if ($device_db) {
                return array_merge($item, $device_db->toArray());
            }

            return $item;
        });

        return $devices;
    }

    public function getDepartment() {
        $page_size = $this->service->get_departments([])["count"];
        $list_department_bios = collect($this->service->get_departments(["page_size" => $page_size])['data']);

        $list_department_db = Department::select('id as dept_id', 'dept_id as id', 'sitting_money', 'still_paid')->get();

        $departments = $list_department_bios->map(function ($item) use ($list_department_db) {
            $department_db = $list_department_db->firstWhere('id', $item['id']);
            if ($department_db) {
                return array_merge($item, $department_db->toArray());
            }

            return $item;
        });

        return $departments;
    }

    public function getEmployee(
        array|null $department = null, 
        Collection $list_department = null, 
        Collection $list_position = null
    ) {
        $list_department = $list_department ?? collect();
        $list_position = $list_position ?? collect();
        
        $list_employee_bios = collect();

        if (!empty($department)) {
            $page_size = $this->service->get_employees(["department" => $department['id']])["count"];
            $list_employee_bios = collect($this->service->get_employees(["page_size" => $page_size, "department" => $department['id']])['data']);
        } else {
            $page_size = $this->service->get_employees([])["count"];
            $list_employee_bios = collect($this->service->get_employees(["page_size" => $page_size])['data']);
        }

        $list_employee_bios_ids = $list_employee_bios->pluck('id')->unique()->values()->toArray();

        $business_id = Session::get('business_id');
        $list_employee_db = Employee::where('business_id', $business_id)->whereIn('emp_id', $list_employee_bios_ids)
            ->select('id as emp_id', 'business_id', 'emp_id as id', 'emp_code', 'daily_salary', 'payment_period')
            ->get();

        $employees = $list_employee_bios->map(function ($item) use ($list_employee_db, $list_department, $list_position) {
            if(!empty($item['position'])) {
                $position = $list_position->firstWhere('id', $item['position']['id']);
                if ($position) {
                    $item['position'] = array_merge($item['position'], $position);
                }
            }

            $department = $list_department->firstWhere('id', $item['department']['id']);
            if ($department) {
                $item['department'] = array_merge($item['department'], $department);
            }

            $employee_db = $list_employee_db->firstWhere('id', $item['id']);
            if ($employee_db) {
                $employee_db->emp_code = $item['emp_code'];
                return array_merge($item, $employee_db->toArray());
            } else {
                // untuk default data karyawan
                $item['payment_period'] = "weekly";
                $item['daily_salary'] = 0;
            }

            return $item;
        });

        $filtered = $employees->filter(function ($item) {
            return isset($item['attemployee']) && $item['attemployee']['enable_attendance']; // Hanya ambil user dengan umur lebih dari 21
        });

        return $filtered;
    }

    public function attendanceTimetableGrouping(
        ?Shift $department_shift = null,
        Collection $attendance_employee,
        Collection $range_dates,
        $emp_code,
    ) {
        if(empty($department_shift)) return $attendance_employee;
        $masaJedaAbsensi = 6;

        $attendance_employee_timetable =  collect([]);
        // if($emp_code == '250313') {
            $range_date_count = count($range_dates);
            foreach ($range_dates as $iDate => $range_date) {
                if ($iDate == $range_date_count - 1) continue;

                $date = Carbon::parse($range_date['date']);
                $date_string = $date->format('Y-m-d');
    
                // $dayOfWeek =  $date->dayOfWeek; 
                $dayOfWeek = $range_date['holiday']['status']? 0: $date->dayOfWeek; 
                $shiftday = $department_shift->shiftdays->firstWhere('code_day', $dayOfWeek);
                // Log::info($date);

                $date_attendances = collect($attendance_employee->get($date_string, collect()));

                // Log::info($date_attendances);
    
                if (!$shiftday) continue;
    
                $attendance_timetable = collect([]);
                $selected_timetable = null;

                foreach ($shiftday->shiftday_has_timetables as $shiftday_has_timetable) {
                    $timetable = $shiftday_has_timetable->timetable;
    
                    if($date_attendances->isEmpty()) break;
    
                    $check_in = Carbon::parse($date->format('Y-m-d'). " " .$timetable->check_in);
                    $check_out = Carbon::parse($date->format('Y-m-d'). " " .$timetable->check_out)
                        ->addDays($timetable->cross_day ?? 0);
        
                    $check_in_limit_min = $check_in->copy()->subMinutes($timetable->check_in_min);
                    $check_in_limit_plus = $check_in->copy()->addMinutes($timetable->check_in_plus);
                    $check_out_limit_min = $check_out->copy()->subMinutes($timetable->check_out_min);
                    $check_out_limit_plus = $check_out->copy()->addMinutes($timetable->check_out_plus);
        
                    $check_out_limit_ot = $check_out->copy()->addHours($timetable->duration_ot_limit ?? 0);
                    // Log::info($check_in_limit_min);
                    // Log::info($check_in_limit_plus);

                    foreach ($date_attendances as $key => $attendance) {
                        $punch_time = Carbon::parse($attendance['punch_time']);
                        if($punch_time->between($check_in_limit_min, $check_in_limit_plus)) {
                            // Log::info($key);
                            // Log::info($attendance);

                            if($key == 0) {
                                $prev_date = $date->copy()->subDay();
                                $prev_date_string = $prev_date->format('Y-m-d');
                                $prev_date_attendance = $attendance_employee->get($prev_date_string, collect());

                                $masa_jeda_start = $check_in_limit_min->copy()->subHours($masaJedaAbsensi);

                                $hasLogInMasaJeda = $prev_date_attendance->contains(function ($log) use ($masa_jeda_start, $punch_time, $check_in_limit_min) {
                                    $log_time = Carbon::parse($log['punch_time']);
                                    return $log_time->between($masa_jeda_start, $check_in_limit_min);
                                });

                    
                                if ($hasLogInMasaJeda) continue;
                            } else {
                                // Cek log dari tanggal yang sama
                                $curr_date_string = $date->format('Y-m-d');
                                $curr_date_attendance = $attendance_employee->get($curr_date_string, collect());

                                // Ambil log sebelum index sekarang
                                $masa_jeda_start = $check_in_limit_min->copy()->subHours($masaJedaAbsensi);

                                $hasLogInMasaJeda = collect($curr_date_attendance->slice(0, $key))->contains(function ($log) use ($masa_jeda_start, $punch_time, $check_in_limit_min) {
                                    $log_time = Carbon::parse($log['punch_time']);
                                    return $log_time->between($masa_jeda_start, $check_in_limit_min);
                                });

                                if ($hasLogInMasaJeda) continue;
                            }

                            // Log::info($check_in_limit_min);
                            // Log::info($check_in_limit_plus);
                            // Log::info($attendance);

                            $selected_timetable = $timetable; 
                            break;
                        }
                    }

                    if($selected_timetable) {
                        break;
                    }
                }

                if($selected_timetable) {
                    $check_in = Carbon::parse($date->format('Y-m-d'). " " .$selected_timetable->check_in);
                    $check_out = Carbon::parse($date->format('Y-m-d'). " " .$selected_timetable->check_out)
                        ->addDays($selected_timetable->cross_day ?? 0);
        
                    $check_in_limit_min = $check_in->copy()->subMinutes($selected_timetable->check_in_min);
                    $check_in_limit_plus = $check_in->copy()->addMinutes($selected_timetable->check_in_plus);
                    $check_out_limit_min = $check_out->copy()->subMinutes($selected_timetable->check_out_min);
                    $check_out_limit_plus = $check_out->copy()->addMinutes($selected_timetable->check_out_plus);
        
                    $check_out_limit_ot = $check_out->copy()->addHours($selected_timetable->duration_ot_limit ?? 0);

                    $date_attendances = $date_attendances->reject(function ($attendance, $index) use (
                        $selected_timetable, $attendance_timetable, $check_in_limit_min, $check_in_limit_plus, $check_out_limit_min, $check_out_limit_plus, $check_out_limit_ot,
                    ) {
                        $punch_time = Carbon::parse($attendance['punch_time']);
                        $is_delete = false;
    
                        if($punch_time->gte($check_in_limit_min) && $punch_time->lte($check_out_limit_ot)) {
                            $existing = $attendance_timetable->get($selected_timetable->id, collect());
                            if($existing->count() == 0) {
                                if($punch_time->between($check_in_limit_min, $check_in_limit_plus)) {
                                    $existing->push($attendance);
                                    $is_delete = true;
                                } else {
                                    $is_delete = false;
                                }
                            } else {
                                $existing->push($attendance);
                                $is_delete = true;
                            }
    
                            if($existing->isNotEmpty()) {
                                $attendance_timetable->put($selected_timetable->id, $existing);
                            }
                        }
    
                        return $is_delete;
                    });

                    $diff_days = $check_out_limit_ot->copy()->startOfDay()->diffInDays($check_out->copy()->startOfDay());
                    $cross_day = $diff_days + ($selected_timetable->cross_day ?? 0);
    
                    if($cross_day > 0 && $attendance_timetable->get($selected_timetable->id, collect())->count() > 0) {
                        $next_date = $date->copy();
                        
                        for ($i = 0; $i < $cross_day; $i++) { 
                            $next_date = $next_date->addDay();
                            $next_date_string = $next_date->format('Y-m-d');
    
                            $next_date_attendance = $attendance_employee->get($next_date_string, collect());
                            if($next_date_attendance->isEmpty()) continue;
    
                            $attendance_employee[$next_date_string] = $next_date_attendance->reject(function ($attendance) use (
                                $selected_timetable, $attendance_timetable, $check_in_limit_min, $check_in_limit_plus, $check_out_limit_min, $check_out_limit_plus, $check_out_limit_ot,
                            ) {
                                $punch_time = Carbon::parse($attendance['punch_time']);
                                if($punch_time->lte($check_out_limit_ot)) {
                                    $existing = $attendance_timetable->get($selected_timetable->id, collect());
                                    $existing->push($attendance);
            
                                    $attendance_timetable->put($selected_timetable->id, $existing);
                                    return true;
                                }
            
                                return false;
                            });
                        }
                    }
                }
    
                // Log::info($attendance_timetable);
                $attendance_employee_timetable->put($date_string, $attendance_timetable);
            }
        // } else {
            // $range_date_count = count($range_dates);
            // foreach ($range_dates as $iDate => $range_date) {
            //     if ($iDate >= $range_date_count - 1) continue;
    
            //     $date = Carbon::parse($range_date['date']);
            //     $date_string = $date->format('Y-m-d');
    
            //     $shiftday = $department_shift->shiftdays->firstWhere('code_day', $date->dayOfWeek);
            //     $date_attendances = collect($attendance_employee->get($date_string, collect()));
    
            //     if (!$shiftday) continue;
    
            //     $attendance_timetable = collect([]);
            //     foreach ($shiftday->shiftday_has_timetables as $shiftday_has_timetable) {
            //         $timetable = $shiftday_has_timetable->timetable;
    
            //         if($date_attendances->isEmpty()) break;
    
            //         $check_in = Carbon::parse($date->format('Y-m-d'). " " .$timetable->check_in);
            //         $check_out = Carbon::parse($date->format('Y-m-d'). " " .$timetable->check_out)
            //             ->addDays($timetable->cross_day ?? 0);
        
            //         $check_in_limit_min = $check_in->copy()->subMinutes($timetable->check_in_min);
            //         $check_in_limit_plus = $check_in->copy()->addMinutes($timetable->check_in_plus);
            //         $check_out_limit_min = $check_out->copy()->subMinutes($timetable->check_out_min);
            //         $check_out_limit_plus = $check_out->copy()->addMinutes($timetable->check_out_plus);
        
            //         $check_out_limit_ot = $check_out->copy()->addHours($timetable->duration_ot_limit ?? 0);
    
            //         $date_attendances = $date_attendances->reject(function ($attendance, $index) use (
            //             $timetable, $attendance_timetable, $check_in_limit_min, $check_in_limit_plus, $check_out_limit_min, $check_out_limit_plus, $check_out_limit_ot,
            //         ) {
            //             $punch_time = Carbon::parse($attendance['punch_time']);
            //             $is_delete = false;
    
            //             if($punch_time->gte($check_in_limit_min) && $punch_time->lte($check_out_limit_ot)) {
            //                 $existing = $attendance_timetable->get($timetable->id, collect());
            //                 if($existing->count() == 0) {
            //                     if($punch_time->between($check_in_limit_min, $check_in_limit_plus)) {
            //                         $existing->push($attendance);
            //                         $is_delete = true;
            //                     } else {
            //                         $is_delete = false;
            //                     }
            //                 } else {
            //                     $existing->push($attendance);
            //                     $is_delete = true;
            //                 }
    
            //                 if($existing->isNotEmpty()) {
            //                     $attendance_timetable->put($timetable->id, $existing);
            //                 }
            //             }
    
            //             return $is_delete;
            //         });
    
            //         $diff_days = $check_out_limit_ot->copy()->startOfDay()->diffInDays($check_out->copy()->startOfDay());
            //         $cross_day = $diff_days + ($timetable->cross_day ?? 0);
    
            //         if($cross_day > 0 && $attendance_timetable->get($timetable->id, collect())->count() > 0) {
            //             $next_date = $date->copy();
                        
            //             for ($i = 0; $i < $cross_day; $i++) { 
            //                 $next_date = $next_date->addDay();
            //                 $next_date_string = $next_date->format('Y-m-d');
    
            //                 $next_date_attendance = $attendance_employee->get($next_date_string, collect());
            //                 if($next_date_attendance->isEmpty()) continue;
    
            //                 $attendance_employee[$next_date_string] = $next_date_attendance->reject(function ($attendance) use (
            //                     $timetable, $attendance_timetable, $check_in_limit_min, $check_in_limit_plus, $check_out_limit_min, $check_out_limit_plus, $check_out_limit_ot,
            //                 ) {
            //                     $punch_time = Carbon::parse($attendance['punch_time']);
            //                     if($punch_time->lte($check_out_limit_ot)) {
            //                         $existing = $attendance_timetable->get($timetable->id, collect());
            //                         $existing->push($attendance);
            
            //                         $attendance_timetable->put($timetable->id, $existing);
            //                         return true;
            //                     }
            
            //                     return false;
            //                 });
            //             }
            //         }
            //     }
    
            //     $attendance_employee_timetable->put($date_string, $attendance_timetable);
            // }
        // }

        return $attendance_employee_timetable;
    }

  
    public function shiftCheck(
        array $employee, 
        Collection $range_date = null,
        Collection $attendances,
        Timetable $timetable,
    ) {
        $result = collect([
            'key' => $range_date['key'],
            'date' => $range_date['date'],
            'timetable' => null,
            'working' => collect(['start_punch' => null, 'end_punch' => null, "status" => false, "info" => null]),
            'break_time' => collect(['start_punch' => null, 'end_punch' => null, "status" => false, "info" => null]),
            'operational' => collect(['date' => $range_date['date'], 'tso_status' => false, 'lb_status' => false, 'overtime_adjustment' => null, "status" => false, "info" => null]),
            'total_shifted_overtime' => 0,
            'total_hk' => 0,
            'total_hk_bonus' => 0,
            'total_jl' => 0,
            'total_hk_pay' => 0,
            'total_overtime' => 0,
            'total_food' => 0,
        ]);

        $date = Carbon::parse($range_date['date']);
        $date_string = $date->format('Y-m-d');

        $check_in = Carbon::parse($date_string ." ". $timetable->check_in);
        $check_out = Carbon::parse($date_string ." ". $timetable->check_out)->addDays($timetable->cross_day ?? 0);
        $work_time = $timetable->work_time;

        $check_in_limit_min = $check_in->copy()->subMinutes($timetable->check_in_min);
        $check_in_limit_plus = $check_in->copy()->addMinutes($timetable->check_in_plus);
        $check_out_limit_min = $check_out->copy()->subMinutes($timetable->check_out_min);
        $check_out_limit_plus = $check_out->copy()->addMinutes($timetable->check_out_plus);

        $warning_check_in_limit_min = $check_in->copy()->subMinutes($timetable->warning_check_in_min);
        $warning_check_in_limit_plus = $check_in->copy()->addMinutes($timetable->warning_check_in_plus);
        $warning_check_out_limit_min = $check_out->copy()->subMinutes($timetable->warning_check_out_min);
        $warning_check_out_limit_plus = $check_out->copy()->addMinutes($timetable->warning_check_out_plus);

        $first_attendance = $attendances->first();
        $last_attendance = null;
        $first_break_time = null;
        $last_break_time = null;

        $attendance_count = $attendances->count();
        if($attendance_count > 1) {
            $last_attendance = $attendances->last();
        }

        if($attendance_count > 2) {
            $break_time_attendance = $attendances->filter(function ($item) use ($check_in_limit_min, $check_in_limit_plus, $check_out_limit_min, $check_out_limit_plus){
                $punch_time = Carbon::parse($item['punch_time']);
                return !($punch_time->between($check_in_limit_min, $check_in_limit_plus) || $punch_time->between($check_out_limit_min, $check_out_limit_plus))
                    && $punch_time->lte($check_out_limit_plus);
            });

            $break_time_attendance_count = $break_time_attendance->count();
            if($break_time_attendance_count > 0) {
                $first_break_time = $break_time_attendance->first();
                if($break_time_attendance_count > 1) {
                    $last_break_time = $break_time_attendance->last();
                }
            }
        } 

        $result['working']['start_punch'] = $first_attendance ? $first_attendance['punch_time'] : null;
        $result['working']['end_punch'] =  $last_attendance ? $last_attendance['punch_time'] : null;
        $result['break_time']['start_punch'] = $first_break_time ? $first_break_time['punch_time'] : null;
        $result['break_time']['end_punch'] = $last_break_time ? $last_break_time['punch_time'] : null;

        $duration_break_time = $timetable->timetable_has_break_time->sum(fn($item) => $item->break_time->duration ?? 0);
        if(!empty($employee['position']) && $employee['position']['enable_extra_break_time'] && !empty($employee['position']['extra_break_time'])) {
            $duration_break_time += $employee['position']['extra_break_time'];
        }

        $check_out_limit_break_time = $check_out_limit_min->copy();
        // check apakah ada absensi istrahat
        if(!$timetable->is_without_break && (empty($result['break_time']['start_punch']) || empty($result['break_time']['end_punch']))) {
            $check_out_limit_break_time->subMinutes($duration_break_time);
        }
       
        if(!empty($result['working']['start_punch']) && !empty($result['working']['end_punch'])) {
            $start_punch = Carbon::parse($result['working']['start_punch']);
            $end_punch = Carbon::parse($result['working']['end_punch']);

            if ($start_punch->between($check_in_limit_min, $check_in_limit_plus)) {
                $result['timetable'] = collect([
                    'id' => $timetable->id,
                    'name' => $timetable->name,
                    'check_in' => $timetable->check_in,
                    'check_in_min' => $timetable->check_in_min,
                    'check_in_plus' => $timetable->check_in_plus,
                    'check_in_plus' => $timetable->check_in_plus,
                    'warning_check_in_min' => $timetable->warning_check_in_min,
                    'warning_check_in_plus' => $timetable->warning_check_in_plus,
                    'check_out' => $timetable->check_out,
                    'check_out_min' => $timetable->check_out_min,
                    'check_out_plus' => $timetable->check_out_plus,
                    'warning_check_out_min' => $timetable->warning_check_out_min,
                    'warning_check_out_plus' => $timetable->warning_check_out_plus,
                    'cross_day' => $timetable->cross_day,
                    'work_time' => $timetable->work_time,
                    'ot_roundone_hr' => $timetable->ot_roundone_hr,
                    'ot_roundhalf_hr' => $timetable->ot_roundhalf_hr,
                    'ot_period' => $timetable->ot_period,
                    'ot_pay' => $timetable->ot_pay,
                    'enable_extra_pay' => $timetable->enable_extra_pay,
                    'extra_pay' => $timetable->extra_pay,
                    'duration_rice_shift' => $timetable->duration_rice_shift,
                    'duration_count_one_shift' => $timetable->duration_count_one_shift,
                    'duration_break_time' => $duration_break_time,
                    'is_without_break' => $timetable->is_without_break,
                ]);

                if($start_punch->gt($warning_check_in_limit_plus)) {
                    $diffInMinutes = $start_punch->diffInMinutes($warning_check_in_limit_plus);
                    $hours = floor($diffInMinutes / 60);
                    $minutes = $diffInMinutes % 60;
                    if ($hours > 0) {
                        $formattedDiff = "{$hours} jam " . ($minutes > 0 ? "{$minutes} menit" : "");
                    } else {
                        $formattedDiff = "{$minutes} menit";
                    }

                    $result['working']['status'] = false;
                    $result['working']['info'] = "Absensi terlambat $formattedDiff";
                } else if($end_punch->lt($warning_check_out_limit_min)) {
                    $diffInMinutes = $end_punch->diffInMinutes($warning_check_out_limit_min);
                    $hours = floor($diffInMinutes / 60);
                    $minutes = $diffInMinutes % 60;
                    if ($hours > 0) {
                        $formattedDiff = "{$hours} jam " . ($minutes > 0 ? "{$minutes} menit" : "");
                    } else {
                        $formattedDiff = "{$minutes} menit";
                    }

                    $result['working']['status'] = false;
                    $result['working']['info'] = "Absensi kurang $formattedDiff";
                } else if($end_punch->gte($check_out_limit_break_time)) {
                    $result['working']['status'] = true;
                    $result['working']['info'] = 'Absensi sudah sesuai jadwal';
                } else {
                    $working_duration = $start_punch->diffInMinutes($end_punch);
                    if($working_duration >= ($work_time / 2)) {
                        $result['working']['status'] = false;
                        $result['working']['info'] = 'Absensi pulang setengah hari';
                    } else {
                        $result['working']['status'] = false;
                        $result['working']['info'] = 'Absensi pulang tidak sesuai jadwal';
                    }
                }
            } else {
                $result['working']['status'] = false;
                $result['working']['info'] = 'Absensi tidak sesuai jadwal';
            }
        } else if(empty($result['working']['start_punch']) && empty($result['working']['end_punch'])) {
            $result['working']['status'] = false;
            $result['working']['info'] = 'Tidak ada absensi';
        } else if(empty($result['working']['start_punch'])) {
            $result['working']['status'] = false;
            $result['working']['info'] = 'Tidak ada absensi di waktu datang kerja';
        } else if(empty($result['working']['end_punch'])) {
            $result['working']['status'] = false;
            $result['working']['info'] = 'Tidak ada absensi di waktu pulang kerja';
        }

        if($duration_break_time != 0) {
            if(!empty($result['break_time']['start_punch']) && !empty($result['break_time']['end_punch'])) {
                $start_break_time_punch = Carbon::parse($result['break_time']['start_punch']);
                $end_break_time_punch = Carbon::parse($result['break_time']['end_punch']);

                $break_duration = $start_break_time_punch->diffInMinutes($end_break_time_punch);
                if($break_duration > $duration_break_time) {
                    $result['break_time']['status'] = false;

                    $exceeded_minutes = $duration_break_time - $break_duration;
                    $formatted_time = $this->formatDuration($exceeded_minutes);

                    $result['break_time']['info'] = "Istirahat melebihi batas " . ($formatted_time);
                } else if($break_duration < $duration_break_time) {
                    $result['break_time']['status'] = true;

                    $exceeded_minutes = $duration_break_time - $break_duration;
                    $formatted_time = $this->formatDuration($exceeded_minutes);

                    $result['break_time']['info'] = "Istirahat kurang $formatted_time";
                } else {
                    $result['break_time']['status'] = true;
                    $result['break_time']['info'] = 'Istirahat sudah sesuai jadwal';
                }
            } else if(empty($result['working']['start_punch']) && empty($result['working']['end_punch'])) {
                $result['working']['status'] = true;
                $result['working']['info'] = 'Tidak ada absensi';
            } else if(empty($result['break_time']['start_punch'])) {
                $result['break_time']['status'] = true;
                $result['break_time']['info'] = 'Tidak ada absensi di waktu awal istirahat';
            } else if(empty($result['break_time']['end_punch'])) {
                $result['break_time']['status'] = true;
                $result['break_time']['info'] = 'Tidak ada absensi di waktu selesai istirahat';
            }
        } else { 
            if(!empty($result['break_time']['start_punch'])|| !empty($result['break_time']['end_punch'])) {
                $result['break_time']['status'] = false;
                $result['break_time']['info'] = 'Terdapat absensi di waktu istrahat, padahal dijadwal tidak ada';
            }
        }
        

        return $result;
    }

    public function operationalCheck(
        array $employee, 
        Collection $range_date = null,
        Collection $shift_data,
        Operational $operational,
    ) {
        $result = collect([
            'tso_status' => false,
            'lb_status' => false,
            'overtime_adjustment' => null,
            'status' => false,
            'info' => null,
        ]);

        $date = Carbon::parse($range_date['date']);
        $date_string = $date->format('Y-m-d');

        if(!empty($shift_data['timetable'])) {
            $timetable_operational = $operational->operational_has_timetables
            ? $operational->operational_has_timetables->firstWhere('timetable_id', $shift_data['timetable']['id'])
            : null;

            if ($timetable_operational && $timetable_operational->status === 'active') {
                $duration_break_time = $shift_data['timetable']['duration_break_time'];
                $end_punch = Carbon::parse($shift_data['working']['end_punch']);
                
                $check_out = Carbon::parse($date_string ." ". $shift_data['timetable']['check_out'])->addDays($shift_data['timetable']['cross_day'] ?? 0);
                $check_out_limit_min = $check_out->copy()->subMinutes($shift_data['timetable']['check_out_min']);
                $check_out_limit_break_time = $check_out_limit_min->copy();
                if(empty($shift_data['break_time']['start_punch']) || empty($shift_data['break_time']['end_punch'])) {
                    if(!$shift_data['timetable']['is_without_break']) {
                        $check_out_limit_break_time->subMinutes($duration_break_time);
                    }
                }

                if ($shift_data['total_shifted_overtime'] > 0) {
                    $total_overtime = $shift_data['total_jl'] * ($shift_data['total_shifted_overtime'] + 1);
                } else {
                    $total_overtime = $shift_data['total_jl'];
                }

                if ($end_punch->gte($check_out_limit_break_time)) {
                    if ($total_overtime == $timetable_operational->ot_limit) {
                        // OPERATIONAL SUDAH SEASUAI
                        $result['status'] = true;
                        $result['info'] = 'Absensi sudah sesuai operational';
                    } else if ($total_overtime < $timetable_operational->ot_limit) {
                        // LEMBUR LEBIH KECIL,DARI WAKTU LEMBUR OPERATIONAL
                        $result['status'] = false;
                        $result['info'] = 'Lembur karyawan tidak sesuai operational';
                        $result['overtime_adjustment'] = $total_overtime - $timetable_operational->ot_limit;
                    } else if ($total_overtime > $timetable_operational->ot_limit) {
                        // LEMBUR LEBIH BESAR,DARI WAKTU LEMBUR OPERATIONAL
                        $result['status'] = false;
                        $result['info'] = 'Lembur karyawan tidak sesuai operational';
                        $result['overtime_adjustment'] = $total_overtime - $timetable_operational->ot_limit;
                    }
                } else {
                    // OPERATIONAL HADIR, KARYAWAN HADIR TETAPI TIDAK SESUAI DENGAN SHIFT
                    $result['status'] = false;
                    $result['info'] = 'Absensi tidak sesuai operational';
                    $result['overtime_adjustment'] = $lembur - $timetable_operational->ot_limit;
                }
            } else {
                $result['status'] = false;
                $result['info'] = 'Operasional diliburkan, tetapi karyawan masuk';
            }
        } else {
            $result['status'] = true;
            $result['lb_status'] = true;
            $result['info'] = 'Sudah sesuai, karena (OPERATIONAL DILIBURKAN DAN KARYAWAN TIDAK MASUK)';
        }
        // if(count($attendance_dailly_data['shifts']) != 0) {
        //     $shift_data = $attendance_dailly_data['shifts'][0];

        //     if(!empty($shift_data['timetable'])) {
        //         $timetable_operational = $operational->operational_has_timetables
        //         ? $operational->operational_has_timetables->firstWhere('timetable_id', $shift_data['timetable']['id'])
        //         : null;

        //         // Log::info("timetable_operational === ");
        //         // Log::info($timetable_operational);

        //         if ($timetable_operational && $timetable_operational->status === 'active') {
        //             $end_punch = Carbon::parse($shift_data['working']['end_punch']);
                    
        //             $check_out = Carbon::parse($date_string ." ". $shift_data['timetable']['check_out'])->addDays($shift_data['timetable']['cross_day'] ?? 0);
        //             $check_out_limit_min = $check_out->copy()->subMinutes($shift_data['timetable']['check_out_min']);
        //             $check_out_limit_break_time = $check_out_limit_min->copy();
        //             if(empty($shift_data['break_time']['start_punch']) || empty($shift_data['break_time']['end_punch'])) {
        //                 if(!$shift_data['timetable']['is_without_break']) {
        //                     $check_out_limit_break_time->subMinutes($duration_break_time);
        //                 }
        //             }

        //             if ($shift_data['total_shifted_overtime'] > 0) {
        //                 $total_overtime = $shift_data['total_jl'] * ($shift_data['total_shifted_overtime'] + 1);
        //             } else {
        //                 $total_overtime = $shift_data['total_jl'];
        //             }

        //             if ($end_punch->gte($check_out_limit_break_time)) {
        //                 if ($total_overtime == $timetable_operational->ot_limit) {
        //                     // OPERATIONAL SUDAH SEASUAI
        //                     $result['status'] = true;
        //                     $result['info'] = 'Absensi sudah sesuai operational';
        //                 } else if ($total_overtime < $timetable_operational->ot_limit) {
        //                     // LEMBUR LEBIH KECIL,DARI WAKTU LEMBUR OPERATIONAL
        //                     $result['status'] = false;
        //                     $result['info'] = 'Lembur karyawan tidak sesuai operational';
        //                     $result['overtime_adjustment'] = $total_overtime - $timetable_operational->ot_limit;
        //                 } else if ($total_overtime > $timetable_operational->ot_limit) {
        //                     // LEMBUR LEBIH BESAR,DARI WAKTU LEMBUR OPERATIONAL
        //                     $result['status'] = false;
        //                     $result['info'] = 'Lembur karyawan tidak sesuai operational';
        //                     $result['overtime_adjustment'] = $total_overtime - $timetable_operational->ot_limit;
        //                 }
        //             } else {
        //                 // OPERATIONAL HADIR, KARYAWAN HADIR TETAPI TIDAK SESUAI DENGAN SHIFT
        //                 $result['status'] = false;
        //                 $result['info'] = 'Absensi tidak sesuai operational';
        //                 $result['overtime_adjustment'] = $lembur - $timetable_operational->ot_limit;
        //             }
        //         } else {
        //             $result['status'] = false;
        //             $result['info'] = 'Operasional diliburkan, tetapi karyawan masuk';
        //         }
        //     } else {
        //         $result['status'] = false;
        //         $result['info'] = 'Operasional diliburkan, tetapi karyawan masuk';
        //     }
            
        // } else {
        //     $result['status'] = true;
        //     $result['lb_status'] = true;
        //     $result['info'] = 'Sudah sesuai, karena (OPERATIONAL DILIBURKAN DAN KARYAWAN TIDAK MASUK)';

        // }

        // if ($timetable_operational->status == 'active') {
        //     $end_punch = Carbon::parse($shift_data['working']['end_punch']);
            
        //     $check_out = Carbon::parse($date_string ." ". $shift_data['timetable']['check_out'])->addDays($shift_data['timetable']['cross_day'] ?? 0);
        //     $check_out_limit_min = $check_out->copy()->subMinutes($shift_data['timetable']['check_out_min']);
        //     $check_out_limit_break_time = $check_out_limit_min->copy();
        //     if(empty($shift_data['break_time']['start_punch']) || empty($shift_data['break_time']['end_punch'])) {
        //         if(!$shift_data['timetable']['is_without_break']) {
        //             $check_out_limit_break_time->subMinutes($duration_break_time);
        //         }
        //     }

        //     if ($shift_data['total_shifted_overtime'] > 0) {
        //         $total_overtime = $shift_data['total_jl'] * ($shift_data['total_shifted_overtime'] + 1);
        //     } else {
        //         $total_overtime = $shift_data['total_jl'];
        //     }

        //     if ($end_punch->gte($check_out_limit_break_time)) {
        //         if ($total_overtime == $timetable_operational->ot_limit) {
        //             // OPERATIONAL SUDAH SEASUAI
        //             $result['status'] = true;
        //             $result['info'] = 'Absensi sudah sesuai operational';
        //         } else if ($total_overtime < $timetable_operational->ot_limit) {
        //             // LEMBUR LEBIH KECIL,DARI WAKTU LEMBUR OPERATIONAL
        //             $result['status'] = false;
        //             $result['info'] = 'Lembur karyawan tidak sesuai operational';
        //             $result['overtime_adjustment'] = $total_overtime - $timetable_operational->ot_limit;
        //         } else if ($total_overtime > $timetable_operational->ot_limit) {
        //             // LEMBUR LEBIH BESAR,DARI WAKTU LEMBUR OPERATIONAL
        //             $result['status'] = false;
        //             $result['info'] = 'Lembur karyawan tidak sesuai operational';
        //             $result['overtime_adjustment'] = $total_overtime - $timetable_operational->ot_limit;
        //         }
        //     } else {
        //         // OPERATIONAL HADIR, KARYAWAN HADIR TETAPI TIDAK SESUAI DENGAN SHIFT
        //         $result['status'] = false;
        //         $result['info'] = 'Absensi tidak sesuai operational';
        //         $result['overtime_adjustment'] = $lembur - $timetable_operational->ot_limit;
        //     }

        // } else {
        //     $result['status'] = false;

        //     $result['lb_status'] = true;

        //     $result['info'] = 'Operasional diliburkan, tetapi karyawan masuk';
        // }

        // $tso_db = AttendanceTso::where('tso_date', $date_string)
        //     ->where('emp_id', $employee['id'])
        //     ->where('operational_id', $operational->id)
        //     ->where('timetable_id', $shift_data['timetable']['id'])
        //     ->first();

        // if (!empty($tso_db)) {
        //     $result['tso_status'] = true;
        //     $result['status'] = true;
        //     $result['info'] = 'kehadiran karyawan telah Disetujui';
        // }


        // $lb_db = AttendanceLb::where('tso_date', $date_string)
        // ->where('emp_id', $employee['id'])
        // ->where('operational_id', $operational->id)
        // ->where('timetable_id', $shift_data['timetable']['id'])
        // ->first();

        // if (!empty($attendance_lb_db)) {
        //     $result['lb_status'] = true;
        // }

        return $result;
    }

    public function checkLb(
        array $employee, 
        Collection $range_date = null,
        Collection $attendance_data
    ) {
        $result = collect(['status' => false]);

        $date = Carbon::parse($range_date['date']);
        $date_string = $date->format('Y-m-d');

        $lb_db = AttendanceLb::where('lb_date', $date_string)->where('emp_id', $employee['id'])->first();
        if (!empty($lb_db)) {
            $result['status'] = $lb_db->lb_status == 'accept';
        } 

        return $result;
    }
   
    public function calculatedSalary(
        Collection $shift_data,
    ) {
        $result = collect(['total_hk' => 0, 'total_hk_bonus' => 0]);
        
        $date = Carbon::parse($shift_data['date']);
        $date_string = $date->format('Y-m-d');

        $check_in = Carbon::parse($date_string . " " . $shift_data['timetable']['check_in']);
        $check_out = Carbon::parse($date_string . " " . $shift_data['timetable']['check_out']);
        $work_time = $shift_data['timetable']['work_time'];

        $check_in_limit_min = $check_in->copy()->subMinutes($shift_data['timetable']['check_in_min']);
        $check_in_limit_plus = $check_in->copy()->addMinutes($shift_data['timetable']['check_in_plus']);
        $check_out_limit_min = $check_out->copy()->subMinutes($shift_data['timetable']['check_out_min']);
        $check_out_limit_plus = $check_out->copy()->addMinutes($shift_data['timetable']['check_out_plus']);

        $duration_break_time = $shift_data['timetable']['duration_break_time'];
        $check_out_limit_break_time = $check_out_limit_min->copy();
        if($shift_data['timetable']['is_without_break']) {
            $check_out_limit_break_time->subMinutes($duration_break_time);
        }

        $start_punch = Carbon::parse($shift_data['working']['start_punch']);
        $end_punch = Carbon::parse($shift_data['working']['end_punch']);

        // Log::info($check_out);
        // Log::info($shift_data['timetable']);
        // Log::info($check_out_limit_min);
        // Log::info($check_out_limit_break_time);
        // Log::info($end_punch);

        if ($start_punch->between($check_in_limit_min, $check_in_limit_plus)) {
            if($end_punch->gte($check_out_limit_break_time)) {
                // Log::info("jalan ek sini kan");
                $result['total_hk'] += 1;

                if($shift_data['timetable']['enable_extra_pay']) {
                    $result['total_hk_bonus'] += $shift_data['timetable']['extra_pay'];
                }
            } else {
                $working_duration = $start_punch->diffInMinutes($end_punch);
                if($working_duration >= ($work_time / 2)) {
                    $result['total_hk'] += 0.5;

                    if($shift_data['timetable']['enable_extra_pay']) {
                        $result['total_hk_bonus'] += $shift_data['timetable']['extra_pay'];
                    }
                }
            }
        }

        return $result;
    }

    public function calculatedOvertime(
        Collection $shift_data,
    ) {
        $result = collect([
            'total_shifted_overtime' => 0,
            'total_jl' => 0,
            'total_overtime' => 0,
        ]);

        $date = Carbon::parse($shift_data['date']);
        $date_string = $date->format('Y-m-d');

        $check_in = Carbon::parse($date_string . " " . $shift_data['timetable']['check_in']);
        $check_out = Carbon::parse($date_string . " " . $shift_data['timetable']['check_out']);
        $check_out_cross_day = $check_out->copy()->addDays($shift_data['timetable']['cross_day'] ?? 0);

        $check_out_limit_break_time = $check_out_cross_day->copy();
        if($shift_data['timetable']['is_without_break'] 
            && empty($shift_data['break_time']['start_punch']) 
            && empty($shift_data['break_time']['end_punch'])) {
            $check_out_limit_break_time->subMinutes($shift_data['timetable']['duration_break_time']);
        }
        
        $start_punch = Carbon::parse($shift_data['working']['start_punch']);
        $end_punch = Carbon::parse($shift_data['working']['end_punch']);

        // if ($start_punch->lt($check_in)) {
        //     $minutes = $start_punch->diffInMinutes($check_in);
        //     $hours = intdiv($minutes, 60);
        //     $remaining_minutes = $minutes % 60;

        //     $result['total_jl'] += $hours;

        //     if (!empty($shift_data['timetable']['ot_roundone_hr']) && !empty($shift_data['timetable']['ot_roundhalf_hr'])) {
        //         if ($remaining_minutes >= $shift_data['timetable']['ot_roundhalf_hr'] && $remaining_minutes < $shift_data['timetable']['ot_roundone_hr']) {
        //             $result['total_jl'] += 0.5;
        //         } else if ($remaining_minutes >= $shift_data['timetable']['ot_roundone_hr']) {
        //             $result['total_jl'] += 1;
        //         }
        //     }
        // }

        if ($end_punch->gte($check_out_limit_break_time)) {
            $minutes = $end_punch->diffInMinutes($check_out_limit_break_time);
            $hours = intdiv($minutes, 60);
            $remaining_minutes = $minutes % 60;

            $result['total_jl'] += $hours;

            if (!empty($shift_data['timetable']['ot_roundone_hr']) && !empty($shift_data['timetable']['ot_roundhalf_hr'])) {
                if ($remaining_minutes >= $shift_data['timetable']['ot_roundhalf_hr'] && $remaining_minutes < $shift_data['timetable']['ot_roundone_hr']) {
                    $result['total_jl'] += 0.5;
                } else if ($remaining_minutes >= $shift_data['timetable']['ot_roundone_hr']) {
                    $result['total_jl'] += 1;
                }
            }
        }

        if (!empty($shift_data['timetable']['duration_count_one_shift'])) {
            if ($shift_data['timetable']['duration_count_one_shift'] <= $result['total_jl']) {
                $result['total_shifted_overtime'] += floor($result['total_jl'] / ($shift_data['timetable']['duration_count_one_shift']));
                $result['total_jl'] = $result['total_jl'] - ($shift_data['timetable']['duration_count_one_shift'] * $result['total_shifted_overtime']);
            }
        }
        
        if ($shift_data['timetable']['ot_period'] && $shift_data['timetable']['ot_pay']) {
            $result['total_overtime'] += ((($result['total_jl'] ?? 0) * 60) / $shift_data['timetable']['ot_period']) * $shift_data['timetable']['ot_pay'];
        } else {
            $result['total_overtime'] += $result['total_jl'];
        }

        return $result;
    }

    public function calculatedFood(
        Collection $shift_data,
    ) {
        $result = collect(['total_food' => 0]);
        if ($shift_data['total_shifted_overtime'] > 0) {
            $lembur = $shift_data['total_jl'] * ($shift_data['total_shifted_overtime'] + 1);
        } else {
            $lembur = $shift_data['total_jl'];
        }

        if (!empty($shift_data['timetable']['duration_rice_shift']) && $lembur > $shift_data['timetable']['duration_rice_shift']) {
            $result['total_food'] += floor($lembur / $shift_data['timetable']['duration_rice_shift']);
        }

        return $result;
    }

    public function calculatedLoan(
        $start_date,
        $end_date,
        array|null $department = null, 
        array|null $employee = null,
        $final_total,
    ) {
        $result = collect([
            'employee_debt_id' => null,
            'total_loan_paid' => 0,
            'total_loan_balance' => 0,
            'loan_installment_count' => '0/0',
            'total_loan_paid_for_payroll' => 0,
            'total_loan_balance_for_payroll' => 0,
            'loan_installment_count_for_payroll' => '0/0',
        ]);

        $business_id = Session::get('business_id');

        // Ambil semua kasbon aktif karyawan
        $kasbons = EmployeeDebt::with(['employee_debt_pays' => function ($query) {
                $query->select('id', 'employee_debt_id', 'debt_payment_date', 'payment');
            }])
            ->where('business_id', $business_id)
            ->where('paid', 0)
            ->where('emp_id', $employee['id'])
            ->get();

        $firstKasbon = $kasbons->first();
        if (!$firstKasbon) return $result;
        $result['employee_debt_id'] = $firstKasbon->id;

        $latest_td_id = SalaryArchiveTh::whereDate('start_date', '>=', $start_date)
            ->whereDate('end_date', '<=', $end_date)
            ->where('dept_id', $department['id'])
            ->join('salary_archive_tds as td', 'td.salary_archive_th_id', '=', 'salary_archive_ths.id')
            ->orderByDesc('td.created_at')
            ->value('td.id');

        $last_kasbon_payment = SalaryArchiveTdEmp::where('emp_id', $employee['id'])
            ->where('salary_archive_td_id', $latest_td_id)
            ->first();

        // Total hutang dan cicilan
        $totalKasbon = $kasbons->sum('debt');
        $cicilan = $kasbons->sum('instalment');
        $totalPembayaran = $kasbons->flatMap->employee_debt_pays->sum('payment');

        $paid_before_this_period = $totalPembayaran;
        if ($last_kasbon_payment && $last_kasbon_payment->kasbon_pay_value) {
            $paid_before_this_period -= $last_kasbon_payment->kasbon_pay_value;
        }

        // Helper function
        $calculateInstallmentInfo = function ($totalKasbon, $cicilan, $totalPaid) {
            $count = '0/0';
            if ($cicilan > 0) {
                $totalCount = ceil($totalKasbon / $cicilan);
                $current = min(floor($totalPaid / $cicilan) + 1, $totalCount);
                $count = "{$current}/{$totalCount}";
            }
            return $count;
        };

        // === FOR PAYROLL ===
        $toPayForPayroll = min($cicilan, $totalKasbon - $paid_before_this_period, $final_total);
        $result['total_loan_paid_for_payroll'] = $toPayForPayroll;
        $result['total_loan_balance_for_payroll'] = max(0, $totalKasbon - $paid_before_this_period);
        $result['loan_installment_count_for_payroll'] = $calculateInstallmentInfo($totalKasbon, $cicilan, $paid_before_this_period);

        // === TOTAL (TERMASUK CICILAN SEKARANG) ===
        $toPay = min($cicilan, $totalKasbon - $totalPembayaran, $final_total);
        $result['total_loan_paid'] = $toPay;
        $result['total_loan_balance'] = max(0, $totalKasbon - $totalPembayaran - $toPay);
        $result['loan_installment_count'] = $calculateInstallmentInfo($totalKasbon, $cicilan, $totalPembayaran);

        return $result;

    }

    public function calculatedJobBonus(
        array $employee, 
        Carbon $start_date_work_day,
        Carbon $end_date_work_day,
        Carbon $start_date_overtime,
        Carbon $end_date_overtime
    ) {
        $result = collect(['total_job_bonus' => 0]);

        $start_date = min($start_date_work_day, $start_date_overtime)->subDay();
        $end_date = max($end_date_work_day, $end_date_overtime)->addDay();

        $position = Position::where('permanently', '!=', 0)->whereHas('employee_has_position.employee', function ($e) use ($employee) {
            $e->where('emp_id', $employee['id']);
        })->get();

        if($position->isNotEmpty()) {
            $payment_period_diff = $this->calculatePaymentPeriodDiff($employee['payment_period'], $start_date, $end_date);
            $total_extra_pay = $position->sum('extra_pay');

            $result['total_job_bonus'] += $total_extra_pay * $payment_period_diff;
        }

        return $result;
    }
    
    public function getAttendanceDisplayValue(Collection $attendance_data) {
        $result = collect(['text_value' => null]);

        if ($attendance_data['total_jl'] != 0 || $attendance_data['total_hk'] != 0) {
            if ($attendance_data['salary_included'] && $attendance_data['overtime_included']) {
                if($attendance_data['total_hk'] > 0) {
                    if ($attendance_data['total_hk'] == 0.5) {
                        if ($attendance_data['total_jl'] > 0) {
                            $result['text_value'] =  '1/2 (' . $attendance_data['total_jl'] . ')';
                        } else {
                            $result['text_value'] = '1/2';
                        }
                    } else if ($attendance_data['total_hk'] > 0.5) {
                        if ($attendance_data['is_holiday'] && $attendance_data['total_hk'] == 1) {
                            $result['text_value'] = '';
                        } else {
                            $result['text_value'] = (string)($attendance_data['total_jl']);
                        }

                    }

                    if (count($attendance_data['operational_status_list']) != 0 && $attendance_data['operational_status_list'][0]['lb_status']) {
                        $result['text_value'] .= ' LB';
                    }
                    // if ($attendance_data['operational']['lb_status']) {
                    //     $result['text_value'] += ' LB';
                    // }
                } else {
                    $result['text_value'] = 'X';
                }
            } else if ($attendance_data['salary_included']) {
                if ($attendance_data['total_hk'] == 0.5) {
                    if ($attendance_data['total_jl'] <= 0) {
                        $result['text_value'] = '1/2';
                    } else {
                        $result['text_value'] =  '1/2 (' . $attendance_data['total_jl'] . ')';
                    }

                    if (count($attendance_data['operational_status_list']) != 0 && $attendance_data['operational_status_list'][0]['lb_status']) {
                        $result['text_value'] .= ' LB';
                    }
                    // if ($attendance_data['operational']['lb_status']) {
                    //     $result['text_value'] += ' LB';
                    // }
                } else if ($attendance_data['total_hk'] > 0.5) {
                    $result['text_value'] = '';
                } else {
                    $result['text_value'] = 'X';
                }
            } else if ($attendance_data['overtime_included']) {
                $result['text_value'] = (string)($attendance_data['total_jl']);
            }
        } else if ($attendance_data['total_jl'] == 0 && $attendance_data['total_hk'] == 0 && count($attendance_data['operational_status_list']) != 0 && $attendance_data['operational_status_list'][0]['lb_status']) {
            $result['text_value'] = 'LB';
        } else if ($attendance_data['total_jl'] == 0 && $attendance_data['total_hk'] == 0) {
            if($attendance_data['is_holiday']) {
                $result['text_value'] = '';
            } else {
                if ($attendance_data['salary_included'] && $attendance_data['overtime_included']) {
                    $result['text_value'] = 'X';
                } else if ($attendance_data['salary_included']) {
                    $result['text_value'] = 'X';
                } else if ($attendance_data['overtime_included']) {
                    $result['text_value'] = '0';
                }
            }
        } 

        return $result;
    }

    public function getAttendance(
        Carbon $start_date_work_day = null,
        Carbon $end_date_work_day = null,
        Carbon $start_date_overtime = null,
        Carbon $end_date_overtime = null,
        string|null $department_code = null
    ) {
        try {
            if (!$start_date_work_day || !$end_date_work_day || !$start_date_overtime || !$end_date_overtime) {
                throw new \InvalidArgumentException("Semua parameter tanggal harus diisi.");
            }
    
            $business_id = Session::get('business_id');
            $business = Business::where('id', $business_id)->select('id', 'pending_day')->first();

            $list_department = $this->getDepartment();
            $list_position = $this->getPosition();
            $list_device = $this->getDevice();

            $department_bios = null;
            if ($department_code) {
                $department_bios = collect($this->service->get_departments(["dept_code" => $department_code])['data'])->first();
            }

            $list_employee = $this->getEmployee($department_bios, $list_department, $list_position);
            $employee_dept_group = $list_employee->groupBy([fn ($item) => $item['department']['id']]);
            $list_emp_code = $employee_dept_group
                ->flatMap(fn($employees) => collect($employees)->pluck('emp_code'))
                ->all();

            $range_dates = $this->getRangeDate($start_date_work_day->copy(), $end_date_work_day->copy(), $start_date_overtime->copy(), $end_date_overtime->copy());
            $attendances = $this->getMergeAttendance($start_date_work_day->copy(), $end_date_work_day->copy(), $start_date_overtime->copy(), $end_date_overtime->copy(), $list_emp_code, $list_device, $department_bios);
            $attendance_grouping = $this->groupingAttendance($attendances);

            $shifts = $this->getShift($department_bios);
            $operationals = $this->getOperationals($start_date_work_day->copy(), $end_date_work_day->copy(), $start_date_overtime->copy(), $end_date_overtime->copy(), $department_bios);

            $range_dates_filter = $range_dates->filter(fn ($item) => !$item['is_addition_date'])->values();

            $start_date = min($start_date_work_day, $start_date_overtime);
            $end_date = max($end_date_work_day, $end_date_overtime);
            $result = collect([
                'range_dates' => $range_dates_filter,
                'start_date' => $start_date,
                'end_date' => $end_date,
                'start_date_work_day' => $start_date_work_day,
                'end_date_work_day' => $end_date_work_day,
                'start_date_overtime' => $start_date_overtime,
                'end_date_overtime' => $end_date_overtime,
                'departments' => collect()
            ]);

            if ($employee_dept_group->isEmpty()) return $result;

            foreach ($employee_dept_group as $key_dept_id => $employees) {
                $department = $list_department->where('id', $key_dept_id)->first();
                $department_shift = $shifts->where('dept_id', $key_dept_id)->first();
                $department_operational = $operationals->where('dept_id', $key_dept_id)->get();

                $attendance_reports = collect([
                    'department' => $department,
                    'employees' => collect(),
                    'total_hk' => 0,
                    'total_jl' => 0,
                    'total_salary' => 0,
                    'total_overtime' => 0,
                    'total_loan_paid' => 0,
                    'total_loan_balance' => 0,
                    'total_loan_paid_for_payroll' => 0,
                    'total_loan_balance_for_payroll' => 0,
                    'total_tbhn_plus_u_libur' => 0,
                    'total_job_bonus' => 0,
                    'total_food' => 0,
                    'final_total' => 0,
                    'final_total_for_payroll' => 0,
                ]);

                foreach ($employees as $employee) {
                    $attendance_report_employee_data = collect([
                        'employee' => collect($employee),
                        'attendances' => collect(),
                        'total_hk' => 0,
                        'total_jl' => 0,
                        'total_salary' => 0,
                        'total_overtime' => 0,
                        'employee_debt_id' => null,
                        'total_loan_paid_for_payroll' => 0,
                        'total_loan_balance_for_payroll' => 0,
                        'loan_installment_count_for_payroll' => null,
                        'total_loan_paid' => 0,
                        'total_loan_balance' => 0,
                        'loan_installment_count' => null,
                        'total_tbhn_plus_u_libur' => 0,
                        'total_food' => 0,
                        'total_job_bonus' => 0,
                        'final_total' => 0,
                        'final_total_for_payroll' => 0,
                    ]);

                    $attendance_employees = $attendance_grouping->get($employee['emp_code'], collect());
                    $attendance_employee_timetables = $this->attendanceTimetableGrouping($department_shift, $attendance_employees, $range_dates, $employee['emp_code']);

                    $calculated_job_bonus = $this->calculatedJobBonus($employee, $start_date_work_day->copy(), $end_date_work_day->copy(), $start_date_overtime->copy(), $end_date_overtime->copy());
                    $attendance_report_employee_data['total_job_bonus'] += $calculated_job_bonus['total_job_bonus'];

                    $range_date_count = count($range_dates);
                    foreach ($range_dates as $iDate => $range_date) {
                        if ($range_date_count - 1 != $iDate && !$range_date['is_addition_date']) {
                            $date = Carbon::parse($range_date['date']);
                            $date_string = $date->format('Y-m-d');

                            $attendance_dailly_data = collect([
                                'date' => $date_string,
                                'key' => $range_date['key'],
                                'total_hk' => 0,
                                'total_hk_holiday' => 0,
                                'total_day_salary' => 0,
                                'total_jl' => 0,
                                'total_overtime' => 0,
                                'total_tbhn_plus_u_libur' => 0,
                                'total_food' => 0,
                                'total_shifted_overtime' => 0,
                                'text_value' => null,
                                'shifts' => collect(),
                                'working_status_list' => collect([]),
                                'break_time_status_list' => collect([]),
                                'operational_status_list' => collect([]),
                                'is_holiday' => $range_date['holiday']['status'],
                                'salary_included' => $range_date['salary_included'],
                                'overtime_included' => $range_date['overtime_included'],
                            ]);

                            $operational = $department_operational->where('date', $date_string)->first();
                            $timetable_attendances = $attendance_employee_timetables->get($date_string, collect());

                            if(empty($department_shift) || empty($department_shift->shiftdays)) continue;
                            $dayOfWeek = $range_date['holiday']['status']? 0: $date->dayOfWeek; 
                            $shiftday = $department_shift->shiftdays->where('code_day', $dayOfWeek)->first();

                            if($timetable_attendances->isNotEmpty()) {
                                foreach ($timetable_attendances as $key => $attendances) {
                                    $timetable = $shiftday->shiftday_has_timetables->where('timetable.id', $key)->first()->timetable;
    
                                    $shift_data = $this->shiftCheck($employee, $range_date, $attendances, $timetable);
                                    if(!empty($shift_data['timetable'])) {
                                        if ($range_date['overtime_included']) {
                                            $overtime_calculated = $this->calculatedOvertime($shift_data);
                                            $shift_data['total_shifted_overtime'] += $overtime_calculated['total_shifted_overtime'];
                                            $shift_data['total_jl'] += $overtime_calculated['total_jl'];
                                            $shift_data['total_overtime'] += $overtime_calculated['total_overtime'];
                                        }

                                        $food_calculated = $this->calculatedFood($shift_data);
                                        $shift_data['total_food'] += $food_calculated['total_food'];

                                        if ($range_date['salary_included']) {
                                            $salary_calculated = $this->calculatedSalary($shift_data);
                                            $shift_data['total_hk'] += $salary_calculated['total_hk'];
                                            $shift_data['total_hk_bonus'] += $salary_calculated['total_hk_bonus'];
                                        }

                                        $shift_data['total_hk'] += $shift_data['total_shifted_overtime'];

                                        if( isset($employee['daily_salary'])  && !empty($employee['daily_salary']) ) {
                                            $shift_data['total_hk_pay'] += $shift_data['total_hk'] * $employee['daily_salary'];
                                        }

                                        // if($employee['emp_code'] == '250101') {
                                        //     if (!empty($operational)) {
                                        //         $operational_data = $this->operationalCheck($employee, $range_date, $shift_data, $operational);
                                        //         $shift_data['operational']['tso_status'] = $operational_data['tso_status'];
                                        //         $shift_data['operational']['lb_status'] = $operational_data['lb_status'];
                                        //         $shift_data['operational']['overtime_adjustment'] = $operational_data['overtime_adjustment'];
                                        //         $shift_data['operational']['status'] = $operational_data['status'];
                                        //         $shift_data['operational']['info'] = $operational_data['info'];
                                        //     }

                                        //     if ($operational_data['lb_status']) {
                                        //         $attendance_dailly_data['total_tbhn_plus_u_libur'] += $department['sitting_money'] ?? 0;
                                        //     }
                                        //     // $ld_data  = $this->checkLb($employee, $range_date, $attendance_dailly_data);
                                        //     // $attendance_dailly_data['operational']['lb_status'] = $ld_data['status'];
                                        // }
                                    }

                                    if (!empty($operational)) {
                                        $operational_data = $this->operationalCheck($employee, $range_date, $shift_data, $operational);
                                        $shift_data['operational']['tso_status'] = $operational_data['tso_status'];
                                        $shift_data['operational']['lb_status'] = $operational_data['lb_status'];
                                        $shift_data['operational']['overtime_adjustment'] = $operational_data['overtime_adjustment'];
                                        $shift_data['operational']['status'] = $operational_data['status'];
                                        $shift_data['operational']['info'] = $operational_data['info'];

                                        if ($operational_data['lb_status'] && empty($shift_data['timetable']) && !$range_date['holiday']['status']) {
                                            $attendance_dailly_data['total_tbhn_plus_u_libur'] += $department['sitting_money'] ?? 0;
                                        }
                                    } else {
                                        $shift_data['operational']['status'] = true;
                                    }

                                    if(!empty($shift_data['working'])) {
                                        $attendance_dailly_data['working_status_list']->push($shift_data['working']);
                                    } 
                                    if(!empty($shift_data['break_time'])) {
                                        $attendance_dailly_data['break_time_status_list']->push($shift_data['break_time']);
                                    } 
                                    if(!empty($shift_data['operational'])) {
                                        $attendance_dailly_data['operational_status_list']->push($shift_data['operational']);
                                    } 

                                    $attendance_dailly_data['shifts']->push($shift_data);
                                }
                            } else {
                                if (!$range_date['holiday']['status']) {
                                    $attendance_dailly_data['working_status_list']->push([
                                        "info" => 'Tidak ada absensi',
                                        "status" => false,
                                    ]);

                                    // $attendance_dailly_data['break_time_status_list']->push([
                                    //     "info" => 'Tidak ada absensi',
                                    //     "status" => false,
                                    // ]);
                                }
                            }

                            // //   if($employee['emp_code'] == '191201') {
                            //     if (!empty($operational)) {
                            //         $operational_data = $this->operationalCheck($employee, $range_date, $attendance_dailly_data, $operational);
                            //         // $shift_data['operational']['tso_status'] = $operational_data['tso_status'];
                            //         // $shift_data['operational']['lb_status'] = $operational_data['lb_status'];
                            //         // $shift_data['operational']['overtime_adjustment'] = $operational_data['overtime_adjustment'];
                            //         // $shift_data['operational']['status'] = $operational_data['status'];
                            //         // $shift_data['operational']['info'] = $operational_data['info'];
                            //         // $attendance_dailly_data['operational_status_list']->push($shift_data['operational']);
                            //         // Log::info($operational_data);

                            //         if ($operational_data['lb_status']) {
                            //             $attendance_dailly_data['total_tbhn_plus_u_libur'] += $department['sitting_money'] ?? 0;
                            //         }
    
                            //         $attendance_dailly_data['operational_status_list']->push($operational_data);
                            //     }

                            //     // $ld_data  = $this->checkLb($employee, $range_date, $attendance_dailly_data);
                            //     // $attendance_dailly_data['operational']['lb_status'] = $ld_data['status'];

                            //     // if ($attendance_dailly_data['operational']['lb_status']) {
                            //     //     $attendance_dailly_data['total_tbhn_plus_u_libur'] += $department['sitting_money'] ?? 0;
                            //     // }
                            // // }
                                
                            if ($range_date['holiday']['status'] && isset($department['still_paid']) && $department['still_paid']) {
                                $attendance_dailly_data['total_hk_holiday'] += 1;
                                $attendance_dailly_data['total_hk'] += 1;
                            }

                            // Log::info($attendance_dailly_data);

                            foreach ($attendance_dailly_data['shifts'] as $shift) {
                                $attendance_dailly_data['total_hk'] += $shift['total_hk'];
                                $attendance_dailly_data['total_tbhn_plus_u_libur'] += $shift['total_hk_bonus'];
                                $attendance_dailly_data['total_jl'] += $shift['total_jl'];
                                $attendance_dailly_data['total_overtime'] += $shift['total_overtime'];
                                $attendance_dailly_data['total_food'] += $shift['total_food'];
                                $attendance_dailly_data['total_shifted_overtime'] += $shift['total_shifted_overtime'];
                            }

                            $display_value = $this->getAttendanceDisplayValue($attendance_dailly_data);
                            $attendance_dailly_data['text_value'] = $display_value['text_value'];

                            if(isset($employee['daily_salary']) && !empty($employee['daily_salary'])) {
                                $attendance_dailly_data['total_day_salary'] += $attendance_dailly_data['total_hk'] * $employee['daily_salary'];
                            }

                            $attendance_report_employee_data['attendances']->push($attendance_dailly_data);
                        }
                    }

                    foreach ($attendance_report_employee_data['attendances'] as $attendance) {
                        $attendance_report_employee_data['total_hk'] += $attendance['total_hk'];
                        $attendance_report_employee_data['total_tbhn_plus_u_libur'] += $attendance['total_tbhn_plus_u_libur'];
                        $attendance_report_employee_data['total_jl'] += $attendance['total_jl'];
                        $attendance_report_employee_data['total_overtime'] += $attendance['total_overtime'];
                        $attendance_report_employee_data['total_food'] += $attendance['total_food'];
                    }

                    if(isset($employee['daily_salary']) && !empty($employee['daily_salary'])) {
                        $attendance_report_employee_data['total_salary'] += $attendance_report_employee_data['total_hk'] * $employee['daily_salary'];
                    }

                    $attendance_report_employee_data['final_total'] += $attendance_report_employee_data['total_salary'] 
                        + $attendance_report_employee_data['total_overtime'] 
                        + $attendance_report_employee_data['total_tbhn_plus_u_libur'] 
                        + $attendance_report_employee_data['total_job_bonus'];
                    $attendance_report_employee_data['final_total_for_payroll'] = $attendance_report_employee_data['final_total'] ;

                    // kasbon
                    $calculated_loan = $this->calculatedLoan($start_date_work_day, $end_date_work_day, $department_bios, $employee, $attendance_report_employee_data['final_total']);
                    $attendance_report_employee_data['employee_debt_id'] = $calculated_loan['employee_debt_id'];

                    $attendance_report_employee_data['total_loan_paid'] += $calculated_loan['total_loan_paid'];
                    $attendance_report_employee_data['total_loan_balance'] += $calculated_loan['total_loan_balance'];
                    $attendance_report_employee_data['loan_installment_count'] = $calculated_loan['loan_installment_count'];

                    $attendance_report_employee_data['total_loan_paid_for_payroll'] += $calculated_loan['total_loan_paid_for_payroll'];
                    $attendance_report_employee_data['total_loan_balance_for_payroll'] += $calculated_loan['total_loan_balance_for_payroll'];
                    $attendance_report_employee_data['loan_installment_count_for_payroll'] = $calculated_loan['loan_installment_count_for_payroll'];

                    $attendance_report_employee_data['final_total'] -= $attendance_report_employee_data['total_loan_paid'];
                    $attendance_report_employee_data['final_total_for_payroll'] -= $attendance_report_employee_data['total_loan_paid'];

                    $attendance_reports['employees']->push($attendance_report_employee_data);
                }

                foreach ($attendance_reports['employees'] as $employee) {
                    $attendance_reports['total_hk'] += $employee['total_hk'];
                    $attendance_reports['total_jl'] += $employee['total_jl'];
                    $attendance_reports['total_salary'] += $employee['total_salary'];
                    $attendance_reports['total_overtime'] += $employee['total_overtime'];
                    $attendance_reports['total_loan_paid_for_payroll'] += $employee['total_loan_paid_for_payroll'];
                    $attendance_reports['total_loan_balance_for_payroll'] += $employee['total_loan_balance_for_payroll'];
                    $attendance_reports['total_loan_paid'] += $employee['total_loan_paid'];
                    $attendance_reports['total_loan_balance'] += $employee['total_loan_balance'];
                    $attendance_reports['total_tbhn_plus_u_libur'] += $employee['total_tbhn_plus_u_libur'];
                    $attendance_reports['total_job_bonus'] += $employee['total_job_bonus'];
                    $attendance_reports['total_food'] += $employee['total_food'];
                    $attendance_reports['final_total'] += $employee['final_total'];
                    $attendance_reports['final_total_for_payroll'] += $employee['final_total_for_payroll'];
                }

                $result['departments']->push($attendance_reports);
            }


            return $result;
        } catch (\InvalidArgumentException $e) {
            Log::warning("Invalid argument: " . $e->getMessage());
            throw $e;
        } catch (\Exception $e) {
            Log::emergency("File:" . $e->getFile() . " Line:" . $e->getLine() . " Message:" . $e->getMessage());
        }
    }
    

}
