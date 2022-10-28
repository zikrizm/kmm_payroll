<?php

namespace App\Models;

use Illuminate\Support\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Session;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ActivityLog extends Model
{
    use HasFactory;

    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = ['id'];

    public static function created_activity($action, $description)
    {
        $business_id = Session::get('business_id');
        ActivityLog::create([
            'business_id' => $business_id,
            'date' => Carbon::now()->toDateTimeString(),
            'action' => $action,
            'description' => $description,
            'ip' => request()->ip(),
            'user_id' => auth()->user()->id,
        ]);
    }
    
    /**
     * Get the business that owns the user.
     */
    public function business()
    {
        return $this->belongsTo(Business::class);
    }
    /**
     * Get the user.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
