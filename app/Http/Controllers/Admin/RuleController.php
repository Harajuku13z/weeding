<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Wedding;
use App\Models\Rule;
use Illuminate\Http\Request;

class RuleController extends Controller
{
    public function index(Wedding $wedding)
    {
        $this->authorize('view', $wedding);
        $rules = $wedding->rules;
        return view('admin.rules.index', compact('wedding', 'rules'));
    }

    public function create(Wedding $wedding)
    {
        $this->authorize('update', $wedding);
        return view('admin.rules.create', compact('wedding'));
    }

    public function store(Request $request, Wedding $wedding)
    {
        $this->authorize('update', $wedding);

        $data = $request->validate([
            'type' => 'required|in:allowed,forbidden,recommendation',
            'icon' => 'nullable|string|max:50',
            'title' => 'required|string|max:200',
            'description' => 'nullable|string',
            'sort_order' => 'nullable|integer',
            'is_active' => 'boolean',
        ]);

        $data['wedding_id'] = $wedding->id;
        $data['is_active'] = $request->boolean('is_active', true);

        Rule::create($data);

        return redirect()->route('admin.weddings.rules.index', $wedding)->with('success', 'Règle ajoutée !');
    }

    public function edit(Wedding $wedding, Rule $rule)
    {
        $this->authorize('update', $wedding);
        return view('admin.rules.edit', compact('wedding', 'rule'));
    }

    public function update(Request $request, Wedding $wedding, Rule $rule)
    {
        $this->authorize('update', $wedding);

        $data = $request->validate([
            'type' => 'required|in:allowed,forbidden,recommendation',
            'icon' => 'nullable|string|max:50',
            'title' => 'required|string|max:200',
            'description' => 'nullable|string',
            'sort_order' => 'nullable|integer',
            'is_active' => 'boolean',
        ]);

        $data['is_active'] = $request->boolean('is_active');
        $rule->update($data);

        return redirect()->route('admin.weddings.rules.index', $wedding)->with('success', 'Règle mise à jour !');
    }

    public function destroy(Wedding $wedding, Rule $rule)
    {
        $this->authorize('update', $wedding);
        $rule->delete();
        return back()->with('success', 'Règle supprimée.');
    }

    public function show(Wedding $wedding, Rule $rule)
    {
        return redirect()->route('admin.weddings.rules.edit', [$wedding, $rule]);
    }
}
