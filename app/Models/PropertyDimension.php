<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;

class PropertyDimension extends Model
{
    use LogsActivity;
    protected $fillable = [
        'label', 'value',
    ];
    protected $table = 'property_dimension';

    protected static $logAttributes = ['*'];
    protected static $logOnlyDirty = true;
    protected static $logName = 'property-dimension';
}
