<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;

class BoundaryDelimitation extends Model
{
    use LogsActivity;
    protected static $logAttributes = ['*'];
}
