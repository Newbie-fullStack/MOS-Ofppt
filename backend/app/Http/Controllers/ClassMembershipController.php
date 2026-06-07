<?php

namespace App\Http\Controllers;

use App\Enums\Role;
use App\Models\ClassRoom;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ClassMembershipController extends Controller
{
    public function available(): JsonResponse
    {
        $classes = ClassRoom::query()
            ->where('is_active', true)
            ->whereIn('code', ClassRoom::ALLOWED_CODES)
            ->orderBy('code')
            ->get(['id', 'name', 'code', 'description']);

        return response()->json(['data' => $classes]);
    }

    public function join(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'class_code' => ['required', 'string', 'max:20'],
        ]);

        $user = $request->user();
        if (($user->role->value ?? $user->role) !== Role::STUDENT->value) {
            return response()->json([
                'message' => 'Seuls les apprenants peuvent rejoindre une classe.',
                'error' => 'NOT_A_STUDENT',
            ], 422);
        }

        $code = strtoupper(trim($validated['class_code']));
        $classRoom = ClassRoom::query()
            ->where('code', $code)
            ->where('is_active', true)
            ->first();

        if (! $classRoom) {
            return response()->json([
                'message' => 'Classe introuvable. Codes valides : DD101, DD201, DD102, DD202.',
                'error' => 'CLASS_NOT_FOUND',
            ], 404);
        }

        $user->classes()->sync([
            $classRoom->id => ['joined_at' => now()],
        ]);

        $user->load('classes');

        return response()->json([
            'data' => [
                'classRoom' => [
                    'id' => $classRoom->id,
                    'name' => $classRoom->name,
                    'code' => $classRoom->code,
                ],
            ],
            'message' => "Vous avez rejoint la classe {$classRoom->code}.",
        ]);
    }

    public static function assignStudentToClass(User $user, string $classCode): void
    {
        if (($user->role->value ?? $user->role) !== Role::STUDENT->value) {
            return;
        }

        $classRoom = ClassRoom::query()
            ->where('code', strtoupper(trim($classCode)))
            ->where('is_active', true)
            ->first();

        if ($classRoom) {
            $user->classes()->sync([
                $classRoom->id => ['joined_at' => now()],
            ]);
        }
    }
}
