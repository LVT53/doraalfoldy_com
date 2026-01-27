import './bootstrap';
import Alpine from 'alpinejs';
import Masonry from 'masonry-layout';
import imagesLoaded from 'imagesloaded';

window.Alpine = Alpine;
Alpine.start();

// Masonry initialization
window.initMasonry = (selector, itemSelector, sizerSelector) => {
    const grid = document.querySelector(selector);
    if (!grid) return;

    const msnry = new Masonry(grid, {
        itemSelector: itemSelector,
        columnWidth: sizerSelector || itemSelector,
        percentPosition: true,
        transitionDuration: '0.4s'
    });

    imagesLoaded(grid).on('progress', function () {
        msnry.layout();
    });
};

document.addEventListener('DOMContentLoaded', () => {
    window.initMasonry('#service-masonry', '.masonry-column-item');
    window.initMasonry('.masonry-grid', '.masonry-item', '.masonry-sizer');
});
