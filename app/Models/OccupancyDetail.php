<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;

class OccupancyDetail extends Model
{
    use LogsActivity;
    protected $fillable = [
        'tenant_first_name', 'middle_name', 'surname', 'mobile_1', 'mobile_2'
    ];

    protected static $logAttributes = ['*'];
    protected static $logOnlyDirty = true;
    protected static $logName = 'property-occupancy-detail';

    public function property()
    {
        return $this->belongsTo(Property::class);
    }

    protected $casts = [
        'owned_tenancy' => 'boolean',
        'rented' => 'boolean',
        'unoccupied_house' => 'boolean'
    ];
}
