@props(['settings'])

<footer data-entrance data-state="pending" {{ $attributes->class(['flex flex-1 flex-col bg-olive-deep text-cream']) }}>
    <div class="py-14 lg:pl-10">
        <x-site.footer-main :settings="$settings" />
    </div>

    <x-site.footer-bar :settings="$settings" class="mt-auto lg:pl-10" data-entrance-cta />
</footer>
