<?php

use App\Http\Controllers\Admin\ActivityController;
use App\Http\Controllers\Admin\AiSettingsController;
use App\Http\Controllers\Admin\EmailSettingsController;
use App\Http\Controllers\Admin\SettingsController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Ai\ContentGeneratorController;
use App\Http\Controllers\Ai\GenerationLogController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\NewPasswordController;
use App\Http\Controllers\Auth\PasswordResetLinkController;
use App\Http\Controllers\Catalog\CategoryController;
use App\Http\Controllers\Catalog\CollectionController;
use App\Http\Controllers\Catalog\ImportExportController;
use App\Http\Controllers\Catalog\ProductController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\Email\CampaignController;
use App\Http\Controllers\Email\ContactController;
use App\Http\Controllers\Email\ContactListController;
use App\Http\Controllers\Email\TrackingController;
use App\Http\Controllers\Images\GeneratorController as ImageGeneratorController;
use App\Http\Controllers\Images\TemplateController as ImageTemplateController;
use App\Http\Controllers\Install\InstallController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Social\SocialAccountController;
use App\Http\Controllers\Social\SocialPostController;
use Illuminate\Support\Facades\Route;

// Seguimiento y baja de email marketing: enlaces públicos incluidos en los
// correos, sin autenticación (usan un token propio o firma de Laravel).
Route::get('/email/abrir/{token}', [TrackingController::class, 'pixel'])->name('email.track.open');
Route::get('/email/clic/{token}', [TrackingController::class, 'click'])->name('email.track.click');
Route::get('/email/baja/{token}', [TrackingController::class, 'unsubscribeForm'])->name('email.unsubscribe.form');
Route::post('/email/baja/{token}', [TrackingController::class, 'unsubscribe'])->name('email.unsubscribe');

Route::redirect('/', '/dashboard');

// Instalador (accesible solo mientras el sistema no esté instalado; ver EnsureSystemIsInstalled)
Route::prefix('install')->name('install.')->group(function () {
    Route::get('/', [InstallController::class, 'welcome'])->name('welcome');
    Route::get('/requisitos', [InstallController::class, 'requirements'])->name('requirements');
    Route::get('/base-datos', [InstallController::class, 'databaseForm'])->name('database.form');
    Route::post('/base-datos', [InstallController::class, 'databaseStore'])->name('database.store');
    Route::get('/empresa', [InstallController::class, 'companyForm'])->name('company.form');
    Route::post('/empresa', [InstallController::class, 'companyStore'])->name('company.store');
    Route::get('/administrador', [InstallController::class, 'adminForm'])->name('admin.form');
    Route::post('/administrador', [InstallController::class, 'adminStore'])->name('admin.store');
    Route::get('/instalar', [InstallController::class, 'run'])->name('run');
    Route::get('/finalizado', [InstallController::class, 'finished'])->name('finished');
});

// Autenticación
Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'create'])->name('login');
    Route::post('/login', [LoginController::class, 'store'])->name('login.store');
    Route::get('/olvide-password', [PasswordResetLinkController::class, 'create'])->name('password.request');
    Route::post('/olvide-password', [PasswordResetLinkController::class, 'store'])->name('password.email');
    Route::get('/restablecer-password/{token}', [NewPasswordController::class, 'create'])->name('password.reset');
    Route::post('/restablecer-password', [NewPasswordController::class, 'store'])->name('password.store');
});

Route::post('/logout', [LoginController::class, 'destroy'])->middleware('auth')->name('logout');

