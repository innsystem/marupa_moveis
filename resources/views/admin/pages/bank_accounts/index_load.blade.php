@if($results->count() > 0)
    @foreach($results as $bank_account)
    <div id="row_bank_account_{{$bank_account->id}}" class="col-12 pb-2 mb-4 border-bottom rounded">
        <div class="row">
            <div class="col-md-8">
                <p>{{ $bank_account->bank_name }}</p>
                <p class="small m-0">{{ $bank_account->account_type }}</p>
                <p class="small m-0">R$ {{ $bank_account->saldo }}</p>
            </div>
            <div class="col-md-4 text-end">
                <button type="button" class="btn btn-sm btn-outline-success button-bank_account-edit" data-bank_account-id="{{ $bank_account->id }}">
                    <i class="ri-pencil-line"></i>
                </button>
                <button type="button" class="btn btn-sm btn-outline-danger button-bank_account-delete" data-bank_account-id="{{ $bank_account->id }}" data-bank_account-name="{{ $bank_account->bank_name }}">
                    <i class="ri-delete-bin-line"></i>
                </button>
            </div>
        </div>
    </div>
    @endforeach
@else
<div class="col-12 p-4">
    <p class="text-center mb-0">Nenhum registro encontrado</p>
</div>
@endif