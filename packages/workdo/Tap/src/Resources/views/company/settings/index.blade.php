<div class="card" id="tap-sidenav">
    {{ Form::open(['route' => ['tap.setting.store'], 'enctype' => 'multipart/form-data', 'id' => 'payment-form']) }}

    <div class="card-header">
        <div class="row">
            <div class="col-lg-10 col-md-10 col-sm-10">
                <h5 class="">{{ __('Tap') }}</h5>
                <small>{{ __('These details will be used to collect invoice payments. Each invoice will have a payment button based on the below configuration.') }}</small>
            </div>
            <div class="col-lg-2 col-md-2 col-sm-2 text-end">
                <div class="form-check form-switch custom-switch-v1 float-end">
                    <input type="checkbox" name="tap_payment_is_on" class="form-check-input input-primary"
                        id="tap_payment_is_on"
                        {{ isset($settings['tap_payment_is_on']) && $settings['tap_payment_is_on'] == 'on' ? ' checked ' : '' }}>
                    <label class="form-check-label" for="tap_payment_is_on"></label>
                </div>
            </div>
        </div>
    </div>

    <div class="card-body">
        <div class="row">
            <div class="col-md-6">
                <div class="form-group">
                    <label for="company_tap_secret_key" class="col-form-label">{{ __('Secret Key') }}</label>
                    <input type="text" name="company_tap_secret_key" id="company_tap_secret_key"
                        class="form-control"
                        value="{{ !isset($settings['company_tap_secret_key']) || is_null($settings['company_tap_secret_key']) ? '' : $settings['company_tap_secret_key'] }}"
                        placeholder="{{ __('Secret Key') }}"
                        {{ isset($settings['tap_payment_is_on']) && $settings['tap_payment_is_on'] == 'on' ? '' : ' disabled' }}>
                </div>
            </div>
        </div>
    </div>
    <div class="card-footer text-end">
        <input class="btn btn-print-invoice btn-primary m-r-10" type="submit" value="{{ __('Save Changes') }}">
    </div>
    {{ Form::close() }}

</div>

<script>
    $(document).on('click', '#tap_payment_is_on', function() {
        if ($('#tap_payment_is_on').prop('checked')) {
            $("#company_tap_secret_key").removeAttr("disabled");
        } else {
            $('#company_tap_secret_key').attr("disabled", "disabled");
        }
    });
</script>
