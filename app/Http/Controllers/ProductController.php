<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProductStoreRequest;
use App\Http\Resources\ProductTransformer;
use App\Models\Product;
use App\Services\DefectService;
use App\Services\FileService;
use App\Services\ProductService;
use App\Traits\HTTPResponseTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class ProductController extends Controller
{
    use HTTPResponseTrait;

    protected $productService, $fileService, $defectService;

    function __construct(ProductService $productService, FileService $fileService, DefectService $defectService)
    {
        $this->productService = $productService;
        $this->fileService = $fileService;
        $this->defectService = $defectService;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        try {
            return $this->successResponse('Retrieved products', 200, $this->productService->all());
        } catch (\Exception $ex) {
            return $this->errorResponse($ex->getMessage(), 400);
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(ProductStoreRequest $request)
    {
        try {
            // product_data
            $data = [
                "user_id" => Auth::id(),
                "name" => $request->name,
                "description" => $request->description,
                "selling_price" => $request->selling_price
            ];
            // create product model
            $product = $this->productService->create($data);
            // get video
            $product_video = $request->file('video');
            //Move Uploaded File
            $filePath = 'public/products/videos';
            // Remane file
            $newFileName = renameFile($product_video->getClientOriginalExtension());
            // Upload video to storage
            Storage::disk('local')->put($filePath . '/' . $newFileName, file_get_contents($product_video->getRealPath()));
            // file data
            $fileData = [
                'source' => $filePath,
                'path' => $filePath . '/' . $newFileName,
                'file_type' => 'video'
            ];

            // attach video to product
            $this->productService->attachFile($product, $fileData);

            if (isset($request->images)) {
                $product_images = $request->images;
                //Move Uploaded File
                $filePath = 'public/products/images';
                // Iterate through images
                foreach ($product_images as $product_image) {
                    // Remane file
                    $newFileName = renameFile($product_image->getClientOriginalExtension());
                    // Upload video to storage
                    Storage::disk('local')->put($filePath . '/' . $newFileName, file_get_contents($product_image->getRealPath()));
                    // file data
                    $fileData = [
                        'source' => $filePath,
                        'path' => $filePath . '/' . $newFileName,
                        'file_type' => 'image'
                    ];
                    // attach video to product
                    $this->productService->attachFile($product, $fileData);
                }
            }

            // Check if product has any defect and upload video
            if ($request->has('defect')) {
                // Create a product defect
                $defect_data = [
                    "description" => $request->defect["description"],
                    "product_id" => $product->id
                ];

                $defect = $this->defectService->create($defect_data);

                if (isset($request->defect["video"])) {
                    $defect_video = $request->defect["video"];
                    //Move Uploaded File
                    $filePath = 'public/products/videos';
                    // Remane file
                    $newFileName = renameFile($defect_video->getClientOriginalExtension());
                    // Upload video to storage
                    Storage::disk('local')->put($filePath . '/' . $newFileName, file_get_contents($defect_video->getRealPath()));
                    // file data
                    $fileData = [
                        'source' => $filePath,
                        'path' => $filePath . '/' . $newFileName,
                        'file_type' => 'video'
                    ];
                    // attach video to product
                    $this->defectService->attachFile($defect, $fileData);
                }

                // Handle defect images
                if (isset($request->defect["images"])) {
                    $defect_images = $request->defect["images"];
                    //Move Uploaded File
                    $filePath = 'public/products/images';
                    // Iterate through images
                    foreach ($defect_images as $defect_image) {
                        // Remane file
                        $newFileName = renameFile($defect_image->getClientOriginalExtension());
                        // Upload video to storage
                        Storage::disk('local')->put($filePath . '/' . $newFileName, file_get_contents($defect_image->getRealPath()));
                        // file data
                        $fileData = [
                            'source' => $filePath,
                            'path' => $filePath . '/' . $newFileName,
                            'file_type' => 'image'
                        ];
                        // attach video to product
                        $this->defectService->attachFile($defect, $fileData);
                    }
                }
            }

            return $this->successResponse('Create a new product', 201, $product->load(['files', 'defect']));
        } catch (\Exception $ex) {
            if (isset($product)) {
                $product->forceDelete();
            }
            return $this->errorResponse($ex->getMessage(), 400);
        }
    }

    /**
     * Display the specified resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        try {
            $product = $this->productService->find($id);
            if (!$product) {
                return $this->errorResponse('Product not found', 404);
            }
            return $this->successResponse('Retrieved a product', 200, $product);
        } catch (\Exception $ex) {
            return $this->errorResponse($ex->getMessage(), 400);
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Product  $product
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Product $product)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        try {
            $product = $this->productService->find($id);
            if (!$product) {
                return $this->errorResponse('Product not found', 404);
            }
            return $this->successResponse('Deleted a product', 204, $this->productService->delete($id));
        } catch (\Exception $ex) {
            return $this->errorResponse($ex->getMessage(), 400);
        }
    }
}
