@if($customers->count() > 0)
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-centered mb-0">
                        <thead>
                            <tr>
                                <th>Nome</th>
                                <th>E-mail</th>
                                <th>Telefone</th>
                                <th>Cadastro</th>
                                <th>Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($customers as $result)
                            <tr id="row_customer_{{ $result->id }}">
                                <td><a href="{{ url('/admin/customers/' . $result->id . '/show') }}">{{ $result->name }}</a></td>
                                <td>{{ $result->email }}</td>
                                <td>{{ $result->ddi }} {{ $result->phone }}</td>
                                <td>
                                    {{ \Carbon\Carbon::parse($result->created_at)->diffForHumans() }}<br>
                                    <small>{{ \Carbon\Carbon::parse($result->created_at)->format('d/m/Y H:i') }}</small>
                                </td>
                                <td>
                                    <div class="d-flex gap-1">
                                        <a href="{{ url('/admin/customers/' . $result->id . '/show') }}" class="btn btn-secondary btn-sm" title="Detalhes">
                                            <i class="fa fa-book"></i> Detalhes
                                        </a>
                                        <a href="{{ route('admin.users.services', $result->id) }}" class="btn btn-success btn-sm" title="Serviços">
                                            <i class="fa fa-cogs"></i>
                                        </a>
                                        <button type="button" class="btn btn-primary btn-sm button-customer-addresses" data-customer-id="{{ $result->id }}" data-customer-name="{{ $result->name }}" title="Endereços">
                                            <i class="fa fa-map-marker-alt"></i>
                                        </button>
                                        <button type="button" class="btn btn-warning btn-sm button-customers-edit" data-customer-id="{{ $result->id }}" title="Editar">
                                            <i class="fa fa-edit"></i>
                                        </button>
                                        <button type="button" class="btn btn-danger btn-sm button-customers-delete" data-customer-id="{{ $result->id }}" data-customer-name="{{ $result->name }}" title="Excluir">
                                            <i class="fa fa-trash"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div> <!-- end card body -->
        </div>
    </div>
</div>

{{-- Faixa de itens e paginação --}}
@include('components.pagination', ['paginator' => $customers, 'resourceName' => 'clientes'])
@else
<div class="alert alert-info">
    Nenhum registro encontrado.
</div>
@endif