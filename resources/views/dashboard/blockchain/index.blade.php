@extends('adminlte::page')

@section('content')
    @if (session('message'))
        <div class="alert alert-{{ session('code') }} alert-dismissible">
            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
            {{ session('message') }}
        </div>
    @endif
    <a href="{{ route('blockchains.create') }}" class="btn btn-custom btn-sm" style="margin-bottom: 10px;">NEW NETWORK</a>
    <a href="{{ route('blockchains.log') }}" class="btn btn-custom btn-sm" style="margin-bottom: 10px;">BLOCKCHAIN LOG</a>
    <div class="box">
        <div class="box-header with-border">
            <h4 class="box-title">blockchains</h4>
        </div>
        <div class="box-body no-padding">
            <div class="table-responsive">
                <table class="table">
                    <thead class="thead-light">
                        <tr>
                            <th>Institution ID</th>
                            <th>Type</th>
                            <th>Nº Nodes</th>
                            <th>Local</th>
                            <th>Status</th>
                            <th>Description</th>
                            <th>Liveness</th> <!-- Nova coluna "Liveness" -->
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($blockchains as $blockchain)
                            <tr>
                                <td>{{ $blockchain->institution_id }}</td>
                                <td>{{ $blockchain->type }}</td>
                                <td>{{ $blockchain->number_nodes }}</td>
                                <td>{{ $blockchain->local }}</td>
                                <td>{{ $blockchain->status }}</td>
                                <td>{{ $blockchain->description }}</td>
                                @if($blockchain->url)
                                    <td class="liveness" data-url="{{ $blockchain->url }}">Checking...</td> <!-- Modificado para usar URL -->
                                @else
                                    <td> --- </td>
                                @endif
                                
                                <td>
                                    <button alt="Delete" title="Delete" class="btn btn-danger btn-sm" onclick="confirmDelete({{ $blockchain->id }}, '{{ route('blockchains.destroy', ['blockchain' => $blockchain->id]) }}')"><i class="fa fa-trash"></i></button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8">No blockchains found</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        <div class="box-footer clearfix">
              {{ ($blockchains != null)? $blockchains->links(): null }}
        </div>
    </div>
@stop

@section('js')
    <script>
        function checkLiveness() {
            $('.liveness').each(function() {
                var row = $(this);
                var originalUrl = row.data('url');

                // Validate the original URL
                if (!originalUrl.startsWith('http://') && !originalUrl.startsWith('https://')) {
                    console.error("Invalid URL:", originalUrl);
                    row.text('Invalid URL').removeClass('status-up').addClass('status-down');
                    return;
                }

                // Function to attempt AJAX request with a given URL
                function attemptRequest(url) {
                    $.ajax({
                        url: url,
                        method: 'GET',
                        dataType: 'json',
                        success: function(response) {
                            if (response.status && response.status === 'UP') {
                                row.text('Up').removeClass('status-down').addClass('status-up');
                            } else {
                                row.text('Down').removeClass('status-up').addClass('status-down');
                            }
                        },
                        error: function(xhr, status, error) {
                            console.error("Error fetching liveness status:", status, error);
                            // Switch protocol if the current attempt failed
                            if (url.startsWith('http://')) {
                                attemptRequest(url.replace('http://', 'https://'));
                            } else if (url.startsWith('https://')) {
                                attemptRequest(url.replace('https://', 'http://'));
                            } else {
                                row.text('Error').removeClass('status-up').addClass('status-down');
                            }
                        }
                    });
                }

                // Append ':8545/liveness' to the URL
                var urlWithPath = originalUrl + ':8545/liveness';

                // Start the first attempt
                attemptRequest(urlWithPath);
            });
        }





        $(document).ready(function() {
            // Check liveness every minute (60000 milliseconds)
            setInterval(checkLiveness, 60000);
            // Initial check
            checkLiveness();
        });
    </script>
@stop
@section('css')
<style>
    .status-up {
        color: green;
        font-weight: bold;
    }

    .status-down {
        color: red;
        font-weight: bold;
    }
</style>
@stop
