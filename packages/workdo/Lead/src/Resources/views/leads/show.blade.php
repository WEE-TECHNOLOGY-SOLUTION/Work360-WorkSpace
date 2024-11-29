@extends('layouts.main')

@section('page-title')
    {{ $lead->name }}
@endsection
@push('css')
    <style>
        .nav-tabs .nav-link-tabs.active {
            background: none;
        }
    </style>
    <link rel="stylesheet" href="{{ asset('assets/js/plugins/summernote-0.8.18-dist/summernote-lite.min.css') }}">
    <link rel="stylesheet" href="{{ asset('packages/workdo/Lead/src/Resources/assets/css/dropzone.min.css') }}">
@endpush

@php
    $lead->activities = $lead->activities->load('user');
    $lead->discussions = $lead->discussions->load('user');
    $lead->calls = $lead->calls->load('getLeadCallUser');
@endphp

@push('scripts')
    <script>
        var scrollSpy = new bootstrap.ScrollSpy(document.body, {
            target: '#useradd-sidenav',
            offset: 300
        })
    </script>
    <script src="{{ asset('packages/workdo/Lead/src/Resources/assets/js/dropzone.min.js') }}"></script>
    <script src="{{ asset('assets/js/plugins/summernote-0.8.18-dist/summernote-lite.min.js') }}"></script>

    <script>
        @if (!Auth::user()->hasRole('client'))
            Dropzone.autoDiscover = false;


            myDropzone2 = new Dropzone("#dropzonewidget2", {
                maxFiles: 20,
                maxFilesize: 20,
                parallelUploads: 1,
                acceptedFiles: ".jpeg,.jpg,.png,.pdf,.doc,.txt",
                url: "{{ route('leads.file.upload', $lead->id) }}",
                success: function(file, response) {
                    if (response.is_success) {
                        dropzoneBtn(file, response);
                    } else {
                        myDropzone2.removeFile(file);
                        toastrs('Error', response.error, 'error');
                    }
                },
                error: function(file, response) {
                    myDropzone2.removeFile(file);
                    if (response.error) {
                        toastrs('Error', response.error, 'error');
                    } else {
                        toastrs('Error', response, 'error');
                    }
                }
            });
            myDropzone2.on("sending", function(file, xhr, formData) {
                formData.append("_token", $('meta[name="csrf-token"]').attr('content'));
                formData.append("lead_id", {{ $lead->id }});
            });

            function dropzoneBtn(file, response) {
                var download = document.createElement('a');
                download.setAttribute('href', response.download);
                download.setAttribute('class', "btn btn-sm btn-primary m-1");
                download.setAttribute('data-toggle', "tooltip");
                download.setAttribute('download', file.name);
                download.setAttribute('data-original-title', "{{ __('Download') }}");
                download.innerHTML = "<i class='ti ti-download'></i>";

                var del = document.createElement('a');
                del.setAttribute('href', response.delete);
                del.setAttribute('class', "btn btn-sm btn-danger mx-1");
                del.setAttribute('data-toggle', "tooltip");
                del.setAttribute('data-original-title', "{{ __('Delete') }}");
                del.innerHTML = "<i class='ti ti-trash'></i>";

                del.addEventListener("click", function(e) {
                    e.preventDefault();
                    e.stopPropagation();
                    if (confirm("Are you sure ?")) {
                        var btn = $(this);
                        $.ajax({
                            url: btn.attr('href'),
                            data: {
                                _token: $('meta[name="csrf-token"]').attr('content')
                            },
                            type: 'DELETE',
                            success: function(response) {
                                if (response.is_success) {
                                    btn.closest('.dz-image-preview').remove();
                                    btn.closest('.dz-file-preview').remove();
                                    toastrs('Success', response.success, 'success');
                                } else {
                                    toastrs('Error', response.error, 'error');
                                }
                            },
                            error: function(response) {
                                response = response.responseJSON;
                                if (response.error) {
                                    toastrs('Error', response.error, 'error');
                                } else {
                                    toastrs('Error', response, 'error');
                                }
                            }
                        })
                    }
                });

                var html = document.createElement('div');
                html.appendChild(download);
                @if (!Auth::user()->hasRole('client'))
                    @permission('lead edit')
                        html.appendChild(del);
                    @endpermission
                @endif

                file.previewTemplate.appendChild(html);
            }

            @foreach ($lead->files as $file)

                // Create the mock file:
                var mockFile2 = {
                    name: "{{ $file->file_name }}",
                    size: "{{ get_size(get_file($file->file_path)) }}"
                };
                // Call the default addedfile event handler
                myDropzone2.emit("addedfile", mockFile2);
                // And optionally show the thumbnail of the file:
                myDropzone2.emit("thumbnail", mockFile2, "{{ get_file($file->file_path) }}");
                myDropzone2.emit("complete", mockFile2);

                dropzoneBtn(mockFile2, {
                    download: "{{ get_file($file->file_path) }}",
                    delete: "{{ route('leads.file.delete', [$lead->id, $file->id]) }}"
                });
            @endforeach
        @endif

        @permission('lead task edit')
            $(document).on("click", ".task-checkbox", function() {
                var chbox = $(this);
                var lbl = chbox.parent().parent().find('label');

                $.ajax({
                    url: chbox.attr('data-url'),
                    data: {
                        _token: $('meta[name="csrf-token"]').attr('content'),
                        status: chbox.val()
                    },
                    type: 'PUT',
                    success: function(response) {
                        if (response.is_success) {
                            chbox.val(response.status);
                            if (response.status) {
                                lbl.addClass('strike');
                                lbl.find('.badge').removeClass('bg-warning').addClass('bg-success');
                            } else {
                                lbl.removeClass('strike');
                                lbl.find('.badge').removeClass('bg-success').addClass('bg-warning');
                            }
                            lbl.find('.badge').html(response.status_label);

                            toastrs('Success', response.success, 'success');
                        } else {
                            toastrs('Error', response.error, 'error');
                        }
                    },
                    error: function(response) {
                        response = response.responseJSON;
                        if (response.is_success) {
                            toastrs('Error', response.error, 'error');
                        } else {
                            toastrs('Error', response, 'error');
                        }
                    }
                })
            });
        @endpermission

        $(document).ready(function() {
            var tab = 'general';
            @if ($tab = Session::get('status'))
                var tab = '{{ $tab }}';
            @endif
            $("#myTab2 .nav-link-tabs[href='#" + tab + "']").trigger("click");
        });
    </script>

    @if (Laratrust::hasPermission('lead edit'))
        <script>
            $(document).ready(function() {
                $('.summernote').on('summernote.blur', function() {
                    $.ajax({
                        url: "{{ route('leads.note.store', $lead->id) }}",
                        data: {
                            _token: $('meta[name="csrf-token"]').attr('content'),
                            notes: $(this).val()
                        },
                        type: 'POST',
                        success: function(response) {
                            if (response.is_success) {} else {
                                toastrs('Error', response.error, 'error');
                            }
                        },
                        error: function(response) {
                            response = response.responseJSON;
                            if (response.is_success) {
                                toastrs('Error', response.error, 'error');
                            } else {
                                toastrs('Error', response, 'error');
                            }
                        }
                    })
                });
            });
        </script>
    @else
        <script>
            $('.summernote').hide('disable');
        </script>
    @endif
    <script>
        if ($(".summernote").length > 0) {
            $('.summernote').summernote({
                toolbar: [
                    ['style', ['style']],
                    ['font', ['bold', 'italic', 'underline', 'strikethrough']],
                    ['list', ['ul', 'ol', 'paragraph']],
                    ['insert', ['link', 'unlink']],
                ],
                height: 230,
            });
        }
    </script>
    {{-- Custom field description --}}
    <script>
        document.querySelectorAll('.description-container').forEach(function(container) {
            container.addEventListener('click', function() {
                var shortDescription = container.querySelector('.shortDescription');
                var fullDescription = container.querySelector('.fullDescription');

                if (shortDescription.style.display === 'block' || shortDescription.style.display === '') {
                    shortDescription.style.display = 'none';
                    fullDescription.style.display = 'block';
                } else {
                    shortDescription.style.display = 'block';
                    fullDescription.style.display = 'none';
                }
            });
        });
    </script>

