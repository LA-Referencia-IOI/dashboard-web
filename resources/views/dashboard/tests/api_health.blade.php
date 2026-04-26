@extends('adminlte::page')

@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="card mt-4">
                <div class="card-header bg-dark text-white d-flex justify-content-between align-items-center">
                    <h3 class="card-title mb-0"><i class="fas fa-heartbeat"></i> API Health Check</h3>
                    <button id="checkAllBtn" class="btn btn-sm btn-light">
                        <i class="fas fa-sync-alt"></i> Check All
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

                btn.prop('disabled', false).html('<i class="fas fa-sync-alt"></i> Check All');
            },
            error: function(xhr, status, error) {
                Swal.fire('Error', 'Failed to check APIs: ' + error, 'error');
                btn.prop('disabled', false).html('<i class="fas fa-sync-alt"></i> Check All');
            }
        });
    }

    $('#checkAllBtn').click(checkAll);

    // Auto-check on page load
    $(document).ready(function() {
        checkAll();
    });
</script>
@stop
