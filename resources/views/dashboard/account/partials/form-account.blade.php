

<h6 class="heading-small text-muted">Basics informations</h6>
<div class="row">
    <div class="col-md-6">
        <div class="form-group">
            {{ Form::label('organization_name', 'Name') }}
            {{ Form::text(
                'organization_name',
                old('organization_name', $institution->name ?? null),
                ['class' => 'form-control', 'readonly' => 'readonly']
            ) }}
        </div>
    </div>
    <div class="col-md-6">
        <div class="form-group">
            {{ Form::label('contact_email', 'Email') }}
            {{ Form::email(
                'contact_email',
                old('contact_email', $institution->email ?? null),
                ['class' => 'form-control', 'readonly' => isset($institution)]
            ) }}
        </div>
    </div>
</div>
<div class="row">
    
    <div class="col-md-1">
        <div class="form-group">
            {{ Form::label('naan', 'Naan') }}
            {{ Form::text(
                'naan',
                old('naan', $account->naan),
                ['class' => 'form-control', 'readonly' => 'readonly']
            ) }}

        </div>
    </div>
    <div class="col-md-2">
        <div class="form-group">
            {{ Form::label('institution_id', 'Institution ID') }}
            {{ Form::text(
                'institution_id',
                old('institution_id', $institution->id ?? null),
                ['class' => 'form-control', 'readonly' => isset($institution)]
            ) }}
        </div>
    </div>
    <div class="col-md-2">
        <div class="form-group">
            {{ Form::label('sholder', 'Sholder') }}
            {{ Form::text(
                'sholder',
                old('sholder', $account->sholder ?? null),
                ['class' => 'form-control']
            ) }}
        </div>
    </div>
    <div class="col-md-2">
        <div class="form-group">
            {{ Form::label('noid', 'Noid') }}
            {{ Form::text(
                'noid',
                old('noid', $account->noid ?? null),
                ['class' => 'form-control']
            ) }}
        </div>
    </div>
    <div class="col-md-5">
        <div class="form-group">
                {{ Form::label('payload_schema', 'Payload Schema') }}
                {{ Form::select('payload_schema', [
                    'basic2024a' => 'basic2024a'
                ], null, array('class' => 'form-control', 'readonly' => 'readonly')) }}
            </div>
        </div>
    </div>
    <div class="col-md-12">
        <div class="form-group">
                {{ Form::label('Noid Prov Addr', 'Noid Provider Address') }}
                {{ Form::text(
                    '$account->noidprovider_addr',
                    old('noidprovider_addr', $account->noidprovider_addr ?? null),
                    ['class' => 'form-control']
                ) }}
            </div>
        </div>
    </div>
    <h6 class="heading-small text-muted">Blockchain Information</h6>
    <div class="col-md-6">
        <div class="form-group">
            {{ Form::label('address', 'Address') }}
            {{ Form::text(
                'address',
                old('address', $account->address ?? null),
                ['class' => 'form-control']
            ) }}
        </div>
    </div>
    <div class="col-md-6">
        <div class="form-group">
                {{ Form::label('private_key', 'Private Key') }}
                {{ Form::text(
                    'private_key',
                    old('private_key', $account->private_key ?? null),
                    ['class' => 'form-control']
                ) }}
            </div>
        </div>
    </div>
</div>


