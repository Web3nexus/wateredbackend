<x-filament-widgets::widget>
    <x-filament::section>
        <x-slot name="heading">
            <div class="flex items-center gap-x-3">
                <div class="p-2 bg-primary-500/10 rounded-lg">
                    <x-filament::icon icon="heroicon-m-sparkles" class="h-5 w-5 text-primary-500" />
                </div>
                <div>
                    <h2 class="text-lg font-bold tracking-tight">Quick Publishing</h2>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Add new content to the Watered library
                        instantly.</p>
                </div>
            </div>
        </x-slot>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mt-4">
            @foreach($this->getActions() as $action)
                <a href="{{ $action['url'] }}"
                    class="group relative flex flex-col p-6 bg-white dark:bg-gray-900 border border-gray-200 dark:border-white/5 rounded-2xl transition-all duration-300 hover:ring-2 hover:ring-{{ $action['color'] }}-500/50 hover:border-{{ $action['color'] }}-500/50 shadow-sm">
                    <div class="flex items-start justify-between mb-4">
                        <div
                            class="p-3 bg-{{ $action['color'] }}-500/10 rounded-xl transition-colors duration-300 group-hover:bg-{{ $action['color'] }}-500/20">
                            <x-filament::icon :icon="$action['icon']" class="h-8 w-8 text-{{ $action['color'] }}-500" />
                        </div>
                        <x-filament::icon icon="heroicon-m-plus-circle"
                            class="h-6 w-6 text-gray-300 dark:text-gray-700 transition-colors duration-300 group-hover:text-{{ $action['color'] }}-400" />
                    </div>

                    <h3
                        class="font-bold text-gray-900 dark:text-white text-lg mb-1 leading-tight group-hover:text-{{ $action['color'] }}-500 transition-colors">
                        {{ $action['label'] }}
                    </h3>
                    <p class="text-sm text-gray-500 dark:text-gray-400 line-clamp-2 leading-relaxed">
                        {{ $action['description'] }}
                    </p>

                    {{-- Bottom accent line --}}
                    <div
                        class="absolute bottom-0 left-0 h-1 w-0 bg-{{ $action['color'] }}-500 rounded-b-2xl transition-all duration-500 group-hover:w-full">
                    </div>
                </a>
            @endforeach
        </div>
    </x-filament::section>

    {{-- Tailwind Color Safelist for the dynamic colors --}}
    <div
        class="hidden bg-primary-500 bg-success-500 bg-warning-500 bg-danger-500 ring-primary-500/50 ring-success-500/50 ring-warning-500/50 ring-danger-500/50 border-primary-500/50 border-success-500/50 border-warning-500/50 border-danger-500/50 text-primary-500 text-success-500 text-warning-500 text-danger-500 group-hover:text-primary-500 group-hover:text-success-500 group-hover:text-warning-500 group-hover:text-danger-500 group-hover:bg-primary-500/20 group-hover:bg-success-500/20 group-hover:bg-warning-500/20 group-hover:bg-danger-500/20 bg-primary-500/10 bg-success-500/10 bg-warning-500/10 bg-danger-500/10">
    </div>
</x-filament-widgets::widget>