<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Utils\Upload;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class UserController extends Controller
{
    private function deleteOldAvatar(?string $avatarPath): void
    {
        Upload::deleteFile($avatarPath);
    }

    // ── GET /api/users — all users except the logged-in admin ─────
    public function index(Request $request): JsonResponse
    {
        $authUser = $request->attributes->get('user');

        try {
            $users = User::select('id', 'username', 'email', 'role', 'avatar', 'email_verified_at', 'created_at')
                ->where('id', '!=', $authUser['id'])
                ->orderBy('id', 'desc')
                ->get();

            return response()->json([
                'success' => true,
                'count'   => $users->count(),
                'data'    => $users,
            ]);

        } catch (\Throwable $e) {
            return response()->json(['success' => false, 'message' => 'Server error', 'error' => $e->getMessage()], 500);
        }
    }

    // ── GET /api/users/{id} ───────────────────────────────────────
    public function show(int $id): JsonResponse
    {
        try {
            $user = User::select('id', 'username', 'email', 'role', 'avatar', 'email_verified_at', 'created_at')
                ->find($id);

            if (!$user) {
                return response()->json(['success' => false, 'message' => 'User not found'], 404);
            }

            return response()->json(['success' => true, 'data' => $user]);

        } catch (\Throwable $e) {
            return response()->json(['success' => false, 'message' => 'Server error', 'error' => $e->getMessage()], 500);
        }
    }

    // ── POST /api/users — create user (admin, avatar optional) ────
    public function store(Request $request): JsonResponse
    {
        $username = trim($request->input('username', ''));
        $email    = trim($request->input('email', ''));
        $password = $request->input('password', '');
        $role     = $request->input('role', 'user');

        if (!$username || !$email || !$password) {
            return response()->json([
                'success' => false,
                'message' => 'username, email, and password are required',
            ], 400);
        }

        if (!in_array($role, ['admin', 'user'], true)) {
            return response()->json(['success' => false, 'message' => 'role must be admin or user'], 400);
        }

        try {
            $exists = User::where('email', $email)->orWhere('username', $username)->first();
            if ($exists) {
                return response()->json(['success' => false, 'message' => 'Username or email already exists'], 409);
            }

            $hashed     = password_hash($password, PASSWORD_BCRYPT, ['cost' => 10]);
            $avatarFile = $request->file('avatar');
            $avatarUrl  = Upload::handleAvatar($avatarFile && $avatarFile->isValid() ? $avatarFile : null);

            $user = User::create([
                'username'          => $username,
                'email'             => $email,
                'password'          => $hashed,
                'role'              => $role,
                'avatar'            => $avatarUrl,
                'email_verified_at' => null,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'User created successfully',
                'data'    => [
                    'id'       => $user->id,
                    'username' => $username,
                    'email'    => $email,
                    'role'     => $role,
                    'avatar'   => $avatarUrl,
                ],
            ], 201);

        } catch (\RuntimeException $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 400);
        } catch (\Throwable $e) {
            return response()->json(['success' => false, 'message' => 'Server error', 'error' => $e->getMessage()], 500);
        }
    }

    // ── PUT /api/users/{id} — update user (avatar optional) ───────
    public function update(Request $request, int $id): JsonResponse
    {
        $username = trim($request->input('username', ''));
        $email    = trim($request->input('email', ''));
        $role     = $request->input('role', '');
        $password = $request->input('password', '');
        $authUser = $request->attributes->get('user');

        if ($id === (int) $authUser['id']) {
            return response()->json([
                'success' => false,
                'message' => 'Use Edit Profile to update your own account',
            ], 400);
        }

        if (!$username || !$email || !$role) {
            return response()->json([
                'success' => false,
                'message' => 'username, email, and role are required',
            ], 400);
        }

        if (!in_array($role, ['admin', 'user'], true)) {
            return response()->json(['success' => false, 'message' => 'role must be admin or user'], 400);
        }

        try {
            $existing = User::find($id);
            if (!$existing) {
                return response()->json(['success' => false, 'message' => 'User not found'], 404);
            }

            $taken = User::where(function ($q) use ($username, $email) {
                $q->where('username', $username)->orWhere('email', $email);
            })->where('id', '!=', $id)->first();

            if ($taken) {
                return response()->json(['success' => false, 'message' => 'Username or email already taken'], 409);
            }

            // Handle avatar
            $avatarUrl  = $existing->avatar;
            $avatarFile = $request->file('avatar');

            if ($avatarFile && $avatarFile->isValid()) {
                $this->deleteOldAvatar($avatarUrl);
                $avatarUrl = Upload::handleAvatar($avatarFile);
            }

            if ($request->input('remove_avatar', '') === 'true') {
                $this->deleteOldAvatar($avatarUrl);
                $avatarUrl = null;
            }

            $updateData = [
                'username' => $username,
                'email'    => $email,
                'role'     => $role,
                'avatar'   => $avatarUrl,
            ];

            // If email changed — reset verification status
            if ($email !== $existing->email) {
                $updateData['email_verified_at'] = null;
            }

            if ($password) {
                if (strlen($password) < 6) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Password must be at least 6 characters',
                    ], 400);
                }
                $updateData['password'] = password_hash($password, PASSWORD_BCRYPT, ['cost' => 10]);
            }

            $existing->update($updateData);

            return response()->json([
                'success' => true,
                'message' => 'User updated successfully',
                'data'    => [
                    'id'       => $id,
                    'username' => $username,
                    'email'    => $email,
                    'role'     => $role,
                    'avatar'   => $avatarUrl,
                ],
            ]);

        } catch (\RuntimeException $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 400);
        } catch (\Throwable $e) {
            return response()->json(['success' => false, 'message' => 'Server error', 'error' => $e->getMessage()], 500);
        }
    }

    // ── DELETE /api/users/{id} ────────────────────────────────────
    public function destroy(Request $request, int $id): JsonResponse
    {
        $authUser = $request->attributes->get('user');

        if ($id === (int) $authUser['id']) {
            return response()->json(['success' => false, 'message' => 'You cannot delete your own account'], 400);
        }

        try {
            $existing = User::find($id);
            if (!$existing) {
                return response()->json(['success' => false, 'message' => 'User not found'], 404);
            }

            $this->deleteOldAvatar($existing->avatar);
            $existing->delete();

            return response()->json(['success' => true, 'message' => 'User deleted successfully']);

        } catch (\Throwable $e) {
            return response()->json(['success' => false, 'message' => 'Server error', 'error' => $e->getMessage()], 500);
        }
    }
}
