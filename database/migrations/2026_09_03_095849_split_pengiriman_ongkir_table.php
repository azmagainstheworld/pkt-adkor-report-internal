<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Buat tabel pengiriman_ongkir
        Schema::create('pengiriman_ongkir', function (Blueprint $table) {
            $table->id();
            $table->integer('tahun');
            $table->string('bulan', 50);
            $table->bigInteger('ongkir_dalam_negeri')->default(0);
            $table->bigInteger('ongkir_luar_negeri')->default(0);
            $table->json('data_tambahan')->nullable();
            $table->timestamps();

            $table->unique(['tahun', 'bulan']);
        });

        // 2. Pindahkan data ongkir dari pengiriman_dokumen ke pengiriman_ongkir
        $dokumenRecords = DB::table('pengiriman_dokumen')->get();
        foreach ($dokumenRecords as $record) {
            if ($record->ongkir_dalam_negeri > 0 || $record->ongkir_luar_negeri > 0) {
                DB::table('pengiriman_ongkir')->insert([
                    'tahun' => $record->tahun,
                    'bulan' => $record->bulan,
                    'ongkir_dalam_negeri' => $record->ongkir_dalam_negeri,
                    'ongkir_luar_negeri' => $record->ongkir_luar_negeri,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }

        // 3. Hapus data dari pengiriman_dokumen yang HANYA berisi ongkir (tidak ada volume)
        DB::table('pengiriman_dokumen')
            ->where('penerimaan_mailroom', 0)
            ->where('registrasi_surat_masuk_dof', 0)
            ->where('pengiriman_dalam_negeri', 0)
            ->where('pengiriman_luar_negeri', 0)
            ->where('e_materai', 0)
            ->delete();

        // 4. Hapus kolom ongkir dari pengiriman_dokumen
        Schema::table('pengiriman_dokumen', function (Blueprint $table) {
            $table->dropColumn(['ongkir_dalam_negeri', 'ongkir_luar_negeri']);
        });
    }

    public function down(): void
    {
        Schema::table('pengiriman_dokumen', function (Blueprint $table) {
            $table->bigInteger('ongkir_dalam_negeri')->default(0);
            $table->bigInteger('ongkir_luar_negeri')->default(0);
        });

        $ongkirRecords = DB::table('pengiriman_ongkir')->get();
        foreach ($ongkirRecords as $record) {
            $dokumen = DB::table('pengiriman_dokumen')
                ->where('tahun', $record->tahun)
                ->where('bulan', $record->bulan)
                ->first();

            if ($dokumen) {
                DB::table('pengiriman_dokumen')
                    ->where('id', $dokumen->id)
                    ->update([
                        'ongkir_dalam_negeri' => $record->ongkir_dalam_negeri,
                        'ongkir_luar_negeri' => $record->ongkir_luar_negeri
                    ]);
            } else {
                DB::table('pengiriman_dokumen')->insert([
                    'tahun' => $record->tahun,
                    'bulan' => $record->bulan,
                    'ongkir_dalam_negeri' => $record->ongkir_dalam_negeri,
                    'ongkir_luar_negeri' => $record->ongkir_luar_negeri,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }

        Schema::dropIfExists('pengiriman_ongkir');
    }
};
