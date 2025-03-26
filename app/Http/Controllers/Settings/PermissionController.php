<?php

namespace App\Http\Controllers\Settings;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

class PermissionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $sort = !empty($request->sort) ? $request->sort : 'id';
        $sortDir = !empty($request->sortDir) ? $request->sortDir : 'desc';
        $limit = ($request->limit) ? $request->limit : 25;
        $search = !empty($request->search) ? $request->search : '';
        $page = $request->page;
        
        $query = Role::withCount('users')->
        when(!empty($search), function($q, $search) {
            $q->where('name', 'like', '%'.$search.'%');
        })
        ->orderBy($sort, $sortDir);

        if($page){
            $data = $query->paginate($limit);
        }else{
            $data = $query->get();
        }
        return response()->json($data);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function list()
    {
        $permissions = Permission::latest()->get()->pluck('name');
        
        $grouped = [];
        foreach ($permissions as $permission) {
            $parts = explode('.', $permission);
            $category = __('base.'.$parts[0]);
            $action = $parts[1];

            if (!isset($grouped[$category])) {
                $grouped[$category] = [];
            }

            $grouped[$category][] = [
                'name' => $permission,
                'action' => $action
            ];
        }

        return response()->json($grouped);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        DB::beginTransaction();
        try{

            $role = Role::create(['name' => $request->name]);
            $role->syncPermissions($request->lines);
            
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
     * Display the specified resource.
     */
    public function show($id)
    {
        
        $data = Role::with('permissions:id,name')->
        where('id', $id)->first();
        return response()->json($data);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        // dd($request->all());
        DB::beginTransaction();
        try{

            $role = Role::where('id', $id)->first();
            $role->name = $request->name;
            $role->save();

            $role->syncPermissions($request->lines);

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
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        DB::beginTransaction();
        try{
            $role = Role::where('id', $id)->first();
            $role->delete();
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
