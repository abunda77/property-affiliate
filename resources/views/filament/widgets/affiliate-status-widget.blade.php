<x-filament-widgets::widget>
    <x-filament::section>
        <div class="flex flex-col items-center justify-center p-6 text-center">
            <h2 class="text-lg font-medium text-gray-950 dark:text-white mb-2">
                Affiliate Status
            </h2>
            
            @php
                $status = auth()->user()->status;
                $color = match($status) {
                    \App\Enums\UserStatus::ACTIVE => 'success',
                    \App\Enums\UserStatus::PENDING => 'warning',
                    \App\Enums\UserStatus::BLOCKED => 'danger',
                    default => 'gray',
                };
                $label = ucfirst($status->value);
            @endphp

            <x-filament::badge :color="$color" size="xl" class="text-xl px-4 py-2">
                {{ $label }}
            </x-filament::badge>

            <p class="mt-4 text-sm text-gray-500 dark:text-gray-400">
                Current status of your affiliate account.
            </p>
        </div>
    </x-filament::section>
</x-filament-widgets::widget>
