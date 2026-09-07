@extends('adminlte::page')

@section('title', 'Worker Errors')
@section('content_header')<h1><i class="fas fa-exclamation-triangle mr-2"></i> Worker Errors</h1>@stop
@section('content')
<div id="summary" class="row mb-3"><div class="col-12 text-muted">Loading...</div></div>
<div class="card card-dark"><div class="card-header"><h3 class="card-title">Records requiring review</h3></div><div class="card-body">
<p class="text-muted">Recoverable records are requeued automatically when normal workers are healthy and idle. Permanent failures require review outside this dashboard.</p>
<div class="form-row mb-3"><div class="col-md-2"><select id="list" class="form-control"><option value="all">All</option><option value="recoverable">Recoverable</option><option value="permanent">Permanent</option></select></div><div class="col-md-2"><select id="stage" class="form-control"><option value="all">All stages</option><option>metadata</option><option>availability</option><option>chain</option><option>replication</option></select></div><div class="col-md-3"><input id="authority_id" class="form-control" placeholder="Authority ID"></div><div class="col-md-3"><input id="error_code" class="form-control" placeholder="Error code"></div><div class="col-md-2"><button id="load" class="btn btn-primary">Apply filters</button></div></div>
<div class="table-responsive"><table class="table table-sm table-striped"><thead><tr><th>ARK</th><th>Status</th><th>Stage</th><th>Code</th><th>Detail</th><th>Attempts</th><th>Updated</th><th>CIDs</th></tr></thead><tbody id="items"><tr><td colspan="8">Loading...</td></tr></tbody></table></div><div id="pagination" class="mt-2"></div>
</div></div>
@endsection
@push('js')
<script>
const errorsUrl = @json(route('workers.errors.apiData')); let page = 1;
const esc = v => { const d=document.createElement('div'); d.textContent=v ?? '—'; return d.innerHTML; };
function loadErrors() { const p=new URLSearchParams({list:document.getElementById('list').value,stage:document.getElementById('stage').value,page,page_size:50}); const a=document.getElementById('authority_id').value.trim(); const c=document.getElementById('error_code').value.trim(); if(a)p.set('authority_id',a); if(c)p.set('error_code',c); fetch(`${errorsUrl}?${p}`).then(r=>r.json()).then(d=>{ const s=d.summary?.summary||d.summary||{}; document.getElementById('summary').innerHTML=`<div class="col-md-4"><div class="info-box"><span class="info-box-icon bg-warning"><i class="fas fa-redo"></i></span><div class="info-box-content"><span class="info-box-text">Recoverable</span><span class="info-box-number">${s.recoverable||0}</span></div></div></div><div class="col-md-4"><div class="info-box"><span class="info-box-icon bg-danger"><i class="fas fa-ban"></i></span><div class="info-box-content"><span class="info-box-text">Permanent</span><span class="info-box-number">${s.permanent||0}</span></div></div></div>`; document.getElementById('items').innerHTML=(d.items||[]).map(i=>`<tr><td><code>${esc(i.ark)}</code></td><td>${esc(i.status)}</td><td>${esc(i.stage)}</td><td><code>${esc(i.error_code)}</code></td><td>${esc(i.error)}</td><td>${esc(i.retry_count)}</td><td>${esc(i.updated_at)}</td><td>${esc(i.metadata?.level1_cid)}<br>${esc(i.metadata?.original_cid)}</td></tr>`).join('')||'<tr><td colspan="8" class="text-muted">No records found</td></tr>'; const q=d.pagination; document.getElementById('pagination').innerHTML=q?`<button class="btn btn-sm btn-outline-secondary" ${q.has_previous?'':'disabled'} onclick="page--;loadErrors()">Previous</button> <span class="mx-2">Page ${q.page} / ${q.total_pages||0}</span> <button class="btn btn-sm btn-outline-secondary" ${q.has_next?'':'disabled'} onclick="page++;loadErrors()">Next</button>`:''; }).catch(e=>document.getElementById('items').innerHTML=`<tr><td colspan="8" class="text-danger">${esc(e.message)}</td></tr>`); }
document.getElementById('load').addEventListener('click',()=>{page=1;loadErrors();}); loadErrors();
</script>
@endpush
