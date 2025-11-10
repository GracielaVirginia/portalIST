<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
// use App\Http\Controllers\ValidacionController;
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
use App\Http\Controllers\Admin\ValidacionController;
use App\Http\Controllers\Security\UserPasswordController;
use App\Http\Controllers\Admin\NoticiasController as AdminNoticiasController;
use App\Http\Controllers\Portal\NoticiasController as PortalNoticiasController;
use App\Http\Controllers\Admin\FaqController as AdminFaqController;
use App\Http\Controllers\Portal\FaqController as PortalFaqController;
use App\Http\Controllers\Admin\AdministradorController;
use App\Http\Controllers\SoporteController;
use App\Http\Controllers\ReviewsController;
use App\Http\Controllers\Admin\ReviewsAdminController;
use App\Http\Controllers\Validaciones\SinValidacionController;
use App\Http\Controllers\Validaciones\NumeroCasoController;
use App\Http\Controllers\Validaciones\TresOpcionesController;
use App\Http\Controllers\Validaciones\CrearCuentaController;
use App\Http\Controllers\Auth\LogoutConfirmController;
use App\Http\Controllers\Admin\HomeSectionSettingsController;
use App\Http\Controllers\Admin\ImageController;
use App\Http\Controllers\PortalPageController;
use App\Http\Controllers\Admin\PortalSectionController;
use App\Http\Controllers\PortalPromocionesController;
use App\Http\Controllers\Admin\PromocionController;
use App\Http\Controllers\BloodPressureController;
use App\Http\Controllers\GlucoseReadingController;
use App\Http\Controllers\WeightEntryController;
use App\Http\Controllers\ControlesReportController;
use App\Http\Controllers\ControlesSeriesController;
use App\Http\Controllers\Admin\AdminGalenSupportController;
use App\Http\Controllers\AgendaPacienteController;
use App\Http\Controllers\TipoProfesionalController;
use App\Http\Controllers\SucursalController;
use App\Http\Controllers\ProfesionalController;
use App\Http\Controllers\HorarioController;
use App\Http\Controllers\BloqueoController;
use App\Http\Controllers\AgendaController;
use App\Http\Controllers\Admin\CitaController;
use App\Http\Controllers\Admin\LoginLogController;
use App\Http\Controllers\SessionController;
use App\Http\Controllers\Admin\OtherSettingController;
use App\Http\Controllers\UserDocumentController;
use App\Http\Controllers\AuthAttemptsReportController;
use App\Http\Controllers\Admin\AuditoriaLoginController;

/*
|--------------------------------------------------------------------------
| Rutas Públicas (visitantes y pacientes no autenticados)
|--------------------------------------------------------------------------
*/
Route::middleware('guest', 'log.auth.attempt')->group(function () {
Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.attempt');
Route::post('/auth/verificar-rut', [PacienteController::class, 'verificarRut'])->name('verificar-rut');
Route::post('/auth/verificar-pasaporte', [PacienteController::class, 'verificarPasaporte'])->name('verificar-pasaporte');
Route::post('/auth/login-pasaporte', [AuthController::class, 'loginPasaporte'])->name('login.attempt.pasaporte');
});

