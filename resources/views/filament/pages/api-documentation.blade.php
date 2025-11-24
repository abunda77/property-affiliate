<x-filament-panels::page>
    {{-- Quick Links Section --}}
    <div class="grid grid-cols-1 gap-6 lg:grid-cols-2 mb-6">
        {{-- Interactive Documentation Card --}}
        <div class="fi-section rounded-xl bg-white shadow-sm ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10">
            <div class="fi-section-content p-6">
                <div class="flex gap-4">
                    <div class="flex h-12 w-12 items-center justify-center rounded-lg bg-primary-50 dark:bg-primary-500/10">
                        <x-filament::icon
                            icon="heroicon-o-document-text"
                            class="h-6 w-6 text-primary-600 dark:text-primary-400"
                        />
                    </div>
                    <div class="flex-1">
                        <h3 class="text-base font-semibold text-gray-950 dark:text-white">
                            Interactive API Documentation
                        </h3>
                        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                            Browse and test API endpoints with live examples
                        </p>
                        <div class="mt-3">
                            <a href="/docs/api" target="_blank" rel="noopener noreferrer" 
                               class="inline-flex items-center gap-1 text-sm font-medium text-primary-600 hover:text-primary-500 dark:text-primary-400">
                                Open Documentation
                                <x-filament::icon
                                    icon="heroicon-m-arrow-top-right-on-square"
                                    class="h-4 w-4"
                                />
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- OpenAPI Spec Card --}}
        <div class="fi-section rounded-xl bg-white shadow-sm ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10">
            <div class="fi-section-content p-6">
                <div class="flex gap-4">
                    <div class="flex h-12 w-12 items-center justify-center rounded-lg bg-success-50 dark:bg-success-500/10">
                        <x-filament::icon
                            icon="heroicon-o-code-bracket"
                            class="h-6 w-6 text-success-600 dark:text-success-400"
                        />
                    </div>
                    <div class="flex-1">
                        <h3 class="text-base font-semibold text-gray-950 dark:text-white">
                            OpenAPI Specification
                        </h3>
                        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                            Download OpenAPI 3.0 JSON schema
                        </p>
                        <div class="mt-3">
                            <a href="/docs/api.json" target="_blank" rel="noopener noreferrer"
                               class="inline-flex items-center gap-1 text-sm font-medium text-success-600 hover:text-success-500 dark:text-success-400">
                                Download JSON
                                <x-filament::icon
                                    icon="heroicon-m-arrow-down-tray"
                                    class="h-4 w-4"
                                />
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- API Info & Endpoints Section --}}
    <div class="grid grid-cols-1 gap-6 lg:grid-cols-2 mb-6">
        {{-- API Information --}}
        <div class="fi-section rounded-xl bg-white shadow-sm ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10">
            <div class="fi-section-header flex items-center gap-x-3 overflow-hidden px-6 py-4">
                <div class="grid flex-1 gap-y-1">
                    <h3 class="fi-section-header-heading text-base font-semibold leading-6 text-gray-950 dark:text-white">
                        API Information
                    </h3>
                </div>
            </div>
            <div class="fi-section-content p-6 pt-0">
                <dl class="space-y-4">
                    <div>
                        <dt class="text-xs font-medium uppercase tracking-wider text-gray-700 dark:text-gray-300">
                            Base URL
                        </dt>
                        <dd class="mt-1">
                            <code class="inline-flex items-center rounded-md bg-gray-50 px-2 py-1 text-xs font-mono text-gray-600 ring-1 ring-inset ring-gray-500/10 dark:bg-gray-400/10 dark:text-gray-400 dark:ring-gray-400/20">
                                {{ config('app.url') }}/api
                            </code>
                        </dd>
                    </div>
                    <div>
                        <dt class="text-xs font-medium uppercase tracking-wider text-gray-700 dark:text-gray-300">
                            Version
                        </dt>
                        <dd class="mt-1">
                            <x-filament::badge color="primary">
                                v{{ config('scramble.info.version') }}
                            </x-filament::badge>
                        </dd>
                    </div>
                    <div>
                        <dt class="text-xs font-medium uppercase tracking-wider text-gray-700 dark:text-gray-300">
                            Authentication
                        </dt>
                        <dd class="mt-1">
                            <x-filament::badge color="warning" icon="heroicon-m-lock-closed">
                                Laravel Sanctum (Bearer Token)
                            </x-filament::badge>
                        </dd>
                    </div>
                </dl>
            </div>
        </div>

        {{-- Available Endpoints --}}
        <div class="fi-section rounded-xl bg-white shadow-sm ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10">
            <div class="fi-section-header flex items-center gap-x-3 overflow-hidden px-6 py-4">
                <div class="grid flex-1 gap-y-1">
                    <h3 class="fi-section-header-heading text-base font-semibold leading-6 text-gray-950 dark:text-white">
                        Available Endpoints
                    </h3>
                </div>
            </div>
            <div class="fi-section-content p-6 pt-0">
                <div class="space-y-4">
                    {{-- Authentication Endpoints --}}
                    <div>
                        <h4 class="mb-2 flex items-center gap-2 text-sm font-semibold text-gray-950 dark:text-white">
                            <x-filament::icon
                                icon="heroicon-m-key"
                                class="h-4 w-4 text-primary-600 dark:text-primary-400"
                            />
                            Authentication
                        </h4>
                        <div class="space-y-1.5 text-xs">
                            <div class="flex items-center gap-2">
                                <x-filament::badge color="info" size="xs">POST</x-filament::badge>
                                <code class="text-gray-600 dark:text-gray-400">/api/login</code>
                            </div>
                            <div class="flex items-center gap-2">
                                <x-filament::badge color="danger" size="xs">POST</x-filament::badge>
                                <code class="text-gray-600 dark:text-gray-400">/api/logout</code>
                            </div>
                            <div class="flex items-center gap-2">
                                <x-filament::badge color="success" size="xs">GET</x-filament::badge>
                                <code class="text-gray-600 dark:text-gray-400">/api/user</code>
                            </div>
                        </div>
                    </div>

                    {{-- Property Endpoints --}}
                    <div>
                        <h4 class="mb-2 flex items-center gap-2 text-sm font-semibold text-gray-950 dark:text-white">
                            <x-filament::icon
                                icon="heroicon-m-home"
                                class="h-4 w-4 text-primary-600 dark:text-primary-400"
                            />
                            Properties
                        </h4>
                        <div class="space-y-1.5 text-xs">
                            <div class="flex items-center gap-2">
                                <x-filament::badge color="success" size="xs">GET</x-filament::badge>
                                <code class="text-gray-600 dark:text-gray-400">/api/properties</code>
                            </div>
                            <div class="flex items-center gap-2">
                                <x-filament::badge color="success" size="xs">GET</x-filament::badge>
                                <code class="text-gray-600 dark:text-gray-400">/api/properties/featured</code>
                            </div>
                            <div class="flex items-center gap-2">
                                <x-filament::badge color="success" size="xs">GET</x-filament::badge>
                                <code class="text-gray-600 dark:text-gray-400">/api/properties/{slug}</code>
                            </div>
                            <div class="flex items-center gap-2">
                                <x-filament::badge color="info" size="xs">POST</x-filament::badge>
                                <code class="text-gray-600 dark:text-gray-400">/api/properties/track-click</code>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Embedded Documentation --}}
    <div class="fi-section rounded-xl bg-white shadow-sm ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10">
        <div class="fi-section-header flex items-center justify-between gap-x-3 overflow-hidden px-6 py-4">
            <div class="flex items-center gap-x-3">
                <x-filament::icon
                    icon="heroicon-o-code-bracket-square"
                    class="h-5 w-5 text-gray-400 dark:text-gray-500"
                />
                <h3 class="fi-section-header-heading text-base font-semibold leading-6 text-gray-950 dark:text-white">
                    Live API Documentation
                </h3>
            </div>
            <div>
                <x-filament::button
                    tag="a"
                    href="/docs/api"
                    target="_blank"
                    rel="noopener noreferrer"
                    size="sm"
                    outlined
                    icon="heroicon-m-arrow-top-right-on-square"
                >
                    Open Full Screen
                </x-filament::button>
            </div>
        </div>
        
    </div>
</x-filament-panels::page>
