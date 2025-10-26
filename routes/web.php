<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ValidacionController;
use App\Http\Controllers\ResultadosController;
use App\Http\Controllers\PacienteController;
use App\Http\Controllers\Portal\HomeController;
use App\Http\Controllers\Portal\CitasController;
use App\Http\Controllers\Portal\LicenciasController;
use App\Http\Controllers\Portal\RecetasController;
use App\Http\Controllers\Portal\NoticiasController;
use App\Http\Controllers\Portal\ControlesController;
use App\Http\Controllers\ClearSessionController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Admin\AdminAuthController;
use App\Http\Controllers\Admin\AdminDashboardController;
use App\Http\Controllers\Admin\AdminPatientsController;
use App\Http\Controllers\ExamenNombreController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\NewsController;
use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Admin\ValidationController;
use App\Http\Controllers\Security\UserPasswordController;
use App\Http\Controllers\Admin\NoticiasController as AdminNoticiasController;
use App\Http\Controllers\Portal\NoticiasController as PortalNoticiasController;


/*
|--------------------------------------------------------------------------
| Rutas Públicas (visitantes y pacientes no autenticados)
|--------------------------------------------------------------------------
*/

Route::middleware('guest')->group(function () {
    // Login
    Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.attempt');
    Route::post('/auth/verificar-rut', [PacienteController::class, 'verificarRut'])->name('verificar-rut');
});

/*
|--------------------------------------------------------------------------
| Rutas Protegidas (pacientes autenticados)
|--------------------------------------------------------------------------
*/
Route::middleware('auth')->group(function () {
    // Cerrar sesión
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
//cerrar sesion en el navegador

    Route::get('/clear-session', [ClearSessionController::class, 'clearSession'])->name('clear.session');
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Validación de cuenta
    Route::get('/validacion', [ValidacionController::class, 'index'])->name('validacion');
    Route::post('/validacion', [ValidacionController::class, 'store'])->name('validacion.procesar');

    // Resultados del paciente
    Route::get('/ver-resultados', [ResultadosController::class, 'index'])->name('ver-resultados');
    // 🏠 Página principal del portal
    Route::get('/portal', [HomeController::class, 'index'])->name('portal.home');
    // Ej: /portal → panel principal (header, KPIs, widgets)

    // 📄 Resultados de exámenes — vista general
    Route::get('/portal/resultados', [ResultadosController::class, 'index'])
        ->name('portal.resultados.index');
    // Ej: /portal/resultados

    // 📄 Resultados por especialidad (RX, LAB, ECO, etc.)
    Route::get('/portal/resultados/especialidad/{esp}', [ResultadosController::class, 'porEspecialidad'])
        ->where('esp', '[A-Za-z]+')
        ->name('portal.resultados.especialidad');
    // Ej: /portal/resultados/especialidad/RX

    // 📄 Detalle de un resultado individual
    Route::get('/portal/resultados/{gestion}', [ResultadosController::class, 'show'])
        ->whereNumber('gestion')
        ->name('portal.resultados.show');
    // Ej: /portal/resultados/152

    // 📄 Descarga o visualización del PDF del informe
    Route::get('/portal/resultados/{gestion}/pdf', [ResultadosController::class, 'pdf'])
        ->whereNumber('gestion')
        ->name('portal.resultados.pdf');
    // Ej: /portal/resultados/152/pdf

    // (Opcional) 🔎 Viewer / PACS
    Route::get('/portal/resultados/{gestion}/viewer', [ResultadosController::class, 'viewer'])
        ->whereNumber('gestion')
        ->name('portal.resultados.viewer');
    // Ej: /portal/resultados/152/viewer

    // 📅 Citas médicas
    Route::get('/portal/citas', [CitasController::class, 'index'])->name('portal.citas.index');
    // Ej: /portal/citas

    // 📋 licencias médicas
    Route::get('/portal/licencias', [LicenciasController::class, 'index'])->name('portal.licencias.index');
    // Ej: /portal/licencias

    // 💊 Recetas médicas
    Route::get('/portal/recetas', [RecetasController::class, 'index'])->name('portal.recetas.index');
    // Ej: /portal/recetas
    Route::post('/portal/controles/store', [ControlesController::class, 'store'])->name('portal.controles.store');

Route::post('/password/update', [UserPasswordController::class, 'update'])->name('password.update');
//rutas admin




// PORTAL (público/pacientes)
Route::get('/portal/noticias', [PortalNoticiasController::class, 'index'])->name('portal.noticias.index');
Route::get('/portal/noticias/{id}', [PortalNoticiasController::class, 'show'])->name('portal.noticias.show');


});

/*
|--------------------------------------------------------------------------
| Página principal pública
|--------------------------------------------------------------------------
|
| La ruta raíz ("/") mostrará la vista de bienvenida del portal.
| No redirige automáticamente al login, así evitamos bucles.
|
*/
Route::get('/', function () {
    return view('welcome');
})->name('welcome');

/*
|--------------------------------------------------------------------------
| Página principal pública DEL ADMIN
|--------------------------------------------------------------------------
|

*/

