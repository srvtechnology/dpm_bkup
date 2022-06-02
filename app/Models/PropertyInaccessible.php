<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;

class PropertyInaccessible extends Model
{
    use LogsActivity;
    protected $fillable = [
        'label', 'value', 'is_active'
    ];
    protected static $logAttributes = ['*'];
    protected static $logOnlyDirty = true;
    protected static $logName = 'property-inaccessibles';
    protected $table = 'property_inaccessibles';

    public function properties()
    {
        return $this->belongsToMany(Property::class, 'property_property_inaccessibles');
    }
}
