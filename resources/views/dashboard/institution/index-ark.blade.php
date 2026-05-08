@extends('adminlte::page')

@section('content')
    @if (session('message'))
        <div class="alert alert-{{ session('code') }} alert-dismissible">
            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
            {{ session('message') }}
        </div>
    @endif
    <!-- <a href="{{ route('institutions.create-ark') }}" class="btn btn-custom btn-sm" style="margin-bottom: 10px;">NEW ARK REGISTRATION</a> -->
    <!-- <a href="{{ route('institutions.export-ark-txt') }}" class="btn btn-custom btn-sm" style="margin-bottom: 10px;">EXPORT TXT</a> -->
    <a href="{{ route('institutions.ark-json-all') }}" class="btn btn-custom btn-sm" style="margin-bottom: 10px;">EXPORT JSON</a>

    <div class="box">
        <div class="box-header with-border">
            <h4 class="box-title">Recent ARKs Stored in Blockchain</h4>
        </div>
        <div class="box-body no-padding">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead class="thead-light">
                        <tr>
                            <th>PID</th>
                            <th>Name</th>
                            <th>NAAN</th>
                            <th>Owner</th>
                            <th>URL</th>
                            <th>CID</th>
                        </tr>
                    </thead>
                    <tbody id="arks-table-body">
                        <tr>
                            <td colspan="6" class="text-center py-4">
                                <i class="fas fa-spinner fa-spin fa-2x text-muted"></i>
                                <p class="mt-2 text-muted">Loading recent ARKs from the blockchain...</p>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@stop

@section('js')
<script>
    document.addEventListener("DOMContentLoaded", function() {
        const tableBody = document.getElementById('arks-table-body');
        
        fetch('http://localhost:8000/api/v1/arks/recent?limit=5')
            .then(response => response.json())
            .then(data => {
                tableBody.innerHTML = ''; // Clear loading state
                
                if (data.error) {
                    tableBody.innerHTML = '<tr><td colspan="6" class="text-center text-danger py-4"><i class="fas fa-exclamation-triangle"></i> ' + data.message + '</td></tr>';
                    return;
                }

                if (!data.arks || data.arks.length === 0) {
                    tableBody.innerHTML = '<tr><td colspan="6" class="text-center text-muted py-4">No ARKs found on the blockchain.</td></tr>';
                    return;
                }

                data.arks.forEach(ark => {
                    const tr = document.createElement('tr');
                    
                    const ownerShort = ark.owner ? ark.owner.substring(0, 8) + '...' + ark.owner.substring(ark.owner.length - 6) : 'N/A';
                    const cidShort = ark.cid ? ark.cid.substring(0, 10) + '...' : 'N/A';

                    tr.innerHTML = `
                        <td><strong>${ark.pid || 'N/A'}</strong></td>
                        <td>${ark.name || 'N/A'}</td>
                        <td><span class="badge badge-dark">${ark.naan || 'N/A'}</span></td>
                        <td><code title="${ark.owner}">${ownerShort}</code></td>
                        <td><a href="${ark.url}" target="_blank" class="btn btn-xs btn-outline-info"><i class="fas fa-external-link-alt"></i> Link</a></td>
                        <td><span class="text-muted" title="${ark.cid}">${cidShort}</span></td>
                    `;
                    
                    tableBody.appendChild(tr);
                });
            })
            .catch(error => {
                console.error("Error fetching recent ARKs:", error);
                tableBody.innerHTML = '<tr><td colspan="6" class="text-center text-danger py-4"><i class="fas fa-exclamation-triangle"></i> Failed to connect to the blockchain API.</td></tr>';
            });
    });
</script>
@stop