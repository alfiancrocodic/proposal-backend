<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\MainModule;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\ValidationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class MainModuleController extends Controller
{
    /**
     * Menampilkan daftar semua main modules
     * 
     * @param Request $request
     * @return JsonResponse
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $query = MainModule::query();
            
            // Filter berdasarkan status aktif
            if ($request->has('is_active')) {
                $query->where('is_active', $request->boolean('is_active'));
            }
            
            // Search berdasarkan nama
            if ($request->filled('search')) {
                $query->where('name', 'like', '%' . $request->search . '%');
            }
            
            // Include sub modules jika diminta
            if ($request->boolean('with_sub_modules')) {
                $query->with(['subModules' => function ($q) {
                    $q->active()->ordered();
                }]);
            }
            
            // Include features jika diminta
            if ($request->boolean('with_features')) {
                $query->with(['features' => function ($q) {
                    $q->orderBy('features.sort_order', 'asc');
                }]);
            }
            
            // Sorting
            $sortBy = $request->get('sort_by', 'sort_order');
            $sortOrder = $request->get('sort_order', 'asc');
            $query->orderBy($sortBy, $sortOrder);
            
            // Pagination
            $perPage = $request->get('per_page', 15);
            $mainModules = $query->paginate($perPage);
            
            return response()->json([
                'success' => true,
                'message' => 'Main modules retrieved successfully',
                'data' => $mainModules
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve main modules',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Menyimpan main module baru
     * 
     * @param Request $request
     * @return JsonResponse
     */
    public function store(Request $request): JsonResponse
    {
        try {
            $validated = $request->validate([
                'name' => 'required|string|max:255|unique:main_modules,name',
                'description' => 'nullable|string',
                'is_active' => 'boolean',
                'sort_order' => 'integer|min:0'
            ]);
            
            // Set default sort_order jika tidak ada
            if (!isset($validated['sort_order'])) {
                $validated['sort_order'] = MainModule::max('sort_order') + 1;
            }
            
            $mainModule = MainModule::create($validated);
            
            return response()->json([
                'success' => true,
                'message' => 'Main module created successfully',
                'data' => $mainModule
            ], 201);
            
        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to create main module',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Menampilkan detail main module
     * 
     * @param int $id
     * @param Request $request
     * @return JsonResponse
     */
    public function show(int $id, Request $request): JsonResponse
    {
        try {
            $query = MainModule::query();
            
            // Include sub modules jika diminta
            if ($request->boolean('with_sub_modules')) {
                $query->with(['subModules' => function ($q) {
                    $q->active()->ordered();
                }]);
            }
            
            // Include features jika diminta
            if ($request->boolean('with_features')) {
                $query->with(['features' => function ($q) {
                    $q->active()->ordered();
                }]);
            }
            
            $mainModule = $query->findOrFail($id);
            
            return response()->json([
                'success' => true,
                'message' => 'Main module retrieved successfully',
                'data' => $mainModule
            ]);
            
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Main module not found'
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve main module',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Mengupdate main module
     * 
     * @param Request $request
     * @param int $id
     * @return JsonResponse
     */
    public function update(Request $request, int $id): JsonResponse
    {
        try {
            $mainModule = MainModule::findOrFail($id);
            
            $validated = $request->validate([
                'name' => 'required|string|max:255|unique:main_modules,name,' . $id,
                'description' => 'nullable|string',
                'is_active' => 'boolean',
                'sort_order' => 'integer|min:0'
            ]);
            
            $mainModule->update($validated);
            
            return response()->json([
                'success' => true,
                'message' => 'Main module updated successfully',
                'data' => $mainModule->fresh()
            ]);
            
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Main module not found'
            ], 404);
        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update main module',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Menghapus main module (soft delete)
     * 
     * @param int $id
     * @return JsonResponse
     */
    public function destroy(int $id): JsonResponse
    {
        try {
            $mainModule = MainModule::findOrFail($id);
            
            // Cek apakah masih memiliki sub modules aktif
            $activeSubModulesCount = $mainModule->subModules()->active()->count();
            if ($activeSubModulesCount > 0) {
                return response()->json([
                    'success' => false,
                    'message' => 'Cannot delete main module that has active sub modules'
                ], 422);
            }
            
            $mainModule->delete();
            
            return response()->json([
                'success' => true,
                'message' => 'Main module deleted successfully'
            ]);
            
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Main module not found'
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete main module',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Mengaktifkan/menonaktifkan main module
     * 
     * @param Request $request
     * @param int $id
     * @return JsonResponse
     */
    public function toggleStatus(Request $request, int $id): JsonResponse
    {
        try {
            $mainModule = MainModule::findOrFail($id);
            
            $validated = $request->validate([
                'is_active' => 'required|boolean'
            ]);
            
            $mainModule->update(['is_active' => $validated['is_active']]);
            
            $status = $validated['is_active'] ? 'activated' : 'deactivated';
            
            return response()->json([
                'success' => true,
                'message' => "Main module {$status} successfully",
                'data' => $mainModule->fresh()
            ]);
            
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Main module not found'
            ], 404);
        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to toggle main module status',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}