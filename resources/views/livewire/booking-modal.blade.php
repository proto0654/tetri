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

            <form wire:submit="submit" novalidate class="mt-6 space-y-4">
                @error('form')
                    <p class="rounded-2xl bg-plum/10 px-4 py-3 text-sm text-plum" role="alert">{{ $message }}</p>
                @enderror

                <div>
                    <label for="booking-name" class="mb-1.5 block text-sm font-medium text-ink">
                        Имя <span class="text-plum" aria-hidden="true">*</span>
                    </label>
                    <input
                        id="booking-name"
                        type="text"
                        wire:model="name"
                        class="w-full rounded-2xl border border-ink/10 bg-surface px-4 py-3 text-sm text-ink outline-none ring-plum/30 focus:ring-2 @error('name') border-plum @enderror"
                        autocomplete="name"
                        aria-required="true"
                        @error('name') aria-invalid="true" aria-describedby="booking-name-error" @enderror
                    >
                    @error('name')
                        <p id="booking-name-error" class="mt-1 text-xs text-plum">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="booking-phone" class="mb-1.5 block text-sm font-medium text-ink">
                        Телефон <span class="text-plum" aria-hidden="true">*</span>
                    </label>
                    <input
                        id="booking-phone"
                        type="tel"
                        wire:model="phone"
                        class="w-full rounded-2xl border border-ink/10 bg-surface px-4 py-3 text-sm text-ink outline-none ring-plum/30 focus:ring-2 @error('phone') border-plum @enderror"
                        autocomplete="tel"
                        inputmode="tel"
                        placeholder="+7 (___) ___-__-__"
                        aria-required="true"
                        @error('phone') aria-invalid="true" aria-describedby="booking-phone-error" @enderror
                    >
                    @error('phone')
                        <p id="booking-phone-error" class="mt-1 text-xs text-plum">{{ $message }}</p>
                    @enderror
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label for="booking-date" class="mb-1.5 block text-sm font-medium text-ink">
                            Дата <span class="text-plum" aria-hidden="true">*</span>
                        </label>
                        <input
                            id="booking-date"
                            type="date"
                            wire:model="date"
                            min="{{ now()->toDateString() }}"
                            class="w-full rounded-2xl border border-ink/10 bg-surface px-4 py-3 text-sm text-ink outline-none ring-plum/30 focus:ring-2 @error('date') border-plum @enderror"
                            aria-required="true"
                            @error('date') aria-invalid="true" aria-describedby="booking-date-error" @enderror
                        >
                        @error('date')
                            <p id="booking-date-error" class="mt-1 text-xs text-plum">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label for="booking-guests" class="mb-1.5 block text-sm font-medium text-ink">
                            Гостей <span class="text-plum" aria-hidden="true">*</span>
                        </label>
                        <input
                            id="booking-guests"
                            type="number"
                            min="1"
                            max="50"
                            wire:model="guests"
                            class="w-full rounded-2xl border border-ink/10 bg-surface px-4 py-3 text-sm text-ink outline-none ring-plum/30 focus:ring-2 @error('guests') border-plum @enderror"
                            aria-required="true"
                            @error('guests') aria-invalid="true" aria-describedby="booking-guests-error" @enderror
                        >
                        @error('guests')
                            <p id="booking-guests-error" class="mt-1 text-xs text-plum">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div>
                    <label for="booking-comment" class="mb-1.5 block text-sm font-medium text-ink">Комментарий</label>
                    <textarea
                        id="booking-comment"
                        wire:model="comment"
                        rows="3"
                        class="w-full rounded-2xl border border-ink/10 bg-surface px-4 py-3 text-sm text-ink outline-none ring-plum/30 focus:ring-2 @error('comment') border-plum @enderror"
                        placeholder="Пожелания к столу, детский праздник…"
                        @error('comment') aria-invalid="true" aria-describedby="booking-comment-error" @enderror
                    ></textarea>
                    @error('comment')
                        <p id="booking-comment-error" class="mt-1 text-xs text-plum">{{ $message }}</p>
                    @enderror
                </div>

                <p class="text-xs text-muted">
                    <span class="text-plum" aria-hidden="true">*</span> — обязательные поля
                </p>

                <button
                    type="submit"
                    class="w-full rounded-full bg-plum px-6 py-3 text-sm font-semibold tracking-wide text-white transition hover:bg-plum-dark disabled:opacity-60"
                    wire:loading.attr="disabled"
                >
                    <span wire:loading.remove wire:target="submit">Отправить</span>
                    <span wire:loading wire:target="submit">Отправка…</span>
                </button>
            </form>
        </div>
    </div>
</div>
