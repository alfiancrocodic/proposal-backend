<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\SubModule;
use App\Models\MainModule;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\ValidationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class SubModuleController extends Controller
{
    /**
     * Menampilkan daftar semua sub modules
     * 
     * @param Request $request
     * @return JsonResponse
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $query = SubModule::with('mainModule');
            
            // Filter berdasarkan main module
            if ($request->filled('main_module_id')) {
                $query->where('main_module_id', $request->main_module_id);
            }
            
            // Filter berdasarkan status aktif
            if ($request->has('is_active')) {
                $query->where('is_active', $request->boolean('is_active'));
            }
            
            // Search berdasarkan nama
            if ($request->filled('search')) {
                $query->where('name', 'like', '%' . $request->search . '%');
            }
            
            // Include features jika diminta
            if ($request->boolean('with_features')) {
                $query->with(['features' => function ($q) {
                    $q->active()->ordered();
                }]);
            }
            
            // Sorting
            $sortBy = $request->get('sort_by', 'sort_order');
            $sortOrder = $request->get('sort_order', 'asc');
            $query->orderBy($sortBy, $sortOrder);
            
            // Pagination
            $perPage = $request->get('per_page', 15);
            $subModules = $query->paginate($perPage);
            
            return response()->json([
                'success' => true,
                'message' => 'Sub modules retrieved successfully',
                'data' => $subModules
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve sub modules',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Menyimpan sub module baru
     * 
     * @param Request $request
     * @return JsonResponse
     */
    public function store(Request $request): JsonResponse
    {
        try {
            $validated = $request->validate([
                'main_module_id' => 'required|exists:main_modules,id',
                'name' => 'required|string|max:255',
                'description' => 'nullable|string',
                'is_active' => 'boolean',
                'sort_order' => 'integer|min:0'
            ]);
            
            // Validasi bahwa main module aktif
            $mainModule = MainModule::findOrFail($validated['main_module_id']);
            if (!$mainModule->is_active) {
                return response()->json([
                    'success' => false,
                    'message' => 'Cannot create sub module for inactive main module'
                ], 422);
            }
            
            // Validasi unique name dalam scope main module
            $existingSubModule = SubModule::where('main_module_id', $validated['main_module_id'])
                                         ->where('name', $validated['name'])
                                         ->first();
            if ($existingSubModule) {
                return response()->json([
                    'success' => false,
                    'message' => 'Sub module name already exists in this main module'
                ], 422);
            }
            
            // Set default sort_order jika tidak ada
            if (!isset($validated['sort_order'])) {
                $validated['sort_order'] = SubModule::where('main_module_id', $validated['main_module_id'])
                                                   ->max('sort_order') + 1;
            }
            
            $subModule = SubModule::create($validated);
            $subModule->load('mainModule');
            
            return response()->json([
                'success' => true,
                'message' => 'Sub module created successfully',
                'data' => $subModule
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
                'message' => 'Failed to create sub module',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Menampilkan detail sub module
     * 
     * @param int $id
     * @param Request $request
     * @return JsonResponse
     */
    public function show(int $id, Request $request): JsonResponse
    {
        try {
            $query = SubModule::with('mainModule');
            
            // Include features jika diminta
            if ($request->boolean('with_features')) {
                $query->with(['features' => function ($q) {
                    $q->active()->ordered();
                }]);
            }
            
            $subModule = $query->findOrFail($id);
            
            return response()->json([
                'success' => true,
                'message' => 'Sub module retrieved successfully',
                'data' => $subModule
            ]);
            
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Sub module not found'
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve sub module',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Mengupdate sub module
     * 
     * @param Request $request
     * @param int $id
     * @return JsonResponse
     */
    public function update(Request $request, int $id): JsonResponse
    {
        try {
            $subModule = SubModule::findOrFail($id);
            
            $validated = $request->validate([
                'main_module_id' => 'required|exists:main_modules,id',
                'name' => 'required|string|max:255',
                'description' => 'nullable|string',
                'is_active' => 'boolean',
                'sort_order' => 'integer|min:0'
            ]);
            
            // Validasi bahwa main module aktif
            $mainModule = MainModule::findOrFail($validated['main_module_id']);
            if (!$mainModule->is_active) {
                return response()->json([
                    'success' => false,
                    'message' => 'Cannot update sub module to inactive main module'
                ], 422);
            }
            
            // Validasi unique name dalam scope main module (kecuali diri sendiri)
            $existingSubModule = SubModule::where('main_module_id', $validated['main_module_id'])
                                         ->where('name', $validated['name'])
                                         ->where('id', '!=', $id)
                                         ->first();
            if ($existingSubModule) {
                return response()->json([
                    'success' => false,
                    'message' => 'Sub module name already exists in this main module'
                ], 422);
            }
            
            $subModule->update($validated);
            $subModule->load('mainModule');
            
            return response()->json([
                'success' => true,
                'message' => 'Sub module updated successfully',
                'data' => $subModule
            ]);
            
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Sub module not found'
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
                'message' => 'Failed to update sub module',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Menghapus sub module (soft delete)
     * 
     * @param int $id
     * @return JsonResponse
     */
    public function destroy(int $id): JsonResponse
    {
        try {
            $subModule = SubModule::findOrFail($id);
            
            // Cek apakah masih memiliki features aktif
            $activeFeaturesCount = $subModule->features()->active()->count();
            if ($activeFeaturesCount > 0) {
                return response()->json([
                    'success' => false,
                    'message' => 'Cannot delete sub module that has active features'
                ], 422);
            }
            
            $subModule->delete();
            
            return response()->json([
                'success' => true,
                'message' => 'Sub module deleted successfully'
            ]);
            
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Sub module not found'
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete sub module',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Mengaktifkan/menonaktifkan sub module
     * 
     * @param Request $request
     * @param int $id
     * @return JsonResponse
     */
    public function toggleStatus(Request $request, int $id): JsonResponse
    {
        try {
            $subModule = SubModule::with('mainModule')->findOrFail($id);
            
            $validated = $request->validate([
                'is_active' => 'required|boolean'
            ]);
            
            // Jika ingin mengaktifkan, pastikan main module juga aktif
            if ($validated['is_active'] && !$subModule->mainModule->is_active) {
                return response()->json([
                    'success' => false,
                    'message' => 'Cannot activate sub module when main module is inactive'
                ], 422);
            }
            
            $subModule->update(['is_active' => $validated['is_active']]);
            
            $status = $validated['is_active'] ? 'activated' : 'deactivated';
            
            return response()->json([
                'success' => true,
                'message' => "Sub module {$status} successfully",
                'data' => $subModule->fresh(['mainModule'])
            ]);
            
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Sub module not found'
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
                'message' => 'Failed to toggle sub module status',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Mendapatkan sub modules berdasarkan main module
     * 
     * @param int $mainModuleId
     * @param Request $request
     * @return JsonResponse
     */
    public function getByMainModule(int $mainModuleId, Request $request): JsonResponse
    {
        try {
            // Validasi main module exists
            MainModule::findOrFail($mainModuleId);
            
            $query = SubModule::where('main_module_id', $mainModuleId);
            
            // Filter berdasarkan status aktif
            if ($request->has('is_active')) {
                $query->where('is_active', $request->boolean('is_active'));
            }
            
            // Include features jika diminta
            if ($request->boolean('with_features')) {
                $query->with(['features' => function ($q) {
                    $q->active()->ordered();
                }]);
            }
            
            $subModules = $query->ordered()->get();
            
            return response()->json([
                'success' => true,
                'message' => 'Sub modules retrieved successfully',
                'data' => $subModules
            ]);
            
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Main module not found'
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve sub modules',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}