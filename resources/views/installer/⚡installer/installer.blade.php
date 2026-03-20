<div class="p-6 sm:p-8" x-data="{
    passwordStrength: 0,
    passwordLabel: '',
    checkStrength(password) {
        let score = 0;
        if (password.length >= 8) score++;
        if (password.length >= 12) score++;
        if (/[a-z]/.test(password) && /[A-Z]/.test(password)) score++;
        if (/\d/.test(password)) score++;
        if (/[^a-zA-Z0-9]/.test(password)) score++;
        this.passwordStrength = Math.min(score, 5);
        const labels = ['', 'Weak', 'Fair', 'Good', 'Strong', 'Excellent'];
        this.passwordLabel = labels[this.passwordStrength] || '';
    }
}">
    <div class="mb-8 text-center">
        <h1 class="text-3xl font-bold text-slate-800">
            {{ $store_name ?: 'System Installation' }}
        </h1>
        <p class="text-slate-500 mt-2">Follow the steps to configure your application</p>
    </div>

    <!-- Stepper Navigation -->
    <div class="mb-10 flex items-center justify-center gap-2 sm:gap-4 px-4 overflow-x-auto">
        @foreach (['Requirements', 'Database', 'Admin', 'Settings', 'Install', 'Finish'] as $index => $label)
            @php $stepNum = $index + 1; @endphp
            <div class="flex items-center">
                <div class="flex flex-col items-center gap-1">
                    <div
                        class="h-8 w-8 rounded-full flex items-center justify-center font-bold text-sm transition-colors duration-300 {{ $step >= $stepNum ? 'bg-primary text-primary-foreground' : 'bg-surface-alt text-foreground-muted' }}">
                        @if ($step > $stepNum)
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"></path>
                            </svg>
                        @else
                            {{ $stepNum }}
                        @endif
                    </div>
                    <span class="text-[10px] sm:text-xs font-medium {{ $step >= $stepNum ? 'text-primary' : 'text-foreground-muted' }} hidden sm:block whitespace-nowrap">
                        {{ $label }}
                    </span>
                </div>
                @if (!$loop->last)
                    <div class="h-0.5 w-4 sm:w-8 mx-1 sm:mx-2 rounded-full transition-colors duration-300 {{ $step > $stepNum ? 'bg-primary' : 'bg-surface-alt' }}"></div>
                @endif
            </div>
        @endforeach
    </div>

    @if ($installationError)
        <div class="mb-6 p-4 bg-red-50 border border-red-200 text-red-700 rounded-xl">
            <div class="flex items-start gap-3">
                <svg class="w-5 h-5 mt-0.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 16.5c-.77.833.192 2.5 1.732 2.5z">
                    </path>
                </svg>
                <div>
                    <strong>Installation Failed</strong>
                    <p class="mt-1 text-sm">{{ $installationError }}</p>
                    <button type="button" wire:click="retryInstallation"
                        class="mt-3 inline-flex items-center gap-1.5 text-sm font-medium text-red-700 hover:text-red-900 underline underline-offset-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15">
                            </path>
                        </svg>
                        Retry Installation
                    </button>
                </div>
            </div>
        </div>
    @endif

    <!-- Step 1: Requirements Check -->
    @if ($step === 1)
        <div wire:key="step-1">
            <h2 class="text-2xl font-semibold mb-4 text-slate-800">Server Requirements</h2>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div class="md:col-span-2 bg-slate-50 rounded-xl border border-slate-200 p-4">
                    <h3 class="font-medium text-slate-700 mb-3 border-b border-slate-200 pb-2">PHP & Extensions</h3>
                    <ul class="space-y-2 grid grid-cols-2 gap-x-4">
                        @foreach ($requirements as $requirement => $passed)
                            <li class="flex items-center justify-between col-span-1">
                                <span class="capitalize text-sm">{{ $requirement }}</span>
                                @if ($passed)
                                    <svg class="w-5 h-5 text-green-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                    </svg>
                                @else
                                    <svg class="w-5 h-5 text-red-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                    </svg>
                                @endif
                            </li>
                        @endforeach
                    </ul>
                </div>

                <div class="md:col-span-1 bg-slate-50 rounded-xl border border-slate-200 p-4">
                    <h3 class="font-medium text-slate-700 mb-3 border-b border-slate-200 pb-2">Directory Permissions</h3>
                    <ul class="space-y-2">
                        @foreach ($permissions as $dir => $passed)
                            <li class="flex items-center justify-between">
                                <span class="font-mono text-sm">{{ $dir }}</span>
                                @if ($passed)
                                    <span class="text-xs font-semibold px-2 py-1 bg-green-100 text-green-700 rounded-full">Writable</span>
                                @else
                                    <span class="text-xs font-semibold px-2 py-1 bg-red-100 text-red-700 rounded-full">Not Writable</span>
                                @endif
                            </li>
                        @endforeach
                    </ul>
                </div>
            </div>

            <div class="mt-8 flex justify-between items-center">
                <button type="button" wire:click="checkRequirements"
                    class="btn-base border border-border bg-surface text-foreground hover:bg-surface-alt flex items-center gap-2">
                    <svg wire:loading wire:target="checkRequirements" class="animate-spin h-4 w-4 text-primary"
                        xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor"
                            d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                        </path>
                    </svg>
                    Re-check
                </button>
                <button type="button" wire:click="nextStep" wire:loading.attr="disabled" @class([
                    'btn-base flex items-center gap-2',
                    'bg-primary text-primary-foreground hover:bg-primary-hover' => $requirementsPassed,
                    'bg-surface-alt text-foreground-muted cursor-not-allowed' => !$requirementsPassed,
                ]) @disabled(!$requirementsPassed)>
                    <span wire:loading.remove wire:target="nextStep">Next Step</span>
                    <span wire:loading wire:target="nextStep">Loading...</span>
                </button>
            </div>
        </div>
    @endif

    <!-- Step 2: Database Setup -->
    @if ($step === 2)
        <div wire:key="step-2">
            <h2 class="text-2xl font-semibold mb-2 text-slate-800">Database Configuration</h2>
            <p class="text-slate-500 mb-6">Enter your database connection details below.</p>

            <form wire:submit="nextStep" class="space-y-4" x-data="{ dbConn: @entangle('db_connection') }">
                <!-- Database Type Selector -->
                <div>
                    <label class="mb-1.5 block text-sm font-medium text-foreground">Database Engine</label>
                    <div class="grid grid-cols-3 gap-3">
                        @foreach (['mysql' => 'MySQL', 'pgsql' => 'PostgreSQL', 'sqlite' => 'SQLite'] as $value => $label)
                            <button type="button" wire:click="$set('db_connection', '{{ $value }}')" @class([
                                'p-3 rounded-xl border-2 text-center text-sm font-medium transition-all duration-200',
                                'border-primary bg-primary/5 text-primary' => $db_connection === $value,
                                'border-border bg-surface text-foreground hover:border-primary/50' => $db_connection !== $value,
                            ])>
                                {{ $label }}
                            </button>
                        @endforeach
                    </div>
                </div>

                <div x-show="dbConn !== 'sqlite'" x-cloak class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="mb-1.5 block text-sm font-medium text-foreground">Database Host</label>
                        <input type="text" wire:model.blur="db_host" class="input-base w-full">
                        @error('db_host') <span class="mt-1 text-xs text-danger">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label class="mb-1.5 block text-sm font-medium text-foreground">Database Port</label>
                        <input type="text" wire:model.blur="db_port" class="input-base w-full">
                        @error('db_port') <span class="mt-1 text-xs text-danger">{{ $message }}</span> @enderror
                    </div>
                </div>

                <div>
                    <label class="mb-1.5 block text-sm font-medium text-foreground">
                        <span x-show="dbConn === 'sqlite'">Database File Path</span>
                        <span x-show="dbConn !== 'sqlite'">Database Name</span>
                    </label>
                    <input type="text" wire:model.blur="db_database" class="input-base w-full"
                        x-bind:placeholder="dbConn === 'sqlite' ? 'database.sqlite' : ''">
                    @error('db_database') <span class="mt-1 text-xs text-danger">{{ $message }}</span> @enderror
                </div>

                <div x-show="dbConn !== 'sqlite'" x-cloak>
                    <label class="mb-1.5 block text-sm font-medium text-foreground">Database Username</label>
                    <input type="text" wire:model.blur="db_username" class="input-base w-full">
                    @error('db_username') <span class="mt-1 text-xs text-danger">{{ $message }}</span> @enderror
                </div>
                <div x-show="dbConn !== 'sqlite'" x-cloak>
                    <label class="mb-1.5 block text-sm font-medium text-foreground">Database Password</label>
                    <input type="password" wire:model.blur="db_password" class="input-base w-full">
                </div>

                <div class="mt-4">
                    <button type="button" wire:click="testDbConnection"
                        class="btn-base border border-border bg-surface text-foreground hover:bg-surface-alt flex items-center gap-2">
                        <svg wire:loading wire:target="testDbConnection" class="animate-spin h-4 w-4 text-primary"
                            xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor"
                                d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                            </path>
                        </svg>
                        Test Connection
                    </button>
                    @if ($dbConnectionError)
                        <div class="mt-2 text-sm text-red-600 bg-red-50 p-3 rounded-lg flex items-start gap-2">
                            <svg class="w-4 h-4 mt-0.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                            {{ $dbConnectionError }}
                        </div>
                    @endif
                    @if ($dbConnectionSuccess)
                        <div class="mt-2 text-sm text-green-600 flex items-center gap-1.5">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                            Connection Successful
                        </div>
                    @endif
                </div>

                <div class="mt-8 flex justify-between gap-3">
                    <button type="button" wire:click="previousStep" wire:loading.attr="disabled"
                        class="btn-base border border-border bg-surface text-foreground hover:bg-surface-alt">
                        Back
                    </button>
                    <button type="submit" wire:loading.attr="disabled" @class([
                        'btn-base flex items-center gap-2',
                        'bg-primary text-primary-foreground hover:bg-primary-hover' => $dbConnectionSuccess,
                        'bg-surface-alt text-foreground-muted cursor-not-allowed' => !$dbConnectionSuccess,
                    ]) @disabled(!$dbConnectionSuccess)>
                        <span wire:loading.remove wire:target="nextStep">Next Step</span>
                        <span wire:loading wire:target="nextStep">Verifying...</span>
                    </button>
                </div>
            </form>
        </div>
    @endif

    <!-- Step 3: Admin Setup -->
    @if ($step === 3)
        <div wire:key="step-3">
            <h2 class="text-2xl font-semibold mb-2 text-slate-800">Admin Account Configuration</h2>
            <p class="text-slate-500 mb-6">Create the primary administrator account.</p>

            <form wire:submit="nextStep" class="space-y-4">
                <div>
                    <label class="mb-1.5 block text-sm font-medium text-foreground">Full Name</label>
                    <input type="text" wire:model="admin_name" class="input-base w-full" placeholder="John Doe">
                    @error('admin_name') <span class="mt-1 text-xs text-danger">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label class="mb-1.5 block text-sm font-medium text-foreground">Email Address</label>
                    <input type="email" wire:model="admin_email" class="input-base w-full" placeholder="admin@example.com">
                    @error('admin_email') <span class="mt-1 text-xs text-danger">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label class="mb-1.5 block text-sm font-medium text-foreground">Password</label>
                    <input type="password" wire:model="admin_password" class="input-base w-full"
                        x-on:input="checkStrength($event.target.value)" placeholder="Minimum 8 characters">
                    @error('admin_password') <span class="mt-1 text-xs text-danger">{{ $message }}</span> @enderror
                    <!-- Password Strength Meter -->
                    <div class="mt-2" x-show="passwordStrength > 0" x-transition>
                        <div class="flex items-center gap-2">
                            <div class="flex-1 flex gap-1">
                                <template x-for="i in 5" :key="i">
                                    <div class="h-1 flex-1 rounded-full transition-colors duration-300" :class="{
                                        'bg-red-400': passwordStrength >= i && passwordStrength <= 1,
                                        'bg-orange-400': passwordStrength >= i && passwordStrength === 2,
                                        'bg-yellow-400': passwordStrength >= i && passwordStrength === 3,
                                        'bg-lime-400': passwordStrength >= i && passwordStrength === 4,
                                        'bg-green-500': passwordStrength >= i && passwordStrength === 5,
                                        'bg-slate-200': passwordStrength < i,
                                    }"></div>
                                </template>
                            </div>
                            <span class="text-xs font-medium text-foreground-muted" x-text="passwordLabel"></span>
                        </div>
                    </div>
                </div>

                <div>
                    <label class="mb-1.5 block text-sm font-medium text-foreground">Confirm Password</label>
                    <input type="password" wire:model="admin_password_confirmation" class="input-base w-full" placeholder="Re-enter password">
                </div>

                <div class="mt-8 flex justify-between gap-3">
                    <button type="button" wire:click="previousStep" wire:loading.attr="disabled"
                        class="btn-base border border-border bg-surface text-foreground hover:bg-surface-alt">
                        Back
                    </button>
                    <button type="submit" wire:loading.attr="disabled"
                        class="btn-base bg-primary text-primary-foreground hover:bg-primary-hover flex items-center gap-2">
                        <span wire:loading.remove wire:target="nextStep">Next Step</span>
                        <span wire:loading wire:target="nextStep">Validating...</span>
                    </button>
                </div>
            </form>
        </div>
    @endif

    <!-- Step 4: Settings Setup -->
    @if ($step === 4)
        <div wire:key="step-4">
            <h2 class="text-2xl font-semibold mb-2 text-slate-800">General Settings</h2>
            <p class="text-slate-500 mb-6">Configure your shop's basic details.</p>

            <form wire:submit="nextStep" class="space-y-4">
                <div>
                    <label class="mb-1.5 block text-sm font-medium text-foreground">Store Name</label>
                    <input type="text" wire:model="store_name" class="input-base w-full">
                    @error('store_name') <span class="mt-1 text-xs text-danger">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label class="mb-1.5 block text-sm font-medium text-foreground">Store Contact Email</label>
                    <input type="email" wire:model="store_email" class="input-base w-full">
                    @error('store_email') <span class="mt-1 text-xs text-danger">{{ $message }}</span> @enderror
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="mb-1.5 block text-sm font-medium text-foreground">Phone Number
                            <span class="text-foreground-muted">(Optional)</span></label>
                        <input type="text" wire:model="store_phone" class="input-base w-full" placeholder="+123456789">
                        @error('store_phone') <span class="mt-1 text-xs text-danger">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label class="mb-1.5 block text-sm font-medium text-foreground">Application URL</label>
                        <input type="url" wire:model="app_url" class="input-base w-full" placeholder="https://yourdomain.com">
                        @error('app_url') <span class="mt-1 text-xs text-danger">{{ $message }}</span> @enderror
                    </div>
                </div>

                <div>
                    <label class="mb-1.5 block text-sm font-medium text-foreground">Store Address
                        <span class="text-foreground-muted">(Optional)</span></label>
                    <input type="text" wire:model="store_address" class="input-base w-full">
                    @error('store_address') <span class="mt-1 text-xs text-danger">{{ $message }}</span> @enderror
                </div>

                <div class="mt-8 flex justify-between gap-3">
                    <button type="button" wire:click="previousStep" wire:loading.attr="disabled"
                        class="btn-base border border-border bg-surface text-foreground hover:bg-surface-alt">
                        Back
                    </button>
                    <button type="submit" wire:loading.attr="disabled"
                        class="btn-base bg-primary text-primary-foreground hover:bg-primary-hover flex items-center gap-2">
                        <span wire:loading.remove wire:target="nextStep">Next Step</span>
                        <span wire:loading wire:target="nextStep">Saving...</span>
                    </button>
                </div>
            </form>
        </div>
    @endif

    <!-- Step 5: Installation Execution -->
    @if ($step === 5)
        <div wire:key="step-5" class="py-6">
            <h2 class="text-2xl font-semibold mb-4 text-slate-800 text-center">Ready to Install</h2>

            <div x-data="{ installing: @entangle('isInstalling'), locked: @entangle('installLocked') }">
                <!-- Pre-install summary (hidden when installing) -->
                <div x-show="!installing && !@js($installationError)" x-cloak>
                    <!-- Installation Summary -->
                    <div class="max-w-lg mx-auto mb-8">
                        <div class="bg-slate-50 rounded-xl border border-slate-200 divide-y divide-slate-200">
                            <div class="p-4 flex justify-between items-center">
                                <span class="text-sm text-slate-500">Database</span>
                                <span class="text-sm font-medium text-slate-800">{{ strtoupper($db_connection) }}
                                    @if ($db_connection !== 'sqlite')
                                        — {{ $db_host }}:{{ $db_port }}/{{ $db_database }}
                                    @else
                                        — {{ $db_database }}
                                    @endif
                                </span>
                            </div>
                            <div class="p-4 flex justify-between items-center">
                                <span class="text-sm text-slate-500">Admin Email</span>
                                <span class="text-sm font-medium text-slate-800">{{ $admin_email }}</span>
                            </div>
                            <div class="p-4 flex justify-between items-center">
                                <span class="text-sm text-slate-500">Store Name</span>
                                <span class="text-sm font-medium text-slate-800">{{ $store_name }}</span>
                            </div>
                            <div class="p-4 flex justify-between items-center">
                                <span class="text-sm text-slate-500">Application URL</span>
                                <span class="text-sm font-medium text-slate-800">{{ $app_url }}</span>
                            </div>
                        </div>
                    </div>

                    <p class="text-slate-500 mb-8 max-w-md mx-auto text-center text-sm">
                        This will create database tables, seed initial data, and configure your environment for production.
                    </p>

                    <div class="flex justify-center gap-4">
                        <button type="button" wire:click="previousStep"
                            class="btn-base border border-border bg-surface text-foreground hover:bg-surface-alt disabled:opacity-50">
                            Review Settings
                        </button>

                        <button type="button" wire:click="startInstallation"
                            x-bind:disabled="locked"
                            class="btn-base bg-primary text-primary-foreground hover:bg-primary-hover flex items-center gap-2 font-medium disabled:opacity-50">
                            <span>Begin Installation</span>
                        </button>
                    </div>
                </div>

                <!-- Progress bar (shown when installing) -->
                <div x-show="installing" x-cloak
                    @if ($isInstalling && $currentInstallStep >= 1 && $currentInstallStep <= 4)
                        wire:poll.2s="runInstallStep"
                    @endif
                >
                    <div class="max-w-md mx-auto mt-4 text-left">
                        <div class="mb-3 flex justify-between items-center">
                            <span class="text-sm font-medium text-slate-700">{{ $installMessage }}</span>
                            <span class="text-sm font-semibold text-primary">{{ $installProgress }}%</span>
                        </div>
                        <div class="w-full bg-slate-200 rounded-full h-3 overflow-hidden">
                            <div class="bg-primary h-3 rounded-full transition-all duration-700 ease-out"
                                style="width: {{ $installProgress }}%"></div>
                        </div>
                        <p class="text-xs text-slate-400 mt-3 text-center">Please do not close or refresh this page.</p>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <!-- Step 6: Success Output -->
    @if ($step === 6)
        <div wire:key="step-6" class="text-center py-10">
            <div class="inline-flex items-center justify-center w-20 h-20 rounded-full bg-green-100 mb-6">
                <svg class="w-10 h-10 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                </svg>
            </div>

            <h2 class="text-2xl font-semibold mb-3 text-slate-800">Installation Successful!</h2>
            <p class="text-slate-500 mb-8 max-w-md mx-auto">Your application has been configured and is ready to use.
                Log in with<br><strong class="text-slate-700">{{ $admin_email }}</strong></p>

            <a href="{{ route('login') }}"
                class="px-8 py-3 rounded-xl bg-primary text-primary-foreground hover:bg-primary-hover transition-colors font-medium text-lg inline-flex items-center gap-2">
                Go to Login
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                </svg>
            </a>
        </div>
    @endif
</div>