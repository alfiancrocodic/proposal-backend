<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Condition;
use App\Models\Feature;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\ValidationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class ConditionController extends Controller
{
    /**
     * Menampilkan daftar semua conditions
     * 
     * @param Request $request
     * @return JsonResponse
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $query = Condition::with(['feature.subModule.mainModule']);
            
            // Filter berdasarkan feature
            if ($request->filled('feature_id')) {
                $query->where('feature_id', $request->feature_id);
            }
            
            // Filter berdasarkan sub module
            if ($request->filled('sub_module_id')) {
                $query->whereHas('feature', function ($q) use ($request) {
                    $q->where('sub_module_id', $request->sub_module_id);
                });
            }
            
            // Filter berdasarkan main module
            if ($request->filled('main_module_id')) {
                $query->whereHas('feature.subModule', function ($q) use ($request) {
                    $q->where('main_module_id', $request->main_module_id);
                });
            }
            
            // Filter berdasarkan status aktif
            if ($request->has('is_active')) {
                $query->where('is_active', $request->boolean('is_active'));
            }
            
            // Search berdasarkan nama atau condition text
            if ($request->filled('search')) {
                $search = $request->search;
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', '%' . $search . '%')
                      ->orWhere('condition_text', 'like', '%' . $search . '%');
                });
            }
            
            // Sorting
            $sortBy = $request->get('sort_by', 'sort_order');
            $sortOrder = $request->get('sort_order', 'asc');
            $query->orderBy($sortBy, $sortOrder);
            
            // Pagination
            $perPage = $request->get('per_page', 15);
            $conditions = $query->paginate($perPage);
            
            return response()->json([
                'success' => true,
                'message' => 'Conditions retrieved successfully',
                'data' => $conditions
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve conditions',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Menyimpan condition baru
     * 
     * @param Request $request
     * @return JsonResponse
     */
    public function store(Request $request): JsonResponse
    {
        try {
            $validated = $request->validate([
                'feature_id' => 'required|exists:features,id',
                'name' => 'required|string|max:255',
                'description' => 'nullable|string',
                'condition_text' => 'required|string',
                'is_active' => 'boolean',
                'sort_order' => 'integer|min:0'
            ]);
            
            // Validasi bahwa feature aktif beserta parent-nya
            $feature = Feature::with(['subModule.mainModule'])->findOrFail($validated['feature_id']);
            if (!$feature->is_active || !$feature->subModule->is_active || !$feature->subModule->mainModule->is_active) {
                return response()->json([
                    'success' => false,
                    'message' => 'Cannot create condition for inactive feature, sub module, or main module'
                ], 422);
            }
            
            // Validasi unique name dalam scope feature
            $existingCondition = Condition::where('feature_id', $validated['feature_id'])
                                         ->where('name', $validated['name'])
                                         ->first();
            if ($existingCondition) {
                return response()->json([
                    'success' => false,
                    'message' => 'Condition name already exists in this feature'
                ], 422);
            }
            
            // Set default sort_order jika tidak ada
            if (!isset($validated['sort_order'])) {
                $validated['sort_order'] = Condition::where('feature_id', $validated['feature_id'])
                                                   ->max('sort_order') + 1;
            }
            
            $condition = Condition::create($validated);
            $condition->load(['feature.subModule.mainModule']);
            
            return response()->json([
                'success' => true,
                'message' => 'Condition created successfully',
                'data' => $condition
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
                'message' => 'Failed to create condition',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Menampilkan detail condition
     * 
     * @param int $id
     * @return JsonResponse
     */
    public function show(int $id): JsonResponse
    {
        try {
            $condition = Condition::with(['feature.subModule.mainModule'])->findOrFail($id);
            
            return response()->json([
                'success' => true,
                'message' => 'Condition retrieved successfully',
                'data' => $condition
            ]);
            
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Condition not found'
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve condition',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Mengupdate condition
     * 
     * @param Request $request
     * @param int $id
     * @return JsonResponse
     */
    public function update(Request $request, int $id): JsonResponse
    {
        try {
            $condition = Condition::findOrFail($id);
            
            $validated = $request->validate([
                'feature_id' => 'required|exists:features,id',
                'name' => 'required|string|max:255',
                'description' => 'nullable|string',
                'condition_text' => 'required|string',
                'is_active' => 'boolean',
                'sort_order' => 'integer|min:0'
            ]);
            
            // Validasi bahwa feature aktif beserta parent-nya
            $feature = Feature::with(['subModule.mainModule'])->findOrFail($validated['feature_id']);
            if (!$feature->is_active || !$feature->subModule->is_active || !$feature->subModule->mainModule->is_active) {
                return response()->json([
                    'success' => false,
                    'message' => 'Cannot update condition to inactive feature, sub module, or main module'
                ], 422);
            }
            
            // Validasi unique name dalam scope feature (kecuali diri sendiri)
            $existingCondition = Condition::where('feature_id', $validated['feature_id'])
                                         ->where('name', $validated['name'])
                                         ->where('id', '!=', $id)
                                         ->first();
            if ($existingCondition) {
                return response()->json([
                    'success' => false,
                    'message' => 'Condition name already exists in this feature'
                ], 422);
            }
            
            $condition->update($validated);
            $condition->load(['feature.subModule.mainModule']);
            
            return response()->json([
                'success' => true,
                'message' => 'Condition updated successfully',
                'data' => $condition
            ]);
            
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Condition not found'
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
                'message' => 'Failed to update condition',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Menghapus condition (soft delete)
     * 
     * @param int $id
     * @return JsonResponse
     */
    public function destroy(int $id): JsonResponse
    {
        try {
            $condition = Condition::findOrFail($id);
            $condition->delete();
            
            return response()->json([
                'success' => true,
                'message' => 'Condition deleted successfully'
            ]);
            
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Condition not found'
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete condition',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Mengaktifkan/menonaktifkan condition
     * 
     * @param Request $request
     * @param int $id
     * @return JsonResponse
     */
    public function toggleStatus(Request $request, int $id): JsonResponse
    {
        try {
            $condition = Condition::with(['feature.subModule.mainModule'])->findOrFail($id);
            
            $validated = $request->validate([
                'is_active' => 'required|boolean'
            ]);
            
            // Jika ingin mengaktifkan, pastikan feature, sub module, dan main module juga aktif
            if ($validated['is_active']) {
                $feature = $condition->feature;
                if (!$feature->is_active || !$feature->subModule->is_active || !$feature->subModule->mainModule->is_active) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Cannot activate condition when feature, sub module, or main module is inactive'
                    ], 422);
                }
            }
            
            $condition->update(['is_active' => $validated['is_active']]);
            
            $status = $validated['is_active'] ? 'activated' : 'deactivated';
            
            return response()->json([
                'success' => true,
                'message' => "Condition {$status} successfully",
                'data' => $condition->fresh(['feature.subModule.mainModule'])
            ]);
            
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Condition not found'
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
                'message' => 'Failed to toggle condition status',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Mendapatkan conditions berdasarkan feature
     * 
     * @param int $featureId
     * @param Request $request
     * @return JsonResponse
     */
    public function getByFeature(int $featureId, Request $request): JsonResponse
    {
        try {
            // Validasi feature exists
            Feature::findOrFail($featureId);
            
            $query = Condition::where('feature_id', $featureId);
            
            // Filter berdasarkan status aktif
            if ($request->has('is_active')) {
                $query->where('is_active', $request->boolean('is_active'));
            }
            
            $conditions = $query->ordered()->get();
            
            return response()->json([
                'success' => true,
                'message' => 'Conditions retrieved successfully',
                'data' => $conditions
            ]);
            
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Feature not found'
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve conditions',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Bulk update sort order untuk conditions dalam satu feature
     * 
     * @param Request $request
     * @param int $featureId
     * @return JsonResponse
     */
    public function updateSortOrder(Request $request, int $featureId): JsonResponse
    {
        try {
            // Validasi feature exists
            Feature::findOrFail($featureId);
            
            $validated = $request->validate([
                'conditions' => 'required|array',
                'conditions.*.id' => 'required|exists:conditions,id',
                'conditions.*.sort_order' => 'required|integer|min:0'
            ]);
            
            // Validasi bahwa semua conditions belong to feature ini
            $conditionIds = collect($validated['conditions'])->pluck('id');
            $validConditions = Condition::where('feature_id', $featureId)
                                      ->whereIn('id', $conditionIds)
                                      ->count();
            
            if ($validConditions !== count($conditionIds)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Some conditions do not belong to this feature'
                ], 422);
            }
            
            // Update sort order
            foreach ($validated['conditions'] as $conditionData) {
                Condition::where('id', $conditionData['id'])
                        ->update(['sort_order' => $conditionData['sort_order']]);
            }
            
            // Return updated conditions
            $updatedConditions = Condition::where('feature_id', $featureId)
                                         ->ordered()
                                         ->get();
            
            return response()->json([
                'success' => true,
                'message' => 'Sort order updated successfully',
                'data' => $updatedConditions
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
                'message' => 'Failed to update sort order',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}