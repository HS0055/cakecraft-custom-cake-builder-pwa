<?php

namespace App\Livewire\Installer;

use App\Models\User;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Livewire\Attributes\Layout;

use Livewire\Attributes\Validate;
use Livewire\Component;

new #[Layout('layouts::installer')] class extends Component {
    public int $step = 1;
    public int $totalSteps = 5;

    /**
     * Installation requirements and permissions state.
     */
    public array $requirements = [];
    public array $permissions = [];
    public bool $requirementsPassed = false;

    /**
     * Database configuration credentials.
     */
    #[Validate('required|in:mysql,pgsql,sqlite')]
    public string $db_connection = 'mysql';

    #[Validate('required_unless:db_connection,sqlite')]
    public string $db_host = '127.0.0.1';

    #[Validate('required_unless:db_connection,sqlite|numeric')]
    public string $db_port = '3306';

    #[Validate('required')]
    public string $db_database = '';

    #[Validate('required_unless:db_connection,sqlite')]
    public string $db_username = '';

    public string $db_password = '';

    public bool $dbConnectionSuccess = false;
    public ?string $dbConnectionError = null;

    /**
     * Administrator account credentials.
     */
    #[Validate('required|string|max:255')]
    public string $admin_name = '';

    #[Validate('required|string|email|max:255')]
    public string $admin_email = '';

    #[Validate('required|string|min:8|confirmed')]
    public string $admin_password = '';

    public string $admin_password_confirmation = '';

    /**
     * General store application settings.
     */
    #[Validate('required|string|max:255')]
    public string $store_name = 'Cake Craft';

    #[Validate('required|string|email|max:255')]
    public string $store_email = '';

    public string $store_phone = '';
    public string $store_address = '';

    #[Validate('required|url|max:255')]
    public string $app_url = '';

    /**
     * Installation process state and progress tracking.
     */
    public bool $isInstalling = false;
    public bool $installLocked = false;
    public ?string $installationError = null;
    public int $installProgress = 0;
    public string $installMessage = '';

    /**
     * Initialize the component and redirect if already installed.
     */
    public function mount()
    {
        if ($this->isInstalled()) {
            $this->redirectRoute('front.home');
            return;
        }

        $this->app_url = url('/');
        $this->checkRequirements();
    }

    /**
     * Check if the application is already installed based on the sentinel file.
     *
     * @return bool
     */
    public static function isInstalled(): bool
    {
        return file_exists(storage_path('installed'));
    }

    /**
     * Verify server requirements and folder permissions.
     */
    public function checkRequirements()
    {
        $this->requirements = [
            'PHP 8.2+' => version_compare(PHP_VERSION, '8.2.0', '>='),
            'PDO Extension' => extension_loaded('pdo'),
            'cURL Extension' => extension_loaded('curl'),
            'JSON Extension' => extension_loaded('json'),
            'mbstring Extension' => extension_loaded('mbstring'),
            'OpenSSL Extension' => extension_loaded('openssl'),
            'XML Extension' => extension_loaded('xml'),
            'ZIP Extension' => extension_loaded('zip'),
            'Fileinfo Extension' => extension_loaded('fileinfo'),
            'BCMath Extension' => extension_loaded('bcmath'),
        ];

        $envPath = base_path('.env');
        $envExists = file_exists($envPath);
        $envWritable = $envExists ? is_writable($envPath) : is_writable(base_path());

        $this->permissions = [
            'Storage Folder' => is_writable(storage_path()),
            'Cache Folder' => is_writable(base_path('bootstrap/cache')),
            'Environment File (.env)' => $envWritable,
        ];

        $this->requirementsPassed = !in_array(false, $this->requirements) && !in_array(false, $this->permissions);
    }

    /**
     * Handle updates to the database connection selector to assign defaults.
     */
    public function updatedDbConnection()
    {
        $this->dbConnectionSuccess = false;
        $this->dbConnectionError = null;

        if ($this->db_connection === 'pgsql') {
            $this->db_port = '5432';
        } elseif ($this->db_connection === 'mysql') {
            $this->db_port = '3306';
        }
    }

    /**
     * Attempt to connect to the database to verify the provided credentials.
     */
    public function testDbConnection()
    {
        if ($this->db_connection === 'sqlite') {
            $this->validate(['db_database' => 'required']);
        } else {
            $this->validate([
                'db_host' => 'required',
                'db_port' => 'required|numeric',
                'db_database' => 'required',
                'db_username' => 'required',
            ]);
        }

        $this->dbConnectionError = null;
        $this->dbConnectionSuccess = false;

        try {
            if ($this->db_connection === 'sqlite') {
                $dbPath = $this->db_database;
                if (!str_starts_with($dbPath, '/')) {
                    $dbPath = database_path($dbPath);
                }
                if (!file_exists($dbPath)) {
                    $dir = dirname($dbPath);
                    if (!is_dir($dir)) {
                        throw new \RuntimeException("Directory does not exist: {$dir}. Please create it first.");
                    }
                    touch($dbPath);
                }
                $dsn = "sqlite:{$dbPath}";
                $pdo = new \PDO($dsn, null, null, [\PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION]);
            } elseif ($this->db_connection === 'pgsql') {
                $dsn = "pgsql:host={$this->db_host};port={$this->db_port};dbname={$this->db_database}";
                $pdo = new \PDO($dsn, $this->db_username, $this->db_password, [\PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION]);
            } else {
                $dsn = "mysql:host={$this->db_host};port={$this->db_port};dbname={$this->db_database}";
                $pdo = new \PDO($dsn, $this->db_username, $this->db_password, [\PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION]);
            }
            $this->dbConnectionSuccess = true;
        } catch (\PDOException $e) {
            $this->dbConnectionError = "Could not connect to database. Make sure your credentials are correct and the database exists.";
            Log::error('Installer DB Form Error: ' . $e->getMessage());
        } catch (\RuntimeException $e) {
            $this->dbConnectionError = $e->getMessage();
        }
    }

    /**
     * Progress to the next step of the installation wizard after validation.
     */
    public function nextStep()
    {
        if ($this->step === 1 && !$this->requirementsPassed)
            return;

        if ($this->step === 2) {
            $this->testDbConnection();
            if (!$this->dbConnectionSuccess)
                return;
        }

        if ($this->step === 3) {
            $this->validate([
                'admin_name' => 'required|string|max:255',
                'admin_email' => 'required|string|email|max:255',
                'admin_password' => 'required|string|min:8|confirmed',
            ]);
        }

        if ($this->step === 4) {
            $this->validate([
                'store_name' => 'required|string|max:255',
                'store_email' => 'required|string|email|max:255',
                'store_phone' => 'nullable|string|max:20',
                'store_address' => 'nullable|string|max:500',
                'app_url' => 'required|url|max:255',
            ]);
        }

        if ($this->step < 5) {
            $this->step++;
        }
    }

    /**
     * Return to the previous step in the wizard.
     */
    public function previousStep()
    {
        if ($this->step > 1) {
            $this->step--;
        }
    }

    /**
     * Sub-step tracker for the installation execution sequence.
     * Starts at 0, goes up to 5 sequentially.
     */
    public int $currentInstallStep = 0;

    /**
     * Initialize the installation process by configuring the environment,
     * which implicitly completes step 1 and queues the sequential polling.
     */
    public function startInstallation()
    {
        if ($this->installLocked)
            return;

        $this->installLocked = true;
        $this->isInstalling = true;
        $this->installationError = null;

        try {
            Log::info('[Installer] Starting installation — writing .env...');

            $this->installProgress = 5;
            $this->installMessage = 'Configuring environment...';
            $this->stepPrepareDb();

            Log::info('[Installer] .env written, setting currentInstallStep=1 for migrations');

            $this->currentInstallStep = 1;
        } catch (\Throwable $e) {
            $this->failInstall($e);
        }
    }

    /**
     * Triggered automatically by the frontend to process the installation sequentially.
     */
    public function runInstallStep()
    {
        if ($this->currentInstallStep < 1 || $this->currentInstallStep > 4) {
            return;
        }

        set_time_limit(0);
        ini_set('memory_limit', '512M');

        try {
            switch ($this->currentInstallStep) {
                case 1:
                    $this->installProgress = 20;
                    $this->installMessage = 'Running database migrations...';
                    $this->stepMigrations();
                    Log::info('[Installer] Migrations completed');
                    $this->currentInstallStep = 2;
                    break;

                case 2:
                    $this->installProgress = 50;
                    $this->installMessage = 'Seeding initial data...';
                    $this->stepSeeds();
                    Log::info('[Installer] Seeds completed');
                    $this->currentInstallStep = 3;
                    break;

                case 3:
                    $this->installProgress = 70;
                    $this->installMessage = 'Creating administrator account...';
                    $this->stepAdminAndSettings();
                    Log::info('[Installer] Admin + settings completed');
                    $this->currentInstallStep = 4;
                    break;

                case 4:
                    $this->installProgress = 85;
                    $this->installMessage = 'Finalizing installation...';
                    $this->stepFinalize();
                    Log::info('[Installer] Installation complete!');
                    $this->currentInstallStep = 5;
                    break;
            }
        } catch (\Throwable $e) {
            $this->failInstall($e);
        }
    }

    /**
     * Unlock the installation process after a failure to permit retry attempts.
     */
    public function retryInstallation()
    {
        $this->installLocked = false;
        $this->isInstalling = false;
        $this->installationError = null;
        $this->installProgress = 0;
        $this->installMessage = '';
        $this->currentInstallStep = 0;
        $this->step = 5;
    }

    /**
     * Write database configuration and environment settings to the .env file.
     */
    private function stepPrepareDb()
    {
        $connection = $this->db_connection;
        $envData = [
            'DB_CONNECTION' => $connection,
            'APP_URL' => $this->app_url,
        ];

        if ($connection === 'sqlite') {
            $dbPath = $this->db_database;
            if (!str_starts_with($dbPath, '/')) {
                $dbPath = database_path($dbPath);
            }
            $envData['DB_DATABASE'] = $dbPath;
        } else {
            $envData['DB_HOST'] = $this->db_host;
            $envData['DB_PORT'] = $this->db_port;
            $envData['DB_DATABASE'] = $this->db_database;
            $envData['DB_USERNAME'] = $this->db_username;
            $envData['DB_PASSWORD'] = $this->db_password;
        }

        $this->updateEnv($envData);

        $this->updateEnv([
            'SESSION_DRIVER' => 'file',
            'CACHE_STORE' => 'file',
            'QUEUE_CONNECTION' => 'sync',
        ]);

        config([
            'session.driver' => 'file',
            'cache.default' => 'file',
            'queue.default' => 'sync',
        ]);

        if (empty(config('app.key')) || config('app.key') === '') {
            Artisan::call('key:generate', ['--force' => true]);
        }

        config(['database.default' => $connection]);
        if ($connection === 'sqlite') {
            $dbPath = $this->db_database;
            if (!str_starts_with($dbPath, '/')) {
                $dbPath = database_path($dbPath);
            }
            config(["database.connections.sqlite.database" => $dbPath]);
        } else {
            config([
                "database.connections.{$connection}.host" => $this->db_host,
                "database.connections.{$connection}.port" => $this->db_port,
                "database.connections.{$connection}.database" => $this->db_database,
                "database.connections.{$connection}.username" => $this->db_username,
                "database.connections.{$connection}.password" => $this->db_password,
            ]);
        }

        DB::purge($connection);
        DB::reconnect($connection);
    }

    /**
     * Run all pending database migrations securely.
     */
    private function stepMigrations()
    {
        set_time_limit(0);
        ini_set('memory_limit', '512M');

        Artisan::call('migrate', ['--force' => true]);
    }

    /**
     * Execute initial database seeders.
     */
    private function stepSeeds()
    {
        set_time_limit(0);
        ini_set('memory_limit', '512M');

        Artisan::call('db:seed', ['--force' => true]);
    }

    /**
     * Configure the system administrator and generic store settings.
     */
    private function stepAdminAndSettings()
    {
        $admin = User::updateOrCreate(
            ['email' => $this->admin_email],
            [
                'name' => $this->admin_name,
                'password' => Hash::make($this->admin_password),
                'email_verified_at' => now(),
            ]
        );

        if (class_exists(\Spatie\Permission\Models\Role::class)) {
            $role = \Spatie\Permission\Models\Role::firstOrCreate(['name' => 'super_admin']);
            if (!$admin->hasRole('super_admin')) {
                $admin->assignRole('super_admin');
            }
        }

        if (class_exists(\App\Settings\GeneralSettings::class)) {
            $settings = app(\App\Settings\GeneralSettings::class);
            $settings->store_name = $this->store_name;
            $settings->store_email = $this->store_email;
            $settings->store_phone = $this->store_phone;
            $settings->store_address = $this->store_address;
            $settings->save();
        }
    }

    /**
     * Finalize the installation process by linking storage 
     * and writing the sentinel file, while shifting production environment updates.
     */
    private function stepFinalize()
    {
        try {
            Artisan::call('storage:link', ['--force' => true]);
        } catch (\Throwable $e) {
            Log::warning('storage:link failed (may already exist): ' . $e->getMessage());
        }

        file_put_contents(storage_path('installed'), now()->toDateTimeString());

        register_shutdown_function(function () {
            $this->updateEnv([
                'APP_INSTALLED' => 'true',
                'APP_ENV' => 'production',
                'APP_DEBUG' => 'false',
                'SESSION_DRIVER' => 'database',
                'CACHE_STORE' => 'database',
                'QUEUE_CONNECTION' => 'database',
            ]);

            try {
                Artisan::call('config:clear');
                Artisan::call('route:clear');
                Artisan::call('view:clear');
            } catch (\Throwable $e) {
                // Ignore silent caching failures post-install
            }
            Log::info('[Installer] Production environment settings applied during shutdown.');
        });

        $this->installProgress = 100;
        $this->installMessage = 'Installation complete!';
        $this->step = 6;
        $this->isInstalling = false;
        $this->installLocked = false;
    }

    /**
     * Mark the installation as failed and properly log the exception.
     *
     * @param \Throwable $e The thrown exception leading to installation failure.
     */
    private function failInstall(\Throwable $e)
    {
        Log::error('Installation sequence failed: ' . $e->getMessage() . "\n" . $e->getTraceAsString());
        $this->installationError = 'Something went wrong during installation. Please check storage/logs/laravel.log for details, then click Retry.';
        $this->isInstalling = false;
        $this->installLocked = false;
    }

    /**
     * Update environment variables dynamically within the .env file.
     *
     * @param array $data Associative array containing the variable keys and values.
     */
    private function updateEnv($data)
    {
        $envPath = base_path('.env');

        if (!file_exists($envPath)) {
            if (file_exists(base_path('.env.example'))) {
                copy(base_path('.env.example'), $envPath);
                Artisan::call('key:generate', ['--force' => true]);
            } else {
                file_put_contents($envPath, '');
            }
        }

        $envContent = file_get_contents($envPath);

        foreach ($data as $key => $value) {
            if (is_string($value) && (str_contains($value, ' ') || str_contains($value, '#')) && !str_starts_with($value, '"')) {
                $value = '"' . $value . '"';
            }

            $pattern = "/^{$key}=(.*)$/m";

            if (preg_match($pattern, $envContent)) {
                $envContent = preg_replace($pattern, "{$key}={$value}", $envContent);
            } else {
                $envContent .= "\n{$key}={$value}\n";
            }
        }

        file_put_contents($envPath, $envContent);
    }
};
