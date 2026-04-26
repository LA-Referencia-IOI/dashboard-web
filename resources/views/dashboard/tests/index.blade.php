@extends('adminlte::page')

@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="card mt-4">
                <div class="card-header bg-dark text-white">
                    <h3 class="card-title"><i class="fas fa-vial"></i> E2E Test Runner</h3>
                </div>
                <div class="card-body">
                    <p>This runner executes the full end-to-end integration test (Authority -> NAAN -> ARK -> L1/L2 Metadata -> IPFS Pinning -> Resolver) identical to the Jupyter Notebook script.</p>
                    <button id="runTestsBtn" class="btn btn-primary">
                        <i class="fas fa-play"></i> Run E2E Tests
                    </button>
                    
                    @if($lastRun)
                    <div class="alert alert-{{ $lastRun['status'] == 'Success' ? 'success' : 'danger' }} mt-4">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <strong>Last Run:</strong> {{ $lastRun['date'] }} 
                                <span class="badge badge-{{ $lastRun['status'] == 'Success' ? 'success' : 'danger' }} ml-2">{{ $lastRun['status'] }}</span>
                            </div>
                            <a href="{{ route('tests.download') }}" class="btn btn-sm btn-outline-dark"><i class="fas fa-download"></i> Download TXT Log</a>
                        </div>
                    </div>
                    @endif
                    
                    <hr>
                    
                    <div id="loading" class="text-center" style="display: none;">
                        <i class="fas fa-spinner fa-spin fa-3x text-primary"></i>
                        <p class="mt-2">Running tests... This may take up to 90 seconds depending on IPFS and the Minter Worker.</p>
                    </div>

                    <div id="logContainer" style="display: none;">
                        <h5>Execution Logs:</h5>
                        <pre id="logOutput" class="bg-dark text-success p-3 rounded" style="min-height: 200px; max-height: 500px; overflow-y: auto; font-family: monospace;"></pre>
                    </div>
                </div>
            </div>
        </div>
    </div>
@stop

@section('js')
<script>
    $('#runTestsBtn').click(function() {
        var btn = $(this);
        var loading = $('#loading');
        var logContainer = $('#logContainer');
        var logOutput = $('#logOutput');

        btn.prop('disabled', true);
        loading.show();
        logContainer.hide();
        logOutput.html('');

        $.ajax({
            url: '{{ route("tests.run") }}',
            type: 'POST',
            data: {
                _token: '{{ csrf_token() }}'
            },
            success: function(response) {
                loading.hide();
                logContainer.show();
                
                var logsText = response.logs.join('\n');
                logOutput.html(logsText);

                if (response.success) {
                    logOutput.removeClass('text-danger').addClass('text-success');
                    Swal.fire('Success', 'E2E Tests passed successfully!', 'success').then(() => {
                        location.reload();
                    });
                } else {
                    logOutput.removeClass('text-success').addClass('text-danger');
                    Swal.fire('Failed', 'E2E Tests failed. Check logs for details.', 'error').then(() => {
                        location.reload();
                    });
                }
                
                btn.prop('disabled', false);
            },
            error: function(xhr, status, error) {
                loading.hide();
                logContainer.show();
                logOutput.removeClass('text-success').addClass('text-danger');
                logOutput.html('AJAX Error: ' + error + '\nStatus: ' + xhr.status + '\nResponse: ' + xhr.responseText);
                Swal.fire('Error', 'An unexpected error occurred during execution.', 'error').then(() => {
                    location.reload();
                });
                btn.prop('disabled', false);
            }
        });
    });
</script>
@stop
