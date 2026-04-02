<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Backpack\CRUD\app\Models\Traits\CrudTrait;

class EmployeeAttendance extends Model
{
    use CrudTrait;
    //
    protected $table = 'employee_attendance';
    protected $guarded = ['id'];

    protected $casts = [
        'date' => 'date',
        'check_in_time' => 'datetime',
        'check_out_time' => 'datetime',
    ];

    public function employee()
    {
        return $this->belongsTo(User::class, 'employee_id');
    }

    // Accessors for compatibility with controller/view
    public function getClockInAttribute()
    {
        return $this->check_in_time;
    }

    public function getClockOutAttribute()
    {
        return $this->check_out_time;
    }
}
