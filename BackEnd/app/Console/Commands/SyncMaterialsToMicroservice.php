<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Material;
use Illuminate\Support\Facades\Http;

class SyncMaterialsToMicroservice extends Command
{
    protected $signature = 'sync:materials';
    protected $description = 'Memindahkan semua data materi lama ke microservice Go';

    public function handle()
    {
        $materials = Material::all();
        $this->info("Memulai sinkronisasi " . $materials->count() . " data...");

        foreach ($materials as $m) {
            try {
                $response = Http::post('http://127.0.0.1:9001/api/materials/sync', [
                    'material_id'   => (int)$m->material_id,
                    'class_id'      => (int)$m->class_id,
                    'user_id'       => (int)($m->user_id ?? 1),
                    'title'         => $m->title,
                    'material_name' => $m->material_name,
                    'week'          => (int)$m->week,
                    'file_path'     => $m->file_path,
                ]);

                if ($response->successful()) {
                    $this->info("✅ Berhasil: {$m->title}");
                } else {
                    $this->error("❌ Gagal: {$m->title} - " . $response->body());
                }
            } catch (\Exception $e) {
                $this->error("🚨 Error koneksi: " . $e->getMessage());
            }
        }

        $this->info("Sinkronisasi Selesai!");
    }
}
