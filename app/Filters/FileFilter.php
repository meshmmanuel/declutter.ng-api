<?php

namespace App\Filters;

use App\Modules\EloquentFilter\QueryFilters;
use Illuminate\Database\Query\Builder;
use Illuminate\Http\Request;

class FileFilter extends QueryFilters
{
    protected $request;

    public function __construct(Request $request)
    {
        $this->request = $request;
        parent::__construct($request);
    }

    public function name($term)
    {
        return $this->builder->where('files.name', 'LIKE', ucwords("%$term%"))
            ->orWhere('files.name', 'LIKE', strtolower("%$term%"))
            ->orWhere('files.name', 'LIKE', strtoupper("%$term%"));
    }
}
