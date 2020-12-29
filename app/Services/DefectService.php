<?php

namespace App\Services;

use App\Filters\DefectFilter;
use App\Models\Defect;
use App\Models\File;

class DefectService
{
    protected $defectFilter;

    public function __construct(DefectFilter $defectFilter)
    {
        $this->defectFilter = $defectFilter;
    }

    public function all()
    {
        return Defect::filter($this->defectFilter)->paginate();
    }

    public function find($id)
    {
        return Defect::find($id);
    }

    public function create(array $data)
    {
        return Defect::create($data);
    }

    public function update(array $data, $id)
    {
        return Defect::where('id', $id)->update($data);
    }

    public function updateByProduct(array $data, $product_id)
    {
        return Defect::where('product_id', $product_id)->update($data);
    }

    public function delete($id)
    {
        return Defect::where('id', $id)->delete();
    }

    public function attachFile(Defect $defect, $file_data)
    {
        return $defect->files()->save(new File($file_data));
    }
}
