<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

use App\Models\MixingBatch;


class TransactionController extends Controller
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

        $query = MixingBatch::with(['outlet', 'product', 'series'])
        ->when(!empty($request->search), function ($q, $search) {
            return $q->where('name', 'LIKE', '%' . $search . '%');
        })
        ->when($request->outlet_id, function ($q, $search) {
            return $q->where('outlet_id', $search);
        })
        ->when($request->date, function ($q, $date) {
            return $q->whereDate('color_mixing_time', '>=', $date[0])
            ->whereDate('color_mixing_time', '<=', $date[1]);
            // return $q->whereBetween(DB::raw('DATE(color_mixing_time)'), $date);
        })
        ->when($user->hasRole('Sales'), function ($q) use($user) {
            return $q->whereIn('outlet_id', $user->outlet()->pluck('id'));
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
    public function show($id)
    {

        
        $data = MixingBatch::with(['outlet', 'product', 'series', 'base_paint', 'color_card', 
        'lines' => function($q){
            return $q->with('colorant');
        }])
        ->where('id', $id)->first();

        if(!$data){
            return response()->json([
                'success' => false
            ],404);
        }

        return response()->json([
            'success' => true,
            'result' => $data
        ],200);
    }
    
}
