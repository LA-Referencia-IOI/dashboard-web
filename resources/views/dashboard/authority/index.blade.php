@extends('adminlte::page')

@section('title', 'Authorities')
@section('plugins.Sweetalert2', true)

@section('content')
    @if (session('message'))
        <div class="alert alert-{{ session('code') }} alert-dismissible">
            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
            {{ session('message') }}
        </div>
    @endif

    <div class="d-flex justify-content-between align-items-center mb-3">
        <h4 class="m-0">Authorities <span class="badge badge-secondary">{{ $total }}</span></h4>
        <a href="{{ route('authorities.create') }}" class="btn btn-custom btn-sm">
            <i class="fas fa-plus mr-1"></i> NEW AUTHORITY
        </a>
    </div>

    <div class="box">
        <div class="box-body no-padding">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead class="thead-light">
                        <tr>
                            <th style="width:260px">UUID</th>
                            <th>Name</th>
                            <th>Responsible</th>
                            <th>Email</th>
                            <th>Institutions</th>
                            <th>NAANs</th>
                            <th>Wallet Balance</th>
                            <th>Status</th>
                            <th>Created at</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($authorities as $authority)
                            <tr>
                                <td><small class="text-muted">{{ $authority->id }}</small></td>
                                <td><strong>{{ $authority->name }}</strong></td>
                                <td>{{ $authority->responsible }}</td>
                                <td>{{ $authority->email }}</td>
                                <td>
                                    <span class="badge badge-info">{{ $authority->institutions_count }}</span>
                                </td>
                                <td class="auth-naans" data-uuid="{{ $authority->id }}">
                                    @if($authority->isRegistered())
                                        <i class="fas fa-spinner fa-spin text-muted"></i>
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                                <td class="auth-balance" data-uuid="{{ $authority->id }}">
                                    @if($authority->isRegistered())
                                        <i class="fas fa-spinner fa-spin text-muted"></i>
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                                <td>{!! $authority->getStatusBadge() !!}</td>
                                <td>{{ $authority->created_at->format('d/m/Y') }}</td>
                                <td>
                                    @if($authority->isRegistered())
                                        <button class="btn btn-success btn-sm btn-fund" data-uuid="{{ $authority->id }}" data-name="{{ $authority->name }}" title="Inject Funds">
                                            <i class="fas fa-hand-holding-usd"></i>
                                        </button>
                                    @endif
                                    <a href="{{ route('authorities.show', $authority->id) }}"
                                       class="btn btn-secondary btn-sm" title="View">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="{{ route('authorities.edit', $authority->id) }}"
                                       class="btn btn-primary btn-sm" title="Edit">
                                        <i class="fas fa-pencil-alt"></i>
                                    </a>
                                    <button class="btn btn-danger btn-sm" title="Delete"
                                        onclick="confirmDelete('{{ $authority->id }}', '{{ route('authorities.destroy', $authority->id) }}')">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center text-muted py-4">
                                    No authorities registered yet.
                                    <a href="{{ route('authorities.create') }}">Create the first one</a>.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        <div class="box-footer clearfix">
            {{ $authorities->links() }}
        </div>
    </div>

    @include('dashboard.partials.confirm-delete')
@stop

@section('js')
<script>
    $(document).ready(function() {
        $('.auth-naans').each(function() {
            let tdNaans = $(this);
            let uuid = tdNaans.data('uuid');
            let tdBalance = $('.auth-balance[data-uuid="' + uuid + '"]');

            // Only fetch if it shows a spinner (meaning it's registered)
            if (tdNaans.find('.fa-spinner').length > 0) {
                fetchApiData(uuid, tdNaans, tdBalance);
            }
        });

        // Funding Flow
        $(document).on('click', '.btn-fund', function() {
            let uuid = $(this).data('uuid');
            let name = $(this).data('name');

            Swal.fire({
                title: 'Inject Funds',
                html: '<div class="text-left"><label>Amount (in <strong>dark</strong>):</label>' +
                      '<input id="swal-fund-amount" type="number" step="0.0001" min="0.0001" class="form-control" placeholder="0.05"></div>' +
                      '<small class="text-muted mt-2 d-block text-left">Funds will be transferred from the Master Wallet.</small>',
                icon: 'info',
                showCancelButton: true,
                confirmButtonText: 'Inject Funds',
                confirmButtonColor: '#28a745',
                onOpen: function(popup) {
                    popup.querySelector('#swal-fund-amount').focus();
                },
                preConfirm: function() {
                    let amount = Swal.getPopup().querySelector('#swal-fund-amount').value;
                    if (!amount || amount <= 0) {
                        Swal.showValidationMessage('Please enter a valid amount');
                    }
                    return amount;
                }
            }).then(function(result) {
                if (result.value) {
                    Swal.fire({
                        title: 'Transferring...',
                        text: 'Please wait while the transaction is mined on the blockchain.',
                        allowOutsideClick: false,
                        onBeforeOpen: () => {
                            Swal.showLoading();
                        }
                    });

                    $.ajax({
                        url: '/dashboard/authority/' + uuid + '/fund',
                        type: 'POST',
                        data: {
                            _token: '{{ csrf_token() }}',
                            amount: result.value
                        },
                        success: function(res) {
                            if (res.success) {
                                Swal.fire('Success!', res.message, 'success');
                                
                                // Refresh the balance
                                let tdNaans = $('.auth-naans[data-uuid="' + uuid + '"]');
                                let tdBalance = $('.auth-balance[data-uuid="' + uuid + '"]');
                                tdBalance.html('<i class="fas fa-spinner fa-spin text-muted"></i>');
                                fetchApiData(uuid, tdNaans, tdBalance);
                            } else {
                                Swal.fire('Error', res.message, 'error');
                            }
                        },
                        error: function(xhr) {
                            Swal.fire('Error', 'Failed to inject funds. Check the console.', 'error');
                            console.error(xhr.responseText);
                        }
                    });
                }
            });
        });

        function fetchApiData(uuid, tdNaans, tdBalance) {
            $.ajax({
                url: '/dashboard/authority/' + uuid + '/api-data',
                type: 'GET',
                success: function(response) {
                    if (response.error) {
                        tdNaans.html('<span class="text-danger" title="' + response.error + '">?</span>');
                        tdBalance.html('<span class="text-muted">' + response.balance + ' <i class="fas fa-exclamation-triangle text-warning" title="' + response.error + '"></i></span>');
                    } else {
                        tdNaans.html('<span class="badge badge-dark">' + response.naans_count + '</span>');
                        tdBalance.html('<strong>' + response.balance + '</strong>');
                    }
                },
                error: function() {
                    tdNaans.html('<span class="text-danger">Error</span>');
                    tdBalance.html('<span class="text-danger">Error</span>');
                }
            });
        }
    });
</script>
@stop
