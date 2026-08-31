<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Cache;

class ProgressController extends Controller
{
    public function importProgress($uuid)
    {
        $total = Cache::get('import_total_' . $uuid, 0);
        $current = Cache::get('import_current_' . $uuid, 0);

        return response()->json([
            'total' => (int)$total,
            'current' => (int)$current,
            'percentage' => $total > 0 ? min(100, round(($current / $total) * 100)) : 0
        ]);
    }
}
