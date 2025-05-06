<div class="row">
    <div class="col-md-4">
        <div class="card shadow">
            <div class="card-body">
                <h6 class="text-muted">TOTAL</h6>
                <h3 class="fw-bold">R$ {{ number_format($totalAmount, 2, ',', '.') }}</h3>
                <span class="badge bg-gray text-muted">{{ $totalInvoices }} fatura(s)</span>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card shadow">
            <div class="card-body">
                <h6 class="text-muted">PAGO</h6>
                <h3 class="text-success fw-bold">R$ {{ number_format($paidAmount, 2, ',', '.') }}</h3>
                <span class="badge bg-gray text-muted">{{ $paidInvoices }} fatura(s)</span>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card shadow">
            <div class="card-body">
                <h6 class="text-muted">PENDENTES</h6>
                <h3 class="text-info fw-bold">R$ {{ number_format($unpaidAmount, 2, ',', '.') }}</h3>
                <span class="badge bg-gray text-muted">{{ $unpaidInvoices }} fatura(s)</span>
            </div>
        </div>
    </div>
</div>

<!-- Faturas Table -->
<div class="card shadow">
    <div class="card-body">
        @include('components.invoices_table', ['invoices' => $invoices])
    </div>
</div>

{{-- Paginação --}}
@include('components.pagination', ['paginator' => $invoices, 'resourceName' => 'faturas', 'totalAll' => $totalInvoices])