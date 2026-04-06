@extends('layouts.admin')
@section('title', 'Règles & consignes')
@section('breadcrumb') <a href="{{ route('admin.weddings.show', $wedding) }}">{{ $wedding->getCoupleName() }}</a> <i class="bi bi-chevron-right mx-2"></i><span>Règles</span> @endsection
@section('topbar-actions') <a href="{{ route('admin.weddings.rules.create', $wedding) }}" class="btn btn-sm btn-primary-custom"><i class="bi bi-plus-circle me-1"></i>Ajouter</a> @endsection

@section('content')
<div class="admin-card">
    @forelse($rules as $rule)
    <div class="d-flex align-items-center gap-3 p-3 border-bottom">
        <span class="badge bg-{{ $rule->type === 'allowed' ? 'success' : ($rule->type === 'forbidden' ? 'danger' : 'warning') }}" style="width:90px;text-align:center">{{ ['allowed' => 'Autorisé', 'forbidden' => 'Interdit', 'recommendation' => 'Conseil'][$rule->type] }}</span>
        @if($rule->icon) <i class="bi {{ $rule->icon }}" style="font-size:18px;color:var(--color-primary)"></i> @endif
        <div class="flex-grow-1"><div class="fw-semibold">{{ $rule->title }}</div>@if($rule->description) <div class="text-muted small">{{ Str::limit($rule->description, 100) }}</div> @endif</div>
        @if(!$rule->is_active) <span class="badge bg-secondary">Masqué</span> @endif
        <div class="d-flex gap-2">
            <a href="{{ route('admin.weddings.rules.edit', [$wedding, $rule]) }}" class="btn btn-xs btn-outline-secondary"><i class="bi bi-pencil"></i></a>
            <form method="POST" action="{{ route('admin.weddings.rules.destroy', [$wedding, $rule]) }}" onsubmit="return confirm('Supprimer ?')">@csrf @method('DELETE')<button type="submit" class="btn btn-xs btn-outline-danger"><i class="bi bi-trash"></i></button></form>
        </div>
    </div>
    @empty
    <div class="text-center py-5 text-muted"><i class="bi bi-shield-check fs-1 d-block mb-2" style="color:var(--color-primary)"></i><h4 class="font-serif">Aucune règle définie</h4><a href="{{ route('admin.weddings.rules.create', $wedding) }}" class="btn btn-primary-custom mt-3"><i class="bi bi-plus-circle me-2"></i>Ajouter une règle</a></div>
    @endforelse
</div>
@endsection
