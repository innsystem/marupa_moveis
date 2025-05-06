@if(isset($results) && count($results) > 0)
<div class="accordion" id="integrationAccordion">
    @foreach($results as $type => $integrations)
    <div class="accordion-item">
        <h2 class="accordion-header" id="heading-{{ $type }}">
            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" 
                    data-bs-target="#collapse-{{ $type }}" aria-expanded="false" aria-controls="collapse-{{ $type }}">
                {{ $integrations->first()->type_translation }} ({{ count($integrations) }})
            </button>
        </h2>
        <div id="collapse-{{ $type }}" class="accordion-collapse collapse" aria-labelledby="heading-{{ $type }}" 
             data-bs-parent="#integrationAccordion">
            <div class="accordion-body">
                @foreach($integrations as $integration)
                <div id="row_integration_{{$integration->id}}" class="col-12 pb-2 mb-4 border-bottom rounded">
                    <div class="d-flex flex-wrap gap-2 align-items-center">
                        <div class="flex-grow-1 d-flex align-items-center">
                            <div>
                                <h5 class="h6 mb-1 fw-bold">
                                    @if($integration->status == 1)
                                    <span class="text-success fs-8"><i class="fa fa-circle"></i></span>
                                    @else
                                    <span class="text-danger fs-8"><i class="fa fa-circle"></i></span>
                                    @endif
                                    {{$integration->name}}
                                </h5>
                                <p class="mb-0">{{$integration->description}}</p>
                            </div>
                        </div>
                        <div>
                            <button type="button" class="btn btn-sm btn-info button-integrations-edit" data-integration-id="{{$integration->id}}">Configurar</button>
                        </div>
                    </div>
                </div><!-- col-12 -->
                @endforeach
            </div>
        </div>
    </div>
    @endforeach
</div>
@else
<div class="alert alert-warning mb-0">Nenhum resultado foi localizado...</div>
@endif
