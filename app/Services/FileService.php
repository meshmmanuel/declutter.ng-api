<?php

namespace App\Services;

use App\Filters\FileFilter;
use App\Models\File;

class FileService
{
    protected $fileFilter;

    public function __construct(FileFilter $fileFilter)
    {
        $this->fileFilter = $fileFilter;
    }

    public function all()
    {
        return File::filter($this->fileFilter)->paginate();
    }

    public function find($id)
    {
        return File::find($id);
    }

    public function create(array $data)
    {
        return File::create($data);
    }

    public function update(array $data, $id)
    {
        return File::where('id', $id)->update($data);
    }

    public function delete($id)
    {
        return File::where('id', $id)->delete();
    }
}
