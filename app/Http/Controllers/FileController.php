<?php

namespace App\Http\Controllers;

use App\Http\Requests\FileStoreRequest;
use App\Http\Requests\ImageFileStoreRequest;
use App\Http\Requests\VideoFileStoreRequest;
use App\Models\File;
use App\Services\DefectService;
use App\Services\FileService;
use App\Services\ProductService;
use App\Traits\HTTPResponseTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class FileController extends Controller
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
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(FileStoreRequest $request)
    {
        // get product
        $product = '';

        // get file
        $uploaded_file = $request->file('file');

        // get file extension
        $extension = $uploaded_file->getClientOriginalExtension();

        // Remane file
        $newFileName = renameFile($extension);

        $video_extensions = ['mp4', 'avi', 'mov'];
        $image_extensions = ['jpeg', 'jpg', 'png'];

        // Treat as video
        if (in_array($extension, $video_extensions)) {
            $filePath = 'declutter_uploads/videos';
            // file data
            $fileData = [
                'source' => $filePath,
                'path' => $filePath . '/' . $newFileName,
                'file_type' => 'video'
            ];
        }

        // Treat as image
        if (in_array($extension, $image_extensions)) {
            $filePath = 'declutter_uploads/images';
            // file data
            $fileData = [
                'source' => $filePath,
                'path' => $filePath . '/' . $newFileName,
                'file_type' => 'image'
            ];
        }
        //Move Uploaded File
        $filePath = 'declutter_uploads/videos';
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\File  $file
     * @return \Illuminate\Http\Response
     */
    public function show(File $file)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\File  $file
     * @return \Illuminate\Http\Response
     */
    public function edit(File $file)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\File  $file
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, File $file)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\File  $file
     * @return \Illuminate\Http\Response
     */
    public function destroy(File $file)
    {
        //
    }

    public function storeImage(ImageFileStoreRequest $request)
    {
        try {
            // fetch product
            $product = $this->productService->find($request->product_id);

            // get file
            $uploaded_file = $request->file('file');

            // get file extension
            $extension = $uploaded_file->getClientOriginalExtension();

            // Remane file
            $newFileName = renameFile($extension);

            // File path
            $filePath = 'declutter_uploads/images';

            // Upload video to storage
            Storage::disk('s3')->put($filePath . '/' . $newFileName, fopen($uploaded_file, 'r+'), 'public');

            // file data
            $fileData = [
                'source' => Storage::disk('s3')->url($filePath . '/' . $newFileName),
                'path' => $filePath . '/' . $newFileName,
                'file_type' => 'image'
            ];

            // attach video to product
            $this->productService->attachFile($product, $fileData);

            return $this->successResponse('File upload successful', 201);
        } catch (\Exception $ex) {
            return $this->errorResponse($ex->getMessage());
        }
    }

    public function storeVideo(VideoFileStoreRequest $request)
    {
        try {
            // fetch product
            $product = $this->productService->find($request->product_id);

            // get file
            $uploaded_file = $request->file('file');

            // get file extension
            $extension = $uploaded_file->getClientOriginalExtension();

            // Remane file
            $newFileName = renameFile($extension);

            // File path
            $filePath = 'declutter_uploads/videos';

            // Upload video to storage
            Storage::disk('s3')->put($filePath . '/' . $newFileName, fopen($uploaded_file, 'r+'), 'public');

            // file data
            $fileData = [
                'source' => Storage::disk('s3')->url($filePath . '/' . $newFileName),
                'path' => $filePath . '/' . $newFileName,
                'file_type' => 'video'
            ];

            // attach video to product
            $this->productService->attachFile($product, $fileData);

            return $this->successResponse('File upload successful', 201);
        } catch (\Exception $ex) {
            return $this->errorResponse($ex->getMessage());
        }
    }
}
