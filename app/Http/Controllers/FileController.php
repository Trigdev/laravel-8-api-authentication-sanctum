<?php

namespace App\Http\Controllers;

use App\Http\Resources\FileResource;
use Illuminate\Http\Request;
use App\Models\Files;

class FileController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $files = Files::paginate(10);
        return FileResource::collection($files);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate([
            "file" => "required|file"
        ]);

        if ($request->hasFile('file')) {
            if ($request->file('file')->isValid()) {
                $image = $request->file('file');
                $image_name = $image->hashName();
                $image->move(public_path('uploads'), $image_name);

                $file = new Files();
                $file->files = $image_name;
                $file->save();
                return new FileResource($file);
            } else {
                return response()->json(['Unprocessible Entity'], 422);
            }
        } else {
            return response()->json(['Bad Request'], 400);
        }
    }

    /**
     * upload multiple files via the API
     *
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function multiple(Request $request)
    {
        $this->validate($request, [
            'files' => 'required',
            'files.*' => 'image|mimes:jpeg,png,jpg|max:10240'
        ]);

        $files = [];
        $requested_files = $request->file('files');

        if ($request->hasFile('files')) {
            for ($image_index = 0; $image_index < count($request->file('files')); $image_index++) {
                $filename = $requested_files[$image_index]->hashName();
                $requested_files[$image_index]->move(public_path('uploads'), $filename);
                array_push($files, $filename);
            }
        } else {
            return response()->json(['Bad Request'], 400);
        }

        $images = new Files();
        $images->files = json_encode($files);
        $images->save();
        return new FileResource($images);
    }

    public function uploader(Request $request)
    {
        if (!$request->hasFile('fileName')) {
            return response()->json(['upload_file_not_found'], 400);
        }

        $allowedfileExtension = ['pdf', 'jpg', 'png', 'jpeg'];
        $files = $request->file('fileName');
        $errors = [];

        foreach ($files as $file) {

            $extension = $file->getClientOriginalExtension();

            $check = in_array($extension, $allowedfileExtension);

            if ($check) {
                foreach ($request->fileName as $mediaFiles) {

                    $name = $mediaFiles->getClientOriginalName();
                    $path = $mediaFiles->move(public_path('images'), $name);

                    //store image file into directory and db
                    $save = new Files();
                    // $save->title = $name;
                    $save->files = $path;
                    $save->save();
                }
            } else {
                return response()->json(['invalid_file_format'], 422);
            }

            return response()->json(['file_uploaded'], 200);
        }
    }
}
