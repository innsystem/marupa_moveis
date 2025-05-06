<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Http;
use App\Models\Status;
use App\Models\User;
use App\Models\UserAddress;
use Carbon\Carbon;
use App\Services\CustomerService;

class CustomersController extends Controller
{
    public $name = 'Cliente'; //  singular
    public $folder = 'admin.pages.customers';

    protected $customerService;

    public function __construct(CustomerService $customerService)
    {
        $this->customerService = $customerService;
    }

    public function index()
    {
        return view($this->folder . '.index');
    }

    public function load(Request $request)
    {
        $filters = $request->only(['name', 'status', 'date_range']);
        $page = $request->input('page', 1);
        $perPage = $request->input('per_page', 10);

        if (!empty($filters['date_range'])) {
            [$startDate, $endDate] = explode(' até ', $filters['date_range']);
            $filters['start_date'] = \Carbon\Carbon::createFromFormat('d/m/Y', $startDate)->format('Y-m-d');
            $filters['end_date'] = \Carbon\Carbon::createFromFormat('d/m/Y', $endDate)->format('Y-m-d');
        }

        $data = $this->customerService->getAllCustomers($filters, $perPage);

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
            'name' => 'required',
            'email' => 'required|unique:users,email',
            'password' => 'required',
            'document' => 'nullable',
            'ddi' => 'required',
            'phone' => 'required',
            'payment_default' => 'nullable|in:pix,boleto,credit_card',
        );
        $messages = array(
            'name.required' => 'Nome é obrigatório',
            'email.required' => 'E-mail é obrigatório',
            'email.unique' => 'E-mail já existe',
            'password.required' => 'Senha é obrigatória',
            'password.nullable' => 'Senha pode ser nula',
            'document.required' => 'Documento é obrigatório',
            'document.nullable' => 'Documento pode ser nulo',
            'ddi.required' => 'DDI é obrigatório',
            'phone.required' => 'Telefone é obrigatório',
            'payment_default.in' => 'Método de pagamento deve ser pix, boleto ou credit_card',
        );

        $validator = Validator::make($result, $rules, $messages);

        if ($validator->fails()) {
            return response()->json($validator->errors()->first(), 422);
        }

        $user = $this->customerService->createCustomer($result);

