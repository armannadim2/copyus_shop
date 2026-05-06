<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class AdminBulkActionController extends Controller
{
    public function handle(Request $request)
    {
        $request->validate([
            'action' => 'required|string',
            'entity_type' => 'required|string',
            'ids' => 'required|array',
            'ids.*' => 'integer' // some ids might be strings but mostly integer, maybe just required
        ]);

        $action = $request->input('action');
        $entityType = $request->input('entity_type');
        $ids = $request->input('ids');

        $modelClass = "App\\Models\\" . $entityType;

        if (!class_exists($modelClass)) {
            return redirect()->back()->with('error', 'Entitat no vàlida.');
        }

        if ($action === 'delete') {
            app($modelClass)->whereIn('id', $ids)->delete();
            return redirect()->back()->with('success', 'Acció en bloc aplicada correctament.');
        }

        return redirect()->back()->with('error', 'Acció no vàlida.');
    }
}
