<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Promotion;
use App\Models\ClassModel;

class PromoController extends Controller
{
    /**
     * Tampilan untuk Web Admin
     */
    public function index()
    {
        $classes = ClassModel::all();
        $promos = Promotion::with('class')->orderBy('created_at', 'desc')->get();
        return view('admin.promo.index', compact('classes', 'promos'));
    }

    /**
     * Simpan Promo dari Web Admin
     */
    public function store(Request $request)
    {
        $request->validate([
            'code' => 'required|unique:promotions,code',
            'class_id' => 'required',
            'discount_type' => 'required|in:percent,fixed',
            'discount_value' => 'required|numeric',
            'quota' => 'required|integer',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
        ]);

        Promotion::create([
            'code'           => strtoupper($request->code),
            'class_id'       => $request->class_id,
            'discount_type'  => $request->discount_type,
            'discount_percent' => $request->discount_value, 
            'quota'          => $request->quota,
            'start_date'     => $request->start_date,
            'end_date'       => $request->end_date,
            'is_active'      => 1
        ]);

        return redirect()->back()->with('success', 'Kode promo berhasil diterbitkan!');
    }

    /**
     * Hapus Promo
     */
    public function destroy($id)
    {
        Promotion::findOrFail($id)->delete();
        return redirect()->back()->with('success', 'Kode promo berhasil dihentikan!');
    }

    /**
     * 🔥 FUNGSI UNTUK MOBILE (API CHECK PROMO)
     * Tambahkan ini agar error "Undefined Method" hilang
     */
    public function checkPromo(Request $request)
    {
        // 1. Validasi Input
        $request->validate([
            'code' => 'required',
            'class_id' => 'required'
        ]);

        // 2. Cari promo yang aktif, sesuai kelas, tanggal masih berlaku, dan kuota > 0
        $promo = Promotion::where('code', strtoupper($request->code))
            ->where('class_id', $request->class_id)
            ->where('start_date', '<=', now())
            ->where('end_date', '>=', now())
            ->where('quota', '>', 0)
            ->where('is_active', 1)
            ->first();

        if (!$promo) {
            return response()->json([
                'status' => 'error',
                'message' => 'Kode promo tidak valid atau kuota sudah habis.'
            ], 404);
        }

        // 3. Cari harga asli kelas
        $kelas = ClassModel::find($request->class_id);
        if (!$kelas) {
            return response()->json(['message' => 'Kelas tidak ditemukan'], 404);
        }

        $basePrice = (int) $kelas->price;
        $discountAmount = 0;

        // 4. Hitung Diskon
        if ($promo->discount_type == 'percent') {
            // Jika tipe persen (misal 20%)
            $discountAmount = ($basePrice * $promo->discount_percent) / 100;
        } else {
            // Jika tipe rupiah (misal Rp 50.000)
            $discountAmount = $promo->discount_percent; 
        }

        $finalPrice = $basePrice - $discountAmount;
        if ($finalPrice < 0) $finalPrice = 0;

        return response()->json([
            'status' => 'success',
            'discount_amount' => (int) $discountAmount,
            'final_price' => (int) $finalPrice
        ]);
    }
}