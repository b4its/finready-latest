<?php

namespace App\Http\Controllers\Learning;

use App\Http\Controllers\Controller;
use App\Models\Modul;
use App\Models\ModuleContent;
use App\Models\Room;
use Illuminate\Http\Request;

class LearningController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // Ambil semua modul beserta kontennya (Eager Loading untuk performa)
        $modules = Modul::with('contents')->get();

        // Hitung statistik untuk sidebar
        $totalModules = $modules->count();
        $totalLessons = $modules->sum(function($module) {
            return $module->contents->count();
        });

        // Kirim data ke view
        return view('learning.index_learning', compact('modules', 'totalModules', 'totalLessons'));
    }

    public function showQuiz($id)
    {
        // Ambil data room beserta soalnya, dan modul untuk breadcrumb
        $room = Room::with(['questions', 'module'])->findOrFail($id);
        
        return view('learning.quiz', compact('room'));
    }

    public function showContent($id)
    {
        // Ambil data konten beserta relasi modulnya
        $content = ModuleContent::with('module')->findOrFail($id);
        $moduleContent_total = $content->count();
        
        $moduleKey = Modul::findOrFail($id);
        return view('learning.content', compact('content', 'moduleKey', 'moduleContent_total'));
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
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
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
