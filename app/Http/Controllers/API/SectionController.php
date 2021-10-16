<?php

namespace App\Http\Controllers\API;

use App\Helpers\CollectionHelper;
use App\Http\Controllers\Controller;
use App\Http\Requests\PostRequest;
use App\Models\Section;
use App\Services\SectionService;
use Illuminate\Http\Request;
use Illuminate\Pagination\Paginator;

class SectionController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return Section::get()->all();
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(PostRequest $request)
    {
        if (SectionService::create($request->all())) {

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
     * @return \Illuminate\Pagination\LengthAwarePaginator
     */
    public function show($id)
    {
        $books = Section::find($id)->books;
        return CollectionHelper::paginate($books, 5);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(PostRequest $request, $id)
    {
        $section = Section::find($id);

        $section -> name = $request->get('name');
        $section -> desc = $request->get('desc');
        $section -> file = $request->get('file');
        $section -> soft_delete = $request->get('soft_delete');

        if ($section -> save()) {

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
        if (Section::find($id)->delete()) {

            return response()->json([
                'status' => 'Запись была удалена!'
            ]);
        }

        return response()->json([
            'status' => '422'
        ]);
    }
}
