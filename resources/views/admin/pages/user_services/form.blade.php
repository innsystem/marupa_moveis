<form id="form-request-user-services">
    <div class="modal-body">
        <div class="form-group mb-3">
            <label for="service_id" class="col-sm-12">Serviço:</label>
            <div class="col-sm-12">
                <select name="service_id" id="service_id" class="form-select" onchange="updatePriceOptions()">
                    <option value="">Selecione um serviço</option>
                    @foreach($services as $service)
                    <option value="{{$service->id}}"
                        data-is-recurring="{{$service->is_recurring}}"
                        data-single-price="{{$service->single_payment_price}}"
                        data-monthly-price="{{$service->monthly_price}}"
                        data-quarterly-price="{{$service->quarterly_price}}"
                        data-semiannual-price="{{$service->semiannual_price}}"
                        data-annual-price="{{$service->annual_price}}"
                        data-biennial-price="{{$service->biennial_price}}"
                        @if (isset($result) && $result->service_id == $service->id) selected @endif>
                        {{$service->title}}
                    </option>
                    @endforeach
                </select>
            </div>
        </div>

        <div class="form-group mb-3">
            <label for="period" class="col-sm-12">Período:</label>
            <div class="col-sm-12">
                <select name="period" id="period" class="form-select">
                    <option value="once" @if (isset($result) && $result->period == 'once') selected @endif>Pagamento Único</option>
                    <option value="monthly" @if (isset($result) && $result->period == 'monthly') selected @endif>Mensal</option>
                    <option value="quarterly" @if (isset($result) && $result->period == 'quarterly') selected @endif>Trimestral</option>
                    <option value="semiannual" @if (isset($result) && $result->period == 'semiannual') selected @endif>Semestral</option>
                    <option value="annual" @if (isset($result) && $result->period == 'annual') selected @endif>Anual</option>
                    <option value="biennial" @if (isset($result) && $result->period == 'biennial') selected @endif>Bienal</option>
                </select>
            </div>
        </div>

        <div class="form-group mb-3">
            <label for="price" class="col-sm-12">Valor:</label>
            <div class="col-sm-12">
                <input type="text" class="form-control mask-money" id="price" name="price" placeholder="0,00" value="{{ isset($result->price) ? number_format($result->price, 2, ',', '.') : '0.00' }}">
            </div>
        </div>

        <div class="d-flex flex-column flex-md-row gap-3">
            <div class="form-group mb-3">
                <label for="start_date" class="col-sm-12">Data de Início:</label>
                <div class="col-sm-12">
                    <input type="text" class="form-control mask-date" id="start_date" name="start_date" placeholder="00/00/0000" value="{{ isset($result->start_date) ? $result->start_date->format('d/m/Y') : date('d/m/Y') }}">
                </div>
            </div>

            <div class="form-group mb-3">
                <label for="end_date" class="col-sm-12">Data de Término:</label>
                <div class="col-sm-12">
                    <input type="text" class="form-control mask-date" id="end_date" name="end_date" placeholder="00/00/0000" value="{{ isset($result->end_date) ? $result->end_date->format('d/m/Y') : '' }}">
                </div>
            </div>

            <div class="form-group mb-3">
                <label for="status" class="col-sm-12">Status:</label>
                <div class="col-sm-12">
                    <select name="status" id="status" class="form-select">
                        @foreach($statuses as $status)
                        <option value="{{$status->id}}" @if (isset($result->status) && $result->status == $status->id) selected @endif>{{$status->name}}</option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>

        <div class="mb-4 p-2 border rounded">
            <div class="form-group mb-3">
                <label class="col-sm-12">Informações Adicionais (opcional):</label>
                <div id="metadata_container" class="col-sm-12">
                    @if(isset($result->metadata) && !empty($result->metadata))
                    @foreach(json_decode($result->metadata, true) as $title => $value)
                    <div class="row mb-2 metadata-row">
                        <div class="col-sm-5">
                            <input type="text" class="form-control" name="metadata_title[]" placeholder="Título" value="{{ $title }}">
                        </div>
                        <div class="col-sm-5">
                            <input type="text" class="form-control" name="metadata_value[]" placeholder="Valor" value="{{ $value }}">
                        </div>
                        <div class="col-sm-2">
                            <button type="button" class="btn btn-danger btn-sm remove-metadata"><i class="fa fa-times"></i></button>
                        </div>
                    </div>
                    @endforeach
                    @else
                    <div class="row mb-2 metadata-row">
                        <div class="col-sm-5">
                            <input type="text" class="form-control" name="metadata_title[]" placeholder="Título">
                        </div>
                        <div class="col-sm-5">
                            <input type="text" class="form-control" name="metadata_value[]" placeholder="Valor">
                        </div>
                        <div class="col-sm-2">
                            <button type="button" class="btn btn-danger btn-sm remove-metadata"><i class="fa fa-times"></i></button>
                        </div>
                    </div>
                    @endif
                </div>
                <div class="col-sm-12 mt-2">
                    <button type="button" class="btn btn-sm btn-primary" id="add_metadata"><i class="fa fa-plus"></i> Adicionar campo</button>
                </div>
                <button type="button" class="btn btn-sm btn-link p-1 fs-7 mt-2" id="add_conta_pessoal"><i class="fa fa-wallet"></i> Adicionar como Conta Pessoal</button>
            </div>
        </div>
    </div>
    <div class="bg-gray modal-footer justify-content-between">
        <button type="button" class="btn btn-success button-user-services-save"><i class="fa fa-check"></i> Salvar</button>
        <button type="button" class="btn btn-light" data-bs-dismiss="offcanvas" aria-label="Fechar">Fechar</button>
    </div>
</form>