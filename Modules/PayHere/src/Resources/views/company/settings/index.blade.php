<div class="card" id="payhere-sidenav">
    {{ Form::model($settings, ['route' => 'payhere.setting.store', 'method' => 'post']) }}

    <div class="card-header">
        <div class="row">
            <div class="col-lg-10 col-md-10 col-sm-10">
                <h5 class="">{{ __('PayHere') }}</h5>

                <small>{{ __('These details will be used to collect invoice payments. Each invoice will have a payment button based on the below configuration.') }}</small>

            </div>
            <div class="col-lg-2 col-md-2 col-sm-2 text-end">
                <div class="form-check form-switch custom-switch-v1 float-end">
                    <input type="checkbox" name="payhere_is_on" class="form-check-input input-primary" id="payhere_is_on"
                        {{ (isset($settings['payhere_is_on']) ? $settings['payhere_is_on'] : 'off') == 'on' ? ' checked ' : '' }}>
                    <label class="form-check-label" for="payhere_is_on"></label>
                </div>
            </div>
        </div>
    </div>
    <div class="card-body">
        <div class="row mt-2">
            <div class="col-md-12">
                <label class="paypal-label col-form-label" for="company_payhere_mode">{{ __('PayHere Mode') }}</label>
                <br>
                <div class="d-flex">
                    <div class="mr-2">
                        <div class="p-3">
                            <div class="form-check">
                                <label class="form-check-labe text-dark">
                                    <input type="radio" name="company_payhere_mode" value="sandbox"
                                        class="form-check-input"
                                        {{ !isset($settings['company_payhere_mode']) || $settings['company_payhere_mode'] == 'sandbox' ? 'checked="checked"' : '' }}>
                                    {{ __('Sandbox') }}
                                </label>
                            </div>
                        </div>
                    </div>
                    <div class="mr-2">
                        <div class="p-3">
                            <div class="form-check">
                                <label class="form-check-labe text-dark">
                                    <input type="radio" name="company_payhere_mode" value="live"
                                        class="form-check-input"
                                        {{ isset($settings['company_payhere_mode']) && $settings['company_payhere_mode'] == 'live' ? 'checked="checked"' : '' }}>
                                    {{ __('Live') }}
                                </label>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    <label for="payhere_merchant_id" class="form-label">{{ __('Merchant ID') }}</label>
                    <input class="form-control stripe_webhook" placeholder="{{ __('Merchant ID') }}"
                        name="payhere_merchant_id" type="text"
                        value="{{ isset($settings['payhere_merchant_id']) ? $settings['payhere_merchant_id'] : '' }}"
                        {{ (isset($settings['payhere_is_on']) ? $settings['payhere_is_on'] : 'off') == 'on' ? '' : ' disabled' }}
                        id="payhere_merchant_id">
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    <label for="payhere_merchant_secret" class="form-label">{{ __('Merchant Secret') }}</label>
                    <input class="form-control stripe_webhook" placeholder="{{ __('Merchant Secret') }}"
                        name="payhere_merchant_secret" type="text"
                        value="{{ isset($settings['payhere_merchant_secret']) ? $settings['payhere_merchant_secret'] : '' }}"
                        {{ (isset($settings['payhere_is_on']) ? $settings['payhere_is_on'] : 'off') == 'on' ? '' : ' disabled' }}
                        id="payhere_merchant_secret">
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    <label for="payhere_app_id" class="form-label">{{ __('App ID') }}</label>
                    <input class="form-control stripe_webhook" placeholder="{{ __('App ID') }}" name="payhere_app_id"
                        type="text"
                        value="{{ isset($settings['payhere_app_id']) ? $settings['payhere_app_id'] : '' }}"
                        {{ (isset($settings['payhere_is_on']) ? $settings['payhere_is_on'] : 'off') == 'on' ? '' : ' disabled' }}
                        id="payhere_app_id">
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    <label for="payhere_app_secret" class="form-label">{{ __('App Secret') }}</label>
                    <input class="form-control stripe_webhook" placeholder="{{ __('App Secret') }}"
                        name="payhere_app_secret" type="text"
                        value="{{ isset($settings['payhere_app_secret']) ? $settings['payhere_app_secret'] : '' }}"
                        {{ (isset($settings['payhere_is_on']) ? $settings['payhere_is_on'] : 'off') == 'on' ? '' : ' disabled' }}
                        id="payhere_app_secret">
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
    $(document).on('click', '#payhere_is_on', function() {
        if ($('#payhere_is_on').prop('checked')) {
            $(".stripe_webhook").removeAttr("disabled");
        } else {
            $('.stripe_webhook').attr("disabled", "disabled");
        }
    });
</script>
