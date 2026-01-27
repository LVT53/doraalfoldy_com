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

    let msnry = null;

    const startMasonry = () => {
        const isMobile = window.innerWidth < 768;

        if (!isMobile && !msnry) {
            msnry = new Masonry(grid, {
                itemSelector: itemSelector,
                columnWidth: sizerSelector || itemSelector,
                percentPosition: true,
                transitionDuration: '0.4s'
            });

            imagesLoaded(grid).on('progress', function () {
                msnry.layout();
            });
        } else if (isMobile && msnry) {
            msnry.destroy();
            msnry = null;
            grid.style.height = '';
            const items = grid.querySelectorAll(itemSelector);
            items.forEach(item => {
                item.style.position = '';
                item.style.top = '';
                item.style.left = '';
            });
        }
    };

    startMasonry();
    window.addEventListener('resize', startMasonry);
};

document.addEventListener('DOMContentLoaded', () => {
    window.initMasonry('#service-masonry', '.masonry-column-item');
    window.initMasonry('.masonry-grid', '.masonry-item', '.masonry-sizer');
});
