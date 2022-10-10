<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;

class NIK implements Rule
{
    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        return preg_match('/^(1[1-9]|21|[37][1-6]|5[1-3]|6[1-5]|[89][10])\d{2}\s?\d{2}([04][1-9]|[1256][0-9]|[37][01])\s?(0[1-9]|1[0-2])\d{2}\s?\d{4}$/', $value);
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return 'The Invalid NIK employee.';
    }
}
