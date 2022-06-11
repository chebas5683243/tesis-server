<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\RecordController;
use App\Http\Controllers\CompanyController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\IncidentController;
use App\Http\Controllers\CauseTypeController;
use App\Http\Controllers\ParameterController;
use App\Http\Controllers\ActionTypeController;
use App\Http\Controllers\ImpactTypeController;
use App\Http\Controllers\IncidentTypeController;
use App\Http\Controllers\InvestigationController;
use App\Http\Controllers\MonitoringPointController;
use App\Http\Controllers\UnitMeasurementController;

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
    Route::put('/activar/{id}', [UserController::class, 'activar']);
    Route::put('/desactivar/{id}', [UserController::class, 'desactivar']);
    Route::post('/cambiarPassword', [UserController::class, 'cambiarPassword']);
    Route::get('/detalle/{id}', [UserController::class, 'detalle']);
    Route::get('/export', [UserController::class, 'export']);
});

Route::group([
    'prefix' => 'parametros'
], function($route) {
    Route::get('/listar', [ParameterController::class, 'listar']);
    Route::post('/crear', [ParameterController::class, 'crear']);
    Route::get('/simpleListar', [ParameterController::class, 'simpleListar']);
    Route::get('/listarConParametrizacion', [ParameterController::class, 'listarConParametrizacion']);
    Route::put('/editar', [ParameterController::class, 'editar']);
    Route::get('/detalle/{id}', [ParameterController::class, 'detalle']);
    Route::delete('/{id}', [ParameterController::class, 'eliminar']);
});

Route::group([
    'prefix' => 'proyectos'
], function($route) {
    Route::get('/listar', [ProjectController::class, 'listar']);
    Route::get('/listarMonitoreo', [ProjectController::class, 'listarMonitoreo']);
    Route::post('/crear', [ProjectController::class, 'crear']);
    Route::put('/editar', [ProjectController::class, 'editar']);
    Route::get('/detalle/{id}', [ProjectController::class, 'detalle']);
    Route::get('/{id}/puntos', [ProjectController::class, 'puntosMonitoreos']);
    Route::get('/simpleListar', [ProjectController::class, 'simpleListar']);
});

Route::group([
    'prefix' => 'unidades'
], function($route) {
    Route::get('/listar', [UnitMeasurementController::class, 'listar']);
    Route::get('/simpleListar', [UnitMeasurementController::class, 'simpleListar']);
    Route::post('/crear', [UnitMeasurementController::class, 'crear']);
    Route::put('/editar', [UnitMeasurementController::class, 'editar']);
    Route::get('/detalle/{id}', [UnitMeasurementController::class, 'detalle']);
    Route::delete('/{id}', [UnitMeasurementController::class, 'eliminar']);
});

Route::group([
    'prefix' => 'puntos'
], function($route) {
    Route::post('/crear', [MonitoringPointController::class, 'crear']);
    Route::post('/modificarParametro', [MonitoringPointController::class, 'modificarParametro']);
    Route::put('/editar', [MonitoringPointController::class, 'editar']);
    Route::put('/desactivar/{id}', [MonitoringPointController::class, 'desactivar']);
    Route::put('/activar/{id}', [MonitoringPointController::class, 'activar']);
    Route::get('/detalle/{id}', [MonitoringPointController::class, 'detalle']);
    Route::get('/{id}/parametros', [MonitoringPointController::class, 'listarParametros']);
    Route::get('/{id}/registros', [MonitoringPointController::class, 'listarRegistros']);
});

Route::group([
    'prefix' => 'registros'
], function($route) {
    Route::get('/punto/{puntoId}', [RecordController::class, 'exportTemplate']);
    Route::post('/importar', [RecordController::class, 'importRecordData']);
    Route::get('/{id}', [RecordController::class, 'reporteRegistro']);
});

Route::group([
    'prefix' => 'incidentes'
], function($route) {
    Route::get('/listar', [IncidentController::class, 'listar']);
    Route::post('/crear', [IncidentController::class, 'crear']);
    Route::get('/detalle/{id}', [IncidentController::class, 'detalle']);
    Route::put('/editar', [IncidentController::class, 'editar']);
    Route::get('/getInfoCrearInvestigacion/{id}', [IncidentController::class, 'getInfoCrearInvestigacion']);
    Route::get('/exportarIncidente/{id}', [IncidentController::class, 'exportarIncidente']);
});

Route::group([
    'prefix' => 'investigaciones'
], function($route) {
    Route::get('/listar', [InvestigationController::class, 'listar']);
    Route::post('/crear', [InvestigationController::class, 'crear']);
    Route::get('/detalle/{id}', [InvestigationController::class, 'detalle']);
    Route::put('/guardarDatosGenerales', [InvestigationController::class, 'guardarDatosGenerales']);
    Route::put('/guardarConsecuencias', [InvestigationController::class, 'guardarConsecuencias']);
    Route::put('/guardarCausasAcciones', [InvestigationController::class, 'guardarCausasAcciones']);
    Route::put('/guardarPlanAcciones', [InvestigationController::class, 'guardarPlanAcciones']);
    Route::put('/validarInvestigacion/{id}', [InvestigationController::class, 'validarInvestigacion']);
    Route::get('/exportarInvestigacion/{id}', [InvestigationController::class, 'exportarInvestigacion']);
    Route::post('/listarConFiltro', [InvestigationController::class, 'listarConFiltro']);
    // Route::put('/editar', [IncidentController::class, 'editar']);
});

Route::group([
    'prefix' => 'causas'
], function($route) {
    Route::get('/listar', [CauseTypeController::class, 'listar']);
});

Route::group([
    'prefix' => 'tipos_impactos'
], function($route) {
    Route::get('/listar', [ImpactTypeController::class, 'listar']);
});

Route::group([
    'prefix' => 'tipos_acciones'
], function($route) {
    Route::get('/listar', [ActionTypeController::class, 'listar']);
});