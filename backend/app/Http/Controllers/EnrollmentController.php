<?php

namespace App\Http\Controllers;

use App\Models\Enrollment;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class EnrollmentController extends Controller
{
    public function store(Request $request, string $module): JsonResponse
    {
        $module = strtoupper($module);
        $enrollment = Enrollment::query()->updateOrCreate(
            ['user_id' => $request->user()->id, 'app_module' => $module],
            ['enrolled_at' => now()]
        );

        return response()->json([
            'data' => $enrollment,
            'message' => 'Inscription module enregistree.',
        ], 201);
    }
}