Route::middleware('auth')->group(function () {
    Route::get('/dashboard', DashboardController::class)->name('dashboard');

    Route::get('/perfil', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::put('/perfil', [ProfileController::class, 'update'])->name('profile.update');

    Route::prefix('catalogo')->name('catalog.')->group(function () {
        Route::get('productos/exportar', [ProductController::class, 'export'])->name('products.export')->middleware('permission:exportar productos');
        Route::post('productos/masivo', [ProductController::class, 'bulk'])->name('products.bulk')->middleware('permission:editar productos');
        Route::post('productos/{product}/duplicar', [ProductController::class, 'duplicate'])->name('products.duplicate')->middleware('permission:crear productos');
        Route::post('productos/{product}/archivar', [ProductController::class, 'archive'])->name('products.archive')->middleware('permission:editar productos');
        Route::post('productos/{product}/restaurar', [ProductController::class, 'restore'])->name('products.restore')->middleware('permission:editar productos');
        Route::get('productos/{product}/vista-previa', [ProductController::class, 'preview'])->name('products.preview')->middleware('permission:ver productos');
        Route::resource('productos', ProductController::class)->parameters(['productos' => 'product'])->names('products');

        Route::resource('colecciones', CollectionController::class)->parameters(['colecciones' => 'collection'])->names('collections');
        Route::resource('categorias', CategoryController::class)->parameters(['categorias' => 'category'])->names('categories');

        Route::get('importar-exportar', [ImportExportController::class, 'index'])->name('import.index')->middleware('permission:importar productos');
        Route::get('importar-exportar/plantilla', [ImportExportController::class, 'template'])->name('import.template')->middleware('permission:importar productos');
        Route::post('importar-exportar/subir', [ImportExportController::class, 'upload'])->name('import.upload')->middleware('permission:importar productos');
        Route::post('importar-exportar/{importBatch}/mapear', [ImportExportController::class, 'mapAndProcess'])->name('import.process')->middleware('permission:importar productos');
        Route::get('importar-exportar/{importBatch}', [ImportExportController::class, 'show'])->name('import.show')->middleware('permission:importar productos');
        Route::get('importar-exportar/{importBatch}/errores', [ImportExportController::class, 'downloadErrors'])->name('import.errors')->middleware('permission:importar productos');
    });

    Route::prefix('admin')->name('admin.')->middleware('permission:ver usuarios')->group(function () {
        Route::resource('usuarios', UserController::class)->parameters(['usuarios' => 'user'])->names('users');
    });

    Route::get('/admin/actividad', [ActivityController::class, 'index'])->name('admin.activity.index')->middleware('permission:ver actividad');

    Route::get('/admin/configuracion', [SettingsController::class, 'edit'])->name('admin.settings.edit')->middleware('permission:ver configuracion');
    Route::put('/admin/configuracion', [SettingsController::class, 'update'])->name('admin.settings.update')->middleware('permission:administrar configuracion');

    Route::prefix('admin/ia')->name('admin.ai.')->middleware('permission:configurar ia')->group(function () {
        Route::get('configuracion', [AiSettingsController::class, 'edit'])->name('settings.edit');
        Route::put('configuracion', [AiSettingsController::class, 'update'])->name('settings.update');
        Route::post('configuracion/probar', [AiSettingsController::class, 'test'])->name('settings.test');
    });

    Route::prefix('ia')->name('ai.')->group(function () {
        Route::get('generador', [ContentGeneratorController::class, 'index'])->name('generator')->middleware('permission:usar ia');
        Route::post('generar', [ContentGeneratorController::class, 'generate'])->name('generate')->middleware('permission:usar ia');
        Route::post('generaciones/{generation}/aprobar', [ContentGeneratorController::class, 'approve'])->name('approve')->middleware('permission:usar ia');
        Route::post('generaciones/{generation}/rechazar', [ContentGeneratorController::class, 'reject'])->name('reject')->middleware('permission:usar ia');
        Route::get('historial', [GenerationLogController::class, 'index'])->name('history')->middleware('permission:ver historial ia');
    });

    Route::prefix('imagenes')->name('images.')->middleware('permission:ver imagenes')->group(function () {
        Route::resource('plantillas', ImageTemplateController::class)
            ->parameters(['plantillas' => 'template'])
            ->except(['show'])
            ->names('templates');

        Route::get('generador', [ImageGeneratorController::class, 'index'])->name('generator');
        Route::post('generar', [ImageGeneratorController::class, 'store'])->name('generate')->middleware('permission:crear imagenes');
        Route::get('historial', [ImageGeneratorController::class, 'history'])->name('history');
        Route::get('generaciones/{generation}', [ImageGeneratorController::class, 'show'])->name('generations.show');
        Route::delete('generaciones/{generation}', [ImageGeneratorController::class, 'destroy'])->name('generations.destroy')->middleware('permission:eliminar imagenes');
        Route::post('generaciones/{generation}/usar-principal', [ImageGeneratorController::class, 'useAsMainImage'])->name('generations.use-main')->middleware('permission:editar imagenes');
        Route::post('generaciones/{generation}/galeria', [ImageGeneratorController::class, 'addToGallery'])->name('generations.add-gallery')->middleware('permission:editar imagenes');
    });

    Route::prefix('redes')->name('social.')->middleware('permission:ver redes')->group(function () {
        Route::resource('cuentas', SocialAccountController::class)
            ->parameters(['cuentas' => 'account'])
            ->except(['show'])
            ->names('accounts');

        Route::get('calendario/exportar', [SocialPostController::class, 'exportCalendar'])->name('posts.export');
        Route::post('publicaciones/{post}/duplicar', [SocialPostController::class, 'duplicate'])->name('posts.duplicate')->middleware('permission:crear redes');
        Route::post('publicaciones/{post}/aprobar', [SocialPostController::class, 'approve'])->name('posts.approve')->middleware('permission:aprobar redes');
        Route::post('publicaciones/{post}/cancelar', [SocialPostController::class, 'cancel'])->name('posts.cancel')->middleware('permission:editar redes');
        Route::post('publicaciones/{post}/publicar', [SocialPostController::class, 'publishNow'])->name('posts.publish')->middleware('permission:publicar redes');
        Route::post('publicaciones/{post}/publicar-manual', [SocialPostController::class, 'markPublishedManually'])->name('posts.publish-manual')->middleware('permission:publicar redes');
        Route::get('publicaciones/{post}/descargar', [SocialPostController::class, 'downloadImage'])->name('posts.download');
        Route::resource('publicaciones', SocialPostController::class)
            ->parameters(['publicaciones' => 'post'])
            ->except(['show'])
            ->names('posts');
    });

    Route::prefix('email')->name('email.')->middleware('permission:ver contactos')->group(function () {
        Route::get('contactos/exportar', [ContactController::class, 'export'])->name('contacts.export')->middleware('permission:exportar contactos');
        Route::get('contactos/importar', [ContactController::class, 'importForm'])->name('contacts.import.form')->middleware('permission:importar contactos');
        Route::post('contactos/importar', [ContactController::class, 'import'])->name('contacts.import')->middleware('permission:importar contactos');
        Route::resource('contactos', ContactController::class)->parameters(['contactos' => 'contact'])->except(['show'])->names('contacts');

        Route::resource('listas', ContactListController::class)->parameters(['listas' => 'list'])->except(['show'])->names('lists');

        Route::get('campanas/{campaign}/reporte', [CampaignController::class, 'report'])->name('campaigns.report')->middleware('permission:ver campanas');
        Route::post('campanas/{campaign}/prueba', [CampaignController::class, 'sendTest'])->name('campaigns.send-test')->middleware('permission:enviar campanas');
        Route::post('campanas/{campaign}/programar', [CampaignController::class, 'schedule'])->name('campaigns.schedule')->middleware('permission:enviar campanas');
        Route::post('campanas/{campaign}/enviar-ahora', [CampaignController::class, 'sendNow'])->name('campaigns.send-now')->middleware('permission:enviar campanas');
        Route::post('campanas/{campaign}/pausar', [CampaignController::class, 'pause'])->name('campaigns.pause')->middleware('permission:enviar campanas');
        Route::resource('campanas', CampaignController::class)->parameters(['campanas' => 'campaign'])->except(['show'])->names('campaigns');
    });

    Route::prefix('admin/email')->name('admin.email.')->middleware('permission:configurar campanas')->group(function () {
        Route::get('configuracion', [EmailSettingsController::class, 'edit'])->name('settings.edit');
        Route::put('configuracion', [EmailSettingsController::class, 'update'])->name('settings.update');
        Route::post('configuracion/probar', [EmailSettingsController::class, 'test'])->name('settings.test');
    });
});
