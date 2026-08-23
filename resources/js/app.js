import './bootstrap';

document.addEventListener('alpine:init', () => {
    Alpine.data('widgetSorter', () => ({
        draggingId: null,
        saving: false,

        start(event, id) {
            this.draggingId = id;
            event.dataTransfer.effectAllowed = 'move';
            event.dataTransfer.setData('text/plain', String(id));
        },

        move(event) {
            const list = event.currentTarget;
            const card = document.querySelector(`[data-widget-id="${this.draggingId}"]`);

            if (! card || ! list.matches('[data-widget-area]')) return;

            const siblings = [...list.querySelectorAll('[data-widget-id]:not(.is-dragging)')];
            const next = siblings.find((item) => event.clientY < item.getBoundingClientRect().top + item.offsetHeight / 2);
            list.insertBefore(card, next ?? null);
        },

        async drop() {
            const layout = {};
            document.querySelectorAll('[data-widget-area]').forEach((list) => {
                layout[list.dataset.widgetArea] = [...list.querySelectorAll('[data-widget-id]')]
                    .map((card) => Number(card.dataset.widgetId));
            });

            this.saving = true;
            await this.$wire.reorderWidgets(layout);
            this.saving = false;
            this.draggingId = null;
        },
    }));
});

// Livewire 3 ships and boots its own Alpine instance, so nothing else is
// required here. Register custom Alpine plugins/directives inside a
// `livewire:init` listener if you need them:
//
// document.addEventListener('livewire:init', () => {
//     Alpine.data('dropdown', () => ({ open: false }));
// });
