/*
 * Welcome to your app's main JavaScript file!
 *
 * We recommend including the built version of this JavaScript file
 * (and its CSS file) in your base layout (base.html.twig).
 */

// any CSS you import will output into a single css file (app.css in this case)
import './styles/app.css';

// start the Stimulus application
import './bootstrap';

window.onload = function () {
    carousel();
};

// Carousel

function carousel() {
    const gap = 16;

    const carousel = document.getElementById("carousel"),
    content = document.getElementById("content"),
    next = document.getElementById("next"),
    prev = document.getElementById("prev");

    next.addEventListener("click", e => {
    carousel.scrollBy(width + gap, 0);
    if (carousel.scrollWidth !== 0) {
        prev.style.display = "flex";
    }
    if (content.scrollWidth - width - gap <= carousel.scrollLeft + width) {
        next.style.display = "none";
    }
    });
    prev.addEventListener("click", e => {
    carousel.scrollBy(-(width + gap), 0);
    if (carousel.scrollLeft - width - gap <= 0) {
        prev.style.display = "none";
    }
    if (!content.scrollWidth - width - gap <= carousel.scrollLeft + width) {
        next.style.display = "flex";
    }
    });

    let width = carousel.offsetWidth;
    window.addEventListener("resize", e => (width = carousel.offsetWidth));

}

$(document).ready(function() {
    // show the alert
    setTimeout(function() {
        $(".alert").alert('close');
    }, 5000);
});

