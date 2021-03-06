<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CompanyController;
use App\Http\Controllers\IncidentTypeController;
use App\Http\Controllers\ParameterController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\UnitMeasurementController;
use App\Http\Controllers\UserController;

Route::group([
    'prefix' => 'auth'
], function ($router) {
    Route::post('register', [AuthController::class, 'register']);
    Route::post('login', [AuthController::class, 'login']);
    Route::post('logout', [AuthController::class, 'logout']);
    Route::post('refresh', [AuthController::class, 'refresh']);
    Route::post('validateUser', [AuthController::class, 'validateUser']);
    Route::post('me', [AuthController::class, 'me']);
});

Route::group([
    'prefix' => 'empresas'
], function($route) {
    Route::get('/listar', [CompanyController::class, 'listar']);
    Route::post('/crear', [CompanyController::class, 'crear']);
    Route::get('/simpleListar', [CompanyController::class, 'simpleListar']);
    Route::get('/detalle/{id}', [CompanyController::class, 'detalle']);
    Route::put('/editar', [CompanyController::class, 'editar']);
    Route::put('/activar/{id}', [CompanyController::class, 'activar']);
    Route::put('/desactivar/{id}', [CompanyController::class, 'desactivar']);
});

Route::group([
    'prefix' => 'tipos_incidentes'
], function($route) {
    Route::get('/listar', [IncidentTypeController::class, 'listar']);
    Route::post('/crear', [IncidentTypeController::class, 'crear']);
    Route::get('/simpleListar', [IncidentTypeController::class, 'simpleListar']);
    Route::get('/detalle/{id}', [IncidentTypeController::class, 'detalle']);
    Route::put('/editar', [IncidentTypeController::class, 'editar']);
    Route::put('/activar/{id}', [IncidentTypeController::class, 'activar']);
    Route::put('/desactivar/{id}', [IncidentTypeController::class, 'desactivar']);
});

Route::group([
    'prefix' => 'usuarios'
], function($route) {
    Route::get('/listar', [UserController::class, 'listar']);
    Route::get('/simpleListarPropio', [UserController::class, 'simpleListarPropio']);
    Route::get('/simpleListar/empresa/{id}', [UserController::class, 'simpleListar']);
    Route::post('/crear', [UserController::class, 'crear']);
    Route::put('/editar', [UserController::class, 'editar']);
    Route::post('/activar', [UserController::class, 'activar']);
    Route::post('/desactivar', [UserController::class, 'desactivar']);
    Route::post('/cambiarPassword', [UserController::class, 'cambiarPassword']);
    Route::get('/detalle/{id}', [UserController::class, 'detalle']);
});

Route::group([
    'prefix' => 'parametros'
], function($route) {
    Route::get('/listar', [ParameterController::class, 'listar']);
    Route::post('/crear', [ParameterController::class, 'crear']);
    Route::get('/simpleListar', [ParameterController::class, 'simpleListar']);
    Route::put('/editar', [ParameterController::class, 'editar']);
    Route::get('/detalle/{id}', [ParameterController  ::class, 'detalle']);
});

Route::group([
    'prefix' => 'proyectos'
], function($route) {
    Route::get('/listar', [ProjectController::class, 'listar']);
    Route::post('/crear', [ProjectController::class, 'crear']);
    Route::put('/editar', [ProjectController::class, 'editar']);
    Route::get('/detalle/{id}', [ProjectController::class, 'detalle']);
});

Route::group([
    'prefix' => 'unidades'
], function($route) {
    Route::get('/listar', [UnitMeasurementController::class, 'listar']);
    Route::get('/simpleListar', [UnitMeasurementController::class, 'simpleListar']);
    Route::post('/crear', [UnitMeasurementController::class, 'crear']);
    Route::put('/editar', [UnitMeasurementController::class, 'editar']);
    Route::get('/detalle/{id}', [UnitMeasurementController::class, 'detalle']);
});