Route::middleware('guest')->group(function () {
Route::get('/ayuda/enviar', [SoporteController::class, 'create'])->name('soporte.create');
Route::post('/ayuda/enviar', [SoporteController::class, 'store'])->name('soporte.store');
Route::post('/assistant/message', [\App\Http\Controllers\Assistant\ChatBotController::class, 'sendMessage'])->name('portal.assistant.message');
Route::get('/conoce-mas', [PortalPageController::class, 'conoceMas'])->name('portal.conoce-mas');
Route::get('/promociones', [PortalPromocionesController::class, 'index'])->name('portal.promociones');
Route::get('/promociones/{promocion}', [PortalPromocionesController::class, 'show'])->name('portal.promociones.show');
Route::post('/admin/support-tickets/galen', [AdminGalenSupportController::class, 'store'])->name('admin.support.galen.store');
Route::get('/portal/faqs', [PortalFaqController::class,'list'])->name('portal.faqs.list');
});
Route::middleware('auth', 'must.be.validated', 'log.auth.attempt')->group(function () {
Route::get('/portal', [HomeController::class, 'index'])->name('portal.home');
});
/*
|--------------------------------------------------------------------------
| Rutas Protegidas (pacientes autenticados) y (validados)
|--------------------------------------------------------------------------
*/
Route::middleware('auth', 'must.be.validated')->group(function () {
Route::get('/logout/confirm', [LogoutConfirmController::class, 'show'])->name('logout.confirm');
Route::post('/logout', [LogoutConfirmController::class, 'logout'])->name('logout'); // sobrescribe si hace falta
Route::get('/clear-session', [ClearSessionController::class, 'clearSession'])->name('clear.session');
Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
// Público JSON para el modal
Route::get('/assistant/list', [\App\Http\Controllers\Portal\AssistantPublicController::class, 'list'])->name('portal.assistant.list');
  Route::post('/controles/tension', [BloodPressureController::class, 'store'])->name('controles.tension.store');
  Route::post('/controles/glucosa', [GlucoseReadingController::class, 'store'])->name('controles.glucosa.store');
  Route::post('/controles/peso',    [WeightEntryController::class, 'store'])->name('controles.peso.store');
  Route::get('/controles/series', [ControlesSeriesController::class, 'index'])->name('controles.series');
  Route::post('/controles/pdf', [ControlesReportController::class, 'pdf'])->name('controles.pdf');

    Route::get('/portal/citas',  [CitasController::class, 'index'])->name('portal.citas.index');
    Route::post('/portal/citas', [CitasController::class, 'store'])->name('portal.citas.store');
    Route::get ('/agenda',                      [AgendaPacienteController::class, 'index'])->name('agenda.index');
    Route::get ('/agenda/medicos',              [AgendaPacienteController::class, 'apiMedicos'])->name('agenda.medicos');
    Route::get ('/agenda/{id}/horarios',    [AgendaPacienteController::class, 'apiHorarios'])->name('agenda.horarios');
    Route::get ('/agenda/{id}/eventos',     [AgendaPacienteController::class, 'apiEventos'])->name('agenda.eventos');
    Route::post('/agenda/verificar',            [AgendaPacienteController::class, 'verificarDisponibilidad'])->name('agenda.verificar-disponibilidad');
    Route::post('/agenda',                      [AgendaPacienteController::class, 'store'])->name('agenda.store');
    Route::post('/agenda/{id}/bloquear',    [AgendaPacienteController::class, 'bloquearSlot'])->name('agenda.bloquear');
    Route::post('/agenda/{id}/mover',           [AgendaPacienteController::class, 'mover'])->name('agenda.mover');
    Route::post('/agenda/estado',               [AgendaPacienteController::class, 'cambiarEstado'])->name('agenda.cambiar-estado');

Route::get('/agenda/{id}/eventos-visibles', [AgendaPacienteController::class, 'apiEventosVisibles'])->name('agenda.eventos-visibles');
Route::post('/session/keepalive', [SessionController::class, 'keepAlive'])->name('session.keepalive');
    Route::get('/documents', [UserDocumentController::class, 'index'])->name('documents.index');
    Route::post('/documents', [UserDocumentController::class, 'store'])->name('documents.store');
    Route::get('/documents/{document}/download', [UserDocumentController::class, 'download'])->name('documents.download');
    Route::put('/documents/{document}', [UserDocumentController::class, 'update'])->name('documents.update');
    Route::delete('/documents/{document}', [UserDocumentController::class, 'destroy'])->name('documents.destroy');
Route::get('/portal/historial-medico', [UserDocumentController::class, 'indexPage'])->name('portal.historial.index');
Route::get('/portal/historial-medico/agregar', [UserDocumentController::class, 'createPage'])->name('portal.historial.create');

// ==============================
// PÁGINAS DE RESULTADOS  (portal)
// ==============================
// 
Route::get('/ver-resultados', [ResultadosController::class, 'index'])->name('ver-resultados');
Route::get('/portal/resultados', [ResultadosController::class, 'index'])->name('portal.resultados.index');
Route::get('/portal/resultados/especialidad/{esp}', [ResultadosController::class, 'porEspecialidad'])
        ->where('esp', '[A-Za-z]+')
        ->name('portal.resultados.especialidad');
Route::get('/portal/resultados/{gestion}', [ResultadosController::class, 'show'])
        ->whereNumber('gestion')
        ->name('portal.resultados.show');
Route::get('/portal/resultados/{gestion}/pdf', [ResultadosController::class, 'pdf'])
        ->whereNumber('gestion')
        ->name('portal.resultados.pdf');
Route::get('/portal/resultados/{gestion}/viewer', [ResultadosController::class, 'viewer'])
        ->whereNumber('gestion')
        ->name('portal.resultados.viewer');

Route::get('/portal/citas', [CitasController::class, 'index'])->name('portal.citas.index');
Route::get('/portal/licencias', [LicenciasController::class, 'index'])->name('portal.licencias.index');
Route::get('/portal/recetas', [RecetasController::class, 'index'])->name('portal.recetas.index');
Route::post('/portal/controles/store', [ControlesController::class, 'store'])->name('portal.controles.store');
Route::post('/password/update', [UserPasswordController::class, 'update'])->name('password.update');

Route::get('/portal/noticias', [PortalNoticiasController::class, 'index'])->name('portal.noticias.index');
Route::get('/portal/noticias/{id}', [PortalNoticiasController::class, 'show'])->name('portal.noticias.show');
Route::post('/reviews', [ReviewsController::class, 'store'])->name('reviews.store');
Route::patch('/reviews/me', [ReviewsController::class, 'updateMine'])->name('reviews.update.mine');

});
// ==============================
// PÁGINAS DE VALIDACIÓN (portal)
// ==============================
Route::middleware('auth', 'log.auth.attempt')->group(function () {
Route::get('/validacion/sin-validacion', [SinValidacionController::class, 'index'])->name('validacion.sin');
Route::get('/validacion/numero-caso', [NumeroCasoController::class, 'index'])->name('validacion.caso');
Route::post('/validacion/procesar', [NumeroCasoController::class, 'procesar'])->name('validacion.procesar');
Route::get('/validacion/tres-opciones', [TresOpcionesController::class, 'index'])->name('validacion.tres');
Route::post('/validacion/verificar', [TresOpcionesController::class, 'verificarUsuario'])->name('verificar-usuario');
Route::get('/validacion/crear-cuenta', [CrearCuentaController::class, 'index'])->name('validacion.cuenta');
Route::post('/validacion/crear-cuenta', [CrearCuentaController::class, 'store'])->name('validacion.cuenta.store');
Route::post('/validacion/cuenta/codigo', [CrearCuentaController::class, 'enviarCodigo'])->name('validacion.cuenta.codigo');
Route::post('/validacion/cuenta', [CrearCuentaController::class, 'store'])->name('validacion.cuenta.store');

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
Route::patch('/users/{id}/toggle-active', [UserController::class, 'toggleActive'])
    ->name('admin.users.toggle.active');
Route::patch('/users/{id}/toggle-block', [UserController::class, 'toggleBlock'])
    ->name('admin.users.toggle.block');
Route::delete('/users/{id}', [UserController::class, 'destroy'])
    ->name('admin.users.delete');
Route::get('/admins', [AdminController::class, 'index'])->name('admin.admins.index');
// Route::get('/validations', [ValidationController::class, 'index'])->name('admin.validations.index');
//no registrados
Route::get('/users/unregistered', [UserController::class, 'unregistered'])->name('admin.users.unregistered');
Route::get('/users/unregistered/{rut}/edit', [UserController::class, 'editUnregistered'])->name('admin.users.unregistered.edit');
Route::patch('/users/unregistered/{rut}/email', [UserController::class, 'updateUnregisteredEmail'])
    ->name('admin.users.unregistered.email');//crear paciente en el portal
Route::get('/users/create', [UserController::class, 'create'])->name('admin.users.create');
Route::post('/users', [UserController::class, 'store'])->name('admin.users.store');
Route::get('/users/unregistered/data', [UserController::class, 'unregisteredData'])
    ->name('admin.users.unregistered.data');
    //ENVIAR EL EMAIL
Route::post('/admin/gestiones/email-route', [AdminGestionesController::class, 'markEmailRoute'])
    ->name('admin.gestiones.emailRoute');
//editar usuario de la tabla gestiones
// Guardar cambios (editar)
Route::put('/admin/users/edit/{rut}', [UserController::class, 'updateUnregistered'])
    ->name('admin.users.unregistered.update');
// PATCH SOLO RUT (cambia el RUT de ese paciente no registrado)
Route::patch('/users/unregistered/{rut}/rut', [UserController::class, 'updateUnregisteredRut'])
    ->name('admin.users.unregistered.rut');

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
Route::get   ('/admin/faqs',              [AdminFaqController::class,'index'])->name('admin.faqs.index');
Route::get   ('/admin/faqs/create',       [AdminFaqController::class,'create'])->name('admin.faqs.create');
Route::post  ('/admin/faqs',              [AdminFaqController::class,'store'])->name('admin.faqs.store');
Route::get   ('/admin/faqs/{faq}/edit',   [AdminFaqController::class,'edit'])->name('admin.faqs.edit');
Route::put   ('/admin/faqs/{faq}',        [AdminFaqController::class,'update'])->name('admin.faqs.update');
Route::delete('/admin/faqs/{faq}',        [AdminFaqController::class,'destroy'])->name('admin.faqs.destroy');
Route::patch ('/admin/faqs/{faq}/toggle', [AdminFaqController::class,'toggle'])->name('admin.faqs.toggle');

// Administradores (CRUD + toggle)
Route::get   ('/admin/administradores',                    [AdministradorController::class, 'index'])->name('admin.administradores.index');
Route::get   ('/admin/administradores/crear',              [AdministradorController::class, 'create'])->name('admin.administradores.create');
Route::post  ('/admin/administradores',                    [AdministradorController::class, 'store'])->name('admin.administradores.store');
Route::get   ('/admin/administradores/{administrador}/editar', [AdministradorController::class, 'edit'])->name('admin.administradores.edit');
Route::put   ('/admin/administradores/{administrador}',    [AdministradorController::class, 'update'])->name('admin.administradores.update');
Route::delete('/admin/administradores/{administrador}',    [AdministradorController::class, 'destroy'])->name('admin.administradores.destroy');
Route::patch ('/admin/administradores/{administrador}/toggle', [AdministradorController::class, 'toggle'])->name('admin.administradores.toggle');
Route::get('/admin/tickets', [SoporteController::class, 'index'])->name('admin.tickets.index');
Route::get('/admin/tickets/{ticket}',   [SoporteController::class, 'show'])->name('admin.tickets.show');
Route::patch('/admin/tickets/{ticket}/resolve', [SoporteController::class, 'resolve'])->name('admin.tickets.resolve');
Route::get('/admin/reviews', [ReviewsAdminController::class, 'index'])->name('admin.reviews.index');
Route::get('/reviews/{review}', [ReviewsController::class, 'show'])->name('admin.reviews.show');

// ==============================
// ADMIN — configuración de modos
// ==============================
Route::get('/admin/validacion/modos', [ValidacionController::class, 'index'])
    ->name('admin.validacion.modos');

Route::post('/admin/validacion/modos', [ValidacionController::class, 'guardar'])
    ->name('admin.validacion.modos.guardar');
// ==============================
// ADMIN — Asistente virtual
// ==============================

 Route::get('/assistant-rules',                 [\App\Http\Controllers\Admin\AssistantRuleController::class, 'index'])->name('admin.assistant_rules.index');
    Route::get('/assistant-rules/create',          [\App\Http\Controllers\Admin\AssistantRuleController::class, 'create'])->name('admin.assistant_rules.create');
    Route::post('/assistant-rules',                [\App\Http\Controllers\Admin\AssistantRuleController::class, 'store'])->name('admin.assistant_rules.store');
    Route::get('/assistant-rules/{assistant_rule}/edit', [\App\Http\Controllers\Admin\AssistantRuleController::class, 'edit'])->name('admin.assistant_rules.edit');
    Route::put('/assistant-rules/{assistant_rule}',      [\App\Http\Controllers\Admin\AssistantRuleController::class, 'update'])->name('admin.assistant_rules.update');
    Route::delete('/assistant-rules/{assistant_rule}',   [\App\Http\Controllers\Admin\AssistantRuleController::class, 'destroy'])->name('admin.assistant_rules.destroy');
    Route::patch('/assistant-rules/{assistant_rule}/toggle', [\App\Http\Controllers\Admin\AssistantRuleController::class, 'toggle'])->name('admin.assistant_rules.toggle');
Route::get('/admin/config-home', [HomeSectionSettingsController::class, 'edit'])->name('admin.config.home');
Route::post('/admin/config-home', [HomeSectionSettingsController::class, 'update'])->name('admin.config.home.update');
Route::get('/admin/images', [ImageController::class, 'index'])->name('admin.images.index');
Route::post('/admin/images', [ImageController::class, 'store'])->name('admin.images.store');
Route::delete('/admin/images/{image}', [ImageController::class, 'destroy'])->name('admin.images.destroy');
Route::patch('/admin/images/{image}/select',  [ImageController::class, 'select'])->name('admin.images.select');


Route::get   ('/admin/portal-sections',                [PortalSectionController::class, 'index'])->name('admin.portal.sections.index');
Route::post  ('/admin/portal-sections',                [PortalSectionController::class, 'store'])->name('admin.portal.sections.store');
Route::get   ('/admin/portal-sections/{section}/edit', [PortalSectionController::class, 'edit'])->name('admin.portal.sections.edit');
Route::put   ('/admin/portal-sections/{section}',      [PortalSectionController::class, 'update'])->name('admin.portal.sections.update');
Route::delete('/admin/portal-sections/{section}',      [PortalSectionController::class, 'destroy'])->name('admin.portal.sections.destroy');
Route::patch ('/admin/portal-sections/{section}/toggle', [PortalSectionController::class, 'toggleVisible'])->name('admin.portal.sections.toggle');
Route::patch ('/admin/portal-sections/reorder',          [PortalSectionController::class, 'reorder'])->name('admin.portal.sections.reorder');

Route::get   ('/admin/promociones',                [PromocionController::class, 'index'])->name('admin.promociones.index');
Route::get   ('/admin/promociones/create',         [PromocionController::class, 'create'])->name('admin.promociones.create');
Route::post  ('/admin/promociones',                [PromocionController::class, 'store'])->name('admin.promociones.store');
Route::get   ('/admin/promociones/{promocion}/edit',[PromocionController::class, 'edit'])->name('admin.promociones.edit');
Route::put   ('/admin/promociones/{promocion}',    [PromocionController::class, 'update'])->name('admin.promociones.update');
Route::delete('/admin/promociones/{promocion}',    [PromocionController::class, 'destroy'])->name('admin.promociones.destroy');
Route::patch ('/admin/promociones/{promocion}/destacar', [PromocionController::class, 'destacar'])->name('admin.promociones.destacar');
Route::patch ('/admin/promociones/{promocion}/toggle',   [PromocionController::class, 'toggle'])->name('admin.promociones.toggle');

Route::get   ('/tipos-profesionales',                   [TipoProfesionalController::class, 'index'])->name('tipos.index');
Route::post  ('/tipos-profesionales',                   [TipoProfesionalController::class, 'store'])->name('tipos.store');
Route::put   ('/tipos-profesionales/{tipoProfesional}', [TipoProfesionalController::class, 'update'])->name('tipos.update');
Route::delete('/tipos-profesionales/{tipoProfesional}', [TipoProfesionalController::class, 'destroy'])->name('tipos.destroy');
Route::patch ('/tipos-profesionales/{tipoProfesional}/toggle', [TipoProfesionalController::class, 'toggle'])->name('tipos.toggle');
Route::post  ('/tipos-profesionales/reorder',                  [TipoProfesionalController::class, 'reorder'])->name('tipos.reorder');
Route::get('/tipos-profesionales/create', [TipoProfesionalController::class, 'create'])->name('tipos-profesionales.create');
Route::get('/tipos-profesionales/{tipoProfesional}/edit', [TipoProfesionalController::class, 'edit'])->name('tipos-profesionales.edit');

Route::get   ('/sucursales',                [SucursalController::class,'index'])->name('sucursales.index');
Route::post  ('/sucursales',                [SucursalController::class,'store'])->name('sucursales.store');
Route::put   ('/sucursales/{sucursal}',     [SucursalController::class,'update'])->name('sucursales.update');
Route::delete('/sucursales/{sucursal}',     [SucursalController::class,'destroy'])->name('sucursales.destroy');
Route::patch ('/sucursales/{sucursal}/toggle', [SucursalController::class,'toggle'])->name('sucursales.toggle');
Route::post  ('/sucursales/reorder',           [SucursalController::class,'reorder'])->name('sucursales.reorder');
Route::get   ('/sucursales/create',           [SucursalController::class, 'create'])->name('sucursales.create');
Route::get   ('/sucursales/{sucursal}/edit',  [SucursalController::class, 'edit'])->name('sucursales.edit');

Route::get   ('/profesionales',                 [ProfesionalController::class, 'index'])->name('profesionales.index');
Route::get   ('/profesionales/create',          [ProfesionalController::class, 'create'])->name('profesionales.create');
Route::post  ('/profesionales',                 [ProfesionalController::class, 'store'])->name('profesionales.store');
Route::get   ('/profesionales/{profesional}/edit', [ProfesionalController::class, 'edit'])->name('profesionales.edit');
Route::put   ('/profesionales/{profesional}',   [ProfesionalController::class, 'update'])->name('profesionales.update');
Route::delete('/profesionales/{profesional}',   [ProfesionalController::class, 'destroy'])->name('profesionales.destroy');

Route::get   ('/horarios',                [HorarioController::class,'index'])->name('horarios.index');
Route::get   ('/horarios/create',         [HorarioController::class,'create'])->name('horarios.create');
Route::post  ('/horarios',                [HorarioController::class,'store'])->name('horarios.store');
Route::get   ('/horarios/{horario}/edit', [HorarioController::class,'edit'])->name('horarios.edit');
Route::put   ('/horarios/{horario}',      [HorarioController::class,'update'])->name('horarios.update');
Route::delete('/horarios/{horario}',      [HorarioController::class,'destroy'])->name('horarios.destroy');

Route::get   ('/bloqueos',               [BloqueoController::class,'index'])->name('bloqueos.index');
Route::get   ('/bloqueos/create',        [BloqueoController::class,'create'])->name('bloqueos.create');
Route::post  ('/bloqueos',               [BloqueoController::class,'store'])->name('bloqueos.store');
Route::get   ('/bloqueos/{bloqueo}/edit',[BloqueoController::class,'edit'])->name('bloqueos.edit');
Route::put   ('/bloqueos/{bloqueo}',     [BloqueoController::class,'update'])->name('bloqueos.update');
Route::delete('/bloqueos/{bloqueo}',     [BloqueoController::class,'destroy'])->name('bloqueos.destroy');

Route::get   ('/admin/citas',                  [CitaController::class, 'index'])->name('admin.citas.index');
Route::get   ('/admin/citas/{cita}/edit',      [CitaController::class, 'edit'])->name('admin.citas.edit');
Route::put   ('/admin/citas/{cita}',           [CitaController::class, 'update'])->name('admin.citas.update');
Route::delete('/admin/citas/{cita}',           [CitaController::class, 'destroy'])->name('admin.citas.destroy');
Route::patch ('/admin/citas/{cita}/confirmar', [CitaController::class, 'confirmar'])->name('admin.citas.confirmar');
Route::patch ('/admin/citas/{cita}/reservada', [CitaController::class, 'reservada'])->name('admin.citas.reservada');
    
Route::get('/admin/login-logs', [LoginLogController::class, 'index'])->name('admin.login_logs.index');
Route::get('/admin/auditoria-logins', [AuditoriaLoginController::class, 'index'])
    ->name('admin.auditoria-logins');
        Route::get('/other-settings',  [OtherSettingController::class, 'edit'])->name('other-settings.edit');
    Route::post('/other-settings', [OtherSettingController::class, 'update'])->name('other-settings.update');
    Route::get('/admin/users/{userId}/documents', [UserDocumentController::class, 'indexForUser'])
        ->name('admin.users.documents.index');

Route::get('/admin/auth-attempts', [AuthAttemptsReportController::class, 'index'])
    ->name('admin.auth_attempts.index');
   

        });
