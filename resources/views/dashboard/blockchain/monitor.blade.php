@extends('adminlte::page')

@section('content')
    @if (session('message'))
        <div class="alert alert-{{ session('code') }} alert-dismissible">
            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
            {{ session('message') }}
        </div>
    @endif

    <h1>Log Monitor - Node RPC</h1>
    <div id="log-container" class="log-container" style="width: 100%; height: 500px; overflow-y: scroll; background-color: #363638; border: 1px solid #ccc; white-space: pre-wrap; word-wrap: break-word; color: white;">
        <!-- Logs will be loaded here via AJAX -->
    </div>
@stop

@section('js')
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        function fetchLogs() {
            $.ajax({
                url: "{{ route('blockchain.logs_fetch') }}", // Route to fetch the logs via AJAX
                type: 'GET',
                success: function(data) {
                    // Update the log container content with the new lines
                    $('#log-container').html(data);
                },
                error: function() {
                    console.log("Failed to fetch logs.");
                }
            });
        }

        // Call the `fetchLogs()` function every 5 seconds
        setInterval(fetchLogs, 5000);

        // Load logs immediately when the page is opened
        $(document).ready(function() {
            fetchLogs();
        });
    </script>
@stop

@section('css')
    <style>
        .log-line {
            font-family: monospace;
            white-space: pre-wrap; /* Allows for wrapping of text */
            color: white; /* Set log text color to white */
        }
    </style>
@stop
