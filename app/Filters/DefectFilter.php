<?php

namespace App\Filters;

use App\Modules\EloquentFilter\QueryFilters;
use Illuminate\Database\Query\Builder;
use Illuminate\Http\Request;

class DefectFilter extends QueryFilters
{
    protected $request;

    public function __construct(Request $request)
    {
        $this->request = $request;
        parent::__construct($request);
    }

    public function name($term)
    {
        return $this->builder->where('defects.name', 'LIKE', ucwords("%$term%"))
            ->orWhere('defects.name', 'LIKE', strtolower("%$term%"))
            ->orWhere('defects.name', 'LIKE', strtoupper("%$term%"));
    }
}
