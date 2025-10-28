<?php
namespace App\Http\Controllers\Portal;

use App\Http\Controllers\Controller;
use App\Models\Faq;
use Illuminate\Http\Request;

class FaqController extends Controller
{
    public function list(Request $request) {
        $q = trim($request->get('q', ''));
        $faqs = Faq::active()->search($q)->ordered()->get([
            'id','question','answer','category','tags'
        ]);
        return response()->json(['ok'=>true, 'items'=>$faqs]);
    }
}
