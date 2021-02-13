<?php

namespace App\Models;

use App\Modules\EloquentFilter\Filterable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Product extends Model
{
    use HasFactory, SoftDeletes, Filterable;

    protected $fillable = ['name', 'description', 'selling_price', 'user_id', 'release_date', 'status', 'product_status', 'customer_name', 'customer_phone'];
    protected $with = ['files', 'defect'];

    // Relationships
    public function files()
    {
        return $this->belongsToMany(File::class, 'file_product');
    }

    public function defect()
    {
        return $this->hasOne(Defect::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
