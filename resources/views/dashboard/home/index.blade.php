@extends('adminlte::page')

@section('title', 'Dashboard')

@section('content_header')
    <h5>
        Welcome {{ \Auth::user()->name }} || Today: {{ now()->format('d/m/Y') }}
    </h5>
@endsection

@section('content')
    @if (session('message'))
        <div class="alert alert-{{ session('code') }} alert-dismissible">
            <h4>Atenção!</h4>
            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
            {{ session('message') }}
        </div>
    @endif
    <div class="row">
        <div class="col-2">
            <div class="small-box bg-purple">
            <div class="inner">
                <h3>NAANs<sup style="font-size: 20px"></sup> </h3>
                <p>{!!$countNaans!!}</p>
            </div>
            <div class="icon">
                <i class="fas fa-fw fa-hashtag"></i>
            </div>
            <a href="{{route('naans.index')}}" class="small-box-footer">See <i class="fas fa-arrow-circle-right"></i></a>
            </div>
        </div>
        <div class="col-2">
            <div class="small-box bg-info">
            <div class="inner">
                <h3>ARKs<sup style="font-size: 20px"> stored</sup> </h3>
                <p id="arks-count-display"><i class="fas fa-spinner fa-spin"></i> Loading...</p>
            </div>
            <div class="icon">
                <i class="fas fa-fw fa-link"></i>
            </div>
            <a href="{{route('institutions.index-ark')}}" class="small-box-footer">See ARKs <i class="fas fa-arrow-circle-right"></i></a>
            </div>
        </div>
        <div class="col-2">
            <div class="small-box bg-purple">
            <div class="inner">
                <h3>Block<sup style="font-size: 20px"> last</sup> </h3>

                <p>{!!$blockNumber!!}</p>
            </div>
            <div class="icon">
                <i class="fas fa-fw fa-spinner"></i>
            </div>
            <a href="{{route('blockchains.index')}}" class="small-box-footer">see <i class="fas fa-arrow-circle-right"></i></a>
            </div>
        </div>
        <div class="col-2">
            <div class="small-box bg-success" id="workers-card">
            <div class="inner">
                <h3 id="workers-count"><i class="fas fa-spinner fa-spin" style="font-size:1.5rem"></i></h3>
                <p>Workers</p>
            </div>
            <div class="icon">
                <i class="fas fa-fw fa-cogs"></i>
            </div>
            <a href="{{route('workers.index')}}" class="small-box-footer">See <i class="fas fa-arrow-circle-right"></i></a>
            </div>
        </div>
        <div class="col-2">
            <div class="small-box bg-warning">
            <div class="inner">
                <h3 id="processing-count"><i class="fas fa-spinner fa-spin" style="font-size:1.5rem"></i></h3>
                <p>Status</p>
            </div>
            <div class="icon">
                <i class="fas fa-fw fa-layer-group"></i>
            </div>
            <a href="{{route('workers.index')}}" class="small-box-footer">See <i class="fas fa-arrow-circle-right"></i></a>
            </div>
        </div>
        <div class="col-2">
            <div class="small-box bg-danger">
            <div class="inner">
                <h3>Errors<sup style="font-size: 20px"></sup></h3>
                <p id="errors-count">—</p>
            </div>
            <div class="icon">
                <i class="fas fa-fw  fa-wrench"></i>
            </div>
            <a href="{{route('workers.errors')}}" class="small-box-footer">see <i class="fas fa-arrow-circle-right"></i></a>
            </div>
        </div>
    </div>
</div>

@endsection

@push('js')
<script>
    // Fetch Worker status
    const workerApiUrl = '{{ route("workers.apiData") }}?detail=simple';
    fetch(workerApiUrl)
        .then(r => r.json())
        .then(d => {
            const card  = document.getElementById('workers-card');
            const count = document.getElementById('workers-count');
            const proc  = document.getElementById('processing-count');

            if (d.error) {
                count.innerHTML = '<small>N/A</small>';
                proc.innerHTML  = '<small>N/A</small>';
                return;
            }

            const overallColor = { ok: 'bg-success', degraded: 'bg-warning', down: 'bg-danger' };
            card.className = 'small-box ' + (overallColor[d.overall] || 'bg-secondary');

            const workers  = d.workers || {};
            const enabled = Object.values(workers).filter(w => w.enabled !== false);
            const alive    = enabled.filter(w => w.alive).length;
            count.textContent = `${alive}/${enabled.length} running`;
            proc.textContent = d.overall || '—';
        })
        .catch(() => {
            document.getElementById('workers-count').innerHTML = '<small>N/A</small>';
            document.getElementById('processing-count').innerHTML = '<small>N/A</small>';
        });

    // Fetch ARKs stored count
    const arksCountUrl = @json(route('arks.api.count'));
    fetch(arksCountUrl)
        .then(response => response.json())
        .then(data => {
            // Assuming the JSON returns something like { "count": 10 } or just the number.
            let countText = 'Error';
            if (data && typeof data.count !== 'undefined') {
                countText = new Intl.NumberFormat().format(data.count);
            } else if (data && !data.error && typeof data === 'number') {
                countText = new Intl.NumberFormat().format(data);
            } else if (data && data.error) {
                countText = '<small class="text-danger">API Error</small>';
            } else {
                // fallback if the JSON structure is different but still holds the number
                // try to extract the first key if it's an object, or just display it if it's string
                try {
                    let vals = Object.values(data);
                    if(vals.length > 0 && typeof vals[0] === 'number') {
                        countText = new Intl.NumberFormat().format(vals[0]);
                    } else {
                        countText = JSON.stringify(data).substring(0, 10);
                    }
                } catch(e) {
                    countText = 'Data Error';
                }
            }
            document.getElementById('arks-count-display').innerHTML = countText;
        })
        .catch(error => {
            console.error('Error fetching ARKs count:', error);
            document.getElementById('arks-count-display').innerHTML = '<small class="text-danger">Failed to connect</small>';
        });
</script>
@endpush
