<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Resources\TagResource;
use App\Models\Tag;

class TagController extends Controller
{
    /**
     * Add new tags to the lists
     * We will use this in the pivot table so no repeated tags is created
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $tag = Tag::firstOrCreate([
            'name' => $request->name
        ]);

        return response()->json(new TagResource($tag));
    }
}
