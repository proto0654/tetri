<x-filament-widgets::widget>
    <x-filament::section heading="Быстрые настройки">
        <form wire:submit="save" class="grid gap-6">
            {{ $this->form }}

            <div class="flex flex-wrap items-center gap-3">
                <x-filament::button type="submit">
                    Сохранить
                </x-filament::button>

                <x-filament::link :href="$allSettingsUrl" color="gray">
                    Все настройки сайта
                </x-filament::link>
            </div>
        </form>
    </x-filament::section>
</x-filament-widgets::widget>
