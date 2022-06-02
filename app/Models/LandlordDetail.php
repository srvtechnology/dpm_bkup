<?php

namespace App\Models;

use Folklore\Image\Facades\Image;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Spatie\Activitylog\Traits\LogsActivity;

class LandlordDetail extends Model
{
    use Notifiable;
    use LogsActivity;

    //
    protected $fillable = [
        'first_name', 'middle_name', 'surname', 'email', 'sex', 'street_number', 'street_name', 'image', 'id_number', 'id_type', 'tin', 'ward', 'constituency', 'section', 'chiefdom', 'district', 'province', 'postcode', 'mobile_1', 'mobile_2'
    ];

    protected static $logAttributes = ['*'];
    protected static $logOnlyDirty = true;
    protected static $logName = 'property-landlord';

    protected $appends = ['original', 'small_preview', 'large_preview', 'phone_number'];

    public function property()
    {
        return $this->belongsTo('App\Models\Property');
    }

    public function canReceiveAlphanumericSender()
    {
        return true;
    }


    public function getName()
    {
        return $this->first_name . ' ' . $this->middle_name . ' ' . $this->surname;
    }

    public function getPhoneNumberAttribute()
    {
        return $this->mobile_1;
    }

    public function getSmallPreviewAttribute()
    {
        return $this->getImageUrl(100, 100);
    }

    public function getLargePreviewAttribute()
    {
        return $this->getImageUrl(800, 800);
    }

    public function getOriginalAttribute()
    {
        return $this->hasImage() ? url(Image::url($this->image)) :  null;
    }

    public function hasImage()
    {
        return $this->image && file_exists($this->getImage());
    }

    public function getImage()
    {
        return storage_path('app/' . $this->image);
    }

    public function getImageUrl($width = 100, $height = 100)
    {
        return $this->hasImage() ? url(Image::url($this->image, $width, $height, ['crop'])) :  null;
    }
}
