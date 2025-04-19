document.addEventListener("DOMContentLoaded", function() {
    const header = document.querySelector('header');
    const backToTop = document.querySelector('.back-to-top');

    let lastScrollTop = 0;
    window.addEventListener('scroll', function() {
        let scrollTop = window.pageYOffset || document.documentElement.scrollTop;
        if (scrollTop > lastScrollTop) {
            header.classList.add('hidden');
        } else {
            header.classList.remove('hidden');
        }
        lastScrollTop = scrollTop <= 0 ? 0 : scrollTop;

        if (scrollTop > 200 && backToTop) {
            backToTop.classList.add('visible');
        } else if (backToTop) {
            backToTop.classList.remove('visible');
        }
    });
});