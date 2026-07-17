<?php

namespace App\Http\Controllers\Install;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use App\Models\User;
use App\Services\Install\EnvEditor;
use App\Services\Install\RequirementsChecker;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

class InstallController extends Controller
{
    public function welcome(): View
    {
        return view('install.welcome', [
            'php' => RequirementsChecker::phpVersion(),
            'extensions' => RequirementsChecker::extensions(),
            'permissions' => RequirementsChecker::permissions(),
            'allPassed' => RequirementsChecker::allPassed(),
        ]);
    }

    public function requirements(): RedirectResponse
    {
        return redirect()->route('install.welcome');
    }

    public function databaseForm(): View
    {
        return view('install.database', [
            'old' => session('install.db', [
                'host' => '127.0.0.1', 'port' => '3306', 'database' => '', 'username' => '', 'password' => '',
            ]),
        ]);
    }

    public function databaseStore(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'host' => ['required', 'string', 'max:255'],
            'port' => ['required', 'numeric'],
            'database' => ['required', 'string', 'max:64'],
            'username' => ['required', 'string', 'max:64'],
            'password' => ['nullable', 'string', 'max:255'],
        ]);

        try {
            $pdo = new \PDO(
                "mysql:host={$data['host']};port={$data['port']};dbname={$data['database']};charset=utf8mb4",
                $data['username'],
                $data['password'] ?? '',
                [\PDO::ATTR_TIMEOUT => 5]
            );
            $pdo = null;
        } catch (\PDOException $e) {
            return back()->withInput()->with('error', 'No se pudo conectar a la base de datos: '.$e->getMessage());
        }

        EnvEditor::set([
            'DB_CONNECTION' => 'mysql',
            'DB_HOST' => $data['host'],
            'DB_PORT' => $data['port'],
            'DB_DATABASE' => $data['database'],
            'DB_USERNAME' => $data['username'],
            'DB_PASSWORD' => $data['password'] ?? '',
        ]);

        Artisan::call('config:clear');

        $request->session()->put('install.db', $data);

        return redirect()->route('install.company.form');
    }

    public function companyForm(): View|RedirectResponse
    {
        if (! session('install.db')) {
            return redirect()->route('install.database.form');
        }

        return view('install.company', [
            'old' => session('install.company', [
                'company_name' => 'NODO 360 MARKETING TECHNOLOGY',
                'system_name' => 'NODO Catalog Manager',
                'company_email' => 'info@nodo360mkt.site',
                'company_website' => 'https://nodo360mkt.site',
                'currency' => 'MXN',
                'timezone' => 'America/Mexico_City',
            ]),
        ]);
    }

    public function companyStore(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'company_name' => ['required', 'string', 'max:255'],
            'system_name' => ['required', 'string', 'max:255'],
            'company_address' => ['nullable', 'string', 'max:255'],
            'company_phone' => ['nullable', 'string', 'max:30'],
            'company_whatsapp' => ['nullable', 'string', 'max:30'],
            'company_email' => ['nullable', 'email', 'max:255'],
            'company_website' => ['nullable', 'url', 'max:255'],
            'currency' => ['required', 'string', 'max:3'],
            'timezone' => ['required', 'string', 'max:64'],
        ]);

        $request->session()->put('install.company', $data);

        return redirect()->route('install.admin.form');
    }

    public function adminForm(): View|RedirectResponse
    {
        if (! session('install.company')) {
            return redirect()->route('install.company.form');
        }

        return view('install.admin');
    }

    public function adminStore(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        $request->session()->put('install.admin', $data);

        return redirect()->route('install.run');
    }

    public function run(Request $request): View|RedirectResponse
    {
        if (! app()->environment('testing') && file_exists(storage_path('app/installed.lock'))) {
            abort(404);
        }

        $db = session('install.db');
        $company = session('install.company');
        $admin = session('install.admin');

        if (! $db || ! $company || ! $admin) {
            return redirect()->route('install.welcome')->with('error', 'Faltan datos del asistente. Comienza de nuevo.');
        }

        EnvEditor::set([
            'APP_URL' => $request->getSchemeAndHttpHost(),
            'APP_ENV' => 'production',
            'APP_DEBUG' => 'false',
        ]);

        Artisan::call('key:generate', ['--force' => true]);
        Artisan::call('config:clear');

        Artisan::call('migrate', ['--force' => true]);
        Artisan::call('db:seed', ['--class' => \Database\Seeders\RolesAndPermissionsSeeder::class, '--force' => true]);
        Artisan::call('db:seed', ['--class' => \Database\Seeders\SettingsSeeder::class, '--force' => true]);
        Artisan::call('db:seed', ['--class' => \Database\Seeders\CollectionsAndProductsSeeder::class, '--force' => true]);

        foreach ($company as $key => $value) {
            Setting::set($key, $value, 'general');
        }

        $adminUser = User::updateOrCreate(
            ['email' => $admin['email']],
            [
                'name' => $admin['name'],
                'password' => Hash::make($admin['password']),
                'is_active' => true,
                'email_verified_at' => now(),
            ]
        );
        $adminUser->syncRoles(['Superadministrador']);

        Artisan::call('storage:link');

        file_put_contents(storage_path('app/installed.lock'), now()->toDateTimeString());

        $request->session()->put('install.finished_email', $admin['email']);
        $request->session()->forget(['install.db', 'install.company', 'install.admin']);

        return redirect()->route('install.finished');
    }

    public function finished(): View|RedirectResponse
    {
        if (! file_exists(storage_path('app/installed.lock'))) {
            return redirect()->route('install.welcome');
        }

        return view('install.finished', [
            'email' => session('install.finished_email', ''),
        ]);
    }
}
