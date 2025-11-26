@php
    use Filament\Support\Icons\Heroicon;
@endphp

<x-filament-widgets::widget>
    <x-filament::section>
        @if($googleAnalyticsId)
            <div class="space-y-6">
                {{-- Header --}}
                <div>
                    <x-filament::section.heading>
                        Google Analytics Overview
                    </x-filament::section.heading>
                    <x-filament::section.description>
                        View detailed analytics and visitor behavior in your 
                        <x-filament::link 
                            href="https://analytics.google.com" 
                            target="_blank"
                            :icon="Heroicon::OutlinedArrowTopRightOnSquare"
                            icon-position="after"
                        >
                            Google Analytics Dashboard
                        </x-filament::link>
                    </x-filament::section.description>
                </div>

                {{-- Stats Grid --}}
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    {{-- Analytics Property ID --}}
                    <div class="fi-section rounded-lg bg-white shadow-sm ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10">
                        <div class="p-6">
                            <div class="flex items-center justify-between">
                                <div class="flex-1">
                                    <div class="text-sm font-medium text-gray-500 dark:text-gray-400">
                                        Analytics Property ID
                                    </div>
                                    <div class="mt-2 text-lg font-semibold text-gray-950 dark:text-white">
                                        {{ $googleAnalyticsId }}
                                    </div>
                                </div>
                                <div class="ml-4">
                                    <x-filament::icon 
                                        :icon="Heroicon::OutlinedChartBar" 
                                        class="h-10 w-10 text-primary-500"
                                    />
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Tracking Status --}}
                    <div class="fi-section rounded-lg bg-white shadow-sm ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10">
                        <div class="p-6">
                            <div class="flex items-center justify-between">
                                <div class="flex-1">
                                    <div class="text-sm font-medium text-gray-500 dark:text-gray-400">
                                        Tracking Status
                                    </div>
                                    <div class="mt-2">
                                        <x-filament::badge color="success" size="lg">
                                            Active
                                        </x-filament::badge>
                                    </div>
                                </div>
                                <div class="ml-4">
                                    <x-filament::icon 
                                        :icon="Heroicon::CheckCircle" 
                                        class="h-10 w-10 text-success-500"
                                    />
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Quick Links --}}
                <div>
                    <h4 class="text-sm font-semibold text-gray-950 dark:text-white mb-3">Quick Links</h4>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
                        <x-filament::button
                            href="https://analytics.google.com/analytics/web/#/report/visitors-overview/{{ $googleAnalyticsId }}"
                            tag="a"
                            target="_blank"
                            color="gray"
                            :icon="Heroicon::OutlinedArrowTopRightOnSquare"
                            icon-position="after"
                            outlined
                        >
                            Audience Overview
                        </x-filament::button>

                        <x-filament::button
                            href="https://analytics.google.com/analytics/web/#/report/content-pages/{{ $googleAnalyticsId }}"
                            tag="a"
                            target="_blank"
                            color="gray"
                            :icon="Heroicon::OutlinedArrowTopRightOnSquare"
                            icon-position="after"
                            outlined
                        >
                            Page Views
                        </x-filament::button>

                        <x-filament::button
                            href="https://analytics.google.com/analytics/web/#/report/trafficsources-overview/{{ $googleAnalyticsId }}"
                            tag="a"
                            target="_blank"
                            color="gray"
                            :icon="Heroicon::OutlinedArrowTopRightOnSquare"
                            icon-position="after"
                            outlined
                        >
                            Traffic Sources
                        </x-filament::button>
                    </div>
                </div>

                {{-- Info Banner --}}
                <x-filament::section
                    :icon="Heroicon::InformationCircle"
                    icon-color="warning"
                >
                    <x-slot name="heading">
                        Note
                    </x-slot>
                    
                    <x-slot name="description">
                        For embedded analytics charts, you'll need to set up Google Analytics Reporting API and create custom visualizations. The links above provide direct access to your Google Analytics dashboard.
                    </x-slot>
                </x-filament::section>
            </div>
        @else
            {{-- Empty State --}}
            <div class="text-center py-12">
                <x-filament::icon 
                    :icon="Heroicon::OutlinedChartBar" 
                    class="mx-auto h-12 w-12 text-gray-400 dark:text-gray-500"
                />
                
                <h3 class="mt-4 text-lg font-semibold text-gray-950 dark:text-white">
                    Google Analytics Not Configured
                </h3>
                
                <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">
                    Add your Google Analytics ID to the .env file to enable tracking.
                </p>
                
                <div class="mt-6">
                    <x-filament::badge size="lg" color="gray">
                        GOOGLE_ANALYTICS_ID=G-XXXXXXXXXX
                    </x-filament::badge>
                </div>
            </div>
        @endif
    </x-filament::section>
</x-filament-widgets::widget>
