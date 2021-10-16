<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\PostRequest;
use App\Models\Book;
use App\Models\Section;
use App\Services\BookService;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class BookController extends Controller
{
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
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(PostRequest $request)
    {
        if (BookService::create($request->all())) {

            return response()->json([
                'status' => 'Запись была добавлена!'
            ]);
        }

        return response()->json([
            'status' => '422'
        ]);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        return Book::find($id);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(PostRequest  $request, $id)
    {
        $book = Book::find($id);

        $book -> name = $request->get('name');
        $book -> desc = $request->get('desc');
        $book -> year = $request->get('year');
        $book -> file = $request->get('file');
        $book -> soft_delete = $request->get('soft_delete');

        if ($book -> save()) {

            return response()->json([
                'status' => 'Запись была изменена!'
            ]);
        }

        return response()->json([
            'status' => '422'
        ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($id)
    {
        if (Book::find($id)->delete()) {

            return response()->json([
                'status' => 'Запись была удалена!'
            ]);
        }

        return response()->json([
            'status' => '422'
        ]);
    }

    public function uploadFile(Request $req)
    {
        $img = null;

        if ($file = $req->file('file')) {
            /** @var $file UploadedFile */

            $imgName = $file->getClientOriginalName();
            if (Storage::disk('public')->missing($imgName)) {
                $file->storeAs('images', $imgName, 'public');
            }
        }

        return response()->json([
            'status' => 'Запись была добавлена!'
        ]);
    }
}
