<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReportCampaign extends Model
{
    use HasFactory;
     /**
     * fillable
     *
     * @var array
     */
    protected $fillable = [
        'image', 'title','description','current_donation','current_date','campaign_id'
    ];


    /**
     * campaign
     
     * @return void
     */
    public function campaigns()
    {
        return $this->belongsTo(Campaign::class,'campaign_id');
    }
    /**
     * getImageAttribute
     *
     * @param  mixed $image
     * @return void
     */
    public function getImageAttribute($image)
    {
        return asset('storage/reportcampaigns/'.$image);
    }
   
}
