/**
 * SkinLab – Docs: TOC active on scroll
 */

(function () {
    'use strict';

    var tocLinks = document.querySelectorAll('.toc-link');
    if (!tocLinks.length) return;

    var sections = [];
    tocLinks.forEach(function (link) {
        var href = link.getAttribute('href');
        if (href && href.startsWith('#')) {
            var section = document.getElementById(href.substring(1));
            if (section) {
                sections.push({ el: section, link: link });
            }
        }
    });

    if (!sections.length) return;

    var scrollContainer = document.querySelector('.admin-main');
    if (!scrollContainer) return;

    function onScroll() {
        var scrollTop = scrollContainer.scrollTop;
        var offset = 120;
        var current = null;

        for (var i = sections.length - 1; i >= 0; i--) {
            if (sections[i].el.offsetTop - offset <= scrollTop) {
                current = sections[i];
                break;
            }
        }

        if (!current) current = sections[0];

        tocLinks.forEach(function (link) { link.classList.remove('active'); });
        current.link.classList.add('active');
    }

    scrollContainer.addEventListener('scroll', onScroll);
    onScroll();
})();