// --- LOGIN (público) ---
Route::get('/login-admin', [AdminAuthController::class, 'showLoginFormAdmin'])->name('admin.login.form');

Route::post('/login-admin', [AdminAuthController::class, 'loginAttemp'])->name('admin.login.attemp');

// --- ÁREA PROTEGIDA ---
Route::middleware('admin.auth')->group(function () {
Route::get('/dashboard-admin', [AdminDashboardController::class, 'index'])->name('admin.dashboard');
Route::post('/logout-admin', [AdminAuthController::class, 'logout'])->name('admin.logout');

Route::get('/users', [UserController::class, 'index'])->name('admin.users.index');
// Registrados
Route::get('/users/registered', [UserController::class, 'registered'])->name('admin.users.registered');
Route::get('/users/registered/data', [UserController::class, 'registeredData'])->name('admin.users.registered.data');
Route::get('/admins', [AdminController::class, 'index'])->name('admin.admins.index');
Route::get('/validations', [ValidationController::class, 'index'])->name('admin.validations.index');
//no registrados
Route::get('/users/unregistered', [UserController::class, 'unregistered'])->name('admin.users.unregistered');
Route::get('/users/unregistered/data', [UserController::class, 'unregisteredData'])->name('admin.users.unregistered.data');
//crear paciente en el portal
Route::get('/users/create', [UserController::class, 'create'])->name('admin.users.create');
//ENVIAR EL EMAIL
Route::post('/admin/gestiones/email-route', [AdminGestionesController::class, 'markEmailRoute'])
    ->name('admin.gestiones.emailRoute');
//editar usuario de la tabla gestiones
Route::get('/admin/users/edit/{rut}', [UserController::class, 'editUnregistered'])
    ->name('admin.users.unregistered.edit');
// Guardar cambios (editar)
Route::put('/admin/users/edit/{rut}', [UserController::class, 'updateUnregistered'])
    ->name('admin.users.unregistered.update');

// 🔎 Buscador AJAX (solo pacientes desde tabla gestiones)
Route::get('/admin/users/search', [AdminDashboardController::class, 'searchUsers'])->name('admin.users.search');

// 🗑️ Eliminar cuenta por RUT (form DELETE del JS)
Route::delete('/admin/users/delete', [AdminDashboardController::class, 'deleteUserByRut'])->name('admin.users.delete');
Route::get('/dashboard/stats', [AdminDashboardController::class, 'stats'])->name('dashboard.stats');
Route::get('/dashboard/stats/by-sede', [AdminDashboardController::class, 'statsBySede'])->name('dashboard.stats.bySede');
Route::get('/admin/pacientes/lookup', [AdminPatientsController::class, 'lookup'])->name('admin.patients.lookup');
Route::post('/admin/users/{user}/unblock', [AdminPatientsController::class, 'unblock'])->name('admin.users.unblock');
Route::delete('/admin/users/{user}', [AdminPatientsController::class, 'destroy'])->name('admin.users.destroy');

//para crear examenes
Route::get('/examen_nombre', [ExamenNombreController::class, 'index'])->name('admin.examen_nombre.index');
Route::get('/examen_nombre/create', [ExamenNombreController::class, 'create'])->name('admin.examen_nombre.create');
Route::post('/examen_nombre', [ExamenNombreController::class, 'store'])->name('admin.examen_nombre.store');
Route::get('/examen_nombre/{examenNombre}', [ExamenNombreController::class, 'show'])->name('admin.examen_nombre.show');
Route::get('/examen_nombre/{examenNombre}/edit', [ExamenNombreController::class, 'edit'])->name('admin.examen_nombre.edit');
Route::put('/examen_nombre/{examenNombre}', [ExamenNombreController::class, 'update'])->name('admin.examen_nombre.update');
Route::delete('/examen_nombre/{examenNombre}', [ExamenNombreController::class, 'destroy'])->name('admin.examen_nombre.destroy');
// ADMIN (CRUD + toggle “poner en home”)
Route::get(   '/admin/noticias',                 [AdminNoticiasController::class, 'index'])->name('admin.noticias.index');
Route::get(   '/admin/noticias/create',          [AdminNoticiasController::class, 'create'])->name('admin.noticias.create');
Route::post(  '/admin/noticias',                 [AdminNoticiasController::class, 'store'])->name('admin.noticias.store');
Route::get(   '/admin/noticias/{noticia}/edit',  [AdminNoticiasController::class, 'edit'])->name('admin.noticias.edit');
Route::put(   '/admin/noticias/{noticia}',       [AdminNoticiasController::class, 'update'])->name('admin.noticias.update');
Route::delete('/admin/noticias/{noticia}',       [AdminNoticiasController::class, 'destroy'])->name('admin.noticias.destroy');

// Toggle “Poner en home” (marca esta noticia como destacada y desmarca el resto)
Route::patch('/admin/noticias/{noticia}/toggle-home', [AdminNoticiasController::class, 'toggleDestacada'])
    ->name('admin.noticias.toggle-home');
});
