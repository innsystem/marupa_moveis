@if(isset($results) && count($results) > 0)
@foreach($results as $testimonial)
<div id="row_testimonial_{{$testimonial->id}}" class="col-12 pb-2 mb-4 border-bottom rounded">
    <div class="d-flex flex-wrap gap-2 align-items-center">
        <div>
            <img src="{{ $testimonial->avatar ? asset('storage/' . $testimonial->avatar) : asset('galerias/avatares/sem_foto.jpg') }}" alt="" class="avatar-md rounded-circle">
        </div>
        <div class="flex-grow-1">
            <h4 class="h4 mb-1 fw-bold">{{$testimonial->name}}</h4>
            <blockquote class="mb-0">{{ Str::limit($testimonial->content, 80) }}</blockquote>
            <div class="d-flex align-items-center mb-1">
                <!-- Renderizando as estrelas -->
                @for($i = 1; $i <= 5; $i++)
                    <i class="fas fa-star {{ $i <= $testimonial->rating ? 'text-warning' : 'text-muted' }}"></i>
                @endfor
            </div>
        </div>
        <div>
            <button type="button" class="btn btn-sm btn-info button-testimonials-edit" data-testimonial-id="{{$testimonial->id}}">Editar</button>
            <button type="button" class="btn btn-sm btn-danger button-testimonials-delete" data-testimonial-id="{{$testimonial->id}}" data-testimonial-name="{{$testimonial->name}}">Apagar</button>
        </div>
    </div>
</div>
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