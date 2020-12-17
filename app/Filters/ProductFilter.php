<?php

namespace App\Filters;

use App\Modules\EloquentFilter\QueryFilters;
use Illuminate\Database\Query\Builder;
use Illuminate\Http\Request;

class ProductFilter extends QueryFilters
{
    protected $request;

    public function __construct(Request $request)
    {
        $this->request = $request;
        parent::__construct($request);
    }

    public function name($term)
    {
        return $this->builder->where('products.name', 'LIKE', ucwords("%$term%"))
            ->orWhere('products.name', 'LIKE', strtolower("%$term%"))
            ->orWhere('products.name', 'LIKE', strtoupper("%$term%"));
    }
}
