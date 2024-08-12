@extends('adminlte::page')

@section('content')
    @if (session('message'))
        <div class="alert alert-{{ session('code') }} alert-dismissible">
            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
            {{ session('message') }}
        </div>
    @endif
    <a href="{{ route('accounts.create') }}" class="btn btn-custom btn-sm" style="margin-bottom: 10px;">NEW ACCOUNT</a>

    @if($manager == 'no')
        <button type="button" class="btn btn-custom btn-sm" style="margin-bottom: 10px;" data-toggle="modal" data-target="#accountManagerModal">
            ACCOUNT MANAGER
        </button>
    @elseif($manager == 'yes')
        <button type="button" class="btn btn-custom btn-sm" style="margin-bottom: 10px;" data-toggle="modal" data-target="#transferFundsModal">
            TRANSFER dARKS
        </button>
    @endif

    <div class="box">
        <div class="box-header with-border">
            <h4 class="box-title">Accounts</h4>
        </div>
        <div class="box-body no-padding">
            <div class="table-responsive">
                <table class="table">
                    <thead class="thead-light">
                        <tr>
                            <th>Name</th>
                            <th>Profile</th>
                            <th>Email</th>
                            <th>Balance</th>
                            <th>Wallet</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($accounts as $account)
                            <tr>
                                <td>{{ $account->organization_name }}</td>
                                <td>{{ $account->getProfileAlias() }}</td>
                                <td>{{ $account->contact_email }}</td>
                                <td>{{ $account->balance }}</td>
                                <td>
                                <span id="wallet"> {{ $account->address }} </span>
                                    <button class="btn btn-sm btn-info" onclick="copyToClipboard('#wallet')">
                                        <i class="fa fa-copy"></i>   Copy
                                    </button>
                                </td> 
                                <td>
                                    @if(!$account->shoulder)
                                        <a href="{{ route('accounts.account-create', $account->id) }}" alt="Create Account" title="Create Account" class="btn btn-success btn-sm"><i class="fa  fa-upload"></i></a>
                                    @endif
                                    @if($account->shoulder)
                                    <a href="{{ route('accounts.card_profile', $account->id) }}" alt="see" title="see" class="btn btn-info btn-sm"><i class="fa  fa-eye"></i></a>
                                    @endif        
                                    <button alt="Delete" title="Delete" class="btn btn-danger btn-sm" onclick="confirmDelete({{ $account->id }}, '{{ route('accounts.destroy', ['account' => $account->id]) }}')"><i class="fa fa-trash"></i></button>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        <div class="box-footer clearfix">
              {{ ($accounts != null)? $accounts->links(): null }}
        </div>
    </div>

    <!-- Modal for Account Manager -->
    <div class="modal fade" id="accountManagerModal" tabindex="-1" role="dialog" aria-labelledby="accountManagerModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="accountManagerModalLabel">Set Account Manager</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form id="accountManagerForm">
                        @csrf <!-- Token CSRF -->
                        <div class="form-group">
                            <label for="managerAddress">Address:</label>
                            <input type="text" class="form-control" id="managerAddress" name="managerAddress" required>
                        </div>
                        <button type="submit" class="btn btn-primary">Submit</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal for Transfer Funds -->
    <div class="modal fade" id="transferFundsModal" tabindex="-1" role="dialog" aria-labelledby="transferFundsModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="transferFundsModalLabel">Transfer Funds</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form id="transferFundsForm">
                        @csrf 
                        <div class="form-group">
                            <label for="fundsAddress">Address:</label>
                            <input type="text" class="form-control" id="fundsAddress" name="fundsAddress" required>
                        </div>
                        <div class="form-group">
                            <label for="balance">Balance:</label>
                            <input type="number" step="0.01" class="form-control" id="balance" name="balance" required>
                        </div>
                        <button type="submit" class="btn btn-primary">Transfer</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
<script>
    function copyToClipboard(element) {
        var $temp = $("<input>");
        $("body").append($temp);
        $temp.val($(element).text()).select();
        document.execCommand("copy");
        $temp.remove();
        alert('Copied to clipboard!');
    }
</script>
@stop

@section('js')
<script>
    document.getElementById('accountManagerForm').addEventListener('submit', function(event) {
        event.preventDefault();
        
        let address = document.getElementById('managerAddress').value;
        let token = document.querySelector('input[name="_token"]').value;

        fetch('{{ route("accounts.manager") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': token
            },
            body: JSON.stringify({ address: address })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('Account manager updated successfully');
                $('#accountManagerModal').modal('hide'); 
            } else {
                alert('Error updating account manager');
            }
        })
        .catch(error => console.error('Error:', error));
    });

    document.getElementById('transferFundsForm').addEventListener('submit', function(event) {
        event.preventDefault();

        let address = document.getElementById('fundsAddress').value;
        let balance = document.getElementById('balance').value;

        // Construa a URL com os parâmetros de consulta
        let url = `{{ route("accounts.transfer_funds") }}?address=${encodeURIComponent(address)}&balance=${encodeURIComponent(balance)}`;

        // Redireciona para a URL construida
        window.location.href = url;
    });
</script>
@stop
