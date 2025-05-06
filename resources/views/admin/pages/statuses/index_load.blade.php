@if(!empty($results))
<div class="d-flex flex-wrap gap-3">
    @foreach($results as $type => $statuses)
    <div class="col border rounded bg-gray p-1">
        <h1 class="h5">{{ucfirst($type)}}</h1>
        @foreach($statuses as $status)
        <div id="row_status_{{$status->id}}" class="col-12 pb-2 mb-2 border-bottom rounded">
            <div class="d-flex flex-wrap gap-2 align-items-center">
                <div class="flex-grow-1 d-flex align-items-center">
                    <div>
                        <h5 class="h6 mb-1 fw-bold badge {{$status->color}}">
                            @if(isset($status->icon))
                            <i class="{{$status->icon}}"></i>
                            @endif
                            {{$status->name}}
                        </h5>
                        <p class="fs-7 text-muted mb-0">{{$status->description}}</p>
                        <a href="javascript:;" class="text-info fs-8 button-statuses-edit" data-status-id="{{$status->id}}">Editar</a>
                        <!-- <button type="button" class="btn btn-sm btn-danger button-statuses-delete" data-status-id="{{$status->id}}" data-status-name="{{$status->name}}">Apagar</button> -->
                    </div>
                </div>
            </div>
        </div><!-- col-12 -->
        @endforeach
    </div>
    @endforeach
</div>
@else
<div class="alert alert-warning mb-0">Não localizamos nenhum resultado.</div>
@endif