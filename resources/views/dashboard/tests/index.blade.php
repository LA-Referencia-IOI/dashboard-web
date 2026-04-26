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
                        <p class="mt-2 mb-2 font-weight-bold">Running tests... This may take up to 90 seconds depending on IPFS and the Minter Worker.</p>
                        <div class="progress" style="height: 25px;">
                            <div id="testProgressBar" class="progress-bar progress-bar-striped progress-bar-animated bg-primary" role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100" style="width: 0%"></div>
                        </div>
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

@section('plugins.Sweetalert2', true)

@section('js')
<script>
    var hasInstitutions = {{ $institutions->count() > 0 ? 'true' : 'false' }};

    $('#runTestsBtn').click(function() {
        if (hasInstitutions) {
            Swal.fire({
                title: 'Select Authority ID',
                html: `
                    <div class="form-group text-left mt-3">
                        <label>Test Mode</label>
                        <select id="testModeSelect" class="form-control">
                            <option value="mocked">Mocked ID (resolver-e2e-...)</option>
                            <option value="institution">Existing Institution</option>
                        </select>
                    </div>
                    <div class="form-group text-left mt-3" id="institutionSelectGroup" style="display:none;">
                        <label>Select Institution</label>
                        <select id="institutionId" class="form-control">
                            @foreach($institutions as $inst)
                                <option value="{{ $inst->id }}">{{ $inst->name ?? 'Institution ID: ' . $inst->id }}</option>
                            @endforeach
                        </select>
                    </div>
                `,
                showCancelButton: true,
                confirmButtonText: 'Run Tests',
                didOpen: () => {
                    $('#testModeSelect').change(function() {
                        if($(this).val() == 'institution') {
                            $('#institutionSelectGroup').show();
                        } else {
                            $('#institutionSelectGroup').hide();
                        }
                    });
                },
                preConfirm: () => {
                    var mode = $('#testModeSelect').val();
                    var instId = mode == 'institution' ? $('#institutionId').val() : null;
                    return instId;
                }
            }).then((result) => {
                if(result.isConfirmed) {
                    executeTests(result.value);
                }
            });
        } else {
            executeTests(null);
        }
    });

    function executeTests(institutionId) {
        var btn = $('#runTestsBtn');
        var loading = $('#loading');
        var logContainer = $('#logContainer');
        var logOutput = $('#logOutput');
        var progressBar = $('#testProgressBar');

        btn.prop('disabled', true);
        loading.show();
        logContainer.hide();
        logOutput.html('');

        // Animated simulated progress bar
        var progress = 0;
        progressBar.removeClass('bg-success bg-danger').addClass('bg-primary').css('width', '0%').attr('aria-valuenow', 0);
        var progressInterval = setInterval(function() {
            if (progress < 95) {
                progress += (95 - progress) * 0.05; // Slows down logarithmically as it gets closer to 95
                progressBar.css('width', progress + '%').attr('aria-valuenow', Math.round(progress));
            }
        }, 1000);

        $.ajax({
            url: '{{ route("tests.run") }}',
            type: 'POST',
            data: {
                _token: '{{ csrf_token() }}',
                institution_id: institutionId
            },
            success: function(response) {
                clearInterval(progressInterval);
                progressBar.css('width', '100%').attr('aria-valuenow', 100).removeClass('bg-primary').addClass('bg-success');
                
                setTimeout(function() {
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
                }, 500); // 500ms delay to show 100% progress
            },
            error: function(xhr, status, error) {
                clearInterval(progressInterval);
                progressBar.css('width', '100%').attr('aria-valuenow', 100).removeClass('bg-primary bg-success').addClass('bg-danger');
                
                setTimeout(function() {
                    loading.hide();
                    logContainer.show();
                    logOutput.removeClass('text-success').addClass('text-danger');
                    logOutput.html('AJAX Error: ' + error + '\nStatus: ' + xhr.status + '\nResponse: ' + xhr.responseText);
                    Swal.fire('Error', 'An unexpected error occurred during execution.', 'error').then(() => {
                        location.reload();
                    });
                    btn.prop('disabled', false);
                }, 500);
            }
        });
    }
</script>
@stop
