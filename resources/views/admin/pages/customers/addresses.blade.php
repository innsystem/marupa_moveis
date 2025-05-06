<div class="d-flex justify-content-between align-items-center mb-3">
    <h5 class="mb-0">Endereços</h5>
    <button type="button" class="btn btn-success btn-sm button-addresses-create" data-customer-id="{{ $customerId }}" data-customer-name="{{ $customerName }}">
        <i class="fa fa-plus"></i> Novo Endereço
    </button>
</div>

<div class="row">
    @forelse($addresses as $address)
    <div class="col-12 mb-3">
        <div class="card shadow-sm border-light">
            <div class="card-body">
                <h6 class="card-title">Endereço #{{ $address->id }}</h6>
                <ul class="list-group list-group-flush">
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        <span><i class="fa fa-map-pin me-2"></i> CEP:</span>
                        <span>{{ $address->zipcode }}</span>
                    </li>
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        <span><i class="fa fa-road me-2"></i> Rua:</span>
                        <span>{{ $address->street }}, Nº {{ $address->number }}</span>
                    </li>
                    @if($address->complement)
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        <span><i class="fa fa-home me-2"></i> Complemento:</span>
                        <span>{{ $address->complement }}</span>
                    </li>
                    @endif
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        <span><i class="fa fa-map-marker me-2"></i> Bairro / Cidade / Estado:</span>
                        <span>{{ $address->district }}, {{ $address->city }} - {{ $address->state }}</span>
                    </li>
                </ul>
                <div class="mt-2 d-flex justify-content-between align-items-center gap-2">
                    <div>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input set-default-address" type="radio" name="default_address" id="default_address_{{ $address->id }}" value="{{ $address->id }}" data-customer-id="{{ $customerId }}" @if($address->is_default) checked @endif>
                            <label class="form-check-label" for="default_address_{{ $address->id }}">
                                @if($address->is_default)
                                    <span class="badge bg-success">Padrão</span>
                                @else
                                    Definir como padrão
                                @endif
                            </label>
                        </div>
                    </div>
                    <div>
                        <button type="button" class="btn btn-warning btn-sm button-addresses-edit" data-customer-id="{{ $customerId }}" data-address-id="{{ $address->id }}" data-customer-name="{{ $customerName }}">
                            <i class="fas fa-edit"></i> Editar
                        </button>
                        <button type="button" class="btn btn-danger btn-sm button-address-delete" data-customer-id="{{ $customerId }}" data-address-id="{{ $address->id }}">
                            <i class="fas fa-trash"></i> Remover
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @empty
    <div class="col-12">
        <div class="alert alert-info">Nenhum endereço cadastrado.</div>
    </div>
    @endforelse
</div>