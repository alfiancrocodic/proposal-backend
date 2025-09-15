<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Feature;
use App\Models\SubModule;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\ValidationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class FeatureController extends Controller
{
    /**
     * Menampilkan daftar semua features
     * 
     * @param Request $request
     * @return JsonResponse
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $query = Feature::with(['subModule.mainModule']);
            
            // Filter berdasarkan sub module
            if ($request->filled('sub_module_id')) {
                $query->where('sub_module_id', $request->sub_module_id);
            }
            
            // Filter berdasarkan main module
            if ($request->filled('main_module_id')) {
                $query->whereHas('subModule', function ($q) use ($request) {
                    $q->where('main_module_id', $request->main_module_id);
                });
            }
            
            // Filter berdasarkan status aktif
            if ($request->has('is_active')) {
                $query->where('is_active', $request->boolean('is_active'));
            }
            
            // Search berdasarkan nama
            if ($request->filled('search')) {
                $query->where('name', 'like', '%' . $request->search . '%');
            }
            
            // Filter berdasarkan range mandays
            if ($request->filled('min_mandays')) {
                $query->where('mandays', '>=', $request->min_mandays);
            }
            if ($request->filled('max_mandays')) {
                $query->where('mandays', '<=', $request->max_mandays);
            }
            
            // Include conditions jika diminta
            if ($request->boolean('with_conditions')) {
                $query->with(['conditions' => function ($q) {
                    $q->active()->ordered();
                }]);
            }
            
            // Sorting
            $sortBy = $request->get('sort_by', 'sort_order');
            $sortOrder = $request->get('sort_order', 'asc');
            $query->orderBy($sortBy, $sortOrder);
            
            // Pagination
            $perPage = $request->get('per_page', 15);
            $features = $query->paginate($perPage);
            
            return response()->json([
                'success' => true,
                'message' => 'Features retrieved successfully',
                'data' => $features
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve features',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Menyimpan feature baru
     * 
     * @param Request $request
     * @return JsonResponse
     */
    public function store(Request $request): JsonResponse
    {
        try {
            $validated = $request->validate([
                'sub_module_id' => 'required|exists:sub_modules,id',
                'name' => 'required|string|max:255',
                'description' => 'nullable|string',
                'mandays' => 'required|numeric|min:0',
                'is_active' => 'boolean',
                'sort_order' => 'integer|min:0'
            ]);
            
            // Validasi bahwa sub module aktif
            $subModule = SubModule::with('mainModule')->findOrFail($validated['sub_module_id']);
            if (!$subModule->is_active || !$subModule->mainModule->is_active) {
                return response()->json([
                    'success' => false,
                    'message' => 'Cannot create feature for inactive sub module or main module'
                ], 422);
            }
            
            // Validasi unique name dalam scope sub module
            $existingFeature = Feature::where('sub_module_id', $validated['sub_module_id'])
                                     ->where('name', $validated['name'])
                                     ->first();
            if ($existingFeature) {
                return response()->json([
                    'success' => false,
                    'message' => 'Feature name already exists in this sub module'
                ], 422);
            }
            
            // Set default sort_order jika tidak ada
            if (!isset($validated['sort_order'])) {
                $validated['sort_order'] = Feature::where('sub_module_id', $validated['sub_module_id'])
                                                  ->max('sort_order') + 1;
            }
            
            $feature = Feature::create($validated);
            $feature->load(['subModule.mainModule']);
            
            return response()->json([
                'success' => true,
                'message' => 'Feature created successfully',
                'data' => $feature
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
                'message' => 'Failed to create feature',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Menampilkan detail feature
     * 
     * @param int $id
     * @param Request $request
     * @return JsonResponse
     */
    public function show(int $id, Request $request): JsonResponse
    {
        try {
            $query = Feature::with(['subModule.mainModule']);
            
            // Include conditions jika diminta
            if ($request->boolean('with_conditions')) {
                $query->with(['conditions' => function ($q) {
                    $q->active()->ordered();
                }]);
            }
            
            $feature = $query->findOrFail($id);
            
            return response()->json([
                'success' => true,
                'message' => 'Feature retrieved successfully',
                'data' => $feature
            ]);
            
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Feature not found'
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve feature',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Mengupdate feature
     * 
     * @param Request $request
     * @param int $id
     * @return JsonResponse
     */
    public function update(Request $request, int $id): JsonResponse
    {
        try {
            $feature = Feature::findOrFail($id);
            
            $validated = $request->validate([
                'sub_module_id' => 'required|exists:sub_modules,id',
                'name' => 'required|string|max:255',
                'description' => 'nullable|string',
                'mandays' => 'required|numeric|min:0',
                'is_active' => 'boolean',
                'sort_order' => 'integer|min:0'
            ]);
            
            // Validasi bahwa sub module aktif
            $subModule = SubModule::with('mainModule')->findOrFail($validated['sub_module_id']);
            if (!$subModule->is_active || !$subModule->mainModule->is_active) {
                return response()->json([
                    'success' => false,
                    'message' => 'Cannot update feature to inactive sub module or main module'
                ], 422);
            }
            
            // Validasi unique name dalam scope sub module (kecuali diri sendiri)
            $existingFeature = Feature::where('sub_module_id', $validated['sub_module_id'])
                                     ->where('name', $validated['name'])
                                     ->where('id', '!=', $id)
                                     ->first();
            if ($existingFeature) {
                return response()->json([
                    'success' => false,
                    'message' => 'Feature name already exists in this sub module'
                ], 422);
            }
            
            $feature->update($validated);
            $feature->load(['subModule.mainModule']);
            
            return response()->json([
                'success' => true,
                'message' => 'Feature updated successfully',
                'data' => $feature
            ]);
            
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Feature not found'
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
                'message' => 'Failed to update feature',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Menghapus feature (soft delete)
     * 
     * @param int $id
     * @return JsonResponse
     */
    public function destroy(int $id): JsonResponse
    {
        try {
            $feature = Feature::findOrFail($id);
            
            // Cek apakah masih memiliki conditions aktif
            $activeConditionsCount = $feature->conditions()->active()->count();
            if ($activeConditionsCount > 0) {
                return response()->json([
                    'success' => false,
                    'message' => 'Cannot delete feature that has active conditions'
                ], 422);
            }
            
            $feature->delete();
            
            return response()->json([
                'success' => true,
                'message' => 'Feature deleted successfully'
            ]);
            
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Feature not found'
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete feature',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Mengaktifkan/menonaktifkan feature
     * 
     * @param Request $request
     * @param int $id
     * @return JsonResponse
     */
    public function toggleStatus(Request $request, int $id): JsonResponse
    {
        try {
            $feature = Feature::with(['subModule.mainModule'])->findOrFail($id);
            
            $validated = $request->validate([
                'is_active' => 'required|boolean'
            ]);
            
            // Jika ingin mengaktifkan, pastikan sub module dan main module juga aktif
            if ($validated['is_active'] && (!$feature->subModule->is_active || !$feature->subModule->mainModule->is_active)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Cannot activate feature when sub module or main module is inactive'
                ], 422);
            }
            
            $feature->update(['is_active' => $validated['is_active']]);
            
            $status = $validated['is_active'] ? 'activated' : 'deactivated';
            
            return response()->json([
                'success' => true,
                'message' => "Feature {$status} successfully",
                'data' => $feature->fresh(['subModule.mainModule'])
            ]);
            
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Feature not found'
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
                'message' => 'Failed to toggle feature status',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Mendapatkan features berdasarkan sub module
     * 
     * @param int $subModuleId
     * @param Request $request
     * @return JsonResponse
     */
    public function getBySubModule(int $subModuleId, Request $request): JsonResponse
    {
        try {
            // Validasi sub module exists
            SubModule::findOrFail($subModuleId);
            
            $query = Feature::where('sub_module_id', $subModuleId);
            
            // Filter berdasarkan status aktif
            if ($request->has('is_active')) {
                $query->where('is_active', $request->boolean('is_active'));
            }
            
            // Include conditions jika diminta
            if ($request->boolean('with_conditions')) {
                $query->with(['conditions' => function ($q) {
                    $q->active()->ordered();
                }]);
            }
            
            $features = $query->ordered()->get();
            
            return response()->json([
                'success' => true,
                'message' => 'Features retrieved successfully',
                'data' => $features
            ]);
            
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Sub module not found'
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve features',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Mendapatkan total mandays berdasarkan filter
     * 
     * @param Request $request
     * @return JsonResponse
     */
    public function getTotalMandays(Request $request): JsonResponse
    {
        try {
            $query = Feature::query();
            
            // Filter berdasarkan sub module
            if ($request->filled('sub_module_id')) {
                $query->where('sub_module_id', $request->sub_module_id);
            }
            
            // Filter berdasarkan main module
            if ($request->filled('main_module_id')) {
                $query->whereHas('subModule', function ($q) use ($request) {
                    $q->where('main_module_id', $request->main_module_id);
                });
            }
            
            // Filter berdasarkan status aktif
            if ($request->has('is_active')) {
                $query->where('is_active', $request->boolean('is_active'));
            }
            
            $totalMandays = $query->sum('mandays');
            $featuresCount = $query->count();
            $averageMandays = $featuresCount > 0 ? $totalMandays / $featuresCount : 0;
            
            return response()->json([
                'success' => true,
                'message' => 'Mandays calculation retrieved successfully',
                'data' => [
                    'total_mandays' => $totalMandays,
                    'features_count' => $featuresCount,
                    'average_mandays' => round($averageMandays, 2)
                ]
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to calculate mandays',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}