<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Asset extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'assets';


    /**
     * The primary key associated with the table.
     *
     * @var string
     */
    protected $primaryKey = 'id';

    public function category()
    {
        return $this->hasOne(Category::class, 'id', 'category_id');
    }

    public function location()
    {
        return $this->location_asset()->with('study_program');
    }

    public function location_asset()
    {
        return $this->hasOne(Location::class, 'id', 'location_id');
    }

    public function user()
    {
        return $this->hasOne(User::class, 'id', 'created_by');
    }

    public function asset_improvements()
    {
        return $this->hasMany(AssetImprovement::class, 'asset_id', 'id')->orderBy('created_at', 'desc');
    }

    public function asset_improvements_total_price()
    {
        return $this->asset_improvements()->sum('improvement_price');
    }
}
