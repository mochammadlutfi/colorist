<?php

namespace App\Http\Controllers\Settings;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use DB;
use App\Models\User;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $sort = !empty($request->sort) ? $request->sort : 'id';
        $sortDir = !empty($request->sortDir) ? $request->sortDir : 'desc';
        
        $query = User::with('roles')
        ->when($request->q, function($q, $search) {
            return $q->where('name', 'LIKE', '%'.$search.'%')
            ->orWhere('email', 'LIKE', '%'.$search.'%')
            ->orWhere('phone', 'LIKE', '%'.$search.'%')
            ->orWhereHas('roles', function($q) use ($search){
                $q->where('name', 'LIKE', '%'.$search.'%');
            });
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

            $data = new User();
            $data->name = $request->name;
            $data->email = $request->email;
            $data->password = $request->password;
            if ($request->hasFile('file')) {
                $file = $request->file('file');
                $fileName = time() . '_' . $file->getClientOriginalName();
                $filePath = 'uploads/' . $request->employee_id . '/certification/';
                $fileSize = $file->getSize(); // Get file size before moving
                $fileType = $file->getClientMimeType(); // Get file type before moving
                $file->move(public_path($filePath), $fileName);
                $data->file_path = '/' . $filePath . $fileName;
                $data->file_name = $fileName;
                $data->file_size = $fileSize;
                $data->file_type = $fileType;
            }
            $data->save();

            $data->assignRole($request->role);
            $data->outlet()->sync($request->outlet_ids);
            
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
    public function show(string $id)
    {
        
        $data = User::with(['roles', 'outlet'])->where('id', $id)->first();
        if(!$data){
            return response()->json([
                'message' => 'Data not found',
            ], 404);
        }
        
        return response()->json($data, 200);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        DB::beginTransaction();
        try{

            $data = User::where('id', $id)->first();
            $data->name = $request->name;
            $data->email = $request->email;
            $data->save();

            $data->assignRole($request->role);

            $data->outlet()->sync($request->outlet_ids);

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
            $role = User::where('id', $id)->first();
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
