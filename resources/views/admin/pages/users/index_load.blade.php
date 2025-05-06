@if(isset($results) && count($results) > 0)
@foreach($results as $user)
<div id="row_user_{{$user->id}}" class="col-12 pb-2 mb-4 border-bottom rounded">
    <div class="d-flex flex-wrap gap-2 align-items-center">
        <div class="flex-grow-1 d-flex align-items-center">
            <div>
                <h5 class="h5 mb-1 fw-bold">{{$user->name}} <span class="fw-normal fs-7">({{$user->email}})</span></h5>
                <p class="mb-0 fs-7">- {{$user->group->name}}</p>
            </div>
        </div>
        <div>
            <button type="button" class="btn btn-sm btn-info button-users-edit" data-user-id="{{$user->id}}">Editar</button>
            @if($user->id != 1 && $user->id != Auth::user()->id)
            <button type="button" class="btn btn-sm btn-danger button-users-delete" data-user-id="{{$user->id}}" data-user-name="{{$user->name}}">Apagar</button>
            @endif
        </div>
    </div>
</div><!-- col-12 -->
@endforeach
@else
<div class="alert alert-warning mb-0">Nenhum resultado foi localizado...</div>
@endif