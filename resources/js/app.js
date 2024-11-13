import './bootstrap';

if (typeof Alpine === 'undefined') {
    console.log('Alpine not loaded')
    import('alpinejs').then((Alpine) => {
        window.Alpine = Alpine.default
        Alpine.default.start()
        console.log('Alpine loaded')
    })
}