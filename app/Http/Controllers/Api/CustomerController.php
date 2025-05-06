<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\CustomerService;
use Illuminate\Http\Request;

class CustomerController extends Controller
{
    protected $customerService;

    public function __construct(CustomerService $customerService)
    {
        $this->customerService = $customerService;
    }

    public function index(Request $request)
    {
        $filters = $request->only(['name', 'status', 'start_date', 'end_date']);
        $page = $request->input('page', 1);
        $perPage = $request->input('per_page', 10);

        $data = $this->customerService->getAllCustomers($filters, $perPage);
        return response()->json($data);
    }

    public function show($id)
    {
        $customer = $this->customerService->getCustomerById($id);
        $customerInvoices = $customer->invoices;
        $preferences = $this->customerService->getCustomerPreferences($id);

        return response()->json([
            'customer' => $customer,
            'invoices' => $customerInvoices,
            'preferences' => $preferences
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required',
            'email' => 'required|unique:users,email',
            'password' => 'required',
            'document' => 'nullable',
            'ddi' => 'required',
            'phone' => 'nullable',
            'payment_default' => 'nullable|in:pix,boleto,credit_card',
        ]);

        $customer = $this->customerService->createCustomer($data);
        return response()->json($customer, 201);
    }

    public function update(Request $request, $id)
    {
        $data = $request->validate([
            'name' => 'required',
            'email' => "required|unique:users,email,$id,id",
            'password' => 'nullable',
            'document' => 'nullable',
            'ddi' => 'required',
            'phone' => 'nullable',
            'payment_default' => 'nullable|in:pix,boleto,credit_card',
        ]);

        $customer = $this->customerService->updateCustomer($id, $data);
        return response()->json($customer);
    }

    public function destroy($id)
    {
        $this->customerService->deleteCustomer($id);
        return response()->json(['message' => 'Cliente excluído com sucesso']);
    }

    // Métodos para endereços
    public function getAddresses($id)
    {
        $customer = $this->customerService->getCustomerById($id);
        return response()->json($customer->addresses);
    }

    public function storeAddress(Request $request, $id)
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

        $address = $this->customerService->addCustomerAddress($id, $data);
        return response()->json($address, 201);
    }

    public function updateAddress(Request $request, $id, $addressId)
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

        $address = $this->customerService->updateCustomerAddress($id, $addressId, $data);
        return response()->json($address);
    }

    public function destroyAddress($id, $addressId)
    {
        $this->customerService->deleteCustomerAddress($id, $addressId);
        return response()->json(['message' => 'Endereço excluído com sucesso']);
    }

    public function setDefaultAddress(Request $request, $id, $addressId)
    {
        // Adiciona is_default = true para o endereço selecionado
        $this->customerService->updateCustomerAddress($id, $addressId, ['is_default' => true]);
        
        // Obtém todos os outros endereços do cliente e seta is_default = false
        $addresses = $this->customerService->getCustomerAddresses($id);
        foreach ($addresses as $address) {
            if ($address->id != $addressId) {
                $this->customerService->updateCustomerAddress($id, $address->id, ['is_default' => false]);
            }
        }
        
        return response()->json(['message' => 'Endereço definido como padrão']);
    }

    // Métodos para preferências
    public function getPreferences($id)
    {
        $preferences = $this->customerService->getCustomerPreferences($id);
        return response()->json($preferences);
    }

    public function updatePreferences(Request $request, $id)
    {
        $data = $request->all();
        $preferences = $this->customerService->updateOrCreatePreferences($id, $data);
        return response()->json($preferences);
    }
} 