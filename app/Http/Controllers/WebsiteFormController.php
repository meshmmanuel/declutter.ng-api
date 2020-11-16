<?php

namespace App\Http\Controllers;

use App\Http\Resources\WebsiteFormTransformer;
use App\Models\WebsiteForm;
use App\Services\WebsiteFormService;
use Exception;
use Illuminate\Http\Request;


class WebsiteFormController extends Controller
{

    protected $websiteFormService;

    public function __construct(WebsiteFormService $websiteFormService)
    {
        $this->websiteFormService = $websiteFormService;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        try {
            $records = $this->websiteFormService->all();
            return $this->successResponse('Retrieved all records', 200, WebsiteFormTransformer::collection($records));
        } catch (Exception $ex) {
            return $this->errorResponse($ex->getMessage(), 400);
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        try {
            $record = $this->websiteFormService->create($request->all());
            return $this->successResponse('Created a record', 201, new WebsiteFormTransformer($record));
        } catch (Exception $ex) {
            return $this->errorResponse($ex->getMessage(), 400);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\WebsiteForm  $websiteForm
     * @return \Illuminate\Http\Response
     */
    public function show(WebsiteForm $websiteForm)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\WebsiteForm  $websiteForm
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, WebsiteForm $websiteForm)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\WebsiteForm  $websiteForm
     * @return \Illuminate\Http\Response
     */
    public function destroy(WebsiteForm $websiteForm)
    {
        //
    }
}
