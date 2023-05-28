<?php

namespace App\Http\Controllers;

use App\Models\Data;
use App\Models\Media;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Services\FileUploadService;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class DataController extends Controller
{
    private $fileUploadService;

    public function __construct(FileUploadService $fileUploadService)
    {
        $this->fileUploadService = $fileUploadService;
    }

    public function index()
    {
        $datas = Data::all();

        return response()->json([
            'status' => true,
            'datas' => $datas,
        ]);
    }

    public function upload(Request $request, Data $data)
    {
        $fileAttributes = [
            'gambar_satu',
            'gambar_dua',
            'dokumen_satu',
            'dokumen_dua',
        ];

        $validationRules = $request->only(['id']);

        $data = Data::find($request->id);

        if (!$data) {
            return response()->json([
                'status' => false,
                'message' => 'Data not found',
            ], 404);
        }

        // Validasi request
        $validator = Validator::make($validationRules, [
            'id' => 'required',
        ]);

        if ($validator->fails()) {
            DB::rollBack();

            return response()->json([
                'status' => false,
                'message' => $validator->errors()->first(),
            ], 400);
        }

        try {
            DB::beginTransaction();

            $existingAttributes = $data->media()->pluck('attribute')->toArray();

            foreach ($fileAttributes as $attribute) {
                
                if (in_array($attribute, $existingAttributes)) {
                    DB::rollBack();
                    return response()->json([
                        'status' => false,
                        'message' => "Duplicate attribute: $attribute",
                    ], 400);
                }

                $file = $request->file($attribute);

                if ($file) {
                    $prefix = $this->getFilePrefix($attribute);
                    $fileValidationRule = $this->getValidationRule($prefix, $file);

                    $validationRules[$attribute] = $fileValidationRule;

                    $mediaValidator = Validator::make([$attribute => $file], $validationRules);

                    if ($mediaValidator->fails()) {
                        DB::rollBack();
                        return response()->json([
                            'status' => false,
                            'message' => $mediaValidator->errors()->first(),
                        ], 400);
                    }

                    // $existingMedia = $data->media()->where('filename', 'like', "%{$prefix}%")->first();

                    // if ($existingMedia) {
                    //     $this->fileUploadService->delete($existingMedia->filename);
                    //     $existingMedia->delete();
                    // }
                    $media = new Media();
                    $media->filename = $file->getClientOriginalName();
                    $media->attribute = $attribute;
                    $media = $this->fileUploadService->upload($file, $prefix, $attribute);
                    $data->media()->save($media);

                    $fileUrl = Storage::disk('public')->url($media->filename);
                }
            }

            DB::commit();

            return response()->json([
                'status' => true,
                'url' => $fileUrl ?? null,
                'message' => 'File berhasil diupload',
            ]);
        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'status' => false,
                'message' => 'An error occurred while processing the request',
            ], 500);
        }
    }

    private function getFilePrefix($attribute)
    {
        $prefix = explode('_', $attribute)[0];

        return $prefix;
    }

    private function getValidationRule($prefix, $file)
    {
        if ($prefix === 'gambar') {
            return 'image';
        } elseif ($prefix === 'dokumen') {
            return 'mimes:pdf,doc,docx';
        }

        return [];
    }
}
