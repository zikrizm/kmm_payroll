<?php

namespace App\Http\Controllers;

use App\Models\Kasbon;
use App\Utils\ResponseUtil;
use Illuminate\Http\Request;
use App\Services\Api\ApiServices;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;

class KasbonController extends Controller
{
    private $apiService;
    private $buildRes;

    public function __construct(ApiServices $apiService, ResponseUtil $buildRes)
    {
        $this->apiService = $apiService;
        $this->buildRes = $buildRes;
    }

    /**
     * Display a listing of the resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        if (!auth()->user()->can('kasbon.view')) {
            abort(403, 'Unauthorized action.');
        }

        try {
            if (request()->ajax()) {
                $business_id = Session::get('business_id');
                $kasbons = Kasbon::where('business_id', $business_id);
                if ($request->has('q') && !empty($request->input('q'))) {
                    $search = str_replace('.', '', $request->q);
                    $kasbons = $kasbons->where('kasbon', 'LIKE', "%" . $search . "%")->orWhereHas('employee', function ($q) use ($search) {
                        $q->where('first_name', 'LIKE', "%" . $search . "%");
                    });
                }

                if ($request->has('kasbon_date')) {
                    $kasbons = $kasbons->whereBetween('date', [$request['kasbon_date']['start_date'], $request['kasbon_date']['end_date']]);
                }

                $order = null;
                if ($request->has('sort')) {
                    $sort = $request->sort;
                    $order = $sort['order'];
                    $kasbons->orderBy($sort['name'], $sort['order']);
                }
                $kasbons = $kasbons->paginate(10);
                $render =  view('Employee.kasbon.table', compact('kasbons', 'order'))->render();

                return $this->buildRes->RESPONSE_REQ('success', $render, null);
            }

            return  view('Employee.kasbon.index');
        } catch (\Exception $e) {
            Log::emergency("File:" . $e->getFile() . "Line:" . $e->getLine() . "Message:" . $e->getMessage());

            return $this->buildRes->RESPONSE_REQ('error', null, ['error' => 'something wrong']);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        if (!auth()->user()->can('kasbon.create') || !request()->ajax()) {
            abort(403, 'Unauthorized action.');
        }

        try {
            $render = view('Employee.kasbon.create')->render();

            return $this->buildRes->RESPONSE_REQ('success', $render, null);
        } catch (\Exception $e) {
            Log::emergency("File:" . $e->getFile() . "Line:" . $e->getLine() . "Message:" . $e->getMessage());

            return $this->buildRes->RESPONSE_REQ('error', null, ['error' => 'something wrong']);
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        if (!auth()->user()->can('kasbon.create')  || !$request->ajax()) {
            abort(403, 'Unauthorized action.');
        }

        try {
            $validator = Validator::make($request->all(), $this->rules());

            if ($validator->fails()) {
                return $this->buildRes->RESPONSE_REQ('error', null, $validator->errors());
            } else {
                $kasbon_data = $request->only(['employee_id', 'date', 'kasbon', 'notes']);
                $kasbon_data['business_id'] = Session::get('business_id');
                $kasbon_data['created_user'] = auth()->user()->id;
                $kasbon_data['updated_user'] = auth()->user()->id;
                $kasbon_data['kasbon'] = str_replace('.', '', $kasbon_data['kasbon']);

                $kasbon = new Kasbon($kasbon_data);
                $kasbon->save();

                return $this->buildRes->RESPONSE_REQ('success', null,  ['success' => ['Add kasbon succesfully']]);
            }
        } catch (\Exception $e) {
            Log::emergency("File:" . $e->getFile() . "Line:" . $e->getLine() . "Message:" . $e->getMessage());

            return $this->buildRes->RESPONSE_REQ('error', null, ['error' => 'something wrong']);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        if (!auth()->user()->can('kasbon.view')) {
            abort(403, 'Unauthorized action.');
        }
    }


    /**
     * Display the specified resource.
     *
     * @param  Kasbon $kasbon
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function edit(Kasbon $kasbon, Request $request)
    {
        if (!auth()->user()->can('kasbon.update') || !$request->ajax()) {
            abort(403, 'Unauthorized action.');
        }

        try {
            $render = view('Employee.kasbon.edit', compact('kasbon'))->render();

            return $this->buildRes->RESPONSE_REQ('success', $render, null);
        } catch (\Exception $e) {
            Log::emergency("File:" . $e->getFile() . "Line:" . $e->getLine() . "Message:" . $e->getMessage());

            return $this->buildRes->RESPONSE_REQ('error', null, ['error' => 'something wrong']);
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  Kasbon $kasbon
     * @return \Illuminate\Http\Response
     */
    public function update(Kasbon $kasbon, Request $request)
    {
        if (!auth()->user()->can('kasbon.update') || !$request->ajax()) {
            abort(403, 'Unauthorized action.');
        }

        try {
            $validator = Validator::make($request->all(), $this->rules());

            if ($validator->fails()) {
                return $this->buildRes->RESPONSE_REQ('error', null, $validator->errors());
            } else {
                $kasbon_data = $request->only(['employee_id', 'date', 'kasbon', 'notes']);
                $kasbon_data['business_id'] = Session::get('business_id');
                $kasbon_data['updated_user'] = auth()->user()->id;
                $kasbon_data['kasbon'] = str_replace('.', '', $kasbon_data['kasbon']);

                $kasbon->update($kasbon_data);

                return $this->buildRes->RESPONSE_REQ('success', null, ['success' => ['Update kasbon succesfully']]);
            }
        } catch (\Exception $e) {
            Log::emergency("File:" . $e->getFile() . "Line:" . $e->getLine() . "Message:" . $e->getMessage());

            return $this->buildRes->RESPONSE_REQ('error', null, ['error' => 'something wrong']);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  Kasbon $kasbon
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function destroy(Kasbon $kasbon, Request $request)
    {
        if (!auth()->user()->can('kasbon.delete') || !$request->ajax()) {
            abort(403, 'Unauthorized action.');
        }

        try {
            $kasbon->delete();

            return $this->buildRes->RESPONSE_REQ('success', null, ['success' => ['Delete kasbon succesfully']]);
        } catch (\Exception $e) {
            return $this->buildRes->RESPONSE_REQ('error', null, ['error' => 'something wrong']);
        }
    }

    /**
     * Rules validation group.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'employee_id' => 'required|string|max:255',
            'date' => 'required',
            'kasbon' => 'required',
        ];
    }
}
