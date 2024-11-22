<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\FileRequest;
use Illuminate\Support\Facades\Storage;
use App\Models\Attachment;

class UploadController extends Controller
{
    /**
     * Store a newly created resource in storage.
     *
     */
    public function store(FileRequest $request)
    {
        $file = $request->file('file');
        $name = uniqid().".".strtolower($file->getClientOriginalExtension());        
        Storage::putFileAs('/', $file, $name);
        $data = Attachment::firstOrCreate([
            'name' => $name
        ]);

        return response()->json($data);
    }
}
