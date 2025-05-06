<div class="table-responsive">
    <table class="table table-striped table-hover">
        <thead>
            <tr>
                <th>N°</th>
                @if(!isset($hideClientColumn))
                <th>Cliente</th>
                @endif
                <th>Criado em</th>
                <th>Venc. em</th>
                <th>Pago em</th>
                <th>Total</th>
                <th>Meio de Pag.</th>
                <th>Status</th>
                <th></th>
            </tr>
        </thead>
        <tbody>
            @if(isset($invoices) && count($invoices) > 0)
            @foreach($invoices as $invoice)
            <tr>
                <td><a href="{{ route('admin.invoices.show', [$invoice->id]) }}">{{ $invoice->id }}</a></td>
                @if(!isset($hideClientColumn))
                <td>{{ $invoice->user->name }}</td>
                @endif
                <td>{{ \Carbon\Carbon::createFromFormat('d/m/Y H:i', $invoice->created_at)->format('d/m/Y') }}</td>
                <td>{{ $invoice->due_at }}</td>
                <td>{{ $invoice->paid_at }}</td>
                <td><b>R$ {{ number_format($invoice->total, 2, ',', '.') }}</b></td>
                <td>
                    <img src="{{ asset('/galerias/icons/payments/'.$invoice->integration->slug.'.png') }}" alt="{{$invoice->integration->name}}" width="64">
                    <span class="ms-1 fs-4">
                        @if($invoice->method_type == 'pix')
                        <i class="ri-qr-code-line" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="PIX"></i>
                        @elseif($invoice->method_type == 'credit_card')
                        <i class="ri-bank-card-2-line" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="Cartão de Crédito"></i>
                        @else
                        <i class="ri-barcode-line" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="Boleto Bancário"></i>
                        @endif
                    </span>
                </td>
                @php
                $statusColors = [
                23 => 'bg-info', // Pendente (amarelo)
                24 => 'bg-success', // Pago (verde)
                25 => 'bg-danger', // Não Pago (vermelho)
                26 => 'bg-secondary',// Cancelado (cinza)
                27 => 'bg-dark', // Estornado (preto)
                28 => 'bg-info', // Aguardando Confirmação (azul claro)
                29 => 'bg-success', // Parcialmente Pago (azul)
                ];
                $badgeClass = $statusColors[$invoice->status] ?? 'bg-secondary'; // Caso algum status novo apareça
                @endphp
                <td><span class="badge {{ $badgeClass }}">{{ $invoice->status_name }}</span></td>
                <td>
                    <div class="dropdown mx-0">
                        <a href="#" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false" class="">
                            <i class="mdi mdi-dots-horizontal text-muted fs-20"></i>
                        </a>
                        <div class="dropdown-menu dropdown-menu-end">
                            <a href="{{ route('admin.invoices.show', [$invoice->id]) }}" target="_Blank" class="dropdown-item">Abrir Fatura <span class="float-end"><i class="fas fa-external-link-alt text-muted fs-7"></i></span></a>

                            @if(in_array($invoice->status, [23, 25, 28, 29]))
                            <a href="#" class="dropdown-item button-invoices-edit" data-invoice-id="{{$invoice->id}}">Editar Fatura</a>
                            @endif

                            @if(in_array($invoice->status, [23, 25, 28, 29]))
                            <a href="#" class="dropdown-item button-invoices-confirm-payment" data-invoice-id="{{$invoice->id}}">
                                Confirmar Pagamento
                            </a>
                            @endif
                            @if(in_array($invoice->status, [23, 28, 29]))
                            <a href="#" class="dropdown-item button-invoices-cancel" data-invoice-id="{{$invoice->id}}">
                                Cancelar Fatura
                            </a>
                            @endif
                            @if(in_array($invoice->status, [23, 25, 28, 29]))
                            <a href="#" class="dropdown-item button-invoices-send-reminder" data-invoice-id="{{$invoice->id}}">
                                Enviar Lembrete
                            </a>
                            @endif

                            <a href="#" class="dropdown-item text-danger fw-bold button-invoices-delete" data-invoice-id="{{$invoice->id}}">
                                <i class="fa fa-trash"></i> Excluir Fatura?
                            </a>
                        </div>
                    </div>
                </td>
            </tr>
            @endforeach
            @else
            <tr>
                <td colspan="{{ isset($hideClientColumn) ? '8' : '9' }}">
                    <div class="alert alert-warning mb-0">Nenhum resultado foi localizado...</div>
                </td>
            </tr>
            @endif
        </tbody>
    </table>
</div>