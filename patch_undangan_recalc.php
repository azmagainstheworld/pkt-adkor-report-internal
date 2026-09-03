<?php

$filePath = 'app/Http/Controllers/UndanganController.php';
$content = file_get_contents($filePath);

$oldRecalculate = <<<PHP
    private function recalculateRekap()
    {
        \$detailCounts = \App\Models\UndanganDetail::selectRaw('undangan_id, jenis_undangan, count(*) as total')
            ->groupBy('undangan_id', 'jenis_undangan')
            ->get();
            
        \App\Models\Undangan::query()->update(['undangan_intern' => 0, 'undangan_ekstern' => 0]);
        
        foreach (\$detailCounts as \$count) {
            \$undangan = \App\Models\Undangan::find(\$count->undangan_id);
            if (\$undangan) {
                if (strtolower(\$count->jenis_undangan) == 'intern') {
                    \$undangan->undangan_intern = \$count->total;
                } else {
                    \$undangan->undangan_ekstern = \$count->total;
                }
                \$undangan->save();
            }
        }

        // Delete any Undangan (rekap) that has no details
        \App\Models\Undangan::whereDoesntHave('details')->delete();
    }
PHP;

$newRecalculate = <<<PHP
    private function recalculateRekap(\$undangan_id = null)
    {
        // If an ID is provided, only recalculate that specific record
        if (\$undangan_id) {
            \$undangan = \App\Models\Undangan::find(\$undangan_id);
            if (\$undangan) {
                \$intern = \App\Models\UndanganDetail::where('undangan_id', \$undangan_id)->where('jenis_undangan', 'Intern')->count();
                \$ekstern = \App\Models\UndanganDetail::where('undangan_id', \$undangan_id)->where('jenis_undangan', 'Eksternal')->count();
                
                // Only update if there are details. If no details exist, leave it alone (it might be manual input)
                if (\$intern > 0 || \$ekstern > 0) {
                    \$undangan->update([
                        'undangan_intern' => \$intern,
                        'undangan_ekstern' => \$ekstern
                    ]);
                }
            }
            return;
        }

        // Fallback for bulk operations: recalculate all that HAVE details
        \$detailCounts = \App\Models\UndanganDetail::selectRaw('undangan_id, jenis_undangan, count(*) as total')
            ->groupBy('undangan_id', 'jenis_undangan')
            ->get();
            
        // Reset ONLY records that have at least one detail (so we don't wipe out manual Rekap imports)
        \$idsWithDetails = \App\Models\UndanganDetail::select('undangan_id')->distinct()->pluck('undangan_id');
        \App\Models\Undangan::whereIn('id', \$idsWithDetails)->update(['undangan_intern' => 0, 'undangan_ekstern' => 0]);
        
        foreach (\$detailCounts as \$count) {
            \$undangan = \App\Models\Undangan::find(\$count->undangan_id);
            if (\$undangan) {
                if (strtolower(\$count->jenis_undangan) == 'intern') {
                    \$undangan->undangan_intern = \$count->total;
                } else {
                    \$undangan->undangan_ekstern = \$count->total;
                }
                \$undangan->save();
            }
        }
    }
PHP;

$content = str_replace($oldRecalculate, $newRecalculate, $content);

// Update calls to pass ID where possible
$content = str_replace(
    "\$this->recalculateRekap();\n\n        return redirect()->back()->with('success', 'Berhasil menambahkan rincian undangan.');",
    "\$this->recalculateRekap(\$undangan->id);\n\n        return redirect()->back()->with('success', 'Berhasil menambahkan rincian undangan.');",
    $content
);

$content = str_replace(
    "\$this->recalculateRekap();\n\n        return redirect()->back()->with('success', 'Berhasil mengupdate rincian undangan.');",
    "\$this->recalculateRekap(\$undangan->id);\n\n        return redirect()->back()->with('success', 'Berhasil mengupdate rincian undangan.');",
    $content
);

// destroyDetail needs the ID before deleting
$destroyOld = <<<PHP
    public function destroyDetail(\$id)
    {
        \App\Models\UndanganDetail::findOrFail(\$id)->delete();
        \$this->recalculateRekap();
        return redirect()->back()->with('success', 'Berhasil menghapus rincian undangan.');
    }
PHP;

$destroyNew = <<<PHP
    public function destroyDetail(\$id)
    {
        \$detail = \App\Models\UndanganDetail::findOrFail(\$id);
        \$undanganId = \$detail->undangan_id;
        \$detail->delete();
        
        // Recount. If it drops to 0, it means it's empty now. We can choose to delete it or leave it as 0.
        // The safest is to recount it.
        \$intern = \App\Models\UndanganDetail::where('undangan_id', \$undanganId)->where('jenis_undangan', 'Intern')->count();
        \$ekstern = \App\Models\UndanganDetail::where('undangan_id', \$undanganId)->where('jenis_undangan', 'Eksternal')->count();
        
        \App\Models\Undangan::where('id', \$undanganId)->update([
            'undangan_intern' => \$intern,
            'undangan_ekstern' => \$ekstern
        ]);
        
        // Delete if 0
        if (\$intern == 0 && \$ekstern == 0) {
            \App\Models\Undangan::where('id', \$undanganId)->delete();
        }

        return redirect()->back()->with('success', 'Berhasil menghapus rincian undangan.');
    }
PHP;

$content = str_replace($destroyOld, $destroyNew, $content);

file_put_contents($filePath, $content);
echo "Fixed recalculateRekap logic.\n";

