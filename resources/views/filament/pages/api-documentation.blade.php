<x-filament-panels::page>
    <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
        {{-- Interactive Documentation Card --}}
        <x-filament::section>
            <x-slot name="heading">
                Interactive API Documentation
            </x-slot>

            <x-slot name="description">
                Browse and test API endpoints with live examples using our interactive documentation.
            </x-slot>

            <x-slot name="headerEnd">
                <x-filament::icon
                    icon="heroicon-o-document-text"
                    class="h-10 w-10 text-primary-500 dark:text-primary-400"
                />
            </x-slot>

            <div class="flex items-center gap-4">
                <x-filament::button
                    tag="a"
                    href="/docs/api"
                    target="_blank"
                    rel="noopener noreferrer"
                    icon="heroicon-m-arrow-top-right-on-square"
                    icon-position="after"
                >
                    Open Documentation
                </x-filament::button>
            </div>
        </x-filament::section>

        {{-- OpenAPI Spec Card --}}
        <x-filament::section>
            <x-slot name="heading">
                OpenAPI Specification
            </x-slot>

            <x-slot name="description">
                Download the OpenAPI 3.0 JSON schema for use in Postman, Insomnia, or other tools.
            </x-slot>

            <x-slot name="headerEnd">
                <x-filament::icon
                    icon="heroicon-o-code-bracket"
                    class="h-10 w-10 text-success-500 dark:text-success-400"
                />
            </x-slot>

            <div class="flex items-center gap-4">
                <x-filament::button
                    tag="a"
                    href="/docs/api.json"
                    target="_blank"
                    rel="noopener noreferrer"
                    color="success"
                    icon="heroicon-m-arrow-down-tray"
                    icon-position="after"
                >
                    Download JSON
                </x-filament::button>
            </div>
        </x-filament::section>
    </div>

    <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
        {{-- API Information --}}
        <div class="lg:col-span-1">
            <x-filament::section>
                <x-slot name="heading">
                    API Information
                </x-slot>
                
                <x-slot name="headerEnd">
                    <x-filament::icon
                        icon="heroicon-o-information-circle"
                        class="h-5 w-5 text-gray-400"
                    />
                </x-slot>

                <dl class="space-y-6">
                    <div>
                        <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Base URL</dt>
                        <dd class="mt-1 flex items-center gap-2">
                            <code class="rounded bg-gray-100 px-2 py-1 text-sm font-mono text-gray-800 dark:bg-gray-800 dark:text-gray-200">
                                {{ config('app.url') }}/api
                            </code>
                        </dd>
                    </div>

                    <div>
                        <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Version</dt>
                        <dd class="mt-1">
                            <x-filament::badge color="primary">
                                v{{ config('scramble.info.version', '1.0.0') }}
                            </x-filament::badge>
                        </dd>
                    </div>

                    <div>
                        <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Authentication</dt>
                        <dd class="mt-1">
                            <div class="flex items-center gap-2 text-sm text-gray-950 dark:text-white">
                                <x-filament::icon
                                    icon="heroicon-m-lock-closed"
                                    class="h-4 w-4 text-warning-500"
                                />
                                <span>Bearer Token (Sanctum)</span>
                            </div>
                        </dd>
                    </div>
                </dl>
            </x-filament::section>
        </div>

        {{-- Available Endpoints --}}
        <div class="lg:col-span-2">
            <x-filament::section>
                <x-slot name="heading">
                    Quick Reference
                </x-slot>
                <x-slot name="description">
                    Commonly used endpoints and their methods.
                </x-slot>

                <div class="grid gap-6 sm:grid-cols-2">
                    {{-- Auth Group --}}
                    <div class="space-y-3">
                        <h4 class="flex items-center gap-2 text-sm font-semibold text-gray-950 dark:text-white">
                            <div class="flex h-6 w-6 items-center justify-center rounded-md bg-primary-50 dark:bg-primary-900/50">
                                <x-filament::icon
                                    icon="heroicon-m-key"
                                    class="h-4 w-4 text-primary-600 dark:text-primary-400"
                                />
                            </div>
                            Authentication
                        </h4>
                        <ul class="space-y-2">
                            <li class="flex items-center justify-between rounded-lg border border-gray-100 bg-gray-50/50 px-3 py-2 dark:border-gray-800 dark:bg-gray-900/50">
                                <code class="text-xs text-gray-600 dark:text-gray-400">/api/login</code>
                                <x-filament::badge color="info" size="xs">POST</x-filament::badge>
                            </li>
                            <li class="flex items-center justify-between rounded-lg border border-gray-100 bg-gray-50/50 px-3 py-2 dark:border-gray-800 dark:bg-gray-900/50">
                                <code class="text-xs text-gray-600 dark:text-gray-400">/api/logout</code>
                                <x-filament::badge color="danger" size="xs">POST</x-filament::badge>
                            </li>
                            <li class="flex items-center justify-between rounded-lg border border-gray-100 bg-gray-50/50 px-3 py-2 dark:border-gray-800 dark:bg-gray-900/50">
                                <code class="text-xs text-gray-600 dark:text-gray-400">/api/user</code>
                                <x-filament::badge color="success" size="xs">GET</x-filament::badge>
                            </li>
                        </ul>
                    </div>

                    {{-- Properties Group --}}
                    <div class="space-y-3">
                        <h4 class="flex items-center gap-2 text-sm font-semibold text-gray-950 dark:text-white">
                            <div class="flex h-6 w-6 items-center justify-center rounded-md bg-primary-50 dark:bg-primary-900/50">
                                <x-filament::icon
                                    icon="heroicon-m-home"
                                    class="h-4 w-4 text-primary-600 dark:text-primary-400"
                                />
                            </div>
                            Properties
                        </h4>
                        <ul class="space-y-2">
                            <li class="flex items-center justify-between rounded-lg border border-gray-100 bg-gray-50/50 px-3 py-2 dark:border-gray-800 dark:bg-gray-900/50">
                                <code class="text-xs text-gray-600 dark:text-gray-400">/api/properties</code>
                                <x-filament::badge color="success" size="xs">GET</x-filament::badge>
                            </li>
                            <li class="flex items-center justify-between rounded-lg border border-gray-100 bg-gray-50/50 px-3 py-2 dark:border-gray-800 dark:bg-gray-900/50">
                                <code class="text-xs text-gray-600 dark:text-gray-400">/api/properties/featured</code>
                                <x-filament::badge color="success" size="xs">GET</x-filament::badge>
                            </li>
                            <li class="flex items-center justify-between rounded-lg border border-gray-100 bg-gray-50/50 px-3 py-2 dark:border-gray-800 dark:bg-gray-900/50">
                                <code class="text-xs text-gray-600 dark:text-gray-400">/api/properties/{slug}</code>
                                <x-filament::badge color="success" size="xs">GET</x-filament::badge>
                            </li>
                        </ul>
                    </div>
                </div>
            </x-filament::section>
        </div>
    </div>

    
</x-filament-panels::page>
