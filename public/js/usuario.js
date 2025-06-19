// Carrusel de Avisos
$(".announcement-carousel").owlCarousel({
    autoplay: true,
    smartSpeed: 800,
    dots: true,
    loop: true,
    margin: 30,
    nav: true,
    navText: [
        '<i class="fas fa-chevron-left"></i>',
        '<i class="fas fa-chevron-right"></i>'
    ],
    responsiveClass: true,
    responsive: {
        0: {
            items: 1
        },
        576: {
            items: 1
        },
        768: {
            items: 2
        },
        992: {
            items: 3
        },
        1200: {
            items: 3
        }
    }
});