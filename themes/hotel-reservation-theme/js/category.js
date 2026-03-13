/*
* 2007-2017 PrestaShop
*
* NOTICE OF LICENSE
*
* This source file is subject to the Academic Free License (AFL 3.0)
* that is bundled with this package in the file LICENSE.txt.
* It is also available through the world-wide-web at this URL:
* http://opensource.org/licenses/afl-3.0.php
* If you did not receive a copy of the license and are unable to
* obtain it through the world-wide-web, please send an email
* to license@prestashop.com so we can send you a copy immediately.
*
* DISCLAIMER
*
* Do not edit or add to this file if you wish to upgrade PrestaShop to newer
* versions in the future. If you wish to customize PrestaShop for your
* needs please refer to http://www.prestashop.com for more information.
*
*  @author PrestaShop SA <contact@prestashop.com>
*  @copyright  2007-2017 PrestaShop SA
*  @license    http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/
$(document).ready(function () {
    $('.mobile-hotel-gallery .owl-carousel').owlCarousel({
        items: 1,
        loop: true,
        nav: false,
        navSpeed: 1000,
        dots: true,
        autoHeight: true,
        autoplay: true,
        autoplaySpeed: 1000,
        autoplayTimeout: 5000,
        autoplayHoverPause: true,
        responsiveClass: true,
        rtl: language_is_rtl,
        padding: 10,
        responsive: {
            0: { items: 1 },
            800: { items: 2 }
        },
        onInitialized: function (event) {
            var nav = $(event.target).find('.owl-nav');
            nav.find('.owl-prev').attr('tabindex', '-1');
            nav.find('.owl-next').attr('tabindex', '-1');
        },
        onChanged: function (event) {
            if (!event.item) return;
            var total = event.item.count;
            var index = event.item.index - event.relatedTarget._clones.length / 2;

            if (index < 0) index = total + index;
            if (index >= total) index = index - total;

            var current = index + 1;
            $('.gallery-counter .current').text(
                current.toString().padStart(2, '0')
            );
        }
    });
    $(document).on('click', '.js-hotel-image', function(e) {
        e.preventDefault();
        e.stopPropagation();

        var gallery = [];

        $('.js-hotel-image').each(function () {
            gallery.push({
                href: $(this).attr('href')
            });
        });

        var index = $('.js-hotel-image').index(this);

        $.fancybox.open(gallery, {
            index: index,
            loop: true,
            padding: 0,
            buttons: ['close'],
            arrows: false,
            keyboard: false,
            touch: {
                horizontal: false
            },
            helpers: {
                overlay: {
                    locked: false
                }
            }
        });
    });

    var fullGallery = {
        modal: null,
        images: [],
        filteredImages: [],
        currentIndex: 0,
        currentCategory: 'all',

        init: function() {
            this.modal = $('#full-gallery-modal');
            this.prepareImages();
            this.bindEvents();
        },

        prepareImages: function() {
            // Get images from the data passed from backend
            if (typeof hotelGalleryByCategory !== 'undefined') {
                // Parse JSON if it's a string, otherwise use as object
                var galleryData = hotelGalleryByCategory;
                if (typeof hotelGalleryByCategory === 'string') {
                    try {
                        galleryData = JSON.parse(hotelGalleryByCategory);
                    } catch (e) {
                        console.error('Error parsing hotelGalleryByCategory:', e);
                        return;
                    }
                }
                
                // Flatten all images for "All photos" view
                for (var categoryId in galleryData) {
                    if (galleryData.hasOwnProperty(categoryId)) {
                        var category = galleryData[categoryId];
                        // Check if category and images array exist
                        if (category && category.images && Array.isArray(category.images)) {
                            for (var i = 0; i < category.images.length; i++) {
                                this.images.push({
                                    id: category.images[i].id,
                                    src: category.images[i].large_link,
                                    thumb: category.images[i].small_link,
                                    categoryId: categoryId,
                                    categoryName: category.name
                                });
                            }
                        }
                    }
                }
            }
            this.filteredImages = this.images.slice();
        },

        bindEvents: function() {
            var self = this;

            // Open gallery on trigger click
            $(document).on('click', '.js-category-full-gallery-trigger', function(e) {
                e.preventDefault();
                self.open();
            });

            // Close gallery
            this.modal.on('click', '.qlo-full-gallery__close, .qlo-full-gallery__overlay', function() {
                self.close();
            });

            // Navigation
            this.modal.on('click', '.qlo-full-gallery__nav--prev', function() {
                self.prevImage();
            });

            this.modal.on('click', '.qlo-full-gallery__nav--next', function() {
                self.nextImage();
            });

            // Combined keyboard navigation (Escape, ArrowLeft, ArrowRight)
            $(document).on('keydown', function(e) {
                if (!self.modal.is(':visible')) return;
                if (e.key === 'Escape') {
                    self.close();
                } else if (e.key === 'ArrowLeft') {
                    self.prevImage();
                } else if (e.key === 'ArrowRight') {
                    self.nextImage();
                }
            });

            // Category tabs
            this.modal.on('click', '.qlo-gallery-tab', function() {
                var category = $(this).data('category');
                self.filterByCategory(category);
                $('.qlo-gallery-tab').removeClass('active');
                $(this).addClass('active');
            });

            // Thumbnail click
            this.modal.on('click', '.qlo-gallery-thumbnail', function() {
                var index = $(this).data('index');
                self.showImage(index);
            });
        },

        open: function() {
            this.modal.fadeIn(300, () => {
                this.initMobileCarousel();
            });
            $('body').addClass('gallery-open');
            this.renderThumbnails();
            if (this.filteredImages.length > 0) {
                this.showImage(0);
            }
        },

        initMobileCarousel: function() {

            var mobileCarousel = $('#gallery-mobile-carousel');

            if (!mobileCarousel.length) return;

            // Destroy if already initialized
            if (mobileCarousel.hasClass('owl-loaded')) {
                mobileCarousel.trigger('destroy.owl.carousel');
                mobileCarousel.removeClass('owl-loaded');
                mobileCarousel.find('.owl-stage-outer').children().unwrap();
            }

            mobileCarousel.empty();

            for (var i = 0; i < this.filteredImages.length; i++) {
                var img = this.filteredImages[i];

                mobileCarousel.append(
                    '<div class="item">' +
                        '<img src="' + img.src + '" alt="">' +
                    '</div>'
                );
            }

            // IMPORTANT: Delay init until modal is visible
            setTimeout(function() {
                mobileCarousel.owlCarousel({
                    items: 1,
                    loop: true,
                    nav: false,
                    dots: true,
                    autoHeight: true,
                    autoplay: true,
                    rtl: typeof language_is_rtl !== 'undefined' ? language_is_rtl : false
                });
            }, 100);
        },

        close: function() {
            this.modal.fadeOut(300);
            $('body').removeClass('gallery-open');
        },

        filterByCategory: function(categoryId) {
            this.currentCategory = categoryId;
            if (categoryId === 'all' || categoryId === 'all') {
                this.filteredImages = this.images.slice();
            } else {
                // Convert both to string for comparison to handle type mismatch
                var targetCategoryId = String(categoryId);
                this.filteredImages = this.images.filter(function(img) {
                    return String(img.categoryId) === targetCategoryId;
                });
            }
            this.renderThumbnails();
            this.initMobileCarousel();
            if (this.filteredImages.length > 0) {
                this.showImage(0);
            }
        },

        showImage: function(index) {
            if (index < 0 || index >= this.filteredImages.length) return;
            this.currentIndex = index;
            var image = this.filteredImages[index];
            
            $('#main-image-modal').attr('src', image.src);            
            // Update thumbnail active state
            $('.qlo-gallery-thumbnail').removeClass('active');
            $('.qlo-gallery-thumbnail[data-index="' + index + '"]').addClass('active');
            
            // Scroll thumbnail into view
            var thumbContainer = $('#gallery-thumbnail');
            var activeThumb = thumbContainer.find('.qlo-gallery-thumbnail.active');
            if (activeThumb.length) {
                thumbContainer.animate({
                    scrollLeft: activeThumb.position().left - thumbContainer.width() / 2 + activeThumb.width() / 2
                }, 200);
            }
        },

        prevImage: function() {
            var newIndex = this.currentIndex - 1;
            if (newIndex < 0) {
                newIndex = this.filteredImages.length - 1;
            }
            this.showImage(newIndex);
        },

        nextImage: function() {
            var newIndex = this.currentIndex + 1;
            if (newIndex >= this.filteredImages.length) {
                newIndex = 0;
            }
            this.showImage(newIndex);
        },

        renderThumbnails: function() {
            var container = $('#gallery-thumbnail');
            container.empty();
            
            for (var i = 0; i < this.filteredImages.length; i++) {
                var img = this.filteredImages[i];
                // Use jQuery DOM methods to prevent XSS
                var thumb = $('<div>', {
                    'class': 'qlo-gallery-thumbnail',
                    'data-index': i
                });
                var thumbImg = $('<img>', {
                    'src': img.thumb,
                    'alt': img.categoryName ? img.categoryName + ' - Photo ' + (i + 1) : 'Gallery photo ' + (i + 1)
                });
                thumb.append(thumbImg);
                container.append(thumb);
            }
        }
    };

    // Initialize full gallery if modal exists
    if ($('#full-gallery-modal').length) {
        fullGallery.init();
    }
});

function initMap() {
    const hotelLocation = {
        lat: Number(hotel_location.latitude),
        lng: Number(hotel_location.longitude),
    };

    const map = new google.maps.Map($('#search-results-wrap .map-wrap').get(0), {
        zoom: 10,
        center: hotelLocation,
        disableDefaultUI: true,
        fullscreenControl: true,
        mapId: PS_MAP_ID
    });

    let icon = document.createElement('img');
    icon.src = PS_STORES_ICON;
    icon.style.width = '24px';
    icon.style.height = '24px';

    const marker = new google.maps.marker.AdvancedMarkerElement({
        map: map,
        position: hotelLocation,
        title: hotel_name,
        content: icon,
    });

    marker.query = location.query || null;
    marker.latitude = hotelLocation.lat;
    marker.longitude = hotelLocation.lng;

    marker.addListener('click', function() {
        let query = '';
        if (this.query) {
            query = this.query;
        } else if (this.latitude && this.longitude) {
            query = `${this.latitude},${this.longitude}`;
        }

        if (query) {
            window.open(`https://www.google.com/maps/search/?api=1&query=${encodeURIComponent(query)}`, '_blank');
        }
    });
}
