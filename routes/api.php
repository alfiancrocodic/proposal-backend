<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\ClientController;
use App\Http\Controllers\Api\ProjectController;
use App\Http\Controllers\Api\ProposalController;
use App\Http\Controllers\Api\ProposalContentController;
use App\Http\Controllers\Api\ProposalTemplateController;
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
});
