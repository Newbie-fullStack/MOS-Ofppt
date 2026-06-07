<?php

namespace App\Http\Controllers;

use App\Enums\AppModule;
use App\Http\Resources\LessonResource;
use App\Models\Lesson;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class LessonController extends Controller
{
    public function modules(Request $request): JsonResponse
    {
        $user = $request->user();
        $modules = [AppModule::WORD, AppModule::EXCEL, AppModule::POWERPOINT];

        $data = collect($modules)->map(function (AppModule $module) use ($user): array {
            $totalLessons = Lesson::query()->published()->forModule($module->value)->count();
            $completedLessons = $user->getCompletedLessonsForModule($module->value);
            $isEnrolled = $user->isEnrolledIn($module->value);
            $progressPct = $totalLessons > 0 ? (int) round(($completedLessons / $totalLessons) * 100) : 0;

            return [
                'module' => $module->value,
                'label' => $module->label(),
                'color' => $module->color(),
                'totalLessons' => $totalLessons,
                'completedLessons' => $completedLessons,
                'progressPct' => $progressPct,
                'isEnrolled' => $isEnrolled,
            ];
        })->values();

        return response()->json(['data' => $data]);
    }

    public function index(string $module): JsonResponse
    {
        $lessons = Lesson::query()
            ->published()
            ->forModule($module)
            ->ordered()
            ->get();

        return response()->json([
            'data' => LessonResource::collection($lessons),
        ]);
    }

    public function show(string $module, string $slug): JsonResponse
    {
        $lesson = Lesson::query()
            ->published()
            ->forModule($module)
            ->where('slug', $slug)
            ->firstOrFail();

        return response()->json([
            'data' => new LessonResource($lesson),
        ]);
    }
}
