<?php

namespace App\Models;

use Folklore\Image\Facades\Image;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Spatie\Activitylog\Traits\LogsActivity;

class PropertyAssessmentDetail extends Model
{
    use LogsActivity;
    protected $table = 'property_assessment_details';

    protected $currentYearTotalDue;
    protected $currentYearTotalPayment;
    protected $totalPaid;
    protected $pastPayableDue;

    protected static $logAttributes = ['*', 'valuesAdded'];
    protected static $logOnlyDirty = true;
    protected static $logName = 'property-assessment';

    protected $fillable = [
        'property_types',
        'property_wall_materials',
        'roofs_materials',
        'property_dimension',
        'value_added',
        'property_rate_without_gst',
        'property_gst',
        'property_rate_with_gst',
        'property_image',
        'property_use',
        'zone',
        'no_of_mast',
        'no_of_shop',
        'no_of_compound_house',
        'compound_name',
        'assessment_images_2',
        'assessment_images_1',
        'gated_community',
        'demand_note_delivered_at',
        'demand_note_recipient_name',
        'demand_note_recipient_mobile',
        'demand_note_recipient_photo'
    ];

    protected $appends = [
        'original_one',
        'small_preview_one',
        'large_preview_one',
        'original_two',
        'small_preview_two',
        'large_preview_two',
        'swimming_pool',
        'is_demand_note_delivered',
        'demand_note_recipient_photo_url',
        'current_year_assessment_amount',
        'current_installment_due_amount',
        'arrear_due',
        'penalty',
        'amount_paid',
        'balance',
        'assessment_year'
    ];

    protected $dates = [
        'last_printed_at',
        'demand_note_delivered_at'
    ];

    public function getIsDemandNoteDeliveredAttribute()
    {
        return !is_null($this->demand_note_delivered_at);
    }

    public function getDemandNoteRecipientPhotoUrlAttribute()
    {
        return $this->attributes['demand_note_recipient_photo'] ? url(Image::url($this->attributes['demand_note_recipient_photo'], 500, 500)) : null;
    }


    public function categories()
    {
        return $this->belongsToMany(
            PropertyCategory::class,
            'property_property_category',
            'assessment_id',
            'property_category_id'
        )->withPivot(['property_id']);
    }

    public function types()
    {
        return $this->belongsToMany(
            PropertyType::class,
            'property_property_type',
            'assessment_id',
            'property_type_id'
        )->withPivot(['property_id']);
    }

    public function typesTotal()
    {
        return $this->belongsToMany(
            PropertyType::class,
            'property_property_types_total',
            'assessment_id',
            'property_type_id'
        )->withPivot(['property_id']);
    }

    public function valuesAdded()
    {
        return $this->belongsToMany(
            PropertyValueAdded::class,
            'property_property_value_added',
            'assessment_id',
            'property_value_added_id'
        )->withPivot(['property_id']);
    }

    public function getSwimmingPoolAttribute()
    {
        return $this->swimming_id;
    }

    public function property()
    {
        return $this->belongsTo('App\Models\Property');
    }

    public function propertyType()
    {
        return $this->belongsTo(PropertyType::class, 'property_types_id');
    }

    public function propertyCategory()
    {
        return $this->belongsTo(PropertyCategory::class, 'property_categories_id');
    }

    public function dimension()
    {
        return $this->belongsTo(PropertyDimension::class, 'property_dimension');
    }

    public function wallMaterial()
    {
        return $this->belongsTo(PropertyWallMaterials::class, 'property_wall_materials');
    }

    public function roofMaterial()
    {
        return $this->belongsTo(PropertyRoofsMaterials::class, 'roofs_materials');
    }

    public function zone()
    {
        return $this->belongsTo(PropertyZones::class, 'zone');
    }

    public function propertyUse()
    {
        return $this->belongsTo(PropertyUse::class, 'property_use');
    }

    public function swimming()
    {
        return $this->belongsTo(Swimming::class, 'swimming_id');
    }

    // for assessment_image_1

    public function getOriginalOneAttribute()
    {
        return $this->hasImageOne() ? url(Image::url($this->assessment_images_1)) : null;
    }

    public function getSmallPreviewOneAttribute()
    {
        return $this->getImageOneUrl(100, 100);
    }

    public function getLargePreviewOneAttribute()
    {
        return $this->getImageOneUrl(800, 800);
    }

    public function hasImageOne()
    {
        return $this->assessment_images_1 && file_exists($this->getImageOne());
    }

    public function getImageOne()
    {
        return storage_path('app/' . $this->assessment_images_1);
    }

    public function getImageOneUrl($width = 100, $height = 100)
    {
        return $this->hasImageOne() ? url(Image::url($this->assessment_images_1, $width, $height, ['crop'])) : '';
    }

    public function getAdminImageOneUrl($width = 100, $height = 100)
    {
        return $this->hasImageOne() ? url(Image::url($this->assessment_images_1, $width, $height, ['crop'])) : url(Image::url(('/images/No_Image_Available.jpg'), $width, $height, ['crop']));
    }

    //assessment_image_2

    public function getOriginalTwoAttribute()
    {
        return $this->hasImageTwo() ? url(Image::url($this->assessment_images_2)) : null;
    }

    public function getSmallPreviewTwoAttribute()
    {
        return $this->getImageTwoUrl(100, 100);
    }

    public function getLargePreviewTwoAttribute()
    {
        return $this->getImageTwoUrl(800, 800);
    }

    public function hasImageTwo()
    {
        return $this->assessment_images_2 && file_exists($this->getImageTwo());
    }

