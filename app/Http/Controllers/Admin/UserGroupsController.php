<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Permission;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Http;
use App\Models\Status;
use App\Models\UserGroup;
use Carbon\Carbon;
use App\Services\UserGroupService;

class UserGroupsController extends Controller
{
    public $name = 'Grupo'; //  singular
    public $folder = 'admin.pages.user_groups';

    protected $usergroupService;

    public function __construct(UserGroupService $usergroupService)
    {
        $this->usergroupService = $usergroupService;
    }

    public function index()
    {
        return view($this->folder . '.index');
    }

    public function load(Request $request)
    {
        $query = [];
        $filters = $request->only(['name', 'status', 'date_range']);

        if (!empty($filters['name'])) {
            $query['name'] = $filters['name'];
        }

        if (!empty($filters['status'])) {
            $query['status'] = $filters['status'];
        }

        if (!empty($filters['date_range'])) {
            [$startDate, $endDate] = explode(' até ', $filters['date_range']);
            $query['start_date'] = Carbon::createFromFormat('d/m/Y', $startDate)->format('Y-m-d');
            $query['end_date'] = Carbon::createFromFormat('d/m/Y', $endDate)->format('Y-m-d');
        }

        $results = $this->usergroupService->getAllUserGroups($filters);

        return view($this->folder . '.index_load', compact('results'));
    }

    public function create()
    {
        $statuses = Status::default();
        
        $permissions = Permission::all()->groupBy('type');

        $groupPermissions = [];

        return view($this->folder . '.form', compact('statuses', 'permissions', 'groupPermissions'));
    }

    public function store(Request $request)
    {
        $result = $request->all();

        $rules = array(
            'name' => 'required|unique:user_groups,name',
        );
        $messages = array(
            'name.required' => 'name é obrigatório',
            'name.unique' => 'name já existe',
        );

        $validator = Validator::make($result, $rules, $messages);

        if ($validator->fails()) {
            return response()->json($validator->errors()->first(), 422);
        }

        $usergroup = $this->usergroupService->createUserGroup($result);

        return response()->json($this->name . ' adicionado com sucesso', 200);
    }

    public function edit($id)
    {
        $result = $this->usergroupService->getUserGroupById($id);
        $statuses = Status::default();

        $permissions = Permission::all()->groupBy('type');

        $groupPermissions = $result->permissions->pluck('id')->toArray();

        return view($this->folder . '.form', compact('result', 'statuses', 'permissions', 'groupPermissions'));
    }

    public function update(Request $request, $id)
    {
        $result = $request->all();

        $rules = array(
            'name' => "required|unique:user_groups,name,$id,id",
        );
        $messages = array(
            'name.required' => 'name é obrigatório',
            'name.unique' => 'name já existe',
        );

        $validator = Validator::make($result, $rules, $messages);

        if ($validator->fails()) {
            return response()->json($validator->errors()->first(), 422);
        }

        $usergroup = $this->usergroupService->updateUserGroup($id, $result);

        if (isset($result['permissions'])) {
            $usergroup->permissions()->sync($result['permissions']);  // Atualiza as permissões associadas
        }

        return response()->json($this->name . ' atualizado com sucesso', 200);
    }

    public function delete($id)
    {
        try {
            $this->usergroupService->deleteUserGroup($id);
            return response()->json($this->name . ' excluído com sucesso', 200);
        } catch (\Exception $e) {
            return response()->json($e->getMessage(), 400); // Retorna o erro com status 400
        }
    }
}
