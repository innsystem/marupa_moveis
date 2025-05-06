@if(isset($results) && count($results) > 0)
    @foreach($results[null] ?? [] as $category) 
        <div id="row_category_{{$category->id}}" class="col-12 pb-2 mb-4 border-bottom rounded">
            <div class="d-flex flex-wrap gap-2 align-items-center pb-2 mb-2 border-bottom">
                <div class="flex-grow-1 d-flex align-items-center">
                    <h5 class="h6 mb-1 fw-bold">{{$category->id . ' - ' . $category->name}}</h5>
                </div>
                <div>
                    <button type="button" class="btn btn-sm btn-info button-categories-edit" data-category-id="{{$category->id}}">Editar</button>
                    <button type="button" class="btn btn-sm btn-danger button-categories-delete" data-category-id="{{$category->id}}" data-category-name="{{$category->name}}">Apagar</button>
                </div>
            </div>
            
            @if(isset($results[$category->id]))
                <div class="mt-3 ms-4">
                    @foreach($results[$category->id] as $subCategory)
                        <div id="row_category_{{$subCategory->id}}" class="pb-2 mb-2 border-bottom">
                            <div class="d-flex flex-wrap gap-2 align-items-center">
                                <div class="flex-grow-1 d-flex align-items-center">
                                    <h6 class="mb-1">{{$subCategory->id . ' - ' . $subCategory->name}}</h6>
                                </div>
                                <div>
                                    <button type="button" class="btn btn-sm btn-info button-categories-edit" data-category-id="{{$subCategory->id}}">Editar</button>
                                    <button type="button" class="btn btn-sm btn-danger button-categories-delete" data-category-id="{{$subCategory->id}}" data-category-name="{{$subCategory->name}}">Apagar</button>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif

        </div>
    @endforeach
@else
    <div class="alert alert-warning mb-0">Nenhuma categoria encontrada...</div>
@endif
