@extends('adminlte::page')

@section('content')
<div class="row">
    <div class="col-md-12">
        <a href="{{ route('accounts.index') }}" class="btn btn-info" style="margin-bottom: 20px;">
            <i class="glyphicon glyphicon-triangle-left"></i>
            <span>Return to Accounts</span>
        </a>
    </div>
</div>
<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-text-width"></i>
                    Account Details
                </h3>
            </div>
            <div class="card-body">
                <blockquote>
                    <h3>{{ $account->organization_name }}</h3>
                    <h5><i class="fa fa-envelope"></i> Email: {{ $account->contact_email }}</h5>
                    <h5><i class="fa fa-calendar"></i> Checkin: {{ $account->checkin_date }}</h5>
                </blockquote>
                <h5><b>DNAM Auth Id</b>: {{ $account->dnam_auth_id }}</h5>
                <h5><b>Naan</b>: {{ $account->naan }}</h5>
                <h5><b>Payload Schema</b>: {{ $account->payload_schema }}</h5>
                <h5><b>Shoulder</b>: {{ $account->shoulder }}</h5>
                <h5><b>Noid Len</b>: {{ $account->noid_len }}</h5>
                <h5><b>Noid Prov Addr</b>: {{ $account->noidprovider_addr }}</h5>
            </div>
        </div>
    </div>
</div>
<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-text-width"></i>
                    Wallet Details
                </h3>
            </div>
            <div class="card-body">
                <!-- Wallet with copy feature -->
                <h5>
                    <i class="fa fa-wallet"></i> Wallet: 
                    <span id="wallet">{{ $account->address }}</span>
                    <button class="btn btn-sm btn-info" onclick="copyToClipboard('#wallet')">
                        <i class="fa fa-copy"></i> Copy
                    </button>
                </h5>

                <!-- Private Key with copy feature -->
                <h5>
                    <i class="fa fa-key"></i> Private Key: 
                    <span id="privateKey">{{ $account->private_key }}</span>
                    <button class="btn btn-sm btn-info" onclick="copyToClipboard('#privateKey')">
                        <i class="fa fa-copy"></i> Copy
                    </button>
                </h5>

                <h5><i class="fa fa-dollar-sign"></i> Balance: {{ $account->balance }}</h5>
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
