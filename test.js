const dummy = {
    range_dates: [],
    working_date: {
        start_time: null,
        end_time: null,
    },
    overtime_date: {
        start_date: null,
        end_date: null,
    },
    departments: [{
        department: null,
        employee_attendances: [{
            employee: null,
            attendances: [{
                date: null,
                total_hk: null,
                total_jl: null,
                total_food: null,
                shifts: [{
                    timetable: null,
                    working: {
                        start_punch: null,
                        end_punch: null,
                        duration: null,
                        status: null,
                        info: null,
                    },
                    break_time: {
                        start_punch: null,
                        end_punch: null,
                        duration: null,
                        status: null,
                        info: null,
                    },
                    text_value: null,
                    hk: null,
                    jl: null,
                    food: null,
                }],
                is_holiday: null,
                salary_included: null,
                overtime_included: null,
            }],
            total_hk: null,
            total_jl: null,
            total_salary: null,
            total_loan_deduction: null,
            total_loan_balance: null,
            total_overtime: null,
            total_rbhn_plus_u_libur: null,
            total_food: null,
            total_job_bonus: null,
            grand_total: null
        }],
        final_total: null
    }],
    
};