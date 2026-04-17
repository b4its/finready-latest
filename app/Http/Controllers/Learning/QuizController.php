<?php

namespace App\Http\Controllers\Learning;

use App\Http\Controllers\Controller;
use App\Models\LearnProgress;
use App\Models\Room;
use App\Models\Score;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class QuizController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
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
        $room = Room::with(['questions', 'module'])->findOrFail($id);
        $userId = Auth::user()->id; // Mengambil ID user yang sedang login
        
        // Cek percobaan (attempts) sebelumnya
        $scoreRecord = Score::where('idUsers', $userId)->where('idRoom', $id)->first();
        $attempts = $scoreRecord ? $scoreRecord->attempts : 0;
        
        return view('learning.quiz', compact('room', 'attempts'));
    }

    /**
     * Menyimpan skor dan menghitung Learn Progress
     */
    public function submitScore(Request $request, string $id)
    {
        $request->validate([
            'correct_answers' => 'required|integer',
            'total_questions' => 'required|integer',
            'score_percentage' => 'required|numeric'
        ]);

        $userId = Auth::id();
        $room = Room::with('module')->findOrFail($id);

        // 1. Simpan ke tabel Score dan Increment Attempts
        $scoreRecord = Score::firstOrNew([
            'idUsers' => $userId,
            'idRoom'  => $room->id,
        ]);

        // Jika data sudah ada, tambahkan attempts. Jika belum, set ke 1.
        if ($scoreRecord->exists) {
            $scoreRecord->attempts += 1;
        } else {
            $scoreRecord->attempts = 1;
        }
        $scoreRecord->score = $request->score_percentage;
        $scoreRecord->save();

        // 2. Simpan ke tabel Learn Progress
        $modul = $room->module;
        $maxPoint = $modul ? $modul->max_point : 100;
        $totalKuis = $request->total_questions > 0 ? $request->total_questions : 1;

        // Kalkulasi: (max_point dari modul / total kuis) * total jawaban benar
        $calculatedPoint = (($maxPoint / $totalKuis) * $request->correct_answers) * 0.8;

        $progress = LearnProgress::firstOrNew([
            'idUsers' => $userId,
            'idModul' => $room->idModule,
            'idRoom'  => $room->id,
        ]);

        $progress->title = 'Quiz: ' . $room->name;
        $progress->type = 'Quiz';
        $progress->contents = 'Diselesaikan dengan skor: ' . $request->score_percentage . '%';
        $progress->point = $calculatedPoint;
        $progress->save();

        return response()->json([
            'success' => true,
            'current_attempts' => $scoreRecord->attempts,
            'point_earned' => $calculatedPoint
        ]);
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
