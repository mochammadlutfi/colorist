<?php

namespace App\Http\Controllers;

use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
// use App\Http\Requests\StoreCustomerRequest;
use App\Models\Employee;
use App\Http\Requests\Sale\CustomerRequest;

class EmployeeController extends Controller
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
     * @return Renderable
     */
    public function index(Request $request)
    {
        $keyword = $request->q;
        $sort = !empty($request->sort) ? $request->sort : 'id';
        $sortDir = !empty($request->sortDir) ? $request->sortDir : 'desc';

        $elq = Employee::with(['department', 'job_position', 'grade', 'expertise'])->when($request->q, function($query, $search){
            $query->where('name', 'LIKE', '%' . $search . '%')
            ->orWhere('phone', 'LIKE', '%' . $search . '%');
        })
        ->when($request->name, function($q, $search){
            $q->where('name', 'LIKE', '%' . $search . '%');
        })
        ->when($request->code, function($q, $search){
            $q->where('code', 'LIKE', '%' . $search . '%');
        })
        ->when($request->status, function($q, $search){
            $q->where('status', $search);
        })
        ->when($request->dept, function($q, $search){
            $q->where('department_id', $search);
        })
        ->when($request->pst, function($q, $search){
            $q->where('job_position_id', $search);
        })
        ->when($request->superior, function($q, $search){
            $q->where('superior_id', $search);
        })
        ->orderBy($sort, $sortDir);

        if($request->limit){
            $data = $elq->paginate($request->limit);
        }else{
            $data = $elq->get();
        }
        return response()->json($data);
    }

    /**
     * Store a newly created resource in storage.
     * @param Request $request
     * @return Renderable
     */
    public function store(Request $request)
    {
        DB::beginTransaction();
        try{
            $data = new Employee();
            $data->code = $request->code;
            $data->name = $request->name;
            $data->email = $request->email;
            $data->birth_date = $request->birth_date;
            $data->birth_place = $request->birth_place;
            $data->blood_type = $request->blood_type;
            $data->gender = $request->gender;
            $data->marital_status = $request->marital_status;
            $data->religion = $request->religion;
            $data->mother_name = $request->mother_name;
            $data->addressline1 = $request->addressline1;
            $data->addressline2 = $request->addressline2;
            $data->state = $request->state;
            $data->city = $request->city;
            $data->zipcode = $request->zipcode;
            $data->country = $request->country;
            $data->phone = $request->phone;
            $data->mobile = $request->mobile;
            $data->emergency_contact_name = $request->emergency_contact_name;
            $data->emergency_contact_phone = $request->emergency_contact_phone;
            $data->emergency_contact_relationship = $request->emergency_contact_relationship;
            $data->last_education = $request->last_education;
            $data->department_id = $request->department_id;
            $data->grade_id = $request->grade_id;
            $data->job_position_id = $request->job_position_id;
            $data->superior_id = $request->superior_id;
            $data->status = $request->status;
            $data->ptkp = $request->ptkp;
            $data->join_date = $request->join_date;
            $data->exit_date = $request->exit_date;
            $data->expertise_id = $request->expertise_id;
            $data->save();

            if ($request->hasFile('image')) {
                $file = $request->file('image');
                $fileName = time() . '_' . $file->getClientOriginalName();
                $filePath = 'uploads/employee/' . $data->id . '/';
                $file->move(public_path($filePath), $fileName);
                $data->image = '/' . $filePath . $fileName;
                $data->save();
            }

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
            'result' => $data->id
        ], 200);
    }

    /**
     * Show the specified resource.
     * @param int $id
     * @return Renderable
     */
    public function show($id)
    {
        $data = Employee::with(['department', 'grade', 'job_position', 'expertise'])
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

    /**
     * Update the specified resource in storage.
     * @param Request $request
     * @param int $id
     * @return Renderable
     */
    public function update(Request $request, $id)
    {
        // dd($request->all());
        DB::beginTransaction();
        try{
            $data = Employee::where('id', $id)->first();
            $data->code = $request->code;
            $data->name = $request->name;
            $data->email = $request->email;
            $data->birth_date = $request->birth_date;
            $data->birth_place = $request->birth_place;
            $data->blood_type = $request->blood_type;
            $data->gender = $request->gender;
            $data->marital_status = $request->marital_status;
            $data->religion = $request->religion;
            $data->mother_name = $request->mother_name;
            $data->addressline1 = $request->addressline1;
            $data->addressline2 = $request->addressline2;
            $data->state = $request->state;
            $data->city = $request->city;
            $data->zipcode = $request->zipcode;
            $data->country = $request->country;
            $data->phone = $request->phone;
            $data->mobile = $request->mobile;
            $data->emergency_contact_name = $request->emergency_contact_name;
            $data->emergency_contact_phone = $request->emergency_contact_phone;
            $data->emergency_contact_relationship = $request->emergency_contact_relationship;
            $data->last_education = $request->last_education;
            $data->department_id = $request->department_id;
            $data->grade_id = $request->grade_id;
            $data->job_position_id = $request->job_position_id;
            $data->superior_id = $request->superior_id;
            $data->expertise_id = $request->expertise_id;
            $data->status = $request->status;
            $data->ptkp = $request->ptkp;
            $data->join_date = $request->join_date;
            $data->exit_date = $request->exit_date;

            // Path lokasi penyimpanan gambar
            $filePath = 'uploads/employee/'. $data->id .'/';

            // Jika request image null tapi user memiliki gambar, hapus gambar lama
            if (is_null($request->image) && !empty($data->image)) {
                $oldImagePath = public_path($data->image);
                if (file_exists($oldImagePath)) {
                    unlink($oldImagePath); // Hapus file
                }
                $data->image = null; // Kosongkan di database
            }

            // Jika ada file gambar baru diunggah
            if ($request->hasFile('image')) {
                $file = $request->file('image');

                // Hapus file lama jika ada
                if (!empty($data->image)) {
                    $oldImagePath = public_path($data->image);
                    if (file_exists($oldImagePath)) {
                        unlink($oldImagePath); // Hapus file lama
                    }
                }

                // Simpan file baru
                $fileName = time() . '_' . $file->getClientOriginalName();
                $file->move(public_path($filePath), $fileName);
                $data->image = '/' . $filePath . $fileName;
            }
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
            'result' => $data->id
        ], 200);
    }

    public function destroy($id)
    {
        DB::beginTransaction();
        try{
            $data = Employee::where('id', $id)->first();
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
