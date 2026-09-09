<div>
    <div
        x-cloak
        x-show="$wire.show"
        x-transition.opacity
        class="fixed inset-0 z-[80] flex items-center justify-center bg-ink/50 p-4"
        @keydown.escape.window="$wire.show && $wire.close()"
        wire:click.self="close"
    >
        <div
            x-show="$wire.show"
            x-transition
            class="w-full max-w-md rounded-[1.75rem] bg-cream p-6 shadow-xl sm:p-8"
            role="dialog"
            aria-modal="true"
            aria-labelledby="booking-modal-title"
            @click.stop
        >
            <div class="flex items-start justify-between gap-4">
                <div>
                    <h2 id="booking-modal-title" class="font-display text-2xl font-bold text-olive">
                        Забронировать стол
                    </h2>
                    <p class="mt-1 text-sm text-muted">Оставьте контакты — мы перезвоним.</p>
                </div>
                <button
                    type="button"
                    wire:click="close"
                    class="inline-flex h-9 w-9 items-center justify-center rounded-full text-ink/60 transition hover:bg-cream-dark hover:text-ink"
                    aria-label="Закрыть"
                >
                    <x-site.icon name="heroicon-o-x-mark" class="h-5 w-5" />
                </button>
            </div>

            <form wire:submit="submit" class="mt-6 space-y-4">
                <div>
                    <label for="booking-name" class="mb-1.5 block text-sm font-medium text-ink">Имя</label>
                    <input
                        id="booking-name"
                        type="text"
                        wire:model="name"
                        class="w-full rounded-2xl border border-ink/10 bg-surface px-4 py-3 text-sm text-ink outline-none ring-plum/30 focus:ring-2"
                        autocomplete="name"
                        required
                    >
                    @error('name') <p class="mt-1 text-xs text-plum">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label for="booking-phone" class="mb-1.5 block text-sm font-medium text-ink">Телефон</label>
                    <input
                        id="booking-phone"
                        type="tel"
                        wire:model="phone"
                        class="w-full rounded-2xl border border-ink/10 bg-surface px-4 py-3 text-sm text-ink outline-none ring-plum/30 focus:ring-2"
                        autocomplete="tel"
                        required
                    >
                    @error('phone') <p class="mt-1 text-xs text-plum">{{ $message }}</p> @enderror
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label for="booking-date" class="mb-1.5 block text-sm font-medium text-ink">Дата</label>
                        <input
                            id="booking-date"
                            type="date"
                            wire:model="date"
                            class="w-full rounded-2xl border border-ink/10 bg-surface px-4 py-3 text-sm text-ink outline-none ring-plum/30 focus:ring-2"
                        >
                        @error('date') <p class="mt-1 text-xs text-plum">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label for="booking-guests" class="mb-1.5 block text-sm font-medium text-ink">Гостей</label>
                        <input
                            id="booking-guests"
                            type="number"
                            min="1"
                            max="50"
                            wire:model="guests"
                            class="w-full rounded-2xl border border-ink/10 bg-surface px-4 py-3 text-sm text-ink outline-none ring-plum/30 focus:ring-2"
                        >
                        @error('guests') <p class="mt-1 text-xs text-plum">{{ $message }}</p> @enderror
                    </div>
                </div>

                <div>
                    <label for="booking-comment" class="mb-1.5 block text-sm font-medium text-ink">Комментарий</label>
                    <textarea
                        id="booking-comment"
                        wire:model="comment"
                        rows="3"
                        class="w-full rounded-2xl border border-ink/10 bg-surface px-4 py-3 text-sm text-ink outline-none ring-plum/30 focus:ring-2"
                    ></textarea>
                    @error('comment') <p class="mt-1 text-xs text-plum">{{ $message }}</p> @enderror
                </div>

                <button
                    type="submit"
                    class="w-full rounded-full bg-plum px-6 py-3 text-sm font-semibold tracking-wide text-white transition hover:bg-plum-dark"
                    wire:loading.attr="disabled"
                >
                    <span wire:loading.remove wire:target="submit">Отправить</span>
                    <span wire:loading wire:target="submit">Отправка…</span>
                </button>
            </form>
        </div>
    </div>
</div>
