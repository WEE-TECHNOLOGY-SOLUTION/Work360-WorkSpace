<div class="col-lg-4 col-md-4 col-sm-6">
    <div class="form-group">
        {{ Form::label('electronic_address', __('Electronic Address'), ['class' => 'form-label']) }}
        <div class="form-icon-user">
            {{ Form::text('electronic_address', null, ['class' => 'form-control','placeholder' => 'Enter Electronic Address']) }}
        </div>
    </div>
</div>
<div class="col-lg-4 col-md-4 col-sm-6">
    <div class="form-group">
        {{ Form::label('electronic_address_scheme', __('Electronic Address Scheme'), ['class' => 'form-label']) }}
        <div class="form-icon-user">
            {{ Form::text('electronic_address_scheme', null, ['class' => 'form-control','placeholder' => 'Enter Electronic Address Scheme']) }}
        </div>
    </div>
</div>