@endpush

@section('page-breadcrumb')
    {{ __('Leads') }},
    {{ $lead->name }}
@endsection


@section('page-action')
    <div class="d-flex">
        @stack('addButtonHook')
        @permission('lead edit')
            <a class="btn btn-sm btn-primary btn-icon me-2" data-bs-toggle="tooltip" data-bs-placement="top"
                title="{{ __('Labels') }}" data-ajax-popup="true" data-size="md" data-title="{{ __('Label') }}"
                data-url="{{ URL::to('leads/' . $lead->id . '/labels') }}"><i class="ti ti-tag text-white"></i></a>
            <a class="btn btn-sm btn-primary btn-icon me-2" data-bs-toggle="tooltip" data-bs-placement="top"
                title="{{ __('Edit') }}" data-ajax-popup="true" data-size="lg" data-title="{{ __('Edit Lead') }}"
                data-url="{{ URL::to('leads/' . $lead->id . '/edit') }}"><i class="ti ti-pencil text-white"></i></a>
        @endpermission

        @permission('lead to deal convert')
            @if (!empty($deal))
                <a href="@permission('deal show') @if ($deal->is_active) {{ route('deals.show', $deal->id) }} @else # @endif @else # @endpermission"
                    class="btn btn-sm btn-primary btn-icon " data-bs-toggle="tooltip" data-bs-placement="top"
                    title="{{ __('Already Converted To Deal') }}"><i class="ti ti-exchange text-white"></i></a>
            @else
                <a class="btn btn-sm btn-primary btn-icon " data-bs-toggle="tooltip" data-bs-placement="top"
                    title="{{ __('Convert To Deal') }}" data-ajax-popup="true" data-size="md"
                    data-title="{{ __('Convert [') . $lead->subject . '] To Deal' }}"
                    data-url="{{ URL::to('leads/' . $lead->id . '/show_convert') }}"><i class="ti ti-exchange text-white"></i></a>
            @endif
        @endpermission
    </div>
@endsection