    public function getImageTwo()
    {
        return storage_path('app/' . $this->assessment_images_2);
    }

    public function getRecipientPhoto($width = 100, $height = 100, $options = ['crop'])
    {
        return Storage::has($this->demand_note_recipient_photo) ? url(Image::url($this->demand_note_recipient_photo, $width, $height, $options)) : asset('images/person-placer.png');
    }

    public function getImageTwoUrl($width = 100, $height = 100)
    {
        return $this->hasImageTwo() ? url(Image::url($this->assessment_images_2, $width, $height, ['crop'])) : '';
    }

    public function getAdminImageTwoUrl($width = 100, $height = 100)
    {
        return $this->hasImageTwo() ? url(Image::url($this->assessment_images_2, $width, $height, ['crop'])) : url(Image::url(('/images/No_Image_Available.jpg'), $width, $height, ['crop']));
    }

    public function getCurrentYearAssessmentAmount()
    {
        return $this->property_rate_without_gst;
    }

    public function getPastPayableDue()
    {
        if ($this->pastPayableDue !== null) {
            return $this->pastPayableDue;
        }

        $pastTotalDue = PropertyAssessmentDetail::where('property_id', $this->property_id)
            ->where('created_at', '<', $this->created_at->startOfYear())
            ->sum('property_rate_without_gst');

        $pastTotalPayments = PropertyPayment::where('property_id', $this->property_id)
            ->where('created_at', '<', $this->created_at->startOfYear())
            ->sum('amount');

        $this->pastPayableDue = round($pastTotalDue) - round($pastTotalPayments);

        if($this->created_at->format('Y') == 2021){
            return $this->pastPayableDue+$pastTotalPayments+round($this->getPenalty()/2);
        }

        return $this->pastPayableDue;
    }

    public function getCurrentQuarter()
    {
        return getQuarter($this->created_at);
    }

    public function payments()
    {
        return $this->hasMany(PropertyPayment::class, 'property_id', 'property_id');
    }

    public function getPenalty()
    {
        
        return max($this->getPastPayableDue() * .25, 0);
        
    }

    public function getCurrentYearTotalPayment()
    {
        if ($this->currentYearTotalPayment !== null) {
            return $this->currentYearTotalPayment;
        }

        return $this->currentYearTotalPayment = $this->payments()->whereYear('created_at', $this->created_at->year)->sum('amount');
    }

    public function getCurrentYearTotalDue()
    {
        if ($this->created_at->format('Y') == 2021) {
            return round($this->getTotalPayable()) +round($this->getPenalty()/2) - round($this->getCurrentYearTotalPayment());
        }
        return round($this->getTotalPayable()) - round($this->getCurrentYearTotalPayment());

        

    }

    public function getTotalPayable()
    {
        return $this->getPastPayableDue() + $this->getCurrentYearAssessmentAmount() + $this->getPenalty();
    }

    public function getTotalPaid()
    {
        if ($this->totalPaid !== null) {
            return $this->totalPaid;
        }

        $this->totalPaid = $this->payments()->sum('amount');
        return $this->totalPaid;
    }

    public function getEachInstallmentAmount()
    {
        return $this->getTotalPayable() / 4;
    }

    public function getQuarter($date = null)
    {
        return getQuarter($this->created_at, $date);
    }

    public function getCurrentInstallmentDueAmount($date = null)
    {
        $totalPaidThisYear = $this->getCurrentYearTotalPayment();
        $currentQuarter = $this->getQuarter($date);

        $paymentShouldPaid = $currentQuarter * $this->getEachInstallmentAmount();

        return max(0, $paymentShouldPaid - $totalPaidThisYear);
    }

    public function setPrinted()
    {
        $this->forceFill([
            'last_printed_at' => $this->freshTimestamp()
        ])->save();
    }

    public function isPrinted()
    {
        return !is_null($this->last_printed_at);
    }

    public function isDelivered()
    {
        return !is_null($this->demand_note_delivered_at);
    }

    public function installmentDates($key = null)
    {
        $year = $this->created_at->format('Y');

        $dates = [
            "0" => "31-03-{$year}",
            "1" => "30-06-{$year}",
            "2" => "30-09-{$year}",
            "3" => "31-12-{$year}",
        ];

        return (isset($dates[$key])) ? $dates[$key] : $dates;
    }

    public function paymentCompleted()
    {
        return $this->getTotalPayable() <= $this->getCurrentYearTotalPayment();
    }

    public function getCurrentYearAssessmentAmountAttribute()
    {
        return $this->getCurrentYearAssessmentAmount();
    }
    public function getArrearDueAttribute()
    {
        return $this->getPastPayableDue();
    }
    public function getPenaltyAttribute()
    {
        return $this->getPenalty();
    }
    public function getAmountPaidAttribute()
    {
        return $this->getCurrentYearTotalPayment();
    }
    public function getBalanceAttribute()
    {
        return $this->getCurrentYearTotalDue();
    }
    public function getAssessmentYearAttribute()
    {
        return $this->created_at->format('Y');
    }
    public function getCurrentInstallmentDueAmountAttribute()
    {
        return $this->getCurrentInstallmentDueAmount();
    }
    public function getImageAnyUrl($width = 100, $height = 100, $resize = false)
    {
        if($this->hasImageOne()){
            return url(Image::url($this->assessment_images_1, $width, $height, $resize ? [] : ['crop']));
        }elseif($this->hasImageTwo()){
            return url(Image::url($this->assessment_images_2, $width, $height, $resize ? [] : ['crop']));
        }else{
            return url(Image::url("District/council_logo.jpg", $width, $height, ['crop']));
        }
    }
}
