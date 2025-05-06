<div class="row">
    <div class="col-md-4">
        <div class="card shadow">
            <div class="card-body">
                <h6 class="text-muted">TOTAL</h6>
                <h3 class="fw-bold">R$ {{ number_format($totalAmount, 2, ',', '.') }}</h3>
                <span class="badge bg-gray text-muted">{{ $totalTransactions }} transações(s)</span>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card shadow">
            <div class="card-body">
                <h6 class="text-muted">Entradas</h6>
                <h3 class="text-success fw-bold">R$ {{ number_format($incomeAmount, 2, ',', '.') }}</h3>
                <span class="badge bg-gray text-muted">{{ $incomeTransactions }} transações(s)</span>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card shadow">
            <div class="card-body">
                <h6 class="text-muted">Despesas</h6>
                <h3 class="text-danger fw-bold">R$ {{ number_format($expenseAmount, 2, ',', '.') }}</h3>
                <span class="badge bg-gray text-muted">{{ $expenseTransactions }} transações(s)</span>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                @if(isset($transactions) && count($transactions) > 0)
                <div class="table-responsive">
                    <table class="table table-striped table-hover">
                        <thead>
                            <tr>
                                <th>N°</th>
                                <th>Descrição</th>
                                <th>Fatura N°</th>
                                <th>Data</th>
                                <th>Total</th>
                                <th>Taxas</th>
                                <th>Meio de Pag.</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($transactions as $transaction)
                            <tr id="row_transaction_{{$transaction->id}}">
                                <td>
                                    {{$transaction->id}}
                                </td>
                                <td>{{$transaction->description}}</td>
                                <td>
                                    @if(isset($transaction->invoice_id))
                                    <a href="{{ route('admin.invoices.show', $transaction->invoice_id) }}" target="_Blank">{{$transaction->invoice_id}}</a>
                                    @else
                                    -
                                    @endif
                                </td>
                                <td>{{$transaction->formatted_date}}</td>
                                <td>
                                    @if($transaction->type == 'income')
                                    <i class="ri-arrow-up-fill fs-6 fw-bold text-success"></i>
                                    @else
                                    <i class="ri-arrow-down-fill fs-6 fw-bold text-danger"></i>
                                    @endif
                                    <b>{{$transaction->formatted_amount}}</b>
                                </td>
                                <td>{{$transaction->formatted_gateway_fee}}</td>
                                <td>
                                    @if(isset($transaction->integration) && $transaction->integration->slug != '')
                                    <img src="{{ asset('/galerias/icons/payments/'.$transaction->integration->slug.'.png') }}" alt="{{$transaction->integration->name}}" width="64" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="{{$transaction->integration->name}}">
                                    @endif
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @else
                <div class="alert alert-warning mb-0">Nenhum resultado foi localizado...</div>
                @endif
            </div>
        </div>
    </div>
</div>

{{-- Paginação --}}
@include('components.pagination', ['paginator' => $transactions, 'resourceName' => 'transações'])