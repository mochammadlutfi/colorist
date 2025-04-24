<?php

namespace App\Http\Controllers\Settings;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

use App\Models\BasePaint;


class BasePaintController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $sort = !empty($request->sort) ? $request->sort : 'id';
        $sortDir = !empty($request->sortDir) ? $request->sortDir : 'desc';

        $query = BasePaint::with(['product'])
        ->when($request->q, function ($q, $search) {
            return $q->where('name', 'LIKE', '%' . $search . '%')
            ->orWhere('code', 'LIKE', '%' . $search . '%')
            ->orWhere('price', 'LIKE', '%' . $search . '%');
        })
        ->when($request->product_id, function ($q, $product_id) {
            return $q->orWhere('product_id', $product_id);
        })
        ->orderBy($sort, $sortDir);

        if($request->limit){
            $data = $query->paginate($request->limit);
        }else{
            $data = $query->get();
        }
        return response()->json($data);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        DB::beginTransaction();
        try{
            $data = new BasePaint();
            $data->code = $request->code;
            $data->name = $request->name;
            $data->product_id = $request->product_id;
            $data->price = $request->price;
            $data->size = $request->size;
            $data->unit = $request->unit;
            $data->save();

        }catch(\QueryException $e){
            DB::rollback();
            return response()->json([
                'success' => false,
                'result' => $e,
            ], 422);
        }
        DB::commit();
        return response()->json([
            'success' => true,
        ], 200);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        DB::beginTransaction();
        try{
            $data = BasePaint::where('id', $id)->first();
            $data->code = $request->code;
            $data->name = $request->name;
            $data->product_id = $request->product_id;
            $data->price = $request->price;
            $data->size = $request->size;
            $data->unit = $request->unit;
            $data->save();

        }catch(\QueryException $e){
            DB::rollback();
            return response()->json([
                'success' => false,
                'result' => $e,
            ], 422);
        }
        DB::commit();
        return response()->json([
            'success' => true,
        ], 200);
    }

    public function destroy($id)
    {
        DB::beginTransaction();
        try{
            $data = BasePaint::where('id', $id)->first();
            $data->delete();

        }catch(\QueryException $e){
            DB::rollback();
            return response()->json([
                'success' => false,
                'result' => $e,
            ], 422);
        }
        DB::commit();
        return response()->json([
            'success' => true,
        ], 200);
    }
    
}
