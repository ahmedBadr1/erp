<?php

namespace App\Http\Controllers\Api\Hr;

use App\Http\Controllers\Controller;
use App\Http\Requests\ListRequest;
use App\Http\Resources\Hr\EmployeesResource;
use App\Http\Resources\Hr\JopTitlesresources;
use App\Models\Hr\Department;
use App\Models\Hr\JopTitle;
use App\Models\System\Status;
use Illuminate\Http\Request;
use App\Models\Hr\Employee;
use Illuminate\Database\Eloquent\SoftDeletes;

class EmployeesController extends Controller
{
    use SoftDeletes;
    public function __construct()
    {
        $this->middleware('permission:hr');
    }
    /**
     * Display a listing of the resource.
     *
     *
     */
    public function index( )
    {
        return  EmployeesResource::collection( Employee::all());
    }

    public function list(ListRequest $request)
    {
        $input = $request->all();
        //
        return  EmployeesResource::collection( Employee::search($input['keywords'])
//            ->orderBy($input['orderBy'], $input['orderDesc'] ? 'desc' : 'asc')
            ->paginate($input['limit']));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $joptitles = JopTitlesresources::collection(JopTitle::all()) ;
        $departments = Department::select('id','name')->pluck('id','name');
        $statuses = Status::where('type','jop')->pluck('id','name');
        return success(['joptitles'=>$joptitles , 'departments' => $departments , 'statuses'=>$statuses]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function put(Request $request, Employee $employee)
    {
        $this->validate($request,[
            'name'=>'required',
            'phone'=>'nullable',
            'address'=>'nullable',
            'salary'=>'nullable|numeric',
            'overtime'=>'nullable|numeric',
            'joptitle_id'=>'nullable',
            'department_id'=>'nullable',
            'status_id'=>'nullable',
            'joined_at'=>'nullable|date',
        ]);
        $input = $request->all();

        if (!$employee){
          //  Employee::create($input);
            $employee = new Employee();
        }
//            $employee->update($input) ;
            $employee->fill($input);
        $employee->save();
        return success(true);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Hr\Employee  $employee
     * @return \Illuminate\Http\Response
     */
    public function show(Employee $employee)
    {
        $employeeData = new EmployeesResource($employee);
        return success($employeeData);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Hr\Employee  $employee
     * @return \Illuminate\Http\Response
     */
    public function edit(Employee $employee)
    {
        $joptitles = JopTitlesresources::collection(JopTitle::all()) ;
        $departments = Department::select('id','name')->pluck('id','name');
        $statuses = Status::where('type','jop')->pluck('id','name');
        return success(['joptitles'=>$joptitles , 'departments' => $departments , 'statuses'=>$statuses]);
    }


    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Hr\Employee  $employee
     * @return \Illuminate\Http\Response
     */
    public function destroy(Employee $employee)
    {
        if (!$employee){

            return error(403,'Employee Can\'t be Deleted');
        }
        $employee->delete();

        return success('Employee Deleted Successfully');
    }
}
