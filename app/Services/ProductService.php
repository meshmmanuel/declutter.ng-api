<?php

namespace App\Services;

use App\Filters\ProductFilter;
use App\Models\Defect;
use App\Models\File;
use App\Models\Product;

class ProductService
{
    protected $productFilter;

    public function __construct(ProductFilter $productFilter)
    {
        $this->productFilter = $productFilter;
    }

    public function all()
    {
        return Product::filter($this->productFilter)->paginate();
    }

    public function find($id)
    {
        return Product::find($id);
    }

    public function create(array $data)
    {
        return Product::create($data);
    }

    public function update(array $data, $id)
    {
        return Product::where('id', $id)->update($data);
    }

    public function delete($id)
    {
        return Product::where('id', $id)->delete();
    }

    public function attachFile(Product $product, $file_data)
    {
        return $product->files()->save(new File($file_data));
    }
}
