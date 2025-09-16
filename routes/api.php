<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\ClientController;
use App\Http\Controllers\Api\ProjectController;
use App\Http\Controllers\Api\ProposalController;
use App\Http\Controllers\Api\ProposalContentController;
use App\Http\Controllers\Api\ProposalTemplateController;
use App\Http\Controllers\Api\MainModuleController;
use App\Http\Controllers\Api\SubModuleController;
use App\Http\Controllers\Api\FeatureController;
use App\Http\Controllers\Api\ConditionController;
use App\Http\Controllers\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

// Public routes
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

// Protected routes
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/user', [AuthController::class, 'user']);
    
    // User CRUD routes
    Route::apiResource('users', UserController::class);
    Route::apiResource('clients', ClientController::class);
    Route::apiResource('projects', ProjectController::class);
    Route::apiResource('proposals', ProposalController::class);
    Route::apiResource('proposal-contents', ProposalContentController::class);
    // Templates for proposal builder
    Route::get('proposal/templates', [ProposalTemplateController::class, 'templates']);
    Route::get('proposals/{proposal}/content', [ProposalTemplateController::class, 'getContent']);
    Route::put('proposals/{proposal}/content', [ProposalTemplateController::class, 'putContent']);
    // Serve OpenAPI spec (YAML)
    Route::get('openapi', function () {
        $path = base_path('openapi/openapi.yaml');
        abort_unless(file_exists($path), 404);
        return response()->file($path, [
            'Content-Type' => 'application/yaml',
        ]);
    });
    
    /*
    |--------------------------------------------------------------------------
    | Main Module Routes
    |--------------------------------------------------------------------------
    */
    Route::prefix('main-modules')->group(function () {
        Route::get('/', [MainModuleController::class, 'index']); // GET /api/main-modules
        Route::post('/', [MainModuleController::class, 'store']); // POST /api/main-modules
        Route::get('/{id}', [MainModuleController::class, 'show']); // GET /api/main-modules/{id}
        Route::put('/{id}', [MainModuleController::class, 'update']); // PUT /api/main-modules/{id}
        Route::delete('/{id}', [MainModuleController::class, 'destroy']); // DELETE /api/main-modules/{id}
        Route::patch('/{id}/toggle-status', [MainModuleController::class, 'toggleStatus']); // PATCH /api/main-modules/{id}/toggle-status
    });
    
    /*
    |--------------------------------------------------------------------------
    | Sub Module Routes
    |--------------------------------------------------------------------------
    */
    Route::prefix('sub-modules')->group(function () {
        Route::get('/', [SubModuleController::class, 'index']); // GET /api/sub-modules
        Route::post('/', [SubModuleController::class, 'store']); // POST /api/sub-modules
        Route::get('/{id}', [SubModuleController::class, 'show']); // GET /api/sub-modules/{id}
        Route::put('/{id}', [SubModuleController::class, 'update']); // PUT /api/sub-modules/{id}
        Route::delete('/{id}', [SubModuleController::class, 'destroy']); // DELETE /api/sub-modules/{id}
        Route::patch('/{id}/toggle-status', [SubModuleController::class, 'toggleStatus']); // PATCH /api/sub-modules/{id}/toggle-status
    });
    
    // Sub modules by main module
    Route::get('main-modules/{mainModuleId}/sub-modules', [SubModuleController::class, 'getByMainModule']); // GET /api/main-modules/{mainModuleId}/sub-modules
    
    /*
    |--------------------------------------------------------------------------
    | Feature Routes
    |--------------------------------------------------------------------------
    */
    Route::prefix('features')->group(function () {
        Route::get('/', [FeatureController::class, 'index']); // GET /api/features
        Route::post('/', [FeatureController::class, 'store']); // POST /api/features
        Route::get('/{id}', [FeatureController::class, 'show']); // GET /api/features/{id}
        Route::put('/{id}', [FeatureController::class, 'update']); // PUT /api/features/{id}
        Route::delete('/{id}', [FeatureController::class, 'destroy']); // DELETE /api/features/{id}
        Route::patch('/{id}/toggle-status', [FeatureController::class, 'toggleStatus']); // PATCH /api/features/{id}/toggle-status
        Route::get('/mandays/total', [FeatureController::class, 'getTotalMandays']); // GET /api/features/mandays/total
    });
    
    // Features by sub module
    Route::get('sub-modules/{subModuleId}/features', [FeatureController::class, 'getBySubModule']); // GET /api/sub-modules/{subModuleId}/features
    
    /*
    |--------------------------------------------------------------------------
    | Condition Routes
    |--------------------------------------------------------------------------
    */
    Route::prefix('conditions')->group(function () {
        Route::get('/', [ConditionController::class, 'index']); // GET /api/conditions
        Route::post('/', [ConditionController::class, 'store']); // POST /api/conditions
        Route::get('/{id}', [ConditionController::class, 'show']); // GET /api/conditions/{id}
        Route::put('/{id}', [ConditionController::class, 'update']); // PUT /api/conditions/{id}
        Route::delete('/{id}', [ConditionController::class, 'destroy']); // DELETE /api/conditions/{id}
        Route::patch('/{id}/toggle-status', [ConditionController::class, 'toggleStatus']); // PATCH /api/conditions/{id}/toggle-status
    });
    
    // Conditions by feature
    Route::get('features/{featureId}/conditions', [ConditionController::class, 'getByFeature']); // GET /api/features/{featureId}/conditions
    Route::patch('features/{featureId}/conditions/sort-order', [ConditionController::class, 'updateSortOrder']); // PATCH /api/features/{featureId}/conditions/sort-order
    
    /*
    |--------------------------------------------------------------------------
    | Nested Resource Routes for Proposal Builder
    |--------------------------------------------------------------------------
    */
    
    // Get complete hierarchy: Main Module -> Sub Modules -> Features -> Conditions
    Route::get('main-modules/{mainModuleId}/complete', function ($mainModuleId) {
        $mainModule = \App\Models\MainModule::with([
            'subModules' => function ($query) {
                $query->active()->ordered();
            },
            'subModules.features' => function ($query) {
                $query->active()->ordered();
            },
            'subModules.features.conditions' => function ($query) {
                $query->active()->ordered();
            }
        ])->findOrFail($mainModuleId);
        
        return response()->json([
            'success' => true,
            'message' => 'Complete main module hierarchy retrieved successfully',
            'data' => $mainModule
        ]);
    }); // GET /api/main-modules/{mainModuleId}/complete
    
    // Get sub module with features and conditions
    Route::get('sub-modules/{subModuleId}/complete', function ($subModuleId) {
        $subModule = \App\Models\SubModule::with([
            'mainModule',
            'features' => function ($query) {
                $query->active()->ordered();
            },
            'features.conditions' => function ($query) {
                $query->active()->ordered();
            }
        ])->findOrFail($subModuleId);
        
        return response()->json([
            'success' => true,
            'message' => 'Complete sub module hierarchy retrieved successfully',
            'data' => $subModule
        ]);
    }); // GET /api/sub-modules/{subModuleId}/complete
    
    // Get feature with conditions
    Route::get('features/{featureId}/complete', function ($featureId) {
        $feature = \App\Models\Feature::with([
            'subModule.mainModule',
            'conditions' => function ($query) {
                $query->active()->ordered();
            }
        ])->findOrFail($featureId);
        
        return response()->json([
            'success' => true,
            'message' => 'Complete feature hierarchy retrieved successfully',
            'data' => $feature
        ]);
    }); // GET /api/features/{featureId}/complete
    
    /*
    |--------------------------------------------------------------------------
    | Search and Filter Routes
    |--------------------------------------------------------------------------
    */
    
    // Global search across all modules
    Route::get('search', function (\Illuminate\Http\Request $request) {
        $search = $request->get('q');
        $results = [];
        
        if ($search) {
            // Search main modules
            $mainModules = \App\Models\MainModule::where('name', 'like', "%{$search}%")
                ->active()
                ->limit(5)
                ->get(['id', 'name', 'description']);
            
            // Search sub modules
            $subModules = \App\Models\SubModule::where('name', 'like', "%{$search}%")
                ->with('mainModule:id,name')
                ->active()
                ->limit(5)
                ->get(['id', 'name', 'description', 'main_module_id']);
            
            // Search features
            $features = \App\Models\Feature::where('name', 'like', "%{$search}%")
                ->with(['subModule:id,name,main_module_id', 'subModule.mainModule:id,name'])
                ->active()
                ->limit(5)
                ->get(['id', 'name', 'description', 'mandays', 'sub_module_id']);
            
            // Search conditions
            $conditions = \App\Models\Condition::where(function ($query) use ($search) {
                    $query->where('name', 'like', "%{$search}%")
                          ->orWhere('description', 'like', "%{$search}%");
                })
                ->with(['feature:id,name,sub_module_id', 'feature.subModule:id,name,main_module_id', 'feature.subModule.mainModule:id,name'])
                ->active()
                ->limit(5)
                ->get(['id', 'name', 'description', 'feature_id']);
            
            $results = [
                'main_modules' => $mainModules,
                'sub_modules' => $subModules,
                'features' => $features,
                'conditions' => $conditions
            ];
        }
        
        return response()->json([
            'success' => true,
            'message' => 'Search results retrieved successfully',
            'data' => $results
        ]);
    }); // GET /api/search?q={query}
    
    /*
    |--------------------------------------------------------------------------
    | Statistics Routes
    |--------------------------------------------------------------------------
    */
    
    // Get dashboard statistics
    Route::get('statistics/dashboard', function () {
        $stats = [
            'main_modules' => [
                'total' => \App\Models\MainModule::count(),
                'active' => \App\Models\MainModule::active()->count(),
                'inactive' => \App\Models\MainModule::inactive()->count()
            ],
            'sub_modules' => [
                'total' => \App\Models\SubModule::count(),
                'active' => \App\Models\SubModule::active()->count(),
                'inactive' => \App\Models\SubModule::inactive()->count()
            ],
            'features' => [
                'total' => \App\Models\Feature::count(),
                'active' => \App\Models\Feature::active()->count(),
                'inactive' => \App\Models\Feature::inactive()->count(),
                'total_mandays' => \App\Models\Feature::active()->sum('mandays'),
                'average_mandays' => \App\Models\Feature::active()->avg('mandays')
            ],
            'conditions' => [
                'total' => \App\Models\Condition::count(),
                'active' => \App\Models\Condition::active()->count(),
                'inactive' => \App\Models\Condition::inactive()->count()
            ]
        ];
        
        return response()->json([
            'success' => true,
            'message' => 'Dashboard statistics retrieved successfully',
            'data' => $stats
        ]);
    }); // GET /api/statistics/dashboard
});
