@extends('adminlte::page')
@section('title', 'Worker Errors')
@section('content_header')<h1><i class="fas fa-exclamation-triangle mr-2"></i>Permanent worker failures</h1>@stop
@section('content')
<div class="alert alert-info">Only records that require administrative review appear here. Scheduled waits and IPFS pinning do not.</div>
<div id="summary" class="mb-3 text-muted">Loading…</div>
<div class="card card-dark"><div class="card-body">
<div class="form-row mb-3"><div class="col-md-3"><select id="stage" class="form-control"><option value="all">All stages</option><option>metadata</option><option>availability</option><option>chain</option><option>replication</option></select></div><div class="col-md-4"><input id="authority" class="form-control" placeholder="Authority ID"></div><div class="col-md-3"><input id="code" class="form-control" placeholder="Error code"></div><div class="col-md-2"><button id="apply" class="btn btn-primary">Apply filters</button></div></div>
<div class="table-responsive"><table class="table table-sm table-striped"><thead><tr><th>ARK</th><th>Public state</th><th>Stage</th><th>Code</th><th>Detail</th><th>Attempts</th><th>Updated</th><th>CIDs</th></tr></thead><tbody id="items"><tr><td colspan="8">Loading…</td></tr></tbody></table></div><div id="pages"></div>
</div></div>
@endsection
@push('js')
<script>
const endpoint=@json(route('workers.errors.apiData')); let page=1;
const esc=v=>{const d=document.createElement('div');d.textContent=v??'—';return d.innerHTML};
function load(){const q=new URLSearchParams({stage:stage.value,page,page_size:50});if(authority.value.trim())q.set('authority_id',authority.value.trim());if(code.value.trim())q.set('error_code',code.value.trim());fetch(`${endpoint}?${q}`).then(r=>r.json()).then(d=>{const s=d.summary||{};summary.textContent=`${s.total||0} permanent failures require administrative review.`;items.innerHTML=(d.items||[]).map(i=>`<tr><td><code>${esc(i.ark)}</code></td><td>${esc(i.public_state)}</td><td>${esc(i.stage)}</td><td><code>${esc(i.error_code)}</code></td><td>${esc(i.detail)}</td><td>${esc(i.attempts)}</td><td>${esc(i.updated_at)}</td><td>${esc(i.level1_cid)}<br>${esc(i.level2_cid)}</td></tr>`).join('')||'<tr><td colspan="8" class="text-muted">No permanent failures</td></tr>';const p=d.pagination;pages.innerHTML=p?`<button class="btn btn-sm btn-outline-secondary" ${p.page>1?'':'disabled'} onclick="page--;load()">Previous</button> <span class="mx-2">Page ${p.page} / ${p.total_pages||0}</span> <button class="btn btn-sm btn-outline-secondary" ${p.page<p.total_pages?'':'disabled'} onclick="page++;load()">Next</button>`:''}).catch(e=>items.innerHTML=`<tr><td colspan="8" class="text-danger">${esc(e.message)}</td></tr>`)}
apply.addEventListener('click',()=>{page=1;load()});load();
</script>
@endpush
