{{Form::model($webhook,array('route' => array('webhook.update', $webhook->id), 'method' => 'PUT')) }}
    <div class="modal-body">
        <div class="row">
            <div class="form-group">
                {{ Form::label('',__('Module'),['class'=>'form-label']) }}
                {{ Form::select('module',$webhookModule,[$webhook->action],['class'=>'form-control']) }}
            </div>
            <div class="form-group">
                {{ Form::label('',__('Method'),['class'=>'form-label']) }}
                {{Form::select('method',$methods,null,array('class'=>'form-control select'))}}
            </div>
            <div class="form-group">
                {{ Form::label('',__('URL'),['class'=>'form-label']) }}
                {{ Form::text('url',$webhook->url,['class'=>'form-control','placeholder'=>__('Enter Webhook Url Here'),'required'=>'required']) }}
            </div>
        </div>
    </div>
    <div class="modal-footer">
        <button type="button" class="btn  btn-light" data-bs-dismiss="modal">{{ __('Cancel') }}</button>
        {{ Form::submit(__('Save Changes'), ['class' => 'btn  btn-primary']) }}
    </div>
{{ Form::close() }}
