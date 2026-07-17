<?php

use App\Http\Controllers\Admin\ActivityController;
use App\Http\Controllers\Admin\AiSettingsController;
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
use App\Http\Controllers\Images\GeneratorController as ImageGeneratorController;
use App\Http\Controllers\Images\TemplateController as ImageTemplateController;
use App\Http\Controllers\Install\InstallController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

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
});
