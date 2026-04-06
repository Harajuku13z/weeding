@extends('layouts.admin')
@section('title', 'Thème & design')
@section('breadcrumb')
<a href="{{ route('admin.weddings.show', $wedding) }}">{{ $wedding->getCoupleName() }}</a>
<i class="bi bi-chevron-right mx-2"></i><span>Thème & design</span>
@endsection

@section('content')
<form method="POST" action="{{ route('admin.weddings.theme.update', $wedding) }}" enctype="multipart/form-data">
    @csrf @method('PUT')
    <div class="row g-4">

        <!-- Couleurs -->
        <div class="col-lg-6">
            <div class="admin-card">
                <div class="card-header-custom"><h3 class="card-title-custom font-serif">Couleurs</h3></div>
                <div class="card-body-custom">
                    @foreach([
                        ['color_primary', 'Couleur principale', '#c8a97e'],
                        ['color_secondary', 'Couleur secondaire', '#f5e6d3'],
                        ['color_accent', 'Couleur accent', '#8b6355'],
                        ['color_background', 'Couleur fond', '#fdfaf7'],
                        ['color_text', 'Couleur texte', '#3d2b1f'],
                    ] as [$field, $label, $default])
                    <div class="row g-2 align-items-center mb-3">
                        <div class="col-7">
                            <label class="form-label mb-1">{{ $label }}</label>
                            <input type="text" name="{{ $field }}" class="form-control form-control-sm"
                                   value="{{ old($field, $theme->$field ?? $default) }}"
                                   id="{{ $field }}_text">
                        </div>
                        <div class="col-5">
                            <label class="form-label mb-1 invisible">Picker</label>
                            <input type="color" class="form-control form-control-sm form-control-color"
                                   value="{{ old($field, $theme->$field ?? $default) }}"
                                   data-sync="{{ $field }}_text" style="height:38px">
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>

        <!-- Typographie -->
        <div class="col-lg-6">
            <div class="admin-card">
                <div class="card-header-custom"><h3 class="card-title-custom font-serif">Typographie & style</h3></div>
                <div class="card-body-custom">
                    <div class="mb-3">
                        <label class="form-label">Police des titres</label>
                        <select name="font_title" class="form-select">
                            @foreach(['Playfair Display', 'Cormorant Garamond', 'Great Vibes', 'Dancing Script', 'Libre Baskerville', 'Cardo'] as $font)
                            <option value="{{ $font }}" {{ ($theme->font_title ?? 'Playfair Display') === $font ? 'selected' : '' }}>{{ $font }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Police du corps</label>
                        <select name="font_body" class="form-select">
                            @foreach(['Lato', 'Open Sans', 'Raleway', 'Nunito', 'Roboto', 'Jost'] as $font)
                            <option value="{{ $font }}" {{ ($theme->font_body ?? 'Lato') === $font ? 'selected' : '' }}>{{ $font }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Arrondi des bords</label>
                        <select name="border_radius" class="form-select">
                            @foreach(['4px' => 'Carré', '8px' => 'Léger', '12px' => 'Moyen', '20px' => 'Arrondi', '50px' => 'Pill'] as $val => $label)
                            <option value="{{ $val }}" {{ ($theme->border_radius ?? '12px') === $val ? 'selected' : '' }}>{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Intensité des animations</label>
                        <select name="animation_intensity" class="form-select">
                            <option value="none" {{ ($theme->animation_intensity ?? 'medium') === 'none' ? 'selected' : '' }}>Aucune</option>
                            <option value="light" {{ ($theme->animation_intensity ?? 'medium') === 'light' ? 'selected' : '' }}>Légère</option>
                            <option value="medium" {{ ($theme->animation_intensity ?? 'medium') === 'medium' ? 'selected' : '' }}>Moyenne</option>
                            <option value="heavy" {{ ($theme->animation_intensity ?? 'medium') === 'heavy' ? 'selected' : '' }}>Intense</option>
                        </select>
                    </div>
                </div>
            </div>
        </div>

        <!-- Dress code -->
        <div class="col-12">
            <div class="admin-card">
                <div class="card-header-custom"><h3 class="card-title-custom font-serif">Ambiance & dress code</h3></div>
                <div class="card-body-custom">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Style / nom du dress code</label>
                            <input type="text" name="dress_code_style" class="form-control" value="{{ old('dress_code_style', $theme->dress_code_style) }}" placeholder="Champêtre chic, Black tie...">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Niveau de formalité</label>
                            <input type="text" name="dress_code_formality" class="form-control" value="{{ old('dress_code_formality', $theme->dress_code_formality) }}" placeholder="Semi-formel, Casual élégant...">
                        </div>
                        <div class="col-12">
                            <label class="form-label">Description générale</label>
                            <textarea name="dress_code_description" class="form-control" rows="3">{{ old('dress_code_description', $theme->dress_code_description) }}</textarea>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Conseils pour les hommes</label>
                            <textarea name="dress_code_men" class="form-control" rows="2">{{ old('dress_code_men', $theme->dress_code_men) }}</textarea>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Conseils pour les femmes</label>
                            <textarea name="dress_code_women" class="form-control" rows="2">{{ old('dress_code_women', $theme->dress_code_women) }}</textarea>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Conseils accessoires</label>
                            <textarea name="dress_code_accessories" class="form-control" rows="2">{{ old('dress_code_accessories', $theme->dress_code_accessories) }}</textarea>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Couleurs interdites</label>
                            <input type="text" name="forbidden_colors" class="form-control" value="{{ old('forbidden_colors', $theme->forbidden_colors) }}" placeholder="Blanc, noir strict...">
                        </div>
                        <div class="col-12">
                            <label class="form-label">Description de l'ambiance / mood</label>
                            <textarea name="mood_description" class="form-control" rows="2" placeholder="Une journée baignée de lumière dorée...">{{ old('mood_description', $theme->mood_description) }}</textarea>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Matières / textures</label>
                            <input type="text" name="mood_textures" class="form-control" value="{{ old('mood_textures', $theme->mood_textures) }}" placeholder="Lin, dentelle, soie légère...">
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Sections à afficher sur l'invitation -->
        <div class="col-12">
            <div class="admin-card">
                <div class="card-header-custom">
                    <h3 class="card-title-custom font-serif">Sections à afficher</h3>
                    <small class="text-muted ms-2">Cochez les blocs à afficher sur la page d'invitation</small>
                </div>
                <div class="card-body-custom">
                    <div class="row g-3">
                        @php
                            $sectionLabels = [
                                'story' => 'Notre histoire',
                                'gallery' => 'Galerie',
                                'details' => 'Détails / Infos',
                                'program' => 'Programme',
                                'venues' => 'Lieux',
                                'dresscode' => 'Dress code',
                                'gifts' => 'Liste de cadeaux',
                                'rules' => 'Consignes',
                                'rsvp' => 'RSVP',
                                'accommodation' => 'Hébergement',
                            ];
                        @endphp
                        @foreach($sectionLabels as $key => $label)
                            @php $section = $sections[$key] ?? null; @endphp
                            @if($section)
                            <div class="col-6 col-md-4">
                                <div class="form-check form-switch">
                                    <input type="checkbox" name="section_visible[]" value="{{ $key }}" id="section_{{ $key }}"
                                           class="form-check-input" {{ $section->is_active ? 'checked' : '' }}>
                                    <label class="form-check-label" for="section_{{ $key }}">
                                        {{ $label }}
                                        @if($key === 'gifts') <span class="text-muted">(cadeaux)</span> @endif
                                    </label>
                                </div>
                            </div>
                            @endif
                        @endforeach
                    </div>
                    <p class="text-muted small mt-2 mb-0">
                        <i class="bi bi-info-circle me-1"></i> Décochez « Liste de cadeaux » pour masquer entièrement la section Cadeaux sur l’invitation.
                    </p>
                </div>
            </div>
        </div>

        <!-- Palette de couleurs (max 4 couleurs) -->
        <div class="col-12">
            <div class="admin-card">
                <div class="card-header-custom">
                    <h3 class="card-title-custom font-serif">Palette de couleurs du mariage</h3>
                    <small class="text-muted ms-2">Jusqu'à 4 couleurs principales</small>
                </div>
                <div class="card-body-custom">
                    <div id="paletteContainer">
                        @for($i = 0; $i < 4; $i++)
                            @php $color = $palette[$i] ?? null; @endphp
                            <div class="palette-row row g-2 align-items-center mb-2" data-index="{{ $i }}">
                                <div class="col-3">
                                    <label class="form-label form-label-sm mb-1">Nom</label>
                                    <input type="text" name="palette[{{ $i }}][name]" class="form-control form-control-sm"
                                           value="{{ $color->name ?? '' }}" placeholder="Ex: Champagne">
                                </div>
                                <div class="col-3">
                                    <label class="form-label form-label-sm mb-1">Couleur</label>
                                    <input type="text" name="palette[{{ $i }}][hex_color]" class="form-control form-control-sm"
                                           value="{{ $color->hex_color ?? '' }}" placeholder="#c8a97e" id="palette_{{ $i }}_hex">
                                </div>
                                <div class="col-2">
                                    <label class="form-label form-label-sm mb-1 invisible">Picker</label>
                                    <input type="color" class="form-control form-control-sm form-control-color"
                                           value="{{ $color->hex_color ?? '#c8a97e' }}" style="height:34px"
                                           data-sync="palette_{{ $i }}_hex">
                                </div>
                                <div class="col-3">
                                    <label class="form-label form-label-sm mb-1">Description</label>
                                    <input type="text" name="palette[{{ $i }}][description]" class="form-control form-control-sm"
                                           value="{{ $color->description ?? '' }}" placeholder="Optionnel">
                                </div>
                                <div class="col-1 d-flex align-items-end">
                                    <button type="button" class="btn btn-outline-danger btn-sm w-100"
                                            onclick="clearPaletteRow({{ $i }})" title="Supprimer cette couleur">
                                        <i class="bi bi-x"></i>
                                    </button>
                                </div>
                            </div>
                        @endfor
                    </div>
                </div>
            </div>
        </div>

        <!-- Inspirations tenues : ajout (dans le formulaire thème) -->
        <div class="col-12">
            <div class="admin-card">
                <div class="card-header-custom">
                    <h3 class="card-title-custom font-serif">Photos d'inspiration tenues</h3>
                    <small class="text-muted ms-2">Elles s'affichent dans la section « L'univers de notre mariage » sur l'invitation</small>
                </div>
                <div class="card-body-custom">
                    @if(!$inspirations->count())
                    <p class="text-muted small mb-3">Aucune photo pour l'instant. Ajoutez des images ci-dessous puis enregistrez le thème.</p>
                    @endif
                    <div>
                        <label class="form-label fw-medium">Ajouter des photos</label>
                        <input type="file" name="inspirations[]" class="form-control" accept="image/*" multiple>
                        <p class="form-text small mb-0">Sélectionnez une ou plusieurs images, puis cliquez sur « Enregistrer le thème » pour les enregistrer.</p>
                    </div>
                </div>
            </div>
        </div>

    </div>

    <div class="d-flex gap-3 mt-4">
        <button type="submit" class="btn btn-primary-custom btn-lg">
            <i class="bi bi-check-circle me-2"></i>Enregistrer le thème
        </button>
        <a href="{{ route('admin.weddings.show', $wedding) }}" class="btn btn-outline-secondary">Annuler</a>
    </div>
</form>

@if($inspirations->count())
<!-- Liste des photos enregistrées (formulaires de suppression hors du formulaire thème pour éviter le 405) -->
<div class="mt-4">
    <div class="admin-card">
        <div class="card-header-custom">
            <h3 class="card-title-custom font-serif">Photos enregistrées</h3>
            <small class="text-muted ms-2">{{ $inspirations->count() }} photo(s) — supprimez si besoin</small>
        </div>
        <div class="card-body-custom">
            <div class="d-flex flex-wrap gap-3">
                @foreach($inspirations as $insp)
                <div class="text-center position-relative">
                    <img src="{{ Storage::url($insp->image) }}" alt="" class="rounded border" style="width:120px;height:150px;object-fit:cover">
                    @if($insp->caption)
                    <div class="small text-muted mt-1" style="max-width:120px">{{ Str::limit($insp->caption, 20) }}</div>
                    @endif
                    <form action="{{ route('admin.weddings.inspirations.destroy', [$wedding, $insp]) }}" method="POST" class="d-inline mt-1" onsubmit="return confirm('Supprimer cette photo d\'inspiration ?');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-sm btn-outline-danger" title="Supprimer">
                            <i class="bi bi-trash"></i>
                        </button>
                    </form>
                </div>
                @endforeach
            </div>
        </div>
    </div>
</div>
@endif
@endsection

@push('scripts')
<script>
// Sync color pickers with text inputs (couleurs principales + palette)
document.querySelectorAll('input[type="color"]').forEach(picker => {
    const syncId = picker.dataset.sync;
    let textInput = null;
    if (syncId) {
        textInput = document.getElementById(syncId);
    } else {
        const row = picker.closest('.row');
        textInput = row?.querySelector('input[type="text"][name*="hex_color"]');
    }
    if (!textInput) return;

    picker.addEventListener('input', () => { textInput.value = picker.value; });
    textInput.addEventListener('input', () => {
        if (/^#[0-9a-fA-F]{6}$/.test(textInput.value)) picker.value = textInput.value;
    });
});

// Vider une ligne de palette
function clearPaletteRow(index) {
    const row = document.querySelector('.palette-row[data-index="'+index+'"]');
    if (!row) return;
    row.querySelectorAll('input[type="text"]').forEach(i => i.value = '');
}
</script>
@endpush
