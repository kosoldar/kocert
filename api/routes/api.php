<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\CatalogoController;
use App\Http\Controllers\Api\CertLayoutController;
use App\Http\Controllers\Api\DashboardController;
use App\Http\Controllers\Api\CertificadoController;
use App\Http\Controllers\Api\EmpresaController;
use App\Http\Controllers\Api\InspectorController;
use App\Http\Controllers\Api\MaterialController;
use App\Http\Controllers\Api\NormaController;
use App\Http\Controllers\Api\ProcesoController;
use App\Http\Controllers\Api\SoldadorController;
use App\Http\Controllers\Api\VerificacionController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// Verificación pública por QR (sin auth)
Route::get('/verificar/{token}', [VerificacionController::class, 'show']);

// Auth
Route::post('/login', [AuthController::class, 'login']);

// Rutas protegidas
Route::middleware('auth:sanctum')->group(function () {
    // ── Catálogos normativos (solo lectura, alimentan los formularios) ────────
    Route::prefix('catalogos')->name('catalogos.')->group(function () {
        Route::get('norma/{norma}',  [CatalogoController::class, 'porNorma']);
        Route::get('posiciones',     [CatalogoController::class, 'posiciones']);
        Route::get('normas',         [CatalogoController::class, 'normas']);
        Route::get('procesos',       [CatalogoController::class, 'procesos']);
        Route::get('next-numero',    [CatalogoController::class, 'nextNumero']);
        Route::get('check-numero',   [CatalogoController::class, 'checkNumero']);
        Route::get('joint-designs',  [CatalogoController::class, 'jointDesigns']);
    });


    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/me', [AuthController::class, 'me']);
    Route::get('/dashboard/stats', [DashboardController::class, 'stats']);

    Route::apiResource('soldadores', SoldadorController::class)->parameters(['soldadores' => 'soldador']);
    Route::post('soldadores/{soldador}/foto', [SoldadorController::class, 'uploadFoto']);
    Route::apiResource('empresas', EmpresaController::class);
    Route::apiResource('procesos', ProcesoController::class);
    Route::apiResource('normas', NormaController::class);
    Route::apiResource('materiales', MaterialController::class)->parameters(['materiales' => 'material']);

    Route::apiResource('certificados', CertificadoController::class);
    Route::post('certificados/{certificado}/renovar',    [CertificadoController::class, 'renovar']);
    Route::post('certificados/{certificado}/ampliar',    [CertificadoController::class, 'ampliar']);
    Route::post('certificados/{certificado}/recalcular', [CertificadoController::class, 'recalcular']);
    Route::get('certificados/{certificado}/pdf',         [CertificadoController::class, 'pdf']);

    Route::apiResource('inspectores', InspectorController::class)->parameters(['inspectores' => 'inspector']);
    Route::post('inspectores/{inspector}/firma', [InspectorController::class, 'uploadFirma']);

    Route::apiResource('cert-layouts', CertLayoutController::class)->parameters(['cert-layouts' => 'certLayout']);
    Route::post('cert-layouts/{certLayout}/preview', [CertLayoutController::class, 'preview']);
    Route::post('cert-layouts/preview-block',        [CertLayoutController::class, 'previewBlock']);

    Route::apiResource('usuarios', \App\Http\Controllers\Api\UserController::class)->parameters(['usuarios' => 'user']);
});
