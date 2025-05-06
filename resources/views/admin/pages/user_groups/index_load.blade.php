@if(isset($results) && count($results) > 0)
@foreach($results as $user_group)
<div id="row_user_group_{{$user_group->id}}" class="col-12 pb-2 mb-4 border-bottom rounded">
    <div class="d-flex flex-wrap gap-2 align-items-center">
        <div class="flex-grow-1 d-flex align-items-center">
            <div>
                <h5 class="h5 mb-1 fw-bold">{{$user_group->name}}</h5>
                <p class="fs-7 text-muted mb-0">Permissões {{$user_group->permissionsCount}}</p>
            </div>
        </div>
        <div>
            <button type="button" class="btn btn-sm btn-info button-user-groups-edit" data-user-group-id="{{$user_group->id}}">Editar Permissões</button>
            <button type="button" class="btn btn-sm btn-danger button-user-groups-delete" data-user-group-id="{{$user_group->id}}" data-user-group-name="{{$user_group->name}}">Apagar</button>
        </div>
    </div>
</div><!-- col-12 -->
@endforeach
@else
<div class="alert alert-warning mb-0">Nenhum resultado foi localizado...</div>
@endif