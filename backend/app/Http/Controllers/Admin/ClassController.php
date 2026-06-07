<?php

namespace App\Http\Controllers\Admin;

use App\Enums\Role;
use App\Http\Controllers\Controller;
use App\Models\ClassRoom;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
class ClassController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $user = $request->user();
        $role = strtoupper((string) ($user->role->value ?? $user->role));

        $query = ClassRoom::query()
            ->with(['trainer:id,first_name,last_name,email'])
            ->withCount('members');

        if ($role === Role::TRAINER->value) {
            $query->where('trainer_id', $user->id);
        }

        $classes = $query->orderBy('code')->get();

        return response()->json(['data' => $classes]);
    }

    public function store(Request $request): JsonResponse
    {
        $user = $request->user();
        $role = strtoupper((string) ($user->role->value ?? $user->role));

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'code' => ['required', 'string', 'max:20', 'unique:class_rooms,code'],
            'trainer_id' => ['sometimes', 'integer', 'exists:users,id'],
        ]);

        $code = strtoupper(trim($validated['code']));
        if (! in_array($code, ClassRoom::ALLOWED_CODES, true)) {
            return response()->json([
                'message' => 'Code classe invalide. Utilisez : DD101, DD201, DD102 ou DD202.',
                'error' => 'INVALID_CLASS_CODE',
            ], 422);
        }

        $trainerId = $role === Role::TRAINER->value
            ? $user->id
            : ($validated['trainer_id'] ?? $user->id);

        $classRoom = ClassRoom::query()->create([
            'name' => $validated['name'],
            'description' => $validated['description'] ?? null,
            'code' => $code,
            'trainer_id' => $trainerId,
            'is_active' => true,
        ]);

        return response()->json([
            'data' => $classRoom->load('trainer:id,first_name,last_name,email'),
            'message' => 'Classe créée avec succès.',
        ], 201);
    }

    public function show(ClassRoom $class): JsonResponse
    {
        return response()->json([
            'data' => $class->load([
                'trainer:id,first_name,last_name,email',
                'members:id,first_name,last_name,email,xp_points,streak_days',
            ]),
        ]);
    }

    public function update(Request $request, ClassRoom $class): JsonResponse
    {
        $this->authorizeClass($request, $class);

        $validated = $request->validate([
            'name' => ['sometimes', 'string', 'max:255'],
            'description' => ['sometimes', 'nullable', 'string'],
            'trainer_id' => ['sometimes', 'integer', 'exists:users,id'],
            'is_active' => ['sometimes', 'boolean'],
        ]);

        $class->update($validated);

        return response()->json([
            'data' => $class->fresh()->load('trainer:id,first_name,last_name,email'),
            'message' => 'Classe mise à jour.',
        ]);
    }

    public function destroy(Request $request, ClassRoom $class): JsonResponse
    {
        $role = strtoupper((string) ($request->user()->role->value ?? $request->user()->role));
        if ($role !== Role::ADMIN->value) {
            return response()->json([
                'message' => 'Seul un administrateur peut supprimer une classe.',
                'error' => 'INSUFFICIENT_PERMISSIONS',
            ], 403);
        }

        $class->delete();

        return response()->json([
            'message' => 'Classe supprimée.',
        ]);
    }

    public function addMember(Request $request, ClassRoom $class): JsonResponse
    {
        $this->authorizeClass($request, $class);

        $validated = $request->validate([
            'user_id' => ['required', 'integer', 'exists:users,id'],
        ]);

        $student = User::query()->findOrFail($validated['user_id']);
        if (strtoupper((string) ($student->role->value ?? $student->role)) !== Role::STUDENT->value) {
            return response()->json([
                'message' => 'Seuls les comptes apprenants peuvent être ajoutés.',
                'error' => 'NOT_A_STUDENT',
            ], 422);
        }

        $student->classes()->sync([
            $class->id => ['joined_at' => now()],
        ]);

        return response()->json([
            'data' => $student->load('classes:id,name,code'),
            'message' => "Apprenant ajouté à la classe {$class->code}.",
        ]);
    }

    public function removeMember(Request $request, ClassRoom $class, User $user): JsonResponse
    {
        $this->authorizeClass($request, $class);

        $class->members()->detach($user->id);

        return response()->json([
            'message' => 'Apprenant retiré de la classe.',
        ]);
    }

    private function authorizeClass(Request $request, ClassRoom $class): void
    {
        $user = $request->user();
        $role = strtoupper((string) ($user->role->value ?? $user->role));

        if ($role === Role::ADMIN->value) {
            return;
        }

        if ($role === Role::TRAINER->value && (int) $class->trainer_id === (int) $user->id) {
            return;
        }

        abort(response()->json([
            'message' => 'Accès refusé à cette classe.',
            'error' => 'INSUFFICIENT_PERMISSIONS',
        ], 403));
    }
}
