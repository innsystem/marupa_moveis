@if(isset($results) && count($results) > 0)
@foreach($results as $slider)
<div id="row_slider_{{$slider->id}}" class="col-12 pb-2 mb-4 border-bottom rounded">
    <div class="d-flex flex-wrap gap-2 align-items-center">
        <div>
            <img src="{{ $slider->image ? asset('storage/' . $slider->image) : asset('galerias/avatares/sem_foto.jpg') }}" alt="" class="avatar-md rounded">
        </div>
        <div class="flex-grow-1 d-flex align-items-center">
            <div>
                <h5 class="h6 mb-1 fw-bold">{{$slider->title}}</h5>
            </div>
        </div>
        <div>
            <button type="button" class="btn btn-sm btn-info button-sliders-edit" data-slider-id="{{$slider->id}}">Editar</button>
            <button type="button" class="btn btn-sm btn-danger button-sliders-delete" data-slider-id="{{$slider->id}}" data-slider-name="{{$slider->name}}">Apagar</button>
        </div>
    </div>
</div><!-- col-12 -->
@endforeach

<div class="d-flex justify-content-center mt-4 pagination-container">
    <nav aria-label="Navegação da paginação">
        <ul class="pagination">
            @if($results->currentPage() > 1)
                <li class="page-item">
                    <button class="page-link pagination-link" data-page="{{ $results->currentPage() - 1 }}">Anterior</button>
                </li>
            @endif

            @for($i = 1; $i <= $results->lastPage(); $i++)
                <li class="page-item {{ $results->currentPage() == $i ? 'active' : '' }}">
                    <button class="page-link pagination-link" data-page="{{ $i }}">{{ $i }}</button>
                </li>
            @endfor

            @if($results->currentPage() < $results->lastPage())
                <li class="page-item">
                    <button class="page-link pagination-link" data-page="{{ $results->currentPage() + 1 }}">Próximo</button>
                </li>
            @endif
        </ul>
    </nav>
</div>

@else
<div class="alert alert-warning mb-0">Nenhum resultado foi localizado...</div>
@endif