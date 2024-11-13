import './bootstrap';

if (typeof Alpine === 'undefined') {
    import('alpinejs').then((Alpine) => {
        window.Alpine = Alpine.default
        Alpine.default.start()
    })
}