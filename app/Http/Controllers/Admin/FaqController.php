<?php
// app/Http/Controllers/Admin/FaqController.php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Faq;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class FaqController extends Controller
{
    public function index() {
        $faqs = Faq::ordered()->get();
        return view('admin.faqs.index', compact('faqs'));
    }

    public function create() {
        return view('admin.faqs.create');
    }

    public function store(Request $request) {
        $data = $request->validate([
            'question'   => ['required','string','max:255'],
            'answer'     => ['required','string'],
            'category'   => ['nullable','string','max:100'],
            'tags'       => ['nullable','array'],
            'is_active'  => ['sometimes','boolean'],
            'sort_order' => ['nullable','integer','min:0'],
        ]);
        $data['tags'] = $data['tags'] ?? [];
        $data['is_active'] = (bool)($data['is_active'] ?? false);
        $data['created_by'] = Auth::id();
        $data['updated_by'] = Auth::id();

        Faq::create($data);
        return redirect()->route('admin.faqs.index')->with('success','FAQ creada.');
    }

    public function edit(Faq $faq) {
        return view('admin.faqs.edit', compact('faq'));
    }

    public function update(Request $request, Faq $faq) {
        $data = $request->validate([
            'question'   => ['required','string','max:255'],
            'answer'     => ['required','string'],
            'category'   => ['nullable','string','max:100'],
            'tags'       => ['nullable','array'],
            'is_active'  => ['sometimes','boolean'],
            'sort_order' => ['nullable','integer','min:0'],
        ]);
        $data['tags'] = $data['tags'] ?? [];
        $data['is_active'] = (bool)($data['is_active'] ?? false);
        $data['updated_by'] = Auth::id();

        $faq->update($data);
        return redirect()->route('admin.faqs.index')->with('success','FAQ actualizada.');
    }

    public function destroy(Faq $faq) {
        $faq->delete();
        return back()->with('success','FAQ eliminada.');
    }

    public function toggle(Faq $faq) {
        $faq->is_active = ! $faq->is_active;
        $faq->updated_by = Auth::id();
        $faq->save();
        return back()->with('success','Estado actualizado.');
    }
}
