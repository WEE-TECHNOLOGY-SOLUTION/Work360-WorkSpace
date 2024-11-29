
<div class="card" id="mollie-sidenav">
    {{ Form::open(['route' => 'mollie.company_setting.store', 'enctype' => 'multipart/form-data']) }}

    <div class="card-header">
        <div class="row">
            <div class="col-lg-10 col-md-10 col-sm-10">
                <h5 class="">{{ __('Mollie') }}</h5>

                    <small>{{ __('These details will be used to collect subscription plan payments.Each subscription plan will have a payment button based on the below configuration.') }}</small>

            </div>
            <div class="col-lg-2 col-md-2 col-sm-2 text-end">
                <div class="form-check form-switch custom-switch-v1 float-end">
                    <input type="checkbox" name="mollie_payment_is_on" class="form-check-input input-primary" id="mollie_payment_is_on"
                        {{ (isset($settings['mollie_payment_is_on']) ? $settings['mollie_payment_is_on'] : 'off') == 'on' ? ' checked ' : '' }}>
                    <label class="form-check-label" for="mollie_payment_is_on"></label>
                </div>
            </div>
        </div>
    </div>

    <div class="card-body">
        <div class="row mt-2">
            <div class="col-md-4">
                <div class="form-group">
                    <label for="company_mollie_api_key" class="form-label">{{ __('Api Key') }}</label>
                    <input class="form-control public_webhook_mollie" placeholder="{{ __('Api Key') }}" name="company_mollie_api_key"
                        type="text" value="{{ isset($settings['company_mollie_api_key']) ? $settings['company_mollie_api_key'] : '' }}"
                        {{ (isset($settings['mollie_payment_is_on']) ? $settings['mollie_payment_is_on'] : 'off') == 'on' ? '' : ' disabled' }} id="company_mollie_api_key">
                </div>
            </div>
            <div class="col-md-4">
                <div class="form-group">
                    <label for="company_mollie_profile_id" class="form-label">{{ __('Profile ID') }}</label>
                    <input class="form-control public_webhook_mollie" placeholder="{{ __('Profile ID') }}"
                        name="company_mollie_profile_id" type="text" value="{{ isset($settings['company_mollie_profile_id']) ? $settings['company_mollie_profile_id'] : '' }}"
                        {{ (isset($settings['mollie_payment_is_on']) ? $settings['mollie_payment_is_on'] : 'off') == 'on' ? '' : ' disabled' }} id="company_mollie_profile_id">
                </div>
            </div>
            <div class="col-md-4">
                <div class="form-group">
                    <label for="company_mollie_partner_id" class="form-label">{{ __('Partner ID') }}</label>
                    <input class="form-control public_webhook_mollie" placeholder="{{ __('Partner ID') }}"
                        name="company_mollie_partner_id" type="text" value="{{ isset($settings['company_mollie_partner_id']) ? $settings['company_mollie_partner_id'] : ''}}"
                        {{ (isset($settings['mollie_payment_is_on']) ? $settings['mollie_payment_is_on'] : 'off') == 'on' ? '' : ' disabled' }} id="company_mollie_partner_id">
                </div>
            </div>
        </div>

    </div>
    <div class="card-footer text-end">
        <input class="btn btn-print-invoice  btn-primary m-r-10" type="submit" value="{{ __('Save Changes') }}">
    </div>
    {{ Form::close() }}
</div>
    <script>
        $(document).on('click', '#mollie_payment_is_on', function() {
            if ($('#mollie_payment_is_on').prop('checked')) {
                $(".public_webhook_mollie").removeAttr("disabled");
            } else {
                $('.public_webhook_mollie').attr("disabled", "disabled");
            }
        });
    </script>
