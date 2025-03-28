<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

use App\Models\Outlet;


class OutletController extends Controller
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

        $user = auth()->user();

        $query = Outlet::when(!empty($request->search), function ($q, $search) {
            return $q->orWhere('name', 'LIKE', '%' . $search . '%');
        })
        ->when($user->hasRole('Sales'), function ($q) use($user) {
            return $q->whereIn('id', $user->outlet()->pluck('id'));
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
            $data = new Outlet();
            $data->name = $request->name;
            $data->machine_type = $request->machine_type;
            $data->machine_code = $request->machine_code;
            $data->machine_serial = $request->machine_serial;
            $data->branch_id = $request->branch_id;
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
            $data = Outlet::where('id', $id)->first();
            $data->name = $request->name;
            $data->machine_type = $request->machine_type;
            $data->machine_code = $request->machine_code;
            $data->machine_serial = $request->machine_serial;
            $data->branch_id = $request->branch_id;
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
            $data = Outlet::where('id', $id)->first();
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
