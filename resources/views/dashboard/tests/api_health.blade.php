@extends('adminlte::page')

@section('title', 'dARK Liveness')

@section('content')
    <div class="row pt-4">
        <div class="col-12">
            <div class="d-flex flex-wrap justify-content-between align-items-center mb-2">
                <div>
                    <h1 class="h3 mb-1">dARK Liveness</h1>
                    <p class="text-muted mb-0">Live service checks and operational tools for the dARK platform.</p>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        @foreach($observabilityLinks as $link)
            <div class="col-lg-4 col-md-6 mb-3">
                <div class="card card-outline card-{{ $link['color'] }} h-100 mb-0">
                    <div class="card-body d-flex flex-column">
                        <div class="d-flex align-items-center mb-3">
                            <span class="text-{{ $link['color'] }} mr-3" aria-hidden="true">
                                <i class="{{ $link['icon'] }} fa-2x"></i>
                            </span>
                            <h2 class="h5 mb-0">{{ $link['name'] }}</h2>
                        </div>
                        <p class="text-muted flex-grow-1">{{ $link['description'] }}</p>
                        @if($link['url'] !== '')
                            <a href="{{ $link['url'] }}"
                               class="btn btn-outline-{{ $link['color'] }}"
                               target="_blank"
                               rel="noopener noreferrer">
                                Open {{ $link['name'] }}
                                <i class="fas fa-external-link-alt ml-1" aria-hidden="true"></i>
                            </a>
                        @else
                            <button class="btn btn-outline-secondary" type="button" disabled>
                                Not configured
                            </button>
                        @endif
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    <div class="row">
        <div class="col-md-12">
            <div class="card mt-3">
                <div class="card-header bg-dark text-white d-flex justify-content-between align-items-center">
                    <h3 class="card-title mb-0"><i class="fas fa-heartbeat"></i> Current Status</h3>
                    <button id="checkAllBtn" class="btn btn-sm btn-light">
                        <i class="fas fa-sync-alt"></i> Refresh Status
                    </button>
                </div>
                <div class="card-body p-0">
                    <table class="table table-striped table-hover mb-0">
                        <thead class="thead-dark">
                            <tr>
                                <th style="width: 5%">#</th>
                                <th style="width: 15%">Service</th>
                                <th style="width: 15%">ENV Variable</th>
                                <th style="width: 30%">URL</th>
                                <th style="width: 10%">Status</th>
                                <th style="width: 8%">HTTP</th>
                                <th style="width: 8%">Latency</th>
                                <th style="width: 9%">Message</th>
                                <th style="width: 5%">Actions</th>
                            </tr>
                        </thead>
                        <tbody id="apiTableBody">
                            @foreach($apis as $i => $api)
                            <tr id="row-{{ $api['env'] }}">
                                <td>{{ $i + 1 }}</td>
                                <td><strong>{{ $api['name'] }}</strong></td>
                                <td><code>{{ $api['env'] }}</code></td>
                                <td><small>{{ $api['url'] ?? 'Not configured' }}</small></td>
                                <td id="status-{{ $api['env'] }}">
                                    <span class="badge badge-secondary"><i class="fas fa-question-circle"></i> Pending</span>
                                </td>
                                <td id="code-{{ $api['env'] }}">-</td>
                                <td id="time-{{ $api['env'] }}">-</td>
                                <td id="msg-{{ $api['env'] }}">-</td>
                                <td>
                                    <button class="btn btn-xs btn-outline-primary edit-url-btn" data-env="{{ $api['env'] }}" data-url="{{ $api['url'] ?? '' }}" data-name="{{ $api['name'] }}" title="Edit URL">
                                        <i class="fas fa-pencil-alt"></i>
                                    </button>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@stop

@section('plugins.Sweetalert2', true)

@section('js')
<script>
    function checkAll() {
        var btn = $('#checkAllBtn');
        btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Checking...');

        // Reset all rows to "Checking..."
        @foreach($apis as $api)
            $('#status-{{ $api["env"] }}').html('<span class="badge badge-info"><i class="fas fa-spinner fa-spin"></i> Checking</span>');
            $('#code-{{ $api["env"] }}').text('-');
            $('#time-{{ $api["env"] }}').text('-');
            $('#msg-{{ $api["env"] }}').text('-');
        @endforeach

        $.ajax({
            url: '{{ route("tests.check_apis") }}',
            type: 'POST',
            data: { _token: '{{ csrf_token() }}' },
            success: function(results) {
                results.forEach(function(r) {
                    var statusBadge = '';
                    if (r.status === 'online') {
                        statusBadge = '<span class="badge badge-success"><i class="fas fa-check-circle"></i> Online</span>';
                    } else if (r.status === 'warning') {
                        statusBadge = '<span class="badge badge-warning"><i class="fas fa-exclamation-triangle"></i> Warning</span>';
                    } else if (r.status === 'error') {
                        statusBadge = '<span class="badge badge-danger"><i class="fas fa-times-circle"></i> Error</span>';
                    } else {
                        statusBadge = '<span class="badge badge-danger"><i class="fas fa-power-off"></i> Offline</span>';
                    }

                    $('#status-' + r.env).html(statusBadge);
                    $('#code-' + r.env).text(r.code);
                    $('#time-' + r.env).text(r.time_ms > 0 ? r.time_ms + ' ms' : '-');
                    $('#msg-' + r.env).text(r.message);
                });

                btn.prop('disabled', false).html('<i class="fas fa-sync-alt"></i> Refresh Status');
            },
            error: function(xhr, status, error) {
                Swal.fire('Error', 'Failed to check APIs: ' + error, 'error');
                btn.prop('disabled', false).html('<i class="fas fa-sync-alt"></i> Refresh Status');
            }
        });
    }

    $('#checkAllBtn').click(checkAll);

    // Edit URL button
    $(document).on('click', '.edit-url-btn', function() {
        var envKey = $(this).data('env');
        var currentUrl = $(this).data('url');
        var name = $(this).data('name');
        var btn = $(this);

        Swal.fire({
            title: 'Edit ' + name + ' URL',
            html: '<div class="form-group text-left mt-3">' +
                  '<label>ENV: <code>' + envKey + '</code></label>' +
                  '<input id="swal-url" class="form-control" type="text" value="' + currentUrl + '" placeholder="http://...">' +
                  '</div>',
            showCancelButton: true,
            confirmButtonText: 'Save',
            onOpen: function(popup) {
                popup.querySelector('#swal-url').focus();
            },
            preConfirm: function() {
                var val = Swal.getPopup().querySelector('#swal-url').value.trim();
                if (!val) {
                    Swal.showValidationMessage('URL cannot be empty');
                    return false;
                }
                return val;
            }
        }).then(function(result) {
            if (!result.dismiss && result.value) {
                $.ajax({
                    url: '{{ route("tests.update_api_url") }}',
                    type: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}',
                        env_key: envKey,
                        url: result.value
                    },
                    success: function(res) {
                        if (res.success) {
                            btn.data('url', result.value);
                            Swal.fire('Saved', 'URL updated in .env. Re-checking APIs...', 'success');
                            setTimeout(function() { location.reload(); }, 1500);
                        } else {
                            Swal.fire('Error', res.message, 'error');
                        }
                    },
                    error: function(xhr) {
                        Swal.fire('Error', 'Failed to update: ' + xhr.responseText, 'error');
                    }
                });
            }
        });
    });

    // Auto-check on page load
    $(document).ready(function() {
        checkAll();
    });
</script>
@stop
