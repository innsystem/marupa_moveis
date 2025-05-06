<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Integration;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Http;
use App\Models\Status;
use App\Models\Transaction;
use Carbon\Carbon;
use App\Services\TransactionService;

class TransactionsController extends Controller
{
    public $name = 'Transação'; //  singular
    public $folder = 'admin.pages.transactions';

    protected $transactionService;

    public function __construct(TransactionService $transactionService)
    {
        $this->transactionService = $transactionService;
    }

    public function index()
    {
        return view($this->folder . '.index');
    }

    public function load(Request $request)
    {
        $query = [];
        $filters = $request->only(['description', 'invoice_id', 'date_range']);
        $filters['per_page'] = $request->input('per_page', 10);

        $data = $this->transactionService->getAllTransactions($filters);

        return view($this->folder . '.index_load', $data);
    }

    public function create()
    {
        $statuses = Status::default();

        return view($this->folder . '.form', compact('statuses'));
    }

    public function store(Request $request)
    {
        $result = $request->all();

        $rules = array(
            'type' => 'required',
            'amount' => 'required',
            'description' => 'required',
            'date' => 'required',
        );
        $messages = array(
            'type.required' => 'Tipo de Entrada é obrigatório',
            'amount.required' => 'Valor Total é obrigatório',
            'description.required' => 'Descrição é obrigatório',
            'date.required' => 'Data da Transação é obrigatório',
        );

        $validator = Validator::make($result, $rules, $messages);

        if ($validator->fails()) {
            return response()->json($validator->errors()->first(), 422);
        }

        $getPagamentoNoLocal = Integration::where('slug', 'pagamento-no-local')->first();

        if ($getPagamentoNoLocal) {
            $result['integration_id'] = $getPagamentoNoLocal->id;
        }
        
        if (!empty($result['date'])) {
			$result['date'] = Carbon::createFromFormat('d/m/Y H:i', $result['date'])->format('Y-m-d H:i');
		}

        $transaction = $this->transactionService->createTransaction($result);

        return response()->json($this->name . ' adicionada com sucesso', 200);
    }

    public function edit($id)
    {
        $result = $this->transactionService->getTransactionById($id);
        $statuses = Status::default();

        return view($this->folder . '.form', compact('result', 'statuses'));
    }

    public function update(Request $request, $id)
    {
        $result = $request->all();

        // 'email'         => "unique:transactions,email,$id,id",
        $rules = array(
            'type' => 'required',
            'amount' => 'required',
            'description' => 'required',
            'date' => 'required',
        );
        $messages = array(
            'type.required' => 'Tipo de Entrada é obrigatório',
            'amount.required' => 'Valor Total é obrigatório',
            'description.required' => 'Descrição é obrigatório',
            'date.required' => 'Data da Transação é obrigatório',
        );

        $validator = Validator::make($result, $rules, $messages);

        if ($validator->fails()) {
            return response()->json($validator->errors()->first(), 422);
        }

        $getPagamentoNoLocal = Integration::where('slug', 'pagamento-no-local')->first();

        if ($getPagamentoNoLocal) {
            $result['integration_id'] = $getPagamentoNoLocal->id;
        }
        
        if (!empty($result['date'])) {
			$result['date'] = Carbon::createFromFormat('d/m/Y H:i', $result['date'])->format('Y-m-d H:i');
		}

        $transaction = $this->transactionService->updateTransaction($id, $result);

        return response()->json($this->name . ' atualizada com sucesso', 200);
    }

    public function delete($id)
    {
        $this->transactionService->deleteTransaction($id);

        return response()->json($this->name . ' excluída com sucesso', 200);
    }
}
