<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;

class PropertyPayment extends Model
{
    use LogsActivity;
    protected $fillable = [
        'assessment',
        'amount',
        'payment_type',
        'cheque_number',
        'payee_name',
        'payment_id',
        'admin_user_id',
        'balance',
        'penalty',
        'total'
    ];

    protected static $logAttributes = ['*'];
    protected static $logOnlyDirty = true;
    protected static $logName = 'property-payment';

    public function admin()
    {
        return $this->belongsTo(AdminUser::class, 'admin_user_id')->withDefault([
            'id' => 1
        ]);
    }

    public static function installmentDates($key = null)
    {
        $dates = [
            '0' => '31-03-2019',
            '1' => '30-06-2019',
            '2' => '30-09-2019',
            '3' => '31-12-2019',
        ];

        return (isset($dates[$key])) ? $dates[$key] : $dates;
    }

    public static function numberToWord($number = null)
    {
        $words = [
            '1' => 'First',
            '2' => 'Second',
            '3' => 'Third',
            '4' => 'Fourth'
        ];

        return (isset($words[$number])) ? $words[$number] : $words;
    }

    public function property()
    {
        return $this->belongsTo(Property::class);
    }

    public function totalAssessment()
    {
        return number_format($this->assessment);
    }

    public function amountPaid()
    {
        return number_format($this->amount);
    }

    public function dueAmount()
    {
        return number_format($this->balance + $this->amount);
    }

    public function getBalance()
    {
        return number_format($this->balance);
    }
}
