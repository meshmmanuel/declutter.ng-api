<?php

namespace App\Http\Controllers;

use App\Http\Requests\CustomStoreRequest;
use App\Http\Requests\ProductStoreRequest;
use App\Http\Requests\RegisterIncompleteProductRequest;
use App\Http\Resources\ProductTransformer;
use App\Models\Product;
use App\Services\DefectService;
use App\Services\FileService;
use App\Services\ProductService;
use App\Traits\HTTPResponseTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

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
                "selling_price" => $request->selling_price,
                "release_date" => $request->release_date,
                "product_status" => $request->product_status
            ];

            // create product model
            $product = $this->productService->create($data);
            // get video
            $product_video = $request->file('video');
            //Move Uploaded File
            $filePath = 'declutter_uploads/videos';
            // Remane file
            $newFileName = renameFile($product_video->getClientOriginalExtension());

            // Upload video to storage
            Storage::disk('s3')->put($filePath . '/' . $newFileName, fopen($request->file('video'), 'r+'), 'public');
            // Storage::disk('s3')->put($filePath . '/' . $newFileName, file_get_contents($product_video->getRealPath()), 'public');
            // Storage::disk('local')->put($filePath . '/' . $newFileName, file_get_contents($product_video->getRealPath()));

            // file data
            $fileData = [
                'source' => Storage::disk('s3')->url($filePath . '/' . $newFileName),
                'path' => $filePath . '/' . $newFileName,
                'file_type' => 'video'
            ];

            // attach video to product
            $this->productService->attachFile($product, $fileData);

            if (isset($request->images)) {
                $product_images = $request->images;
                //Move Uploaded File
                $filePath = 'declutter_uploads/images';
                // Iterate through images
                foreach ($product_images as $product_image) {
                    // Remane file
                    $newFileName = renameFile($product_image->getClientOriginalExtension());
                    // Upload video to storage
                    Storage::disk('s3')->put($filePath . '/' . $newFileName, fopen($product_image, 'r+'), 'public');
                    // Storage::disk('s3')->put($filePath . '/' . $newFileName, file_get_contents($product_image->getRealPath()), 'public');
                    // Storage::disk('local')->put($filePath . '/' . $newFileName, file_get_contents($product_image->getRealPath()));
                    // file data
                    $fileData = [
                        'source' => Storage::disk('s3')->url($filePath . '/' . $newFileName),
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
                    $filePath = 'declutter_uploads/videos';
                    // Remane file
                    $newFileName = renameFile($defect_video->getClientOriginalExtension());
                    // Upload video to storage
                    Storage::disk('s3')->put($filePath . '/' . $newFileName, fopen($request->file('video'), 'r+'), 'public');
                    // Storage::disk('s3')->put($filePath . '/' . $newFileName, file_get_contents($product_video->getRealPath()), 'public');
                    // Storage::disk('local')->put($filePath . '/' . $newFileName, file_get_contents($defect_video->getRealPath()));
                    // file data
                    $fileData = [
                        'source' => Storage::disk('s3')->url($filePath . '/' . $newFileName),
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
                    $filePath = 'declutter_uploads/images';
                    // Iterate through images
                    foreach ($defect_images as $defect_image) {
                        // Remane file
                        $newFileName = renameFile($defect_image->getClientOriginalExtension());
                        // Upload video to storage
                        $path = Storage::disk('s3')->put($filePath . '/' . $newFileName, fopen($product_image, 'r+'), 'public');
                        // Storage::disk('s3')->put($filePath . '/' . $newFileName, file_get_contents($defect_image->getRealPath()), 'public');
                        // Storage::disk('local')->put($filePath . '/' . $newFileName, file_get_contents($defect_image->getRealPath()));
                        // file data
                        $fileData = [
                            'source' => Storage::disk('s3')->url($filePath . '/' . $newFileName),
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
     * Store products data
     */
    public function customStore(CustomStoreRequest $request)
    {
        try {
            // product_data
            $data = [
                "user_id" => Auth::id(),
                "name" => $request->name,
                "description" => $request->description,
                "selling_price" => $request->selling_price,
                "release_date" => $request->release_date,
                "product_status" => $request->product_status
            ];

            // Checj if product description has been filled
            $product = $this->productService->find($request->product_id);
            if ($product->description !== null) {
                return $this->errorResponse("Product has already been completed", 400);
            }

            // create product model
            $this->productService->update($data, $request->product_id);

            // Check if product has any defect and upload video
            if ($request->has('defect')) {
                // Create a product defect
                $defect_data = [
                    "description" => $request->defect["description"],
                    "product_id" => $request->product_id
                ];

                $defect = $this->defectService->updateByProduct($defect_data, $request->product_id);
            }

            $product = $this->productService->find($request->product_id);

            return $this->successResponse('Updated a product', 200, $product->load(['files', 'defect']));
        } catch (\Exception $ex) {
            if (isset($product)) {
                $product->forceDelete();
            }
            if (isset($defect)) {
                $defect->forceDelete();
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
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        try {
            // product_data
            $data = [
                "user_id" => Auth::id(),
                "name" => $request->name,
                "description" => $request->description,
                "selling_price" => $request->selling_price,
                "release_date" => $request->release_date,
                "product_status" => $request->product_status,
                "customer_name" => $request->get('customer_name'),
                "customer_phone" => $request->get('customer_phone'),
            ];

            // create product model
            $product = $this->productService->create($data);

            // Check if product has any defect and upload video
            if ($request->has('defect')) {
                // Create a product defect
                $defect_data = [
                    "description" => $request->defect["description"],
                    "product_id" => $product->id
                ];

                $defect = $this->defectService->create($defect_data);
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
     * Soft delete the specified resource from storage.
     *
     * @return \Illuminate\Http\Response
     */
    public function softDelete($id)
    {
        try {
            $product = $this->productService->find($id);
            if (!$product) {
                return $this->errorResponse('Product not found', 404);
            }
            $this->productService->delete($id, false);
            return $this->successResponse('Soft deleted a product', 204);
        } catch (\Exception $ex) {
            return $this->errorResponse($ex->getMessage(), 400);
        }
    }


    /**
     * RemovForce delte the specified resource from storage.
     *
     * @return \Illuminate\Http\Response
     */
    public function forceDelete($id)
    {
        try {
            $product = $this->productService->find($id);
            if (!$product) {
                return $this->errorResponse('Product not found', 404);
            }
            $this->productService->delete($id, true);
            return $this->successResponse('Force deleted a product', 204);
        } catch (\Exception $ex) {
            return $this->errorResponse($ex->getMessage(), 400);
        }
    }

    /**
     * RemovForce delte the specified resource from storage.
     *
     * @return \Illuminate\Http\Response
     */
    public function deleteIncomplete()
    {
        try {
            $user_id = Auth::id();
            $product = $this->productService->incomplete($user_id);
            if (!$product) {
                return $this->errorResponse('No incomplete product not found', 404);
            }
            $this->productService->deleteIncomplete($user_id);
            return $this->successResponse('Deleted an incomplete product', 204);
        } catch (\Exception $ex) {
            return $this->errorResponse($ex->getMessage(), 400);
        }
    }

    public function incomplete()
    {
        try {
            $user_id = Auth::id();

            $data = [
                'role' => Auth::user()->role,
                $this->productService->incomplete($user_id)
            ];

            return $this->successResponse('Retrieved incomplete product', 200,);
        } catch (\Exception $ex) {
            return $this->errorResponse($ex->getMessage(), 400);
        }
    }

    /**
     * Register product by product name
     */
    public function registerIncompleteProduct(RegisterIncompleteProductRequest $request)
    {
        try {
            $data = [
                'name' => $request->name,
                'user_id' => Auth::id()
            ];

            if ($this->productService->incomplete($data['user_id'])) {
                return $this->errorResponse("Complete or delete pending incompleted product", 400);
            }

            $product = $this->productService->create($data);
            return $this->successResponse('Created an incomplete product', 201, $product);
        } catch (\Exception $ex) {
            return $this->errorResponse($ex->getMessage(), 400);
        }
    }
}
