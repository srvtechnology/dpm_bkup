<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;

class PropertyCategory extends Model
{
    use LogsActivity;
    protected $fillable = [
        'label', 'value', 'is_active',
    ];

    protected static $logAttributes = ['*'];
    protected static $logOnlyDirty = true;
    protected static $logName = 'property-categories';

    protected $table = 'property_categories';

    public function assessments()
    {
        return $this->hasMany('App\Models\Property', 'property_categories_id');
    }

    public function categories()
    {
        return $this->belongsToMany(Property::class);
    }
}