        return response()->json($this->name . ' adicionado com sucesso', 200);
    }

    public function show($id)
    {
        $customer = $this->customerService->getCustomerById($id);
        $customerInvoices = $customer->invoices;
        $preferences = $this->customerService->getCustomerPreferences($id);

        return view($this->folder . '.show', compact('customer', 'customerInvoices', 'preferences'));
    }

    public function edit($id)
    {
        $result = $this->customerService->getCustomerById($id);
        $statuses = Status::default();
        $preferences = $this->customerService->getCustomerPreferences($id);

        return view($this->folder . '.form', compact('result', 'statuses', 'preferences'));
    }

    public function update(Request $request, $id)
    {
        $result = $request->all();

        $rules = array(
            'name' => 'required',
            'email' => "required|unique:users,email,$id,id",
            'password' => 'nullable',
            'document' => 'nullable',
            'ddi' => 'required',
            'phone' => 'nullable',
            'payment_default' => 'nullable|in:pix,boleto,credit_card',
        );
        $messages = array(
            'name.required' => 'Nome é obrigatório',
            'email.required' => 'E-mail é obrigatório',
            'email.unique' => 'E-mail já existe',
            'password.required' => 'Senha é obrigatória',
            'password.nullable' => 'Senha pode ser nula',
            'document.required' => 'Documento é obrigatório',
            'document.nullable' => 'Documento pode ser nulo',
            'ddi.required' => 'DDI é obrigatório',
            'phone.required' => 'Telefone é obrigatório',
            'phone.nullable' => 'Telefone pode ser nulo',
            'payment_default.in' => 'Método de pagamento deve ser pix, boleto ou cartão de crédito',
        );

        $validator = Validator::make($result, $rules, $messages);

        if ($validator->fails()) {
            return response()->json($validator->errors()->first(), 422);
        }
        
        $user = $this->customerService->updateCustomer($id, $result);

        return response()->json($this->name . ' atualizado com sucesso', 200);
    }

    public function delete($id)
    {
        $this->customerService->deleteCustomer($id);

        return response()->json($this->name . ' excluído com sucesso', 200);
    }

    public function getAddresses($id)
    {
        $customer = $this->customerService->getCustomerById($id);
        $addresses = $customer->addresses;
        $customerId = $id;
        $customerName = $customer->name;

        return view($this->folder . '.addresses', compact('addresses', 'customerId', 'customerName'));
    }

    public function createAddress($id)
    {
        $customer = $this->customerService->getCustomerById($id);
        $customerId = $id;
        $customerName = $customer->name;

        return view($this->folder . '.address_form', compact('customerId', 'customerName'));
    }

    public function storeAddress(Request $request, $id)
    {
        $result = $request->all();

        $rules = array(
            'zipcode' => 'required|string|max:10',
            'street' => 'required|string|max:255',
            'number' => 'required|string|max:20',
            'complement' => 'nullable|string|max:100',
            'district' => 'required|string|max:100',
            'city' => 'required|string|max:100',
            'state' => 'required|string|max:2',
        );
        $messages = array(
            'zipcode.required' => 'CEP é obrigatório',
            'street.required' => 'Rua é obrigatória',
            'number.required' => 'Número é obrigatório',
            'district.required' => 'Bairro é obrigatório',
            'city.required' => 'Cidade é obrigatória',
            'state.required' => 'Estado é obrigatório',
        );

        $validator = Validator::make($result, $rules, $messages);

        if ($validator->fails()) {
            return response()->json($validator->errors()->first(), 422);
        }

        $result['user_id'] = $id;
        UserAddress::create($result);

        return response()->json('Endereço adicionado com sucesso', 200);
    }

    public function editAddress($id, $addressId)
    {
        $customer = $this->customerService->getCustomerById($id);
        $customerId = $id;
        $customerName = $customer->name;
        $address = UserAddress::findOrFail($addressId);

        return view($this->folder . '.address_form', compact('customerId', 'customerName', 'address'));
    }

    public function updateAddress(Request $request, $id, $addressId)
    {
        $result = $request->all();

        $rules = array(
            'zipcode' => 'required|string|max:10',
            'street' => 'required|string|max:255',
            'number' => 'required|string|max:20',
            'complement' => 'nullable|string|max:100',
            'district' => 'required|string|max:100',
            'city' => 'required|string|max:100',
            'state' => 'required|string|max:2',
        );
        $messages = array(
            'zipcode.required' => 'CEP é obrigatório',
            'street.required' => 'Rua é obrigatória',
            'number.required' => 'Número é obrigatório',
            'district.required' => 'Bairro é obrigatório',
            'city.required' => 'Cidade é obrigatória',
            'state.required' => 'Estado é obrigatório',
        );

        $validator = Validator::make($result, $rules, $messages);

        if ($validator->fails()) {
            return response()->json($validator->errors()->first(), 422);
        }

        $address = UserAddress::findOrFail($addressId);
        $address->update($result);

        return response()->json('Endereço atualizado com sucesso', 200);
    }

    public function deleteAddress($id, $addressId)
    {
        $address = UserAddress::findOrFail($addressId);
        $address->delete();

        return response()->json('Endereço excluído com sucesso', 200);
    }

    public function setDefaultAddress(Request $request, $customerId, $addressId)
    {
        // Desmarcar todos os outros endereços como padrão
        \App\Models\UserAddress::where('user_id', $customerId)->update(['is_default' => false]);
        // Marcar o endereço selecionado como padrão
        $address = \App\Models\UserAddress::where('user_id', $customerId)->findOrFail($addressId);
        $address->is_default = true;
        $address->save();
        return response()->json('Endereço padrão atualizado com sucesso!', 200);
    }
    
    public function updatePreferences(Request $request, $id)
    {
        $result = $request->all();
        
        $rules = [
            'payment_default' => 'required|in:pix,boleto,credit_card',
        ];
        
        $messages = [
            'payment_default.required' => 'Método de pagamento padrão é obrigatório',
            'payment_default.in' => 'Método de pagamento deve ser pix, boleto ou credit_card',
        ];
        
        $validator = Validator::make($result, $rules, $messages);
        
        if ($validator->fails()) {
            return response()->json($validator->errors()->first(), 422);
        }
        
        $this->customerService->updateOrCreatePreferences($id, $result);
        
        return response()->json('Preferências atualizadas com sucesso', 200);
    }
}
