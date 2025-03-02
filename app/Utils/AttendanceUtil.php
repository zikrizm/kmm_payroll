<?php

namespace App\Utils;

use App\Models\AttendanceLb;
use App\Models\AttendanceTso;
use App\Models\User;
use App\Models\Shift;
use App\Models\Holiday;
use App\Models\Business;
use App\Models\Employee;
use App\Models\Position;
use App\Models\Timetable;
use App\Models\Department;
use App\Models\EmployeeTso;
use App\Models\Operational;
use App\Models\Transaction;
use App\Models\EmployeeStatusLb;
use App\Models\SalaryArchiveTh;
use App\Services\Api\ApiServices;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Support\Collection;
use Spatie\Permission\Models\Permission;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Log;

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
        array|null $department
    ) {
        $start_date = min($start_date_work_day, $start_date_overtime)->subDay();
        $end_date = max($end_date_work_day, $end_date_overtime)->addDay();

        $list_attendance_db = Transaction::whereBetween('punch_time', [$start_date, $end_date])->orderBy('punch_time', 'ASC')->get();

        $start_date_new = $start_date->startOfDay();
        $end_date_new = $end_date->addDays(1)->endOfDay();

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
        }else {
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
            'shiftdays.shiftday_has_timetables'  => fn ($query) => $query->select('id', 'shift_day_id', 'timetable_id'),
            'shiftdays.shiftday_has_timetables.timetable' => fn ($query) => $query->select('id', 'name', 'check_in', 'check_out', 'check_in_min', 'check_in_plus', 'check_out_min', 'check_out_plus', 'check_in_plusmn', 'check_out_plusmn', 'cross_day', 'work_time', 'is_without_break', 'ot_roundone_hr', 'ot_roundhalf_hr', 'ot_period', 'ot_pay', 'enable_extra_pay', 'extra_pay', 'duration_count_one_shift', 'duration_ot_limit', 'duration_rice_shift'),
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

    public function getEmployee(array|null $department = null, Collection $list_department = null) {
        $list_department = $list_department ?? collect();
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

        $employees = $list_employee_bios->map(function ($item) use ($list_employee_db, $list_department) {
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
    ) {
        if(empty($department_shift)) return $attendance_employee;
        $attendance_employee_timetable =  collect([]);
        
        $range_date_count = count($range_dates);
        foreach ($range_dates as $iDate => $range_date) {
            if ($iDate >= $range_date_count - 1) continue;

            $date = Carbon::parse($range_date['date']);
            $date_string = $date->format('Y-m-d');

            $shiftday = $department_shift->shiftdays->firstWhere('code_day', $date->dayOfWeek);
            $date_attendances = collect($attendance_employee->get($date_string, collect()));

            if (!$shiftday) continue;

            $attendance_timetable = collect([]);
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

                $date_attendances = $date_attendances->reject(function ($attendance) use (
                    $timetable, $attendance_timetable, $check_in_limit_min, $check_in_limit_plus, $check_out_limit_min, $check_out_limit_plus, $check_out_limit_ot,
                ) {
                    $punch_time = Carbon::parse($attendance['punch_time']);
                    $is_delete = false;

                    if($punch_time->gte($check_in_limit_min) && $punch_time->lte($check_out_limit_ot)) {
                    // if($punch_time->gte($check_in_limit_min) && $punch_time->lte($check_out_limit_plus)) {
                        $existing = $attendance_timetable->get($timetable->id, collect());
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
                            $attendance_timetable->put($timetable->id, $existing);
                        }
                    }

                    return $is_delete;
                });

                if($timetable->cross_day > 0  && $attendance_timetable->get($timetable->id, collect())->count() > 0) {
                    $next_date = $date->copy();
                    
                    for ($i = 0; $i < $timetable->cross_day; $i++) { 
                        $next_date = $date->addDay();
                        $next_date_string = $next_date->format('Y-m-d');

                        $next_date_attendance = $attendance_employee->get($next_date_string, collect());
                        if($next_date_attendance->isEmpty()) continue;

                        $attendance_employee[$next_date_string] = $next_date_attendance->reject(function ($attendance) use (
                            $timetable, $attendance_timetable, $check_in_limit_min, $check_in_limit_plus, $check_out_limit_min, $check_out_limit_plus, $check_out_limit_ot,
                        ) {
                            $punch_time = Carbon::parse($attendance['punch_time']);
                            if($punch_time->lte($check_out_limit_ot)) {
                            // if($punch_time->lte($check_out_limit_plus)) {
                                $existing = $attendance_timetable->get($timetable->id, collect());
                                $existing->push($attendance);
        
                                $attendance_timetable->put($timetable->id, $existing);
                                return true;
                            }
        
                            return false;
                        });
                    }
                }
            }

            $attendance_employee_timetable->put($date_string, $attendance_timetable);
        }
        
        return $attendance_employee_timetable;
    }

  
    public function shiftCheck(
        Collection $range_date = null,
        Collection $attendances,
        Timetable $timetable,
    ) {
        $result = collect([
            'key' => $range_date['key'],
            'date' => $range_date['date'],
            'timetable' => null,
            'working' => collect([
                'start_punch' => null,
                'end_punch' => null,
                'duration' => null,
                'status' => false,
                'info' => null,
            ]),
            'break_time' => collect([
                'start_punch' => null,
                'end_punch' => null,
                'duration' => null,
                'status' => false,
                'info' => null,
            ]),
            'text_value' => null,
            'total_shifted_overtime' => 0,
            'total_invalid_attendance' => 0,
            'total_invalid_break_time' => 0,
            'total_hk' => 0,
            'total_jl' => 0,
            'total_hk_pay' => 0,
            'total_jl_pay' => 0,
            'total_food' => 0,
        ]);

        $date = Carbon::parse($range_date['date']);
        $date_string = $date->format('Y-m-d');

        $first_attendance = $attendances->first();
        $last_attendance = null;
        $first_break_time = null;
        $last_break_time = null;

        $attendance_count = $attendances->count();
        if($attendance_count > 1) {
            $last_attendance = $attendances->last();
        }
        if($attendance_count > 2) {
            $middle_attendances = $attendances->slice(1, -1)->values();
            $middle_attendance_count = $attendances->count();
            
            $first_break_time = $middle_attendances->first();
            if($middle_attendance_count > 1) {
                $last_break_time = $attendances->last();
            }
        } 

        $result['working']['start_punch'] = $first_attendance ? $first_attendance['punch_time'] : null;
        $result['working']['end_punch'] =  $last_attendance ? $last_attendance['punch_time'] : null;
        $result['break_time']['start_punch'] = $first_break_time ? $first_break_time['punch_time'] : null;
        $result['break_time']['end_punch'] = $last_break_time ? $last_break_time['punch_time'] : null;

        $check_in = Carbon::parse($date_string ." ". $timetable->check_in);
        $check_out = Carbon::parse($date_string ." ". $timetable->check_out)->addDays($timetable->cross_day ?? 0);
        $work_time = $timetable->work_time;

        $check_in_limit_min = $check_in->copy()->subMinutes($timetable->check_in_min);
        $check_in_limit_plus = $check_in->copy()->addMinutes($timetable->check_in_plus);
        $check_out_limit_min = $check_out->copy()->subMinutes($timetable->check_out_min);
        $check_out_limit_plus = $check_out->copy()->addMinutes($timetable->check_out_plus);

        $duration_break_time = $timetable->timetable_has_break_time->sum(fn($item) => $item->break_time->duration ?? 0);
        $check_out_limit_break_time = $check_out_limit_min->copy();
        if(!$timetable->is_without_break) {
            $check_out_limit_break_time->subMinutes($duration_break_time);
        }

        if(!empty($result['working']['start_punch']) && !empty($result['working']['end_punch'])) {
            $start_punch = Carbon::parse($result['working']['start_punch']);
            $end_punch = Carbon::parse($result['working']['end_punch']);

            if ($start_punch->between($check_in_limit_min, $check_in_limit_plus)) {
                $result['timetable'] = collect([
                    'name' => $timetable->name,
                    'check_in' => $timetable->check_in,
                    'check_in_min' => $timetable->check_in_min,
                    'check_in_plus' => $timetable->check_in_plus,
                    'check_out' => $timetable->check_out,
                    'check_out_min' => $timetable->check_out_min,
                    'check_out_plus' => $timetable->check_out_plus,
                    'cross_day' => $timetable->cross_day,
                    'work_time' => $timetable->work_time,
                    'ot_roundone_hr' => $timetable->ot_roundone_hr,
                    'ot_roundhalf_hr' => $timetable->ot_roundhalf_hr,
                    'ot_period' => $timetable->ot_period,
                    'ot_pay' => $timetable->ot_pay,
                    'duration_rice_shift' => $timetable->duration_rice_shift,
                    'duration_count_one_shift' => $timetable->duration_count_one_shift,
                    'duration_break_time' => $duration_break_time,
                    'is_without_break' => $timetable->is_without_break,
                ]);

                if($end_punch->gte($check_out_limit_break_time)) {
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
            $result['working']['info'] = 'Tidak ada absensi di waktu kerja';
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
                    $result['break_time']['info'] = 'Istirahat tidak sesuai jadwal';
                } else {
                    $result['break_time']['status'] = true;
                    $result['break_time']['info'] = 'Istirahat sudah sesuai jadwal';
                }
    
            } else if(empty($result['break_time']['start_punch']) && empty($result['break_time']['end_punch'])) {
                if($timetable->is_without_break) {
                    $result['break_time']['status'] = true;
                    $result['break_time']['info'] = 'Di bolehkan untuk tidak istirahat';
                } else {
                    $result['break_time']['status'] = false;
                    $result['break_time']['info'] = 'Tidak ada absensi di waktu istirahat';
                }
            } else if(empty($result['break_time']['start_punch'])) { 
                $result['break_time']['status'] = false;
                $result['break_time']['info'] = 'Tidak ada absensi di waktu awal istirahat';
            } else if(empty($result['break_time']['end_punch'])) { 
                $result['break_time']['status'] = false;
                $result['break_time']['info'] = 'Tidak ada absensi di waktu akhir istirahat';
            }
        } else { 
            if(!empty($result['break_time']['start_punch'])|| !empty($result['break_time']['end_punch'])) {
                $result['break_time']['status'] = false;
                $result['break_time']['info'] = 'Terdapat absensi di waktu istrahat, padahal dijadwal tidak ada';
            }
        }

        if(!$result['working']['status']) {
            $result['total_invalid_attendance'] += 1;
        }
        if(!$result['break_time']['status']) {
            $result['total_invalid_break_time'] += 1;
        }

        return $result;
    }

   
    public function calculatedSalary(
        Collection $shift_data,
    ) {
        $result = collect(['total_hk' => 0]);
        
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
        if(!$shift_data['timetable']['is_without_break']) {
            $check_out_limit_break_time->subMinutes($duration_break_time);
        }

        $start_punch = Carbon::parse($shift_data['working']['start_punch']);
        $end_punch = Carbon::parse($shift_data['working']['end_punch']);

        if ($start_punch->between($check_in_limit_min, $check_in_limit_plus)) {
            if($end_punch->gte($check_out_limit_break_time)) {
                $result['total_hk'] += 1;
            } else {
                $working_duration = $start_punch->diffInMinutes($end_punch);
                if($working_duration >= ($work_time / 2)) {
                    $result['total_hk'] += 0.5;
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
            'total_jl_pay' => 0,
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

        if ($start_punch->lt($check_in)) {
            $minutes = $start_punch->diffInMinutes($check_in);
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
                $result['total_jl'] = $result['total_jl'] - ($shift_data['timetable']['duration_count_one_shift'] * $shift_data['total_shifted_overtime']);
            }
        }
        
        if ($shift_data['timetable']['ot_period'] && $shift_data['timetable']['ot_pay']) {
            $result['total_jl_pay'] += ((($result['total_jl'] ?? 0) * 60) / $shift_data['timetable']['ot_period']) * $shift_data['timetable']['ot_pay'];
        } else {
            $result['total_jl_pay'] += $result['total_jl'];
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

            $department_bios = null;
            if ($department_code) {
                $department_bios = collect($this->service->get_departments(["dept_code" => $department_code])['data'])->first();
            }

            $range_dates = $this->getRangeDate($start_date_work_day->copy(), $end_date_work_day->copy(), $start_date_overtime->copy(), $end_date_overtime->copy());
            $attendances = $this->getMergeAttendance($start_date_work_day->copy(), $end_date_work_day->copy(), $start_date_overtime->copy(), $end_date_overtime->copy(), $department_bios);
            $attendance_grouping = $this->groupingAttendance($attendances);
           
            $list_employee = $this->getEmployee($department_bios, $list_department);
            $employee_dept_group = $list_employee->groupBy([fn ($item) => $item['department']['id']]);

            
            $shifts = $this->getShift($department_bios);
            $operationals = $this->getOperationals($start_date_work_day->copy(), $end_date_work_day->copy(), $start_date_overtime->copy(), $end_date_overtime->copy(), $department_bios);

            $result = collect([
                'range_dates' => $range_dates,
                'working_date' => ['start_date' => $start_date_work_day, 'end_date' => $end_date_work_day],
                'overtime_date' => ['start_date' => $start_date_overtime, 'end_date' => $end_date_overtime],
                'department_reports' => collect()
            ]);

            if ($attendance_grouping->isEmpty()) return $result;
            if ($employee_dept_group->isEmpty()) return $result;

            foreach ($employee_dept_group as $key_dept_id => $employees) {
                $department = $list_department->where('id', $key_dept_id)->first();
                $department_shift = $shifts->where('dept_id', $key_dept_id)->first();
                $operational = $operationals->where('dept_id', $key_dept_id)->first();

                $attendance_reports = collect([
                    'department' => $department,
                    'employee_attendances' => collect(),
                ]);

                foreach ($employees as $employee) {
                    $attendance_report_employee_data = collect([
                        'employee' => collect($employee),
                        'attendances' => collect(),
                        'total_hk' => 0,
                        'total_jl' => 0,
                        'total_salary' => 0,
                        'total_overtime' => 0,
                        'total_loan_deduction' => 0,
                        'total_loan_balance' => 0,
                        'total_rbhn_plus_u_libur' => 0,
                        'total_food' => 0,
                        'total_job_bonus' => 0,
                        'total_invalid_attendance' => 0,
                        'total_invalid_break_time' => 0,
                        'grand_total' => 0,
                    ]);

                    $attendance_employees = $attendance_grouping->get($employee['emp_code'], collect());
                    $attendance_employee_timetables = $this->attendanceTimetableGrouping($department_shift, $attendance_employees, $range_dates);

                    Log::info($attendance_employee_timetables);

                    $range_date_count = count($range_dates);
                    foreach ($range_dates as $iDate => $range_date) {
                        if ($range_date_count - 1 != $iDate && !$range_date['is_addition_date']) {
                            $date = Carbon::parse($range_date['date']);
                            $date_string = $date->format('Y-m-d');

                            $attendance_dailly_data = collect([
                                'date' => $date_string,
                                'key' => $range_date['key'],
                                'total_hk' => 0,
                                'total_jl' => 0,
                                'total_jl_pay' => 0,
                                'total_food' => 0,
                                'total_shifted_overtime' => 0,
                                'text_value' => null,
                                'shifts' => collect(),
                                'total_invalid_attendance' => 0,
                                'total_invalid_break_time' => 0,
                                'is_holiday' => $range_date['holiday']['status'],
                                'salary_included' => $range_date['salary_included'],
                                'overtime_included' => $range_date['overtime_included'],
                            ]);

                            $date_attendances = $attendance_employees->get($date_string,  collect());
                            $timetable_attendances = $attendance_employee_timetables->get($date_string, collect());

                            if(!empty($department_shift->shiftdays)) {
                                $shiftday = $department_shift->shiftdays->where('code_day', $date->dayOfWeek)->first();
                                foreach ($timetable_attendances as $key => $attendances) {
                                    $timetable = $shiftday->shiftday_has_timetables->where('timetable.id', $key)->first()->timetable;
    
                                    if($attendances->isNotEmpty()) {
                                        $shift_data = $this->shiftCheck($range_date, $attendances, $timetable);
                                        if(!empty($shift_data['timetable'])) {
                                            if ($range_date['overtime_included']) {
                                                $overtime_calculated = $this->calculatedOvertime($shift_data);
                                                $shift_data['total_shifted_overtime'] += $overtime_calculated['total_shifted_overtime'];
                                                $shift_data['total_jl'] += $overtime_calculated['total_jl'];
                                                $shift_data['total_jl_pay'] += $overtime_calculated['total_jl_pay'];
                                            }
    
                                            $food_calculated = $this->calculatedFood($shift_data);
                                            $shift_data['total_food'] += $food_calculated['total_food'];
    
                                            if ($range_date['salary_included']) {
                                                $salary_calculated = $this->calculatedSalary($shift_data);
                                                $shift_data['total_hk'] += $salary_calculated['total_hk'];
                                            }
    
                                            $shift_data['total_hk'] += $shift_data['total_shifted_overtime'];
                                        }
    
                                        $attendance_dailly_data['shifts']->push($shift_data);
                                    }
                                }
    
                                if ($range_date['holiday']['status'] && isset($department['still_paid']) && $department['still_paid']) {
                                    $attendance_dailly_data['total_hk'] += 1;
                                }
    
                                foreach ($attendance_dailly_data['shifts'] as $shift) {
                                    $attendance_dailly_data['total_hk'] += $shift['total_hk'];
                                    $attendance_dailly_data['total_jl'] += $shift['total_jl'];
                                    $attendance_dailly_data['total_jl_pay'] += $shift['total_jl_pay'];
                                    $attendance_dailly_data['total_food'] += $shift['total_food'];
                                    $attendance_dailly_data['total_invalid_attendance'] += $shift['total_invalid_attendance'];
                                    $attendance_dailly_data['total_invalid_break_time'] += $shift['total_invalid_break_time'];
                                }
                            }

                            $attendance_report_employee_data['attendances']->push($attendance_dailly_data);
                        }
                    }

                    foreach ($attendance_report_employee_data['attendances'] as $attendance) {
                        $attendance_report_employee_data['total_hk'] += $attendance['total_hk'];
                        $attendance_report_employee_data['total_jl'] += $attendance['total_jl'];
                        $attendance_report_employee_data['total_overtime'] += $attendance['total_jl_pay'];
                        $attendance_report_employee_data['total_food'] += $attendance['total_food'];
                        $attendance_report_employee_data['total_invalid_attendance'] += $attendance['total_invalid_attendance'];
                        $attendance_report_employee_data['total_invalid_break_time'] += $attendance['total_invalid_break_time'];
                    }

                    if(isset($employee['daily_salary']) && !empty($employee['daily_salary'])) {
                        $attendance_report_employee_data['total_salary'] += $attendance_report_employee_data['total_hk'] * $employee['daily_salary'];
                    }

                    $attendance_reports['employee_attendances']->push($attendance_report_employee_data);
                }

               
                $result['department_reports']->push($attendance_reports);
            }

            Log::info($result);

            return $result;
        } catch (\InvalidArgumentException $e) {
            Log::warning("Invalid argument: " . $e->getMessage());
            throw $e;
        } catch (\Exception $e) {
            Log::emergency("File:" . $e->getFile() . " Line:" . $e->getLine() . " Message:" . $e->getMessage());
        }
    }
    

}
