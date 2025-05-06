<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Http;
use App\Models\Status;
use App\Models\BankAccount;
use Carbon\Carbon;
use App\Services\BankAccountService;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;

class BankAccountController extends Controller
{
    public $name = 'Contas Bancárias'; //  singular
    public $folder = 'admin.pages.bank_accounts';

    protected $bank_accountService;

    public function __construct(BankAccountService $bank_accountService)
    {
        $this->bank_accountService = $bank_accountService;
    }

    public function index()
    {
        return view($this->folder . '.index');
    }

    public function load(Request $request)
    {
        $query = [];
        $filters = $request->only(['name', 'status', 'date_range']);

        $user = Auth::user();
        $filters['user_id'] = $user->id;

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

        $results = $this->bank_accountService->getAllBankAccount($filters);

        return view($this->folder . '.index_load', compact('results'));
    }

    public function create()
    {
        $statuses = Status::default();

        return view($this->folder . '.form', compact('statuses'));
    }

    public function store(Request $request)
    {
        $result = $request->all();
        $user = Auth::user();
        $result['user_id'] = $user->id;

        $rules = array(
            'bank_name' => 'required',
            'saldo' => 'required',
            'account_type' => 'required',
        );
        $messages = array(
            'bank_name.required' => 'Nome do banco é obrigatório',
            'saldo.required' => 'Saldo é obrigatório',
            'account_type.required' => 'Tipo de conta é obrigatório',
        );

        $validator = Validator::make($result, $rules, $messages);

        if ($validator->fails()) {
            return response()->json($validator->errors()->first(), 422);
        }

        $bank_account = $this->bank_accountService->createBankAccount($result);

        return response()->json($this->name . ' adicionado com sucesso', 200);
    }

    public function edit($id)
    {
        $result = $this->bank_accountService->getBankAccountById($id);
        $statuses = Status::default();

        return view($this->folder . '.form', compact('result', 'statuses'));
    }

    public function update(Request $request, $id)
    {
        $result = $request->all();

        // 'email'         => "unique:bank_accounts,email,$id,id",
        $rules = array(
            'bank_name' => 'required',
            'saldo' => 'required',
            'account_type' => 'required',
        );
        $messages = array(
            'bank_name.required' => 'Nome do banco é obrigatório',
            'saldo.required' => 'Saldo é obrigatório',
            'account_type.required' => 'Tipo de conta é obrigatório',
        );

        $validator = Validator::make($result, $rules, $messages);

        if ($validator->fails()) {
            return response()->json($validator->errors()->first(), 422);
        }

        $bank_account = $this->bank_accountService->updateBankAccount($id, $result);

        return response()->json($this->name . ' atualizado com sucesso', 200);
    }

    public function delete($id)
    {
        $this->bank_accountService->deleteBankAccount($id);

        return response()->json($this->name . ' excluído com sucesso', 200);
    }

    public function usersSearch(Request $request)
    {
        $q = $request->input('q');
        $users = \App\Models\User::query()
            ->where('name', 'like', "%{$q}%")
            ->orWhere('email', 'like', "%{$q}%")
            ->limit(20)
            ->get(['id', 'name', 'email']);
        return response()->json($users);
    }
}
