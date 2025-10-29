<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AssistantRule;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AssistantRuleController extends Controller
{
    public function index() {
        $rules = AssistantRule::ordered()->get();
        return view('admin.assistant_rules.index', compact('rules'));
    }

    public function create() {
        return view('admin.assistant_rules.create');
    }

    public function store(Request $request) {
        $data = $request->validate([
            'title'      => ['required','string','max:150'],
            'keywords'   => ['required','string'], // coma o \n
            'use_regex'  => ['sometimes','boolean'],
            'match_mode' => ['required','in:any,all'],
            'response'   => ['required','string'],
            'is_active'  => ['sometimes','boolean'],
            'sort_order' => ['nullable','integer','min:0'],
        ]);

        $data['use_regex'] = (bool)($data['use_regex'] ?? false);
        $data['is_active'] = (bool)($data['is_active'] ?? false);
        $data['created_by'] = Auth::id();
        $data['updated_by'] = Auth::id();

        AssistantRule::create($data);
        return redirect()->route('admin.assistant_rules.index')->with('success', 'Regla creada.');
    }

    public function edit(AssistantRule $assistant_rule) {
        return view('admin.assistant_rules.edit', ['rule' => $assistant_rule]);
    }

    public function update(Request $request, AssistantRule $assistant_rule) {
        $data = $request->validate([
            'title'      => ['required','string','max:150'],
            'keywords'   => ['required','string'],
            'use_regex'  => ['sometimes','boolean'],
            'match_mode' => ['required','in:any,all'],
            'response'   => ['required','string'],
            'is_active'  => ['sometimes','boolean'],
            'sort_order' => ['nullable','integer','min:0'],
        ]);

        $data['use_regex'] = (bool)($data['use_regex'] ?? false);
        $data['is_active'] = (bool)($data['is_active'] ?? false);
        $data['updated_by'] = Auth::id();

        $assistant_rule->update($data);
        return redirect()->route('admin.assistant_rules.index')->with('success', 'Regla actualizada.');
    }

    public function destroy(AssistantRule $assistant_rule) {
        $assistant_rule->delete();
        return back()->with('success','Regla eliminada.');
    }

    public function toggle(AssistantRule $assistant_rule) {
        $assistant_rule->is_active = ! $assistant_rule->is_active;
        $assistant_rule->updated_by = Auth::id();
        $assistant_rule->save();
        return back()->with('success','Estado actualizado.');
    }
}
