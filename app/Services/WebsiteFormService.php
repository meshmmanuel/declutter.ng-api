<?php

namespace App\Services;

use App\Models\WebsiteForm;

class WebsiteFormService
{
    public function all()
    {
        return WebsiteForm::paginate();
    }

    public function create(array $data)
    {
        return WebsiteForm::create($data);
    }
}
