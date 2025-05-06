@if(isset($results) && count($results) > 0)
    <div class="table-responsive">
        <table class="table table-striped table-hover">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Integração</th>
                    <th>Nome da Categoria</th>
                    <th>ID da Categoria</th>
                    <th>Comissão</th>
                    <th>Data de Criação</th>
                    <th>Ações</th>
                </tr>
            </thead>
            <tbody>
                @foreach($results as $category)
                <tr id="row_category_{{$category->id}}">
                    <td>{{ $category->id }}</td>
                    <td>{{ $category->integration->name }}</td>
                    <td>{{ $category->api_category_name }}</td>
                    <td>{{ $category->api_category_id }}</td>
                    <td>{{ $category->api_category_commission ?? 'N/A' }}</td>
                    <td>{{ \Carbon\Carbon::parse($category->created_at)->format('d/m/Y H:i') }}</td>
                    <td>
                        <button type="button" class="btn btn-sm btn-info button-integration-categories-edit" data-category-id="{{$category->id}}">Editar</button>
                        <button type="button" class="btn btn-sm btn-danger button-integration-categories-delete" data-category-id="{{$category->id}}" data-category-name="{{$category->api_category_name}}">Apagar</button>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
@else
    <div class="alert alert-warning mb-0">Nenhuma categoria de integração encontrada...</div>
@endif 