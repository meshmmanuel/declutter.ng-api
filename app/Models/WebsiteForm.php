<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class WebsiteForm extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = ['id', 'name', 'state', 'email', 'phone', 'items_interested', 'items_to_sell'];
}
