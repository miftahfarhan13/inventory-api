<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AssetImprovement extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'asset_improvements';


    /**
     * The primary key associated with the table.
     *
     * @var string
     */
    protected $primaryKey = 'id';

    public function asset_query()
    {
        return $this->hasOne(Asset::class, 'id', 'asset_id');
    }

    public function asset()
    {
        return $this->asset_query()->with('location');
    }

    public function location_asset()
    {
        return $this->hasOne(Location::class, 'id', 'location_id');
    }

    public function location()
    {
        return $this->location_asset()->with('study_program');
    }

    public function user()
    {
        return $this->hasOne(User::class, 'id', 'created_by');
    }

    public function approved_user()
    {
        return $this->hasOne(User::class, 'id', 'approved_by');
    }
}
