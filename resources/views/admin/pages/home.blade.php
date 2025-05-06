@extends('admin.base')

@section('title', 'Bem-vindos')

@section('content')
<div class="container">
    <div class="py-2 gap-2 d-flex align-items-sm-center flex-sm-row flex-column">
        <div class="flex-grow-1">
            <h4 class="fs-18 fw-semibold m-0">@yield('title')</h4>
        </div>
    </div>
    <div class="row mt-4">
        <!-- Métricas Principais do Sistema -->
        <div class="col-md-3">
            <div class="card border-primary">
                <div class="card-body">
                    <h5 class="card-title">Usuários</h5>
                    <p class="card-text fs-4 fw-bold text-primary">{{ $metrics['total_usuarios'] ?? '-' }}</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-success">
                <div class="card-body">
                    <h5 class="card-title">Clientes</h5>
                    <p class="card-text fs-4 fw-bold text-success">{{ $metrics['total_clientes'] ?? '-' }}</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-info">
                <div class="card-body">
                    <h5 class="card-title">Serviços</h5>
                    <p class="card-text fs-4 fw-bold text-info">{{ $metrics['total_servicos'] ?? '-' }}</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-warning">
                <div class="card-body">
                    <h5 class="card-title">Faturas</h5>
                    <p class="card-text fs-4 fw-bold text-warning">{{ $metrics['total_faturas'] ?? '-' }}</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-success">
                <div class="card-body">
                    <h5 class="card-title">Faturas Pagas</h5>
                    <p class="card-text fs-4 fw-bold text-success">{{ $metrics['faturas_pagas'] ?? '-' }}</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-danger">
                <div class="card-body">
                    <h5 class="card-title">Faturas Pendentes</h5>
                    <p class="card-text fs-4 fw-bold text-danger">{{ $metrics['faturas_pendentes'] ?? '-' }}</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-dark">
                <div class="card-body">
                    <h5 class="card-title">Valor Total Faturas</h5>
                    <p class="card-text fs-5 fw-bold text-dark">R$ {{ number_format($metrics['valor_total_faturas'] ?? 0, 2, ',', '.') }}</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-primary">
                <div class="card-body">
                    <h5 class="card-title">Valor Recebido</h5>
                    <p class="card-text fs-5 fw-bold text-primary">R$ {{ number_format($metrics['valor_recebido'] ?? 0, 2, ',', '.') }}</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-danger">
                <div class="card-body">
                    <h5 class="card-title">Valor Pago</h5>
                    <p class="card-text fs-5 fw-bold text-danger">R$ {{ number_format($metrics['valor_pago'] ?? 0, 2, ',', '.') }}</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-secondary">
                <div class="card-body">
                    <h5 class="card-title">Transações</h5>
                    <p class="card-text fs-4 fw-bold text-secondary">{{ $metrics['total_transacoes'] ?? '-' }}</p>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <!-- Gráfico de Faturas dos Últimos 6 Meses -->
        <div class="col-md-12">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Faturas Emitidas e Pagas - Últimos 6 Meses</h5>
                    <div id="dashboard-data"
                        data-meses='@json($chartFaturas["meses"])'
                        data-emitidas='@json($chartFaturas["emitidas"])'
                        data-pagas='@json($chartFaturas["pagas"])'
                        style="display:none;"></div>
                    <div id="chart-faturas-mes"></div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('pageMODAL')
@endsection

@section('pageCSS')
<link rel="stylesheet" href="/tpl_dashboard/vendor/apexcharts/apexcharts.css">
@endsection

@section('pageJS')
<script src="/tpl_dashboard/vendor/apexcharts/apexcharts.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        var el = document.getElementById('dashboard-data');
        var meses = JSON.parse(el.dataset.meses);
        var faturasEmitidas = JSON.parse(el.dataset.emitidas);
        var faturasPagas = JSON.parse(el.dataset.pagas);

        var options = {
            chart: {
                type: 'bar',
                height: 350,
                toolbar: {
                    show: false
                }
            },
            plotOptions: {
                bar: {
                    horizontal: false,
                    columnWidth: '55%',
                    endingShape: 'rounded'
                },
            },
            dataLabels: {
                enabled: false
            },
            stroke: {
                show: true,
                width: 2,
                colors: ['transparent']
            },
            series: [{
                    name: 'Emitidas',
                    data: faturasEmitidas
                },
                {
                    name: 'Pagas',
                    data: faturasPagas
                }
            ],
            xaxis: {
                categories: meses,
            },
            yaxis: {
                title: {
                    text: 'Quantidade'
                }
            },
            fill: {
                opacity: 1
            },
            tooltip: {
                y: {
                    formatter: function(val) {
                        return val + " faturas";
                    }
                }
            },
            colors: ['#3b82f6', '#10b981']
        };

        var chart = new ApexCharts(document.querySelector("#chart-faturas-mes"), options);
        chart.render();
    });
</script>
@endsection