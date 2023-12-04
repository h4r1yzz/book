<?php

namespace App\Models;

use MongoDB\Laravel\Eloquent\Model;

/**
 * Class Events
 *
 * The Events model represents contests within the application and interacts with the MongoDB database.
 *
 * @package App\Models
 */

class Events extends Model
{
    /**
     * The MongoDB connection for the model.
     *
     * @var string
     */
    protected $connection = 'mongodb';

    /**
     * The name of the collection associated with the model.
     *
     * @var string
     */
    protected $collection = 'live_streaming_contest';

    /**
     * The primary key associated with the model.
     *
     * @var string
     */
    protected $primaryKey = '_id';
    
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'contest_id',
        'title',
        'title_locale',
        'contest_start_at',
        'contest_end_at',
        'contest_display_start_at',
        'contest_display_end_at',
        'contest_type',   
        'sorting',    
        'is_countdown_required',    
        'auto_enrollment',
        'gifts_bounded',
        'gifts_bounded.*.id',
        'gifts_bounded.*.pricing_id',
        'graphics',
        'graphics.*.type',
        'graphics.*.asset_url',
        'graphics.*.reference',
        'graphics.*.url',  
        'graphics.*.text',
        'graphics.*.targeted_audiences',
        'graphics.*.is_countdown_required',
        'graphics.*.countdown_font_color',
        'graphics.*.title_font_color',
        'graphics.*.cta',
        'graphics.*.tier',
        'graphics.*.template',
        'tier_system',
    ];
}
