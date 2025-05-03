$(document).ready(function () {


    var link = $('.list-cv--menu .list-link');
    var header = $('#mainHeader');
    var sidebar= $('.sidebar-sticky');
    var bannerHeight = $('.banner.banner--main').outerHeight();
        var sidebarHeight = $('.sidebar-sticky').outerHeight();
    var headerHeight = header.outerHeight();



    // Smooth scrolling to section on menu link click
    link.on('click', function (e) {
        e.preventDefault();
        var target = $($(this).attr('href'));
        var scrollHeight = headerHeight;

        if ($(this).parents('li').is(':first-child')) {
            scrollHeight += bannerHeight + aboutUsHeight;
        }

        $('html, body').animate({
            scrollTop: target.offset().top - scrollHeight
        }, 600);

        $('.list-group-item').removeClass('active');
        $(this).parents('.list-group-item').addClass('active');
    });

    // Scroll event to update navigation
    $(window).on('scroll', function () {
        toggleStickyHeader();
        updateActiveNav();
    });

    function updateActiveNav() {
        var scrollTop = $(window).scrollTop();
        $('.article-item').each(function () {
            var id = $(this).attr('id');
            var offset = $(this).offset().top - headerHeight;
            var height = $(this).height();

            if (scrollTop >= offset && scrollTop < offset + height) {
                link.parents('li').removeClass('active');
                $('.list-cv--menu').find(`[data-scroll="${id}"]`).parents('li').addClass('active');
            }
        });
    }

    function toggleStickyHeader() {
        const scrollTop = $(window).scrollTop();
        
        if (scrollTop >= 100 && scrollTop <= (bannerHeight - 10)) {
            header.addClass('is-sticky');
            sidebar.removeClass('is-sticky'); // Ensure sidebar is not sticky when header is
        } else if (scrollTop >= bannerHeight) {
            header.removeClass('is-sticky');
            sidebar.addClass('is-sticky');
        } else {
            header.removeClass('is-sticky');
            sidebar.removeClass('is-sticky'); // Reset both when below threshold
        }
    }
    
});



