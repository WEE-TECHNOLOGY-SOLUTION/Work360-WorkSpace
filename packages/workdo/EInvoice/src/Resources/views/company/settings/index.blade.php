<div class="card" id="e-invoice-sidenav">
    {{ Form::open(['route' => 'einvoice.setting.store']) }}
    <div class="card-header">
        <div class="row">
            <div class="col-lg-10 col-md-10 col-sm-10">
                <h5 class="">{{ __('E-Invoice Setting') }}</h5>
            </div>
        </div>
    </div>
    <div class="card-body">
        <div class="row mt-2">
            <div class="col-md-6">
                <div class="form-group">
                    <label for="electronic_address" class="form-label">{{ __('Electronic Address') }}</label>
                    <input class="form-control" placeholder="{{ __('Electronic Address') }}" name="electronic_address"
                        type="text" value="{{ isset($settings['electronic_address']) ? $settings['electronic_address'] : '' }}"
                        id="electronic_address">
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    <label for="company_id" class="form-label">{{ __('Company ID') }}</label>
                    <input class="form-control" placeholder="{{ __('Company Id') }}"
                        name="company_id" type="text" value="{{ isset($settings['company_id']) ? $settings['company_id'] : '' }}"
                        id="company_id">
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    <label for="electronic_address_schema" class="form-label">{{ __('Electronic Address Scheme') }}</label>
                    <input class="form-control" placeholder="{{ __('Electronic Address Schema') }}" name="electronic_address_schema"
                        type="text" value="{{ isset($settings['electronic_address_schema']) ? $settings['electronic_address_schema'] : '' }}"
                        id="electronic_address_schema">
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    <label for="company_id_schema" class="form-label">{{ __('Company ID Scheme') }}</label>
                    <input class="form-control" placeholder="{{ __('Company ID Scheme') }}"
                        name="company_id_schema" type="text" value="{{ isset($settings['company_id_schema']) ? $settings['company_id_schema'] : '' }}"
                        id="company_id_schema">
                </div>
            </div>
        </div>

    </div>
    <div class="card-footer text-end">
        <input class="btn btn-print-invoice  btn-primary m-r-10" type="submit" value="{{ __('Save Changes') }}">
    </div>
    {{ Form::close() }}
</div>
