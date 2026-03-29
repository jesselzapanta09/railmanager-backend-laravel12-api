<?php

namespace App\Http\Controllers;

use App\Models\Train;
use App\Utils\Upload;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class TrainController extends Controller
{
    // ── GET /api/trains ───────────────────────────────────────────
    public function index(): JsonResponse
    {
        try {
            $trains = Train::orderBy('id', 'desc')->get();

            return response()->json([
                'success' => true,
                'count'   => $trains->count(),
                'data'    => $trains,
            ]);

        } catch (\Throwable $e) {
            return response()->json(['success' => false, 'message' => 'Server error', 'error' => $e->getMessage()], 500);
        }
    }

    // ── GET /api/trains/{id} ──────────────────────────────────────
    public function show(int $id): JsonResponse
    {
        try {
            $train = Train::find($id);

            if (!$train) {
                return response()->json(['success' => false, 'message' => 'Train not found'], 404);
            }

            return response()->json(['success' => true, 'data' => $train]);

        } catch (\Throwable $e) {
            return response()->json(['success' => false, 'message' => 'Server error', 'error' => $e->getMessage()], 500);
        }
    }

    // ── POST /api/trains (admin only) ─────────────────────────────
    public function store(Request $request): JsonResponse
    {
        $trainName = trim($request->input('train_name', ''));
        $price     = $request->input('price', '');
        $route     = trim($request->input('route', ''));

        if (!$trainName || $price === '' || !$route) {
            return response()->json([
                'success' => false,
                'message' => 'train_name, price, and route are required',
            ], 400);
        }

        try {
            $imageFile = $request->file('image');
            $imageUrl  = Upload::handleTrainImage($imageFile && $imageFile->isValid() ? $imageFile : null);

            $train = Train::create([
                'train_name' => $trainName,
                'price'      => $price,
                'route'      => $route,
                'image'      => $imageUrl,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Train created',
                'data'    => [
                    'id'         => $train->id,
                    'train_name' => $trainName,
                    'price'      => $price,
                    'route'      => $route,
                    'image'      => $imageUrl,
                ],
            ], 201);

        } catch (\RuntimeException $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 400);
        } catch (\Throwable $e) {
            return response()->json(['success' => false, 'message' => 'Server error', 'error' => $e->getMessage()], 500);
        }
    }

    // ── PUT /api/trains/{id} (admin only) ─────────────────────────
    public function update(Request $request, int $id): JsonResponse
    {
        $trainName = trim($request->input('train_name', ''));
        $price     = $request->input('price', '');
        $route     = trim($request->input('route', ''));

        if (!$trainName || $price === '' || !$route) {
            return response()->json(['success' => false, 'message' => 'All fields are required'], 400);
        }

        try {
            $existing = Train::find($id);

            if (!$existing) {
                return response()->json(['success' => false, 'message' => 'Train not found'], 404);
            }

            $imageUrl  = $existing->image;
            $imageFile = $request->file('image');

            if ($imageFile && $imageFile->isValid()) {
                // New image uploaded — delete old one and store new
                if ($imageUrl) {
                    Upload::deleteFile($imageUrl);
                }
                $imageUrl = Upload::handleTrainImage($imageFile);
            } elseif ($request->input('remove_image', '') === 'true') {
                // Explicitly removing image with no replacement
                if ($imageUrl) {
                    Upload::deleteFile($imageUrl);
                }
                $imageUrl = null;
            }

            $existing->update([
                'train_name' => $trainName,
                'price'      => $price,
                'route'      => $route,
                'image'      => $imageUrl,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Train updated',
                'data'    => [
                    'id'         => $id,
                    'train_name' => $trainName,
                    'price'      => $price,
                    'route'      => $route,
                    'image'      => $imageUrl,
                ],
            ]);

        } catch (\RuntimeException $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 400);
        } catch (\Throwable $e) {
            return response()->json(['success' => false, 'message' => 'Server error', 'error' => $e->getMessage()], 500);
        }
    }

    // ── DELETE /api/trains/{id} (admin only) ──────────────────────
    public function destroy(int $id): JsonResponse
    {
        try {
            $existing = Train::find($id);

            if (!$existing) {
                return response()->json(['success' => false, 'message' => 'Train not found'], 404);
            }

            if ($existing->image) {
                Upload::deleteFile($existing->image);
            }

            $existing->delete();

            return response()->json(['success' => true, 'message' => 'Train deleted']);

        } catch (\Throwable $e) {
            return response()->json(['success' => false, 'message' => 'Server error', 'error' => $e->getMessage()], 500);
        }
    }
}
