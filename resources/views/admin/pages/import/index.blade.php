@extends('admin.base')

@section('content')
<div class="content-wrapper">
    <div class="row">
        <div class="col-md-12 grid-margin stretch-card">
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title">Importação de Clientes, Serviços e Faturas</h4>
                    <p class="card-description">
                        Esta funcionalidade permite importar um arquivo JSON contendo dados de clientes, serviços e faturas.
                        A importação respeitará os IDs originais dos dados, o que pode sobrescrever registros existentes.
                    </p>

                    @if(session('success'))
                        <div class="alert alert-success">
                            {{ session('success') }}
                        </div>
                    @endif

                    @if(session('error'))
                        <div class="alert alert-danger">
                            {{ session('error') }}
                        </div>
                    @endif

                    <form action="{{ route('admin.import.data') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="form-group">
                            <label for="json_file">Arquivo JSON</label>
                            <input type="file" class="form-control" id="json_file" name="json_file" required accept=".json">
                            <small class="form-text text-muted">Selecione o arquivo JSON de exportação para importar.</small>
                        </div>
                        
                        <div class="alert alert-warning">
                            <h5>Atenção!</h5>
                            <p>Esta operação irá importar dados que podem substituir informações existentes no sistema. Certifique-se de ter um backup do banco de dados antes de prosseguir.</p>
                            <p>A estrutura do JSON deve seguir o formato específico esperado pelo sistema.</p>
                        </div>
                        
                        <button type="submit" class="btn btn-primary mt-3">Iniciar Importação</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 