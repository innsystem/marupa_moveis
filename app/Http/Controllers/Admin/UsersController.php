<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Http;
use App\Models\Status;
use App\Models\User;
use App\Models\UserGroup;
use Carbon\Carbon;
use App\Services\UserService;

class UsersController extends Controller
{
    public $name = 'Administrador'; //  singular
    public $folder = 'admin.pages.users';

    protected $userService;

    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }

    public function index()
    {
        return view($this->folder . '.index');
    }

    public function load(Request $request)
    {
        $query = [];
        $filters = $request->only(['name', 'status', 'date_range']);

        $filters['user_group_id'] = [1,2]; // User Group Id for Developers and Administrators

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

        $results = $this->userService->getAllUsers($filters);

        return view($this->folder . '.index_load', compact('results'));
    }

    public function create()
    {
        $statuses = Status::default();

        $user_groups = UserGroup::all();

        return view($this->folder . '.form', compact('statuses', 'user_groups'));
    }

    public function store(Request $request)
    {
        $result = $request->all();

        $rules = array(
            'user_group_id' => 'required',
            'name' => 'required',
            'email' => 'required|unique:users,email',
            'password' => 'required',
            'document' => 'nullable',
            'phone' => 'nullable',
        );
        $messages = array(
            'user_group_id.required' => 'Grupo de usuário é obrigatório',
            'name.required' => 'Nome é obrigatório',
            'email.required' => 'E-mail é obrigatório',
            'email.unique' => 'E-mail já existe',
            'password.required' => 'Senha é obrigatória',
            'password.nullable' => 'Senha pode ser nula',
            'document.required' => 'Documento é obrigatório',
            'document.nullable' => 'Documento pode ser nulo',
            'phone.required' => 'Telefone é obrigatório',
            'phone.nullable' => 'Telefone pode ser nulo',
        );

        $validator = Validator::make($result, $rules, $messages);

        if ($validator->fails()) {
            return response()->json($validator->errors()->first(), 422);
        }

        $user = $this->userService->createUser($result);

        return response()->json($this->name . ' adicionado com sucesso', 200);
    }

    public function edit($id)
    {
        $result = $this->userService->getUserById($id);
        $statuses = Status::default();

        $user_groups = UserGroup::all();

        return view($this->folder . '.form', compact('result', 'statuses', 'user_groups'));
    }

    public function update(Request $request, $id)
    {
        $result = $request->all();

        $rules = array(
            'user_group_id' => 'required',
            'name' => 'required',
            'email' => "required|unique:users,email,$id,id",
            'password' => 'nullable',
            'document' => 'nullable',
            'phone' => 'nullable',
        );
        $messages = array(
            'user_group_id.required' => 'Grupo de usuário é obrigatório',
            'name.required' => 'Nome é obrigatório',
            'email.required' => 'E-mail é obrigatório',
            'email.unique' => 'E-mail já existe',
            'password.required' => 'Senha é obrigatória',
            'password.nullable' => 'Senha pode ser nula',
            'document.required' => 'Documento é obrigatório',
            'document.nullable' => 'Documento pode ser nulo',
            'phone.required' => 'Telefone é obrigatório',
            'phone.nullable' => 'Telefone pode ser nulo',
        );

        $validator = Validator::make($result, $rules, $messages);

        if ($validator->fails()) {
            return response()->json($validator->errors()->first(), 422);
        }

        $user = $this->userService->updateUser($id, $result);

        return response()->json($this->name . ' atualizado com sucesso', 200);
    }

    public function delete($id)
    {
        $this->userService->deleteUser($id);

        return response()->json($this->name . ' excluído com sucesso', 200);
    }

    public function addresses($userId)
    {
        $addresses = $this->userService->getUserAddresses($userId);
        return view('admin.users.addresses', compact('addresses', 'userId'));
    }

    public function showAddress($userId, $addressId)
    {
        $address = $this->userService->getUserAddressById($userId, $addressId);
        return view('admin.users.address_show', compact('address', 'userId'));
    }

    public function createAddress($userId)
    {
        return view('admin.users.address_create', compact('userId'));
    }

    public function storeAddress(Request $request, $userId)
    {
        $data = $request->validate([
            'zipcode' => 'required|string|max:10',
            'street' => 'required|string|max:255',
            'number' => 'required|string|max:20',
            'complement' => 'nullable|string|max:100',
            'district' => 'required|string|max:100',
            'city' => 'required|string|max:100',
            'state' => 'required|string|max:2',
        ]);
        $this->userService->addUserAddress($userId, $data);
        return redirect()->route('admin.users.addresses', $userId)->with('success', 'Endereço adicionado com sucesso!');
    }

    public function editAddress($userId, $addressId)
    {
        $address = $this->userService->getUserAddressById($userId, $addressId);
        return view('admin.users.address_edit', compact('address', 'userId'));
    }

    public function updateAddress(Request $request, $userId, $addressId)
    {
        $data = $request->validate([
            'zipcode' => 'required|string|max:10',
            'street' => 'required|string|max:255',
            'number' => 'required|string|max:20',
            'complement' => 'nullable|string|max:100',
            'district' => 'required|string|max:100',
            'city' => 'required|string|max:100',
            'state' => 'required|string|max:2',
        ]);
        $this->userService->updateUserAddress($userId, $addressId, $data);
        return redirect()->route('admin.users.addresses', $userId)->with('success', 'Endereço atualizado com sucesso!');
    }

    public function destroyAddress($userId, $addressId)
    {
        $this->userService->deleteUserAddress($userId, $addressId);
        return redirect()->route('admin.users.addresses', $userId)->with('success', 'Endereço removido com sucesso!');
    }
}
