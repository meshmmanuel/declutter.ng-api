<?php

namespace App\Models;

use App\Modules\EloquentFilter\Filterable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Defect extends Model
{
    use HasFactory, SoftDeletes, Filterable;

    protected $fillable = ['description', 'product_id'];

    protected $with = ['files'];

    // Relationships
    public function files()
    {
        return $this->belongsToMany(File::class, 'defect_file');
    }
}