@section('content')
    <div class="row">
        <div class="col-12 mb-3">
            <div class="row">
                <div class="col-3">
                    <div class="card sticky-top" style="top:30px">
                        <div class="list-group list-group-flush" id="useradd-sidenav">
                            <a class="list-group-item list-group-item-action border-0" href="#general">{{ __('General') }}
                                <div class="float-end"><i class="ti ti-chevron-right"></i></div>
                            </a>
                            @if (!Auth::user()->hasRole('client'))
                                <a class="list-group-item list-group-item-action border-0"
                                    href="#tasks">{{ __('Tasks') }}
                                    <div class="float-end"><i class="ti ti-chevron-right"></i></div>
                                </a>
                            @endif
                            @if (!Auth::user()->hasRole('client'))
                                <a class="list-group-item list-group-item-action border-0"
                                    href="#users-products">{{ __('Users | Products') }}
                                    <div class="float-end"><i class="ti ti-chevron-right"></i></div>
                                </a>
                            @endif
                            @if (!Auth::user()->hasRole('client'))
                                <a class="list-group-item list-group-item-action border-0"
                                    href="#sources-emails">{{ __('Sources | Emails') }}
                                    <div class="float-end"><i class="ti ti-chevron-right"></i></div>
                                </a>
                            @endif
                            <a class="list-group-item list-group-item-action border-0"
                                href="#discussion-notes">{{ __('Discussion | Notes') }}
                                <div class="float-end"><i class="ti ti-chevron-right"></i></div>
                            </a>
                            @if (!Auth::user()->hasRole('client'))
                                <a class="list-group-item list-group-item-action border-0"
                                    href="#files">{{ __('Files') }}
                                    <div class="float-end"><i class="ti ti-chevron-right"></i></div>
                                </a>
                            @endif
                            @if (!Auth::user()->hasRole('client'))
                                <a class="list-group-item list-group-item-action border-0"
                                    href="#calls">{{ __('Calls') }}
                                    <div class="float-end"><i class="ti ti-chevron-right"></i></div>
                                </a>
                            @endif
                            @if (!Auth::user()->hasRole('client'))
                                <a class="list-group-item list-group-item-action border-0"
                                    href="#activity">{{ __('Activity') }}
                                    <div class="float-end"><i class="ti ti-chevron-right"></i></div>
                                </a>
                            @endif
                            @stack('indiamart_tab')

                        </div>
                    </div>
                </div>

                <div class="col-9">
                    <div id="general">
                        <?php
                        $tasks = $lead->tasks;
                        $products = $lead->products();
                        $sources = $lead->sources();
                        $calls = $lead->calls;
                        $emails = $lead->emails;
                        ?>
                        <div class="card">
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-4 col-sm-4">
                                        <div class="d-flex align-items-start">
                                            <div class="theme-avtar bg-primary badge">
                                                <i class="ti ti-mail"></i>
                                            </div>
                                            <div class="ms-2">
                                                <strong>{{ __('Email') }}</strong>
                                                <h5 class="mb-0 text-primary">{{ $lead->email }}</h5>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-4 col-sm-4">
                                        <div class="d-flex align-items-start">
                                            <div class="theme-avtar bg-warning badge">
                                                <i class="ti ti-phone"></i>
                                            </div>
                                            <div class="ms-2">
                                                <strong>{{ __('Phone') }}</strong>
                                                <h5 class="mb-0 text-warning">{{ $lead->phone }}</h5>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-4 col-sm-4">
                                        <div class="d-flex align-items-start">
                                            <div class="theme-avtar bg-info badge">
                                                <i class="ti ti-test-pipe"></i>
                                            </div>
                                            <div class="ms-2">
                                                <strong>{{ __('Pipeline') }}</strong>
                                                <h5 class="mb-0 text-info">{{ $lead->pipeline->name }}</h5>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-4 col-sm-4 mt-4">
                                        <div class="d-flex align-items-start">
                                            <div class="theme-avtar bg-primary badge">
                                                <i class="ti ti-server"></i>
                                            </div>
                                            <div class="ms-2">
                                                <strong>{{ __('Stage') }}</strong>
                                                <h5 class="mb-0 text-primary">{{ $lead->stage->name }}</h5>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-4 col-sm-4 mt-4">
                                        <div class="d-flex align-items-start">
                                            <div class="theme-avtar bg-warning badge">
                                                <i class="ti ti-calendar"></i>
                                            </div>
                                            <div class="ms-2">
                                                <strong>{{ __('Created') }}</strong>
                                                <h5 class="mb-0 text-warning">
                                                    {{ company_date_formate($lead->created_at) }}</h5>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-4 col-sm-4 mt-4">
                                        <div class="d-flex align-items-start">
                                            <div class="theme-avtar bg-info badge">
                                                <i class="ti ti-chart-bar"></i>
                                            </div>
                                            <div class="ms-2">
                                                <h3 class="mb-0 text-info">{{ $precentage }}%</h3>
                                                <div class="progress">
                                                    <div class="progress-bar bg-info"
                                                        style="width: {{ $precentage }}%;"></div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-4 col-sm-4 mt-4">
                                        <div class="d-flex align-items-start">
                                            <div class="theme-avtar bg-primary badge">
                                                <i class="ti ti-calendar-time"></i>
                                            </div>
                                            <div class="ms-2">
                                                <strong>{{ __('Follow Up Date') }}</strong>
                                                <h5
                                                    class="mb-0 {{ $lead->follow_up_date != null && $lead->follow_up_date < date('Y-m-d') ? 'text-danger' : 'text-primary' }}">
                                                    {{ isset($lead->follow_up_date) && !empty($lead->follow_up_date) ? company_date_formate($lead->follow_up_date) : '-' }}
                                                </h5>
                                            </div>
                                        </div>
                                    </div>
                                    @if (!empty($customFields) && count($lead->customField) > 0)
                                        @foreach ($customFields as $key => $field)
                                            <div class="col-md-4 col-sm-4 mt-4">
                                                <div class="d-flex align-items-start">
                                                    @if ($key % 3 === 2)
                                                        <div
                                                            class="theme-avtar bg-primary badge {{ $field->type == 'textarea' && !empty($lead->customField[$field->id]) ? 'mt-2' : '' }}">
                                                            <i class="ti ti-circle-plus"></i>
                                                        </div>
                                                        <div class="ms-2">
                                                            <strong>{{ Str::ucfirst($field->name) }}</strong><br>
                                                            @if ($field->type == 'attachment')
                                                                <a href="{{ !empty($lead->customField[$field->id]) ? get_file($lead->customField[$field->id]) : get_file('packages/workdo/Lead/src/Resources/assets/upload/default-img.png') }}"
                                                                    target=_blank class="btn btn-md p-0"
                                                                    data-bs-toggle="tooltip" title="{{ __('Preview') }}">
                                                                    <h5 class="text-primary">
                                                                        {{ __('Preview Attachment') }}
                                                                        <i class="ti ti-arrow-up-right"></i>
                                                                    </h5>
                                                                </a>
                                                            @else
                                                                @if ($field->type == 'textarea')
                                                                    <div class="description-container"
                                                                        data-key="{{ $key }}">
                                                                        <h5 class="text-primary shortDescription"
                                                                            id="shortDescription{{ $key }}">
                                                                            {{ \Illuminate\Support\Str::limit(!empty($lead->customField[$field->id]) ? $lead->customField[$field->id] : '-', 40) }}
                                                                        </h5>
                                                                        <h5 class="text-primary fullDescription"
                                                                            style="display: none;">
                                                                            {{ !empty($lead->customField[$field->id]) ? $lead->customField[$field->id] : '-' }}
                                                                        </h5>
                                                                    </div>
                                                                @else
                                                                    <h5 class="text-primary">
                                                                        {{ !empty($lead->customField[$field->id]) ? $lead->customField[$field->id] : '-' }}
                                                                    </h5>
                                                                @endif
                                                            @endif
                                                        </div>
                                                    @elseif($key % 3 === 0)
                                                        <div
                                                            class="theme-avtar bg-warning badge {{ $field->type == 'textarea' && !empty($lead->customField[$field->id]) ? 'mt-2' : '' }}">
                                                            <i class="ti ti-circle-plus"></i>
                                                        </div>
                                                        <div class="ms-2">
                                                            <strong>{{ Str::ucfirst($field->name) }}</strong><br>
                                                            @if ($field->type == 'attachment')
                                                                <a href="{{ !empty($lead->customField[$field->id]) ? get_file($lead->customField[$field->id]) : get_file('packages/workdo/Lead/src/Resources/assets/upload/default-img.png') }}"
                                                                    target=_blank class="btn btn-md p-0"
                                                                    data-bs-toggle="tooltip" title="{{ __('Preview') }}">
                                                                    <h5 class="text-warning">
                                                                        {{ __('Preview Attachment') }}
                                                                        <i class="ti ti-arrow-up-right"></i>
                                                                    </h5>
                                                                </a>
                                                            @else
                                                                @if ($field->type == 'textarea')
                                                                    <div class="description-container"
                                                                        data-key="{{ $key }}">
                                                                        <h5 class="text-warning shortDescription"
                                                                            id="shortDescription{{ $key }}">
                                                                            {{ \Illuminate\Support\Str::limit(!empty($lead->customField[$field->id]) ? $lead->customField[$field->id] : '-', 40) }}
                                                                        </h5>
                                                                        <h5 class="text-warning fullDescription"
                                                                            style="display: none;">
                                                                            {{ !empty($lead->customField[$field->id]) ? $lead->customField[$field->id] : '-' }}
                                                                        </h5>
                                                                    </div>
                                                                @else
                                                                    <h5 class="text-warning">
                                                                        {{ !empty($lead->customField[$field->id]) ? $lead->customField[$field->id] : '-' }}
                                                                    </h5>
                                                                @endif
                                                            @endif
                                                        </div>
                                                    @elseif($key % 3 === 1)
                                                        <div
                                                            class="theme-avtar bg-info badge {{ $field->type == 'textarea' && !empty($lead->customField[$field->id]) ? 'mt-2' : '' }}">
                                                            <i class="ti ti-circle-plus"></i>
                                                        </div>
                                                        <div class="ms-2">
                                                            <strong>{{ Str::ucfirst($field->name) }}</strong><br>
                                                            @if ($field->type == 'attachment')
                                                                <a href="{{ !empty($lead->customField[$field->id]) ? get_file($lead->customField[$field->id]) : get_file('packages/workdo/Lead/src/Resources/assets/upload/default-img.png') }}"
                                                                    target=_blank class="btn btn-md p-0"
                                                                    data-bs-toggle="tooltip" title="{{ __('Preview') }}">
                                                                    <h5 class="text-info">{{ __('Preview Attachment') }}
                                                                        <i class="ti ti-arrow-up-right"></i>
                                                                    </h5>
                                                                </a>
                                                            @else
                                                                @if ($field->type == 'textarea')
                                                                    <div class="description-container"
                                                                        data-key="{{ $key }}">
                                                                        <h5 class="text-info shortDescription"
                                                                            id="shortDescription{{ $key }}">
                                                                            {{ \Illuminate\Support\Str::limit(!empty($lead->customField[$field->id]) ? $lead->customField[$field->id] : '-', 40) }}
                                                                        </h5>
                                                                        <h5 class="text-info fullDescription"
                                                                            style="display: none;">
                                                                            {{ !empty($lead->customField[$field->id]) ? $lead->customField[$field->id] : '-' }}
                                                                        </h5>
                                                                    </div>
                                                                @else
                                                                    <h5 class="text-info">
                                                                        {{ !empty($lead->customField[$field->id]) ? $lead->customField[$field->id] : '-' }}
                                                                    </h5>
                                                                @endif
                                                            @endif
                                                        </div>
                                                    @endif
                                                </div>
                                            </div>
                                        @endforeach
                                    @endif

                                </div>
                            </div>
                        </div>
                        <div class="col-xxl-12">
                            <div class="row">

                                <div class="col-lg-3 col-6">
                                    <div class="card report_card">
                                        <div class="card-body">
                                            <div class="row align-items-center justify-content-between">
                                                <div class="col-auto mb-3 mb-sm-0">
                                                    <small class="text-muted">{{ __('Tasks') }}</small>
                                                    <h3 class="mb-0">{{ count($tasks) }}</h3>
                                                </div>
                                                <div class="col-auto">
                                                    <div class="theme-avtar bg-danger badge">
                                                        <i class="ti ti-subtask text-white"></i>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-lg-3 col-6">
                                    <div class="card report_card">
                                        <div class="card-body">
                                            <div class="row align-items-center justify-content-between">
                                                <div class="col-auto mb-3 mb-sm-0">
                                                    <small class="text-muted">{{ __('Product') }}</small>
                                                    <h3 class="mb-0">{{ count($products) }}</h3>
                                                </div>
                                                <div class="col-auto">
                                                    <div class="theme-avtar bg-info badge">
                                                        <i class="ti ti-shopping-cart text-white"></i>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-lg-3 col-6">
                                    <div class="card report_card">
                                        <div class="card-body">
                                            <div class="row align-items-center justify-content-between">
                                                <div class="col-auto mb-3 mb-sm-0">
                                                    <small class="text-muted">{{ __('Source') }}</small>
                                                    <h3 class="mb-0">{{ count($sources) }}</h3>
                                                </div>
                                                <div class="col-auto">
                                                    <div class="theme-avtar bg-success badge">
                                                        <i class="ti ti-social text-white"></i>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-lg-3 col-6">
                                    <div class="card report_card">
                                        <div class="card-body">
                                            <div class="row align-items-center justify-content-between">
                                                <div class="col-auto mb-3 mb-sm-0">
                                                    <small class="text-muted">{{ __('Files') }}</small>
                                                    <h3 class="mb-0">{{ count($lead->files) }}</h3>
                                                </div>
                                                <div class="col-auto">
                                                    <div class="theme-avtar bg-warning badge">
                                                        <i class="ti ti-file-invoice  text-white"></i>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div id="tasks">
                                <div class="col-md-12">
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="card table-card">
                                                <div class="card-header">
                                                    <div class="d-flex justify-content-between align-items-center">
                                                        <div>
                                                            <h5>{{ __('Tasks') }}</h5>
                                                        </div>
                                                        @permission('lead task create')
                                                            <div class="float-end">
                                                                <a class="btn btn-sm btn-primary float-end "
                                                                    data-bs-toggle="tooltip" data-bs-placement="top"
                                                                    title="{{ __('Add Task') }}"
                                                                    data-url="{{ route('leads.tasks.create', $lead->id) }}"
                                                                    data-ajax-popup="true" data-title="{{ __('Add Task') }}">
                                                                    <i class="ti ti-plus text-white"></i>
                                                                </a>
                                                            </div>
                                                        @endpermission
                                                    </div>
                                                </div>
                                                <div class="card-body pt-0 table-border-style bg-none"
                                                    style ="height:300px;overflow: auto;">
                                                    <div class="">
                                                        <table class="table align-items-center mb-0" id="tasks">
                                                            <tbody class="list">
                                                                @foreach ($tasks as $task)
                                                                    <tr>
                                                                        <td>
                                                                            <div
                                                                                class="custom-control custom-switch form-check form-switch mb-2">
                                                                                @permission('lead task edit')
                                                                                    <input type="checkbox"
                                                                                        class="form-check-input task-checkbox"
                                                                                        role="switch"
                                                                                        id="task_{{ $task->id }}"
                                                                                        @if ($task->status) checked="checked" @endif
                                                                                        type="checkbox"
                                                                                        value="{{ $task->status }}"
                                                                                        data-url="{{ route('leads.tasks.update.status', [$lead->id, $task->id]) }}" />
                                                                                @endpermission
                                                                                <label for="task_{{ $task->id }}"
                                                                                    class="custom-control-label ml-4 @if ($task->status) strike @endif">
                                                                                    <h6
                                                                                        class="media-title text-sm form-check-label">
                                                                                        {{ $task->name }}
                                                                                        @if ($task->status)
                                                                                            <div
                                                                                                class="badge p-2 px-3 bg-success mb-1">
                                                                                                {{ __(Workdo\Lead\Entities\LeadTask::$status[$task->status]) }}
                                                                                            </div>
                                                                                        @else
                                                                                            <div
                                                                                                class="badge p-2 px-3 bg-warning mb-1">
                                                                                                {{ __(Workdo\Lead\Entities\LeadTask::$status[$task->status]) }}
                                                                                            </div>
                                                                                        @endif
                                                                                    </h6>
                                                                                    <div class="text-xs text-muted">
                                                                                        {{ __(Workdo\Lead\Entities\LeadTask::$priorities[$task->priority]) }}
                                                                                        -
                                                                                        <span
                                                                                            class="text-primary">{{ company_datetime_formate($task->date . ' ' . $task->time) }}</span>
                                                                                    </div>
                                                                                </label>
                                                                            </div>
                                                                        </td>
                                                                        <td class="Action text-end">
                                                                            <span>
                                                                                @permission('lead task edit')
                                                                                    <div class="action-btn me-2">
                                                                                        <a data-size="md"
                                                                                            data-url="{{ route('leads.tasks.edit', [$lead->id, $task->id]) }}"
                                                                                            data-ajax-popup="true"
                                                                                            data-title="{{ __('Edit Task') }}"
                                                                                            class="mx-3 btn btn-sm align-items-center bg-info"
                                                                                            data-bs-toggle="tooltip"
                                                                                            data-bs-placement="top"
                                                                                            title="{{ __('Edit Task') }}"><i
                                                                                                class="ti ti-pencil text-white"></i></a>
                                                                                    </div>
                                                                                @endpermission
                                                                                @permission('lead task delete')
                                                                                    <div class="action-btn">
                                                                                        {!! Form::open([
                                                                                            'method' => 'DELETE',
                                                                                            'route' => ['leads.tasks.destroy', $lead->id, $task->id],
                                                                                            'id' => 'delete-form-' . $task->id,
                                                                                        ]) !!}
                                                                                        <a href="#!"
                                                                                            class="mx-3 btn btn-sm align-items-center show_confirm bg-danger"
                                                                                            data-bs-toggle="tooltip"
                                                                                            data-bs-placement="top"
                                                                                            title="{{ __('Delete Task') }}"
                                                                                            data-confirm="{{ __('Are You Sure?') }}"
                                                                                            data-text="{{ __('This action can not be undone. Do you want to continue?') }}">
                                                                                            <span class="text-white"> <i
                                                                                                    class="ti ti-trash"></i></span></a>
                                                                                        {!! Form::close() !!}
                                                                                    </div>
                                                                                @endpermission
                                                                            </span>
                                                                        </td>
                                                                    </tr>
                                                                @endforeach
                                                            </tbody>
                                                        </table>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div id="users-products">
                        <div class="row">
                            @if (!Auth::user()->hasRole('client'))
                                <div class="col-md-6">
                                    <div class="card table-card deal-card">
                                        <div class="card-header">
                                            <div class="d-flex justify-content-between align-items-center">
                                                <div>
                                                    <h5>{{ __('Users') }}</h5>
                                                </div>
                                                @permission('lead edit')
                                                    <div class="float-end">
                                                        <a class="btn btn-sm btn-primary float-end " data-bs-toggle="tooltip"
                                                            data-bs-placement="top" title="{{ __('Add User') }}"
                                                            data-url="{{ route('leads.users.edit', $lead->id) }}"
                                                            data-ajax-popup="true" data-title="{{ __('Add User') }}">
                                                            <i class="ti ti-plus text-white"></i>
                                                        </a>
                                                    </div>
                                                @endpermission
                                            </div>
                                        </div>
                                        <div class="card-body pt-0 table-border-style bg-none"
                                            style ="height:300px;overflow: auto;">
                                            <div class="">
                                                <table class="table align-items-center mb-0">
                                                    <tbody class="list">
                                                        @foreach ($lead->users as $user)
                                                            <tr>
                                                                <td>
                                                                    <a @if ($user->avatar) href="{{ get_file($user->avatar) }}" @else href="{{ get_file('uploads/users-avatar/avatar.png') }}" @endif
                                                                        target="_blnak">
                                                                        <img @if ($user->avatar) src="{{ get_file($user->avatar) }}" @else src="{{ get_file('uploads/users-avatar/avatar.png') }}" @endif
                                                                            class="rounded border-2 border border-primary" width="40" height="40">
                                                                    </a>
                                                                </td>
                                                                <td>
                                                                    <span class="number-id">{{ $user->name }}</span>
                                                                </td>
                                                                @permission('lead edit')
                                                                    <td>
                                                                        @if ($lead->created_by == \Auth::user()->id)
                                                                            <div class="action-btn float-end">
                                                                                {!! Form::open([
                                                                                    'method' => 'DELETE',
                                                                                    'route' => ['leads.users.destroy', $lead->id, $user->id],
                                                                                    'id' => 'delete-form-' . $lead->id,
                                                                                ]) !!}
                                                                                <a href="#!"
                                                                                    class="mx-3 btn btn-sm align-items-center show_confirm bg-danger"
                                                                                    data-bs-toggle="tooltip"
                                                                                    data-bs-placement="top"
                                                                                    title="{{ __('Delete User') }}"
                                                                                    data-confirm="{{ __('Are You Sure?') }}"
                                                                                    data-text="{{ __('This action can not be undone. Do you want to continue?') }}">
                                                                                    <span class="text-white"> <i
                                                                                            class="ti ti-trash"></i></span></a>
                                                                                {!! Form::close() !!}
                                                                            </div>
                                                                        @endif
                                                                    </td>
                                                                @endpermission
                                                            </tr>
                                                        @endforeach
                                                    </tbody>
                                                </table>
                                            </div>

                                        </div>

                                    </div>
                                </div>
                            @endif
                            @if (!Auth::user()->hasRole('client'))
                                <div class="col-md-6">
                                    <div class="card table-card deal-card">
                                        <div class="card-header">
                                            <div class="d-flex justify-content-between align-items-center">
                                                <div>
                                                    <h5>{{ __('Products') }}</h5>
                                                </div>
                                                @permission('lead edit')
                                                    <div class="float-end">
                                                        <a class="btn btn-sm btn-primary float-end " data-bs-toggle="tooltip"
                                                            data-bs-placement="top" title="{{ __('Add Products') }}"
                                                            data-url="{{ route('leads.products.edit', $lead->id) }}"
                                                            data-ajax-popup="true" data-title="{{ __('Add Products') }}">
                                                            <i class="ti ti-plus text-white"></i>
                                                        </a>
                                                    </div>
                                                @endpermission
                                            </div>
                                        </div>
                                        <div class="card-body pt-0 table-border-style bg-none"
                                            style ="height:300px;overflow: auto;">
                                            <div class="">
                                                <table class="table align-items-center mb-0">
                                                    <tbody class="list">
                                                        @if ($products)
                                                            @foreach ($products as $product)
                                                                <tr>
                                                                    <td>
                                                                        <?php
                                                                        if (check_file($product->image) == false) {
                                                                            $path = asset('packages/workdo/ProductService/src/Resources/assets/image/img01.jpg');
                                                                        } else {
                                                                            $path = get_file($product->image);
                                                                        }
                                                                        ?>
                                                                        <a href="{{ $path }}" target="_blank" class="image-fixsize">
                                                                            <img style="width: 50px; height: 40px;" src="{{ $path }}" class="rounded border-2 border border-primary">
                                                                        </a>
                                                                    </td>
                                                                    <td>
                                                                        <span class="number-id">{{ $product->name }}
                                                                        </span> (<span
                                                                            class="text-muted">{{ currency_format_with_sym($product->sale_price) }}</span>)
                                                                    </td>
                                                                    @permission('lead edit')
                                                                        <td>
                                                                            <div class="action-btn float-end">
                                                                                {!! Form::open([
                                                                                    'method' => 'DELETE',
                                                                                    'route' => ['leads.products.destroy', $lead->id, $product->id],
                                                                                    'id' => 'delete-form-' . $lead->id,
                                                                                ]) !!}
                                                                                <a href="#!"
                                                                                    class="mx-3 btn btn-sm align-items-center show_confirm bg-danger"
                                                                                    data-bs-toggle="tooltip"
                                                                                    data-bs-placement="top"
                                                                                    title="{{ __('Delete Product') }}"
                                                                                    data-confirm="{{ __('Are You Sure?') }}"
                                                                                    data-text="{{ __('This action can not be undone. Do you want to continue?') }}">
                                                                                    <span class="text-white"> <i
                                                                                            class="ti ti-trash"></i></span>
                                                                                </a>
                                                                                {!! Form::close() !!}
                                                                            </div>
                                                                        </td>
                                                                    @endpermission
                                                                </tr>
                                                            @endforeach
                                                        @else
                                                            <tr>
                                                                <td class="text-center">{{ __('No Product Found.!') }}
                                                                </td>
                                                            </tr>
                                                        @endif
                                                    </tbody>
                                                </table>
                                            </div>

                                        </div>

                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>

                    @if (!Auth::user()->hasRole('client'))
                        <div id="sources-emails">
                            <div class="row pt-2">
                                <div class="col-6">
                                    <div class="card table-card">
                                        <div class="card-header">
                                            <div class="d-flex justify-content-between align-items-center">
                                                <div>
                                                    <h5>{{ __('Sources') }}</h5>
                                                </div>
                                                @permission('lead edit')
                                                    <div class="float-end">
                                                        <a class="btn btn-sm btn-primary float-end " data-bs-toggle="tooltip"
                                                            data-bs-placement="top" title="{{ __('Edit Sources') }}"
                                                            data-url="{{ route('leads.sources.edit', $lead->id) }}"
                                                            data-ajax-popup="true" data-title="{{ __('Edit Sources') }}">
                                                            <i class="ti ti-plus text-white"></i>
                                                        </a>
                                                    </div>
                                                @endpermission
                                            </div>
                                        </div>
                                        <div class="card-body pt-0 table-border-style bg-none"
                                            style ="height:300px;overflow: auto;">
                                            <div class="">
                                                <table class="table align-items-center mb-0">
                                                    <tbody class="list">
                                                        @if ($sources)
                                                            @foreach ($sources as $source)
                                                                <tr>
                                                                    <td>
                                                                        <span
                                                                            class="text-dark">{{ $source->name }}</span>
                                                                    </td>
                                                                    <td>
                                                                        @permission('lead edit')
                                                                            <div class="action-btn float-end">
                                                                                {!! Form::open([
                                                                                    'method' => 'DELETE',
                                                                                    'route' => ['leads.sources.destroy', $lead->id, $source->id],
                                                                                    'id' => 'delete-form-' . $lead->id,
                                                                                ]) !!}
                                                                                <a href="#!"
                                                                                    class="mx-3 btn btn-sm align-items-center show_confirm bg-danger"
                                                                                    data-bs-toggle="tooltip"
                                                                                    data-bs-placement="top"
                                                                                    title="{{ __('Delete Sources') }}"
                                                                                    data-confirm="{{ __('Are You Sure?') }}"
                                                                                    data-text="{{ __('This action can not be undone. Do you want to continue?') }}">
                                                                                    <span class="text-white"> <i
                                                                                            class="ti ti-trash"></i></span>
                                                                                </a>
                                                                                {!! Form::close() !!}
                                                                            </div>
                                                                        @endpermission
                                                                    </td>
                                                                </tr>
                                                            @endforeach
                                                        @endif
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-6">
                                    <div class="card table-card">
                                        <div class="card-header">
                                            <div class="d-flex justify-content-between align-items-center">
                                                <div>
                                                    <h5>{{ __('Email') }}</h5>
                                                </div>
                                                @permission('lead email create')
                                                    <div class="float-end">
                                                        <a class="btn btn-sm btn-primary float-end " data-size="md"
                                                            data-bs-toggle="tooltip" data-bs-placement="top"
                                                            title="{{ __('Add Email') }}"
                                                            data-url="{{ route('leads.emails.create', $lead->id) }}"
                                                            data-ajax-popup="true" data-title="{{ __('Add Email') }}">
                                                            <i class="ti ti-plus text-white"></i>
                                                        </a>
                                                    </div>
                                                @endpermission
                                            </div>
                                        </div>
                                        <div class="card-body table-border-style bg-none"
                                            style ="height:300px;overflow: auto;">
                                            <ul class="list-unstyled list-unstyled-border">
                                                @foreach ($emails as $email)
                                                    <li class="media mb-3">
                                                        <div style="margin-right: 10px;">
                                                            <a @if ($user->avatar) href="{{ get_file($user->avatar) }}" @else href="{{ asset('custom/img/avatar/avatar-1.png') }}" @endif target="_blank">
                                                                <img @if ($user->avatar) src="{{ get_file($user->avatar) }}" @else src="{{ asset('custom/img/avatar/avatar-1.png') }}" @endif class="avatar-sm rounded border-2 border border-primary" width="40" height="40">
                                                            </a>
                                                        </div>
                                                        <div class="media-body">
                                                            <div class="mt-0 mb-1 font-weight-bold text-sm">
                                                                {{ $email->subject }} <small
                                                                    class="float-right">{{ $email->created_at->diffForHumans() }}</small>
                                                            </div>
                                                            <div class="text-xs"> {{ $email->to }}</div>
                                                        </div>
                                                    </li>
                                                @endforeach
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif


                    <div id="discussion-notes">
                        <div class="row pt-2">
                            <div class="col-6">
                                <div class="card table-card">
                                    <div class="card-header">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <div>
                                                <h5>{{ __('Discussion') }}</h5>
                                            </div>
                                            <div class="float-end">
                                                <a class="btn btn-sm btn-primary float-end " data-bs-toggle="tooltip"
                                                    data-bs-placement="top" title="{{ __('Add Message') }}"
                                                    data-url="{{ route('leads.discussions.create', $lead->id) }}"
                                                    data-ajax-popup="true" data-title="{{ __('Add Message') }}">
                                                    <i class="ti ti-plus text-white"></i>
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="card-body table-border-style bg-none"
                                        style="height:330px;overflow: auto;">
                                        <ul class="list-unstyled list-unstyled-border">
                                            @foreach ($lead->discussions as $discussion)
                                                <li class="media">
                                                    <div style="margin-right: 10px;">

                                                        <a @if ($discussion->user->avatar) href="{{ get_file($discussion->user->avatar) }}" @else href="{{ get_file('uploads/users-avatar/avatar.png') }}" @endif
                                                            target="_blnak">
                                                            <img @if ($discussion->user->avatar) src="{{ get_file($discussion->user->avatar) }}" @else src="{{ get_file('uploads/users-avatar/avatar.png') }}" @endif
                                                                width="40" height="40" class="avatar-sm rounded border-2 border border-primary">
                                                        </a>
                                                    </div>
                                                    <div class="media-body">
                                                        <div class="mt-0 mb-1 font-weight-bold text-sm fw-bold">
                                                            {{ $discussion->user->name }}
                                                            <small>{{ $discussion->user->type }}</small> <small
                                                                class="float-end">{{ $discussion->created_at->diffForHumans() }}</small>
                                                        </div>
                                                        <div class="text-xs"> {{ $discussion->comment }}</div>
                                                    </div>
                                                </li>
                                            @endforeach
                                        </ul>
                                    </div>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="card">
                                    <div class="card-header">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <h5>{{ __('Notes') }}</h5>
                                            <div class="col-6 text-end">
                                                @if (module_is_active('AIAssistant'))
                                                    @include('aiassistant::ai.generate_ai_btn', [
                                                        'template_module' => 'lead_notes',
                                                        'module' => 'Lead',
                                                    ])
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                    <div class="card-body" style="height:330px;">
                                        <textarea name="notes"
                                            class="form-control summernote {{ !empty($errors->first('description')) ? 'is-invalid' : '' }}" required
                                            id="lead-summernote">{!! $lead->notes !!}</textarea>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    @if (!Auth::user()->hasRole('client'))
                        <div id="files">
                            <div class="row pt-2">
                                <div class="col-12">
                                    <div class="card table-card">
                                        <div class="card-header">
                                            <h5>{{ __('Files') }}</h5>
                                        </div>
                                        <div class="card-body">
                                            <div class="card-body bg-none">
                                                <div class="col-md-12 dropzone browse-file" id="dropzonewidget2"></div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif

                    @if (!Auth::user()->hasRole('client'))
                        <div id="calls">
                            <div class="row pt-2">
                                <div class="col-12">
                                    <div class="card table-card">
                                        <div class="card-header">
                                            <div class="d-flex justify-content-between align-items-center">
                                                <div>
                                                    <h5>{{ __('Calls') }}</h5>
                                                </div>
                                                @permission('lead call create')
                                                    <div class="float-end">
                                                        <a data-size="lg" class="btn btn-sm btn-primary float-end "
                                                            data-bs-toggle="tooltip" data-bs-placement="top"
                                                            title="{{ __('Add Call') }}"
                                                            data-url="{{ route('leads.calls.create', $lead->id) }}"
                                                            data-ajax-popup="true" data-title="{{ __('Add Call') }}">
                                                            <i class="ti ti-plus text-white"></i>
                                                        </a>
                                                    </div>
                                                @endpermission
                                            </div>
                                        </div>
                                        <div class=" card-body table-border-style">

                                            <div class="">
                                                <table class="table mb-0 pc-dt-simple" id="leadCall">
                                                    <thead>
                                                        <tr>
                                                            <th width="">{{ __('Subject') }}</th>
                                                            <th>{{ __('Call Type') }}</th>
                                                            <th>{{ __('Duration') }}</th>
                                                            <th>{{ __('User') }}</th>
                                                            <th class="text-end">{{ __('Action') }}</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        @foreach ($calls as $call)
                                                            <tr>
                                                                <td>{{ $call->subject }}</td>
                                                                <td>{{ ucfirst($call->call_type) }}</td>
                                                                <td>{{ $call->duration }}</td>
                                                                <td>{{ isset($call->getLeadCallUser) ? $call->getLeadCallUser->name : '-' }}
                                                                </td>
                                                                <td class="text-end">
                                                                    @permission('lead call edit')
                                                                        <div class="action-btn me-2">
                                                                            <a data-size="lg"
                                                                                data-url="{{ URL::to('leads/' . $lead->id . '/call/' . $call->id . '/edit') }}"
                                                                                data-ajax-popup="true"
                                                                                data-title="{{ __('Edit Call') }}"
                                                                                class="mx-3 btn btn-sm align-items-center bg-info"
                                                                                data-bs-toggle="tooltip"
                                                                                data-bs-placement="top"
                                                                                title="{{ __('Edit Call') }}">
                                                                                <i class="ti ti-pencil text-white"></i>
                                                                            </a>
                                                                        </div>
                                                                    @endpermission
                                                                    @permission('lead call delete')
                                                                        <div class="action-btn">
                                                                            {!! Form::open([
                                                                                'method' => 'DELETE',
                                                                                'route' => ['leads.calls.destroy', $lead->id, $call->id],
                                                                                'id' => 'delete-form-' . $call->id,
                                                                            ]) !!}
                                                                            <a href="#!"
                                                                                class="mx-3 btn btn-sm align-items-center show_confirm bg-danger"
                                                                                data-bs-toggle="tooltip"
                                                                                data-bs-placement="top"
                                                                                title="{{ __('Delete Call') }}"
                                                                                data-confirm="{{ __('Are You Sure?') }}"
                                                                                data-text="{{ __('This action can not be undone. Do you want to continue?') }}">
                                                                                <span class="text-white">
                                                                                    <i class="ti ti-trash"></i>
                                                                                </span>
                                                                            </a>
                                                                            {!! Form::close() !!}
                                                                        </div>
                                                                    @endpermission
                                                                </td>
                                                            </tr>
                                                        @endforeach
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif

                    <div class="row">
                        @if (!Auth::user()->hasRole('client'))
                            <div id="activity">
                                <div class="col-12">
                                    <div class="card">
                                        <div class="card-header">
                                            <h5>{{ __('Activity') }}</h5>
                                        </div>
                                        <div class="card-body height-450 ">

                                            <div class="row" style="height:350px !important;overflow-y: scroll;">
                                                <ul class="event-cards list-group list-group-flush mt-3 w-100">
                                                    @foreach ($lead->activities as $activity)
                                                        <li class="list-group-item card mb-3">
                                                            <div class="row align-items-center justify-content-between">
                                                                <div class="col-auto mb-3 mb-sm-0">
                                                                    <div class="d-flex align-items-center">
                                                                        <div class="theme-avtar bg-primary badge">
                                                                            <i
                                                                                class="fas {{ $activity->logIcon() }}"></i>
                                                                        </div>
                                                                        <div class="ms-3">
                                                                            <h6 class="m-0">{!! $activity->getLeadRemark() !!}
                                                                            </h6>
                                                                            <small
                                                                                class="text-muted">{{ $activity->created_at->diffForHumans() }}</small>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                <div class="col-auto">

                                                                </div>
                                                            </div>
                                                        </li>
                                                    @endforeach
                                                </ul>
                                            </div>

                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endif
                    </div>
                    @stack('indiamart_div')
                </div>
            </div>
        </div>
    </div>

@endsection
