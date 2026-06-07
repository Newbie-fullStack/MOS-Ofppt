<?php

namespace App\Http\Controllers;

use App\Models\Exercise;
use Illuminate\Http\JsonResponse;

class ExerciseController extends Controller
{
    public function index(string $module): JsonResponse
    {
        $items = Exercise::query()
            ->where('is_published', true)
            ->whereHas('lesson', fn ($query) => $query->where('app_module', strtoupper($module)))
            ->orderBy('order')
            ->get();

        return response()->json([
            'data' => $items,
        ]);
    }

    public function download(Exercise $exercise): JsonResponse
    {
        if (! $exercise->file_url) {
            return response()->json([
                'message' => 'Aucun fichier attache a cet exercice.',
            ], 404);
        }

        return response()->json([
            'data' => [
                'exerciseId' => $exercise->id,
                'fileUrl' => $exercise->file_url,
            ],
        ]);
    }
}
