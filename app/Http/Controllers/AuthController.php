<?php

namespace App\Http\Controllers;

use App\Models\TokenBlacklist;
use App\Models\User;
use App\Models\UserToken;
use App\Utils\Mailer;
use App\Utils\Upload;
use Firebase\JWT\JWT;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AuthController extends Controller
{
    /**
     * Create / replace a user_tokens record and return the raw hex token.
     */
    private function createToken(int $userId, string $type, int $expiresInHours = 24): string
    {
        $token     = bin2hex(random_bytes(32));
        $expiresAt = date('Y-m-d H:i:s', time() + $expiresInHours * 3600);

        // Remove any existing token of the same type for this user
        UserToken::where('user_id', $userId)->where('type', $type)->delete();

        UserToken::create([
            'user_id'    => $userId,
            'token'      => $token,
            'type'       => $type,
            'expires_at' => $expiresAt,
        ]);

        return $token;
    }

    // ── POST /api/register ────────────────────────────────────────
    public function register(Request $request): JsonResponse
    {
        $username = trim($request->input('username', ''));
        $email    = trim($request->input('email', ''));
        $password = $request->input('password', '');

        if (!$username || !$email || !$password) {
            return response()->json(['success' => false, 'message' => 'All fields are required'], 400);
        }

        try {
            $exists = User::where('email', $email)->orWhere('username', $username)->first();
            if ($exists) {
                return response()->json(['success' => false, 'message' => 'Username or email already exists'], 409);
            }

            $hashed = password_hash($password, PASSWORD_BCRYPT, ['cost' => 10]);

            $user = User::create([
                'username' => $username,
                'email'    => $email,
                'password' => $hashed,
                'role'     => 'admin',
            ]);

            $token = $this->createToken($user->id, 'email_verify', 24);
            Mailer::sendVerificationEmail($email, $username, $token);

            return response()->json([
                'success' => true,
                'message' => 'Account created! Please check your email to verify your account.',
                'data'    => ['id' => $user->id, 'username' => $username, 'email' => $email, 'role' => 'user'],
            ], 201);

        } catch (\Throwable $e) {
            return response()->json(['success' => false, 'message' => 'Server error', 'error' => $e->getMessage()], 500);
        }
    }

    // ── POST /api/login ───────────────────────────────────────────
    public function login(Request $request): JsonResponse
    {
        $email    = trim($request->input('email', ''));
        $password = $request->input('password', '');

        if (!$email || !$password) {
            return response()->json(['success' => false, 'message' => 'Email and password are required'], 400);
        }

        try {
            $user = User::where('email', $email)->first();

            if (!$user) {
                return response()->json(['success' => false, 'message' => 'Invalid email or password'], 401);
            }

            if (!password_verify($password, $user->password)) {
                return response()->json(['success' => false, 'message' => 'Invalid email or password'], 401);
            }

            if (empty($user->email_verified_at)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Please verify your email before logging in.',
                    'code'    => 'EMAIL_NOT_VERIFIED',
                ], 403);
            }

            $secret  = env('JWT_SECRET', '');
            $payload = [
                'id'       => $user->id,
                'username' => $user->username,
                'email'    => $user->email,
                'role'     => $user->role,
                'iat'      => time(),
                'exp'      => time() + 86400, // 24h
            ];
            $token = JWT::encode($payload, $secret, 'HS256');

            return response()->json([
                'success' => true,
                'message' => 'Login successful',
                'data'    => [
                    'token' => $token,
                    'user'  => [
                        'id'       => $user->id,
                        'username' => $user->username,
                        'email'    => $user->email,
                        'role'     => $user->role,
                        'avatar'   => $user->avatar,
                    ],
                ],
            ]);

        } catch (\Throwable $e) {
            return response()->json(['success' => false, 'message' => 'Server error', 'error' => $e->getMessage()], 500);
        }
    }

    // ── POST /api/logout ──────────────────────────────────────────
    public function logout(Request $request): JsonResponse
    {
        $token = $request->attributes->get('token');

        try {
            TokenBlacklist::create(['token' => $token, 'created_at' => now()]);

            return response()->json(['success' => true, 'message' => 'Logged out successfully']);

        } catch (\Throwable $e) {
            return response()->json(['success' => false, 'message' => 'Server error', 'error' => $e->getMessage()], 500);
        }
    }

    // ── GET /api/verify-email?token=xxx ──────────────────────────
    public function verifyEmail(Request $request): JsonResponse
    {
        $token = $request->query('token', '');

        if (!$token) {
            return response()->json(['success' => false, 'message' => 'Token is required'], 400);
        }

        try {
            $row = UserToken::where('token', $token)->where('type', 'email_verify')->first();

            if (!$row) {
                return response()->json(['success' => false, 'message' => 'Invalid or expired verification link.'], 400);
            }

            if (now() > $row->expires_at) {
                return response()->json(['success' => false, 'message' => 'Verification link has expired.'], 400);
            }

            // Check if already verified (race condition / double submit)
            $user = User::find($row->user_id);
            if ($user && !empty($user->email_verified_at)) {
                $row->delete();
                return response()->json(['success' => true, 'message' => 'Email already verified! You can log in.']);
            }

            User::where('id', $row->user_id)->update(['email_verified_at' => now()]);
            $row->delete();

            return response()->json(['success' => true, 'message' => 'Email verified successfully! You can now log in.']);

        } catch (\Throwable $e) {
            return response()->json(['success' => false, 'message' => 'Server error', 'error' => $e->getMessage()], 500);
        }
    }

    // ── POST /api/resend-verification ─────────────────────────────
    public function resendVerification(Request $request): JsonResponse
    {
        $email = trim($request->input('email', ''));

        if (!$email) {
            return response()->json(['success' => false, 'message' => 'Email is required'], 400);
        }

        try {
            $user = User::where('email', $email)->first();

            if (!$user) {
                return response()->json(['success' => false, 'message' => 'No account found with that email.'], 404);
            }

            if (!empty($user->email_verified_at)) {
                return response()->json(['success' => false, 'message' => 'Email is already verified.'], 400);
            }

            $token = $this->createToken($user->id, 'email_verify', 24);
            Mailer::sendVerificationEmail($email, $user->username, $token);

            return response()->json(['success' => true, 'message' => 'Verification email resent! Check your inbox.']);

        } catch (\Throwable $e) {
            return response()->json(['success' => false, 'message' => 'Server error', 'error' => $e->getMessage()], 500);
        }
    }

    // ── POST /api/forgot-password ─────────────────────────────────
    public function forgotPassword(Request $request): JsonResponse
    {
        $email = trim($request->input('email', ''));

        if (!$email) {
            return response()->json(['success' => false, 'message' => 'Email is required'], 400);
        }

        try {
            $user = User::where('email', $email)->first();

            // Always return 200 to prevent user enumeration
            if (!$user) {
                return response()->json(['success' => true, 'message' => 'If that email exists, a reset link has been sent.']);
            }

            $token = $this->createToken($user->id, 'password_reset', 1);
            Mailer::sendPasswordResetEmail($email, $user->username, $token);

            return response()->json(['success' => true, 'message' => 'Password reset email sent! Check your inbox.']);

        } catch (\Throwable $e) {
            return response()->json(['success' => false, 'message' => 'Server error', 'error' => $e->getMessage()], 500);
        }
    }

    // ── POST /api/reset-password ──────────────────────────────────
    public function resetPassword(Request $request): JsonResponse
    {
        $token    = $request->input('token', '');
        $password = $request->input('password', '');

        if (!$token || !$password) {
            return response()->json(['success' => false, 'message' => 'Token and new password are required'], 400);
        }

        if (strlen($password) < 6) {
            return response()->json(['success' => false, 'message' => 'Password must be at least 6 characters'], 400);
        }

        try {
            $row = UserToken::where('token', $token)->where('type', 'password_reset')->first();

            if (!$row) {
                return response()->json(['success' => false, 'message' => 'Invalid or expired reset link.'], 400);
            }

            if (now() > $row->expires_at) {
                return response()->json(['success' => false, 'message' => 'Reset link has expired. Please request a new one.'], 400);
            }

            $hashed = password_hash($password, PASSWORD_BCRYPT, ['cost' => 10]);
            User::where('id', $row->user_id)->update(['password' => $hashed]);
            $row->delete();

            return response()->json(['success' => true, 'message' => 'Password reset successfully! You can now log in.']);

        } catch (\Throwable $e) {
            return response()->json(['success' => false, 'message' => 'Server error', 'error' => $e->getMessage()], 500);
        }
    }

    // ── POST /api/change-password (authenticated) ─────────────────
    public function changePassword(Request $request): JsonResponse
    {
        $currentPassword = $request->input('currentPassword', '');
        $newPassword     = $request->input('newPassword', '');
        $authUser        = $request->attributes->get('user');

        if (!$currentPassword || !$newPassword) {
            return response()->json(['success' => false, 'message' => 'Both passwords are required'], 400);
        }

        if (strlen($newPassword) < 6) {
            return response()->json(['success' => false, 'message' => 'New password must be at least 6 characters'], 400);
        }

        try {
            $user = User::find($authUser['id']);

            if (!password_verify($currentPassword, $user->password)) {
                return response()->json(['success' => false, 'message' => 'Current password is incorrect'], 401);
            }

            $hashed = password_hash($newPassword, PASSWORD_BCRYPT, ['cost' => 10]);
            $user->update(['password' => $hashed]);

            return response()->json(['success' => true, 'message' => 'Password changed successfully!']);

        } catch (\Throwable $e) {
            return response()->json(['success' => false, 'message' => 'Server error', 'error' => $e->getMessage()], 500);
        }
    }

    // ── POST /api/update-profile (authenticated) ──────────────────
    public function updateProfile(Request $request): JsonResponse
    {
        $username     = trim($request->input('username', ''));
        $email        = trim($request->input('email', ''));
        $removeAvatar = $request->input('remove_avatar', '') === 'true';
        $authUser     = $request->attributes->get('user');

        if (!$username) {
            return response()->json(['success' => false, 'message' => 'Username is required'], 400);
        }

        try {
            $current = User::find($authUser['id']);

            // Check username uniqueness
            if ($username !== $current->username) {
                $taken = User::where('username', $username)->where('id', '!=', $authUser['id'])->first();
                if ($taken) {
                    return response()->json(['success' => false, 'message' => 'Username is already taken'], 409);
                }
            }

            // Check email uniqueness
            $newEmail = $email ?: $current->email;
            if ($newEmail !== $current->email) {
                $taken2 = User::where('email', $newEmail)->where('id', '!=', $authUser['id'])->first();
                if ($taken2) {
                    return response()->json(['success' => false, 'message' => 'Email is already in use by another account'], 409);
                }
            }

            // Handle avatar upload
            $avatarFile = $request->file('avatar');
            $avatarUrl  = $current->avatar;

            if ($avatarFile && $avatarFile->isValid()) {
                if ($avatarUrl) {
                    Upload::deleteFile($avatarUrl);
                }
                $avatarUrl = Upload::handleAvatar($avatarFile);
            }

            if ($removeAvatar) {
                if ($avatarUrl) {
                    Upload::deleteFile($avatarUrl);
                }
                $avatarUrl = null;
            }

            $emailChanged = $newEmail !== $current->email;

            $updateData = [
                'username' => $username,
                'email'    => $newEmail,
                'avatar'   => $avatarUrl,
            ];

            if ($emailChanged) {
                $updateData['email_verified_at'] = null;
            }

            $current->update($updateData);

            $updated = User::select('id', 'username', 'email', 'role', 'avatar')->find($authUser['id']);

            return response()->json([
                'success'      => true,
                'message'      => $emailChanged
                    ? 'Email updated! Please verify your new email address before logging in again.'
                    : 'Profile updated successfully!',
                'data'         => $updated,
                'emailChanged' => $emailChanged,
            ]);

        } catch (\RuntimeException $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 400);
        } catch (\Throwable $e) {
            return response()->json(['success' => false, 'message' => 'Server error', 'error' => $e->getMessage()], 500);
        }
    }
}
