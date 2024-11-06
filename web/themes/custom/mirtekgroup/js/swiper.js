/**
 * @file
 * Initializes Swiper slider functionality.
 */

(function ($, Drupal, drupalSettings) {
    'use strict';

    Drupal.behaviors.swiperCarouselCompany = {
        attach: function (context, settings) {
            if (typeof Swiper !== 'undefined') {
                context.querySelectorAll('.swiper.swiper-row').forEach(function (el) {
                    const swiper = new Swiper(el, {
                        // Конфигурация Swiper
                        slidesPerView: 5,
                        breakpoints: {
                            320: { slidesPerView: 2, spaceBetween: 20 },
                            480: { slidesPerView: 3, spaceBetween: 30 },
                            640: { slidesPerView: 5, spaceBetween: 50 }
                        },
                        loop: false,
                    });
                });
            }

        }
    };

    Drupal.behaviors.swiperRowTrustUs = {
        attach: function (context, settings) {
            if (typeof Swiper !== 'undefined') {
                context.querySelectorAll('.swiper.trust-us-row').forEach(function (el) {
                    const swiper = new Swiper(el, {
                        slidesPerView: 7,
                        loop: true,
                        speed: 5000,
                        autoplay: {
                            delay: 0,
                            disableOnInteraction: false,
                        },
                        freeMode: true,
                    });
                });
            }
        }
    };

    Drupal.behaviors.swiperBehavior = {
        attach: function (context, settings) {
            if (typeof Swiper !== 'undefined') {
                context.querySelectorAll('.swiper.swiper-product').forEach(function (el) {
                    const swiper = new Swiper(el, {
                        // Конфигурация Swiper
                        slidesPerView: 4,
                        slidesPerGroup: 4,
                        spaceBetween: 40,
                        breakpoints: {
                            320: { slidesPerView: 2, spaceBetween: 20 },
                            480: { slidesPerView: 3, spaceBetween: 30 },
                            640: { slidesPerView: 4, spaceBetween: 40 }
                        },
                        loop: false,
                        pagination: { el: '.swiper-pagination', type: "fraction" },
                        navigation: { nextEl: '.swiper-button-next', prevEl: '.swiper-button-prev' },
                    });
                });
            }
        }
    };

    Drupal.behaviors.swiperCarousel = {
        attach: function (context, settings) {
            if (typeof Swiper !== 'undefined') {
                context.querySelectorAll('.swiper.swiper-carousel').forEach(function (el) {
                    const swiperNews = new Swiper(el, {
                        // Конфигурация Swiper
                        slidesPerView: 1,
                        pagination: { el: '.swiper-pagination', clickable: true, dynamicBullets: true, dynamicMainBullets: 3,},
                        navigation: { nextEl: '.swiper-button-next', prevEl: '.swiper-button-prev' },
                        mousewheel: true,
                        keyboard: true,
                        updateOnWindowResize: true,
                    });
                });
            }
        }
    };

    Drupal.behaviors.gallerycolorbox = {
        attach: function (context, settings) {
            $(window).on('load', function() {
                $('.swiper .swiper-wrapper .swiper-slide a').each(function() {
                    // Проверяем, не находится ли ссылка внутри .swiper.trust-us-row
                    if (!$(this).closest('.swiper.trust-us-row').length) {
                        $(this).colorbox({
                            rel: 'gal',
                        });
                    }
                });
            });
        }
    };

})(jQuery, Drupal, drupalSettings);





