@props([
    'settings',
    'withMain' => true,
    'withBar' => true,
])

<footer class="bg-olive-deep text-cream">
    @if ($withMain)
        <x-site.shell class="py-14">
            <div class="grid gap-10 lg:grid-cols-3 lg:gap-x-10">
                <div class="hidden lg:block" aria-hidden="true"></div>
                <div class="lg:col-span-2">
                    <x-site.footer-main :settings="$settings" />
                </div>
            </div>
        </x-site.shell>

        @if ($withBar)
            <div class="border-t border-cream/15">
                <x-site.shell class="flex flex-col gap-2 py-4 text-xs text-cream/60 sm:flex-row sm:items-center sm:justify-between">
                    <span>@typo($settings['copyright'] ?? '© ТЕТРИ')</span>
                    <a href="#top" class="hover:text-cream">Наверх ↑</a>
                </x-site.shell>
            </div>
        @endif
    @endif
</footer>
