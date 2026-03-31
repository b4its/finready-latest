<?php

namespace App\Http\Controllers\Learning;

use App\Http\Controllers\Controller;
use App\Models\LearnProgress;
use App\Models\Modul;
use App\Models\ModuleContent;
use App\Models\Room;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LearningController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // Ambil semua modul beserta kontennya (Eager Loading untuk performa)
        $modules = Modul::with('contents')->get();
        $user_account = Auth::user();

        // Hitung statistik untuk sidebar
        $totalModules = $modules->count();
        $totalLessons = $modules->sum(function($module) {
            return $module->contents->count();
        });

        // Kirim data ke view
        return view('learning.index_learning', 
        [
            'modules'=> $modules,
            'totalModules'=> $totalModules,
            'totalLessons'=> $totalLessons,
            'user_account'=> $user_account,
        ]
        );
    }
    
    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // 1. Validasi Input sesuai key dari AJAX
        $request->validate([
            'user_id'           => 'required',
            'modul_id'          => 'required',
            'module_content_id' => 'required',
            'score'             => 'required|numeric',
            'summary'           => 'nullable|string',
            'title'             => 'required|string',
            'type'              => 'required|string',
        ]);

        try {
            // 2. Simpan atau Update data ke tabel learn_progress
            // Menggunakan updateOrCreate agar jika user mengulang konten yang sama, 
            // data point/summary diperbarui (tidak duplikat)
            $progress = LearnProgress::updateOrCreate(
                [
                    'idUsers'        => $request->user_id,
                    'idModulContent' => $request->module_content_id,
                ],
                [
                    'idModul'  => $request->modul_id,
                    'title'    => $request->title,
                    'type'     => $request->type,
                    'contents' => $request->summary, // Mapping summary ke kolom contents
                    'point'    => $request->score,   // Mapping score ke kolom point
                ]
            );

            return response()->json([
                'status'  => 'success',
                'message' => 'Progress belajar berhasil disimpan!',
                'data'    => $progress
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Gagal menyimpan: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
                // 1. Ambil data konten berserta relasi modul (dan room jika butuh)
        $content = ModuleContent::with(['module.contents', 'module.rooms'])->findOrFail($id);
        $user_account = Auth::user();
        // 2. Ambil modul utama dari relasi yang sudah didapat
        $moduleKey = $content->module;
        
        // 3. Hitung jumlah KONTEN YANG ADA DI DALAM MODUL TERSEBUT saja
        // Jika total konten = 0 (meski mustahil karena $content sudah ketemu 1), jadikan 1 agar tidak error 'Division by zero' di JS.
        $moduleContent_total = $moduleKey->contents->count() ?: 1;
        
        return view('learning.content', 
        [
            'content'=> $content,
            'moduleKey'=> $moduleKey,
            'user_account'=> $user_account,
            'moduleContent_total'=> $moduleContent_total,
        
        ]
        );
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
