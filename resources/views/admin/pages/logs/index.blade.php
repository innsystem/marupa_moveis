@extends('admin.base')

@section('title', 'Log do Sistema')

@section('content')
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h4 class="mb-0"><i class="fas fa-file-alt"></i> Log do Sistema</h4>
        <div>
            <a href="{{ route('admin.logs.download') }}" class="btn btn-outline-primary btn-sm me-2" title="Download">
                <i class="fas fa-download"></i> Download
            </a>
            <form action="{{ route('admin.logs.clear') }}" method="POST" class="d-inline">
                @csrf
                <button type="submit" class="btn btn-outline-danger btn-sm" title="Limpar" onclick="return confirm('Tem certeza que deseja limpar o log?')">
                    <i class="fas fa-trash-alt"></i> Limpar
                </button>
            </form>
        </div>
    </div>
    <div class="mb-2 text-muted small">
        Tamanho do log: <strong>{{ number_format($logSize / 1024, 2, ',', '.') }} KB</strong>
    </div>
    <div class="card">
        <div class="card-body">
            <textarea class="form-control bg-light" rows="20" readonly style="font-family: monospace; font-size: 13px;">{{ $logContent }}</textarea>
        </div>
    </div>
    @if(session('success'))
    <div class="alert alert-success mt-3">{{ session('success') }}</div>
    @endif
    @if(session('error'))
    <div class="alert alert-danger mt-3">{{ session('error') }}</div>
    @endif
</div>
@endsection