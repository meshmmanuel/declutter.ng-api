<?php

namespace App\Http\Controllers;

use App\Models\HeardAboutUs;
use App\Traits\HTTPResponseTrait;
use Illuminate\Http\Request;

class HeardAboutUsController extends Controller
{
    use HTTPResponseTrait;

    public function index(Request $request)
    {
        try {
            // check if platform exist, if true increase count
            if (!$record = HeardAboutUs::where('platform', $request->platform)->first()) {
                $record = HeardAboutUs::create([
                    'platform' => $request->platform,
                    'count' => 1
                ]);
            } else {
                $record->count = (int)$record->count + 1;
                $record->save();
            }
            return $this->successResponse('Success', 200, $record->fresh());
        } catch (\Exception $ex) {
            return $this->errorResponse($ex->getMessage());
        }
    }
}
