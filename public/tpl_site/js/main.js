(function ($) {
    "use strict";
    /*=================================
      JS Index Here
  ==================================*/
    /*
    01. On Load Function
    02. Preloader
    03. Mobile Menu Active
    04. Sticky fix
    05. Scroll To Top
    06. Set Background & Mask Image
    07. Global Slider
    08. Custom Animaiton For Slider
    09. Ajax Contact Form
    10. Popup Sidemenu  
    11. Search Box Popup
    12. Counter Up
    13. Progress Bar Animation
    14. Image to SVG Code
    15. Custom Cursor
    16. WOW Js (Scroll Animation)
    17. Magnific Popup
    00. Inspect Element Disable
  */
    /*=================================
      JS Index End
  ==================================*/
    /*

  /*---------- 01. On Load Function ----------*/
    $(window).on("load", function () {
        $(".preloader").fadeOut();
    });

    /*---------- 02. Preloader ----------*/
    if ($(".preloader").length > 0) {
        $(".preloaderCls").each(function () {
            $(this).on("click", function (e) {
                e.preventDefault();
                $(".preloader").css("display", "none");
            });
        });
    }

    /*---------- 03. Mobile Menu Active ----------*/
    $.fn.thmobilemenu = function (options) {
        var opt = $.extend(
            {
                menuToggleBtn: ".th-menu-toggle",
                bodyToggleClass: "th-body-visible",
                subMenuClass: "th-submenu",
                subMenuParent: "th-item-has-children",
                subMenuParentToggle: "th-active",
                meanExpandClass: "th-mean-expand",
                appendElement: '<span class="th-mean-expand"></span>',
                subMenuToggleClass: "th-open",
                toggleSpeed: 400,
            },
            options
        );

        return this.each(function () {
            var menu = $(this); // Select menu

            // Menu Show & Hide
            function menuToggle() {
                menu.toggleClass(opt.bodyToggleClass);

                // collapse submenu on menu hide or show
                var subMenu = "." + opt.subMenuClass;
                $(subMenu).each(function () {
                    if ($(this).hasClass(opt.subMenuToggleClass)) {
                        $(this).removeClass(opt.subMenuToggleClass);
                        $(this).css("display", "none");
                        $(this).parent().removeClass(opt.subMenuParentToggle);
                    }
                });
            }

            // Class Set Up for every submenu
            menu.find("li").each(function () {
                var submenu = $(this).find("ul");
                submenu.addClass(opt.subMenuClass);
                submenu.css("display", "none");
                submenu.parent().addClass(opt.subMenuParent);
                submenu.prev("a").append(opt.appendElement);
                submenu.next("a").append(opt.appendElement);
            });

            // Fecha o menu mobile ao clicar em qualquer link dentro do menu mobile
            menu.find('.th-mobile-menu a').on('click', function() {
                menu.removeClass(opt.bodyToggleClass);
            });

            // Toggle Submenu
            function toggleDropDown($element) {
                if ($($element).next("ul").length > 0) {
                    $($element).parent().toggleClass(opt.subMenuParentToggle);
                    $($element).next("ul").slideToggle(opt.toggleSpeed);
                    $($element).next("ul").toggleClass(opt.subMenuToggleClass);
                } else if ($($element).prev("ul").length > 0) {
                    $($element).parent().toggleClass(opt.subMenuParentToggle);
                    $($element).prev("ul").slideToggle(opt.toggleSpeed);
                    $($element).prev("ul").toggleClass(opt.subMenuToggleClass);
                }
            }

            // Submenu toggle Button
            var expandToggler = "." + opt.meanExpandClass;
            $(expandToggler).each(function () {
                $(this).on("click", function (e) {
                    e.preventDefault();
                    toggleDropDown($(this).parent());
                });
            });

            // Menu Show & Hide On Toggle Btn click
            $(opt.menuToggleBtn).each(function () {
                $(this).on("click", function () {
                    menuToggle();
                });
            });

            // Hide Menu On out side click
            menu.on("click", function (e) {
                e.stopPropagation();
                menuToggle();
            });

            // Stop Hide full menu on menu click
            menu.find("div").on("click", function (e) {
                e.stopPropagation();
            });
        });
    };

    $(".th-menu-wrapper").thmobilemenu();

    /*---------- 04. Sticky fix ----------*/
   $(window).scroll(function () {
        var topPos = $(this).scrollTop();
        if (topPos > 500) {
            $('.sticky-wrapper').addClass('sticky');
        } else {
            $('.sticky-wrapper').removeClass('sticky')
        }
    })

    /*---------- 05. Scroll To Top ----------*/
    // progressAvtivation
    if($('.scroll-top').length > 0) {
        var scrollTopbtn = $('.scroll-top');
        var progressPath = $('.scroll-top path');
        var pathLength = progressPath.get(0).getTotalLength();
        progressPath.css({
          'transition': 'none',
          '-webkit-transition': 'none',
          'stroke-dasharray': pathLength + ' ' + pathLength,
          'stroke-dashoffset': pathLength
        });
        progressPath.get(0).getBoundingClientRect();
        progressPath.css({
          'transition': 'stroke-dashoffset 10ms linear',
          '-webkit-transition': 'stroke-dashoffset 10ms linear'
        });
    
        var updateProgress = function () {
            var scroll = $(window).scrollTop();
            var height = $(document).height() - $(window).height();
            var progress = pathLength - (scroll * pathLength / height);
            progressPath.css('stroke-dashoffset', progress);
        }
    
        updateProgress();
        $(window).on('scroll', updateProgress);	
        var offset = 50;
        var duration = 750;
        $(window).on('scroll', function() {
            if ($(this).scrollTop() > offset) {
                $(scrollTopbtn).addClass('show');
            } else {
                $(scrollTopbtn).removeClass('show');
            }
        });				
        $(scrollTopbtn).on('click', function(event) {
            event.preventDefault();
            $('html, body').animate({scrollTop: 0}, duration);
            return false;
        });
    }

    /*---------- 06. Set Background & Mask Image ----------*/
    if ($("[data-bg-src]").length > 0) {
        $("[data-bg-src]").each(function () {
            var src = $(this).attr("data-bg-src");
            $(this).css("background-image", "url(" + src + ")");
            $(this).removeAttr("data-bg-src").addClass("background-image");
        });
    }
    // Mask Image
    if ($("[data-mask-src]").length > 0) {
        $("[data-mask-src]").each(function () {
            var mask = $(this).attr("data-mask-src");
            $(this).css({
                "mask-image": "url(" + mask + ")",
                "-webkit-mask-image": "url(" + mask + ")",
            });
            $(this).removeAttr("data-mask-src");
        });
    }

    /*----------- 07. Global Slider ----------*/
    $(".th-carousel").each(function () {
        var thSlide = $(this);

        // Collect Data
        function d(data) {
            return thSlide.data(data);
        }

        // Custom Arrow Button
        var prevButton =
                '<button type="button" class="slick-prev"><i class="' +
                d("prev-arrow") +
                '"></i></button>',
            nextButton =
                '<button type="button" class="slick-next"><i class="' +
                d("next-arrow") +
                '"></i></button>';

        // Function For Custom Arrow Btn
        $("[data-slick-next]").each(function () {
            $(this).on("click", function (e) {
                e.preventDefault();
                $($(this).data("slick-next")).slick("slickNext");
            });
        });

        $("[data-slick-prev]").each(function () {
            $(this).on("click", function (e) {
                e.preventDefault();
                $($(this).data("slick-prev")).slick("slickPrev");
            });
        });

        // Check for arrow wrapper
        if (d("arrows") == true) {
            if (!thSlide.closest(".arrow-wrap").length) {
                thSlide.closest(".container").parent().addClass("arrow-wrap");
            }
        }

        thSlide.slick({
            dots: d("dots") ? true : false,
            fade: d("fade") ? true : false,
            arrows: d("arrows") ? true : false,
            speed: d("speed") ? d("speed") : 1000,
            asNavFor: d("asnavfor") ? d("asnavfor") : false,
            autoplay: d("autoplay") == false ? false : true,
            infinite: d("infinite") == false ? false : true,
            slidesToShow: d("slide-show") ? d("slide-show") : 1,
            adaptiveHeight: d("adaptive-height") ? true : false,
            centerMode: d("center-mode") ? true : false,
            autoplaySpeed: d("autoplay-speed") ? d("autoplay-speed") : 8000,
            centerPadding: d("center-padding") ? d("center-padding") : "0",
            focusOnSelect: d("focuson-select") == false ? false : true,
            pauseOnFocus: d("pauseon-focus") ? true : false,
            pauseOnHover: d("pauseon-hover") ? true : false,
            variableWidth: d("variable-width") ? true : false,
            vertical: d("vertical") ? true : false,
            verticalSwiping: d("vertical") ? true : false,
            prevArrow: d("prev-arrow")
                ? prevButton
                : '<button type="button" class="slick-prev"><i class="fal fa-long-arrow-left"></i></button>',
            nextArrow: d("next-arrow")
                ? nextButton
                : '<button type="button" class="slick-next"><i class="fal fa-long-arrow-right"></i></button>',
            rtl: $("html").attr("dir") == "rtl" ? true : false,
            responsive: [
                {
                    breakpoint: 1600,
                    settings: {
                        arrows: d("xl-arrows") ? true : false,
                        dots: d("xl-dots") ? true : false,
                        slidesToShow: d("xl-slide-show")
                            ? d("xl-slide-show")
                            : d("slide-show"),
                        centerMode: d("xl-center-mode") ? true : false,
                        centerPadding: 0,
                    },
                },
                {
                    breakpoint: 1400,
                    settings: {
                        arrows: d("ml-arrows") ? true : false,
                        dots: d("ml-dots") ? true : false,
                        slidesToShow: d("ml-slide-show")
                            ? d("ml-slide-show")
                            : d("slide-show"),
                        centerMode: d("ml-center-mode") ? true : false,
                        centerPadding: 0,
                    },
                },
                {
                    breakpoint: 1200,
                    settings: {
                        arrows: d("lg-arrows") ? true : false,
                        dots: d("lg-dots") ? true : false,
                        slidesToShow: d("lg-slide-show")
                            ? d("lg-slide-show")
                            : d("slide-show"),
                        centerMode: d("lg-center-mode")
                            ? d("lg-center-mode")
                            : false,
                        centerPadding: 0,
                    },
                },
                {
                    breakpoint: 992,
                    settings: {
                        arrows: d("md-arrows") ? true : false,
                        dots: d("md-dots") ? true : false,
                        slidesToShow: d("md-slide-show")
                            ? d("md-slide-show")
                            : 1,
                        centerMode: d("md-center-mode")
                            ? d("md-center-mode")
                            : false,
                        centerPadding: 0,
                    },
                },
                {
                    breakpoint: 768,
                    settings: {
                        arrows: d("sm-arrows") ? true : false,
                        dots: d("sm-dots") ? true : false,
                        slidesToShow: d("sm-slide-show")
                            ? d("sm-slide-show")
                            : 1,
                        centerMode: d("sm-center-mode")
                            ? d("sm-center-mode")
                            : false,
                        centerPadding: 0,
                    },
                },
                {
                    breakpoint: 576,
                    settings: {
                        arrows: d("xs-arrows") ? true : false,
                        dots: d("xs-dots") ? true : false,
                        slidesToShow: d("xs-slide-show")
                            ? d("xs-slide-show")
                            : 1,
                        centerMode: d("xs-center-mode")
                            ? d("xs-center-mode")
                            : false,
                        centerPadding: 0,
                    },
                },
                // You can unslick at a given breakpoint now by adding:
                // settings: "unslick"
                // instead of a settings object
            ],
        });
    });

    // Sincronizar lista fixa de serviços com o carrossel horizontal
    if ($('#sr-grid').length && $('.service-list-fixed').length) {
        $('#sr-grid').on('afterChange', function(event, slick, currentSlide){
            var $list = $('.service-list-fixed .service-list');
            $list.removeClass('active');
            $list.eq(currentSlide).addClass('active');
        });
        // Clique na lista fixa navega no carrossel
        $('.service-list-fixed .service-list').on('click', function(){
            var idx = $(this).data('index');
            $('#sr-grid').slick('slickGoTo', idx);
        });
    }

    /*----------- 08. Custom Animaiton For Slider ----------*/
    $("[data-ani-duration]").each(function () {
        var durationTime = $(this).data("ani-duration");
        $(this).css("animation-duration", durationTime);
    });

    $("[data-ani-delay]").each(function () {
        var delayTime = $(this).data("ani-delay");
        $(this).css("animation-delay", delayTime);
    });

    $("[data-ani]").each(function () {
        var animaionName = $(this).data("ani");
        $(this).addClass(animaionName);
        $(".slick-current [data-ani]").addClass("th-animated");
    });

    $(".slick-slider").on(
        "afterChange",
        function (event, slick, currentSlide, nextSlide) {
            $(slick.$slides).find("[data-ani]").removeClass("th-animated");
            $(slick.$slides[currentSlide])
                .find("[data-ani]")
                .addClass("th-animated");
        }
    );

    // Manual Slider Activation
    $("#heroSlide2").each(function () {
        $(this).slick({
            slidesToShow: 1,
            speed: 1000,
            autoplaySpeed: 6000,
            arrows: false,
            fade: true,
            dots: true,
            appendDots: $(this)
                .siblings(".slider-nav-wrap")
                .find(".custom-dots"),
        });
    });

    $("#projectSlide1").each(function () {
        $(this).slick({
            slidesToShow: 2,
            speed: 1000,
            autoplaySpeed: 6000,
            variableWidth: true,
            arrows: false,
            dots: true,
            appendDots: $(this)
                .siblings(".slider-nav-wrap")
                .find(".custom-dots"),
        });
    });

    $("#projectSlide3").each(function () {
        $(this).slick({
            slidesToShow: 2,
            speed: 1000,
            autoplaySpeed: 6000,
            arrows: false,
            dots: true,
            appendDots: $(this)
                .siblings(".slider-nav-wrap")
                .find(".custom-dots"),
            responsive: [
                {
                    breakpoint: 767,
                    settings: {
                        slidesToShow: 1,
                    },
                }
            ]
        });
    });

    /*----------- 09. Ajax Contact Form ----------*/
    var form = ".ajax-contact";
    var invalidCls = "is-invalid";
    var $email = '[name="email"]';
    var $validation =
        '[name="name"],[name="email"],[name="number"],[name="subject"],[name="message"]'; // Must be use (,) without any space
    var formMessages = $(".form-messages");

    function sendContact() {
        var formData = $(form).serialize();
        var valid;
        valid = validateContact();
        if (valid) {
            jQuery
                .ajax({
                    url: $(form).attr("action"),
                    data: formData,
                    type: "POST",
                })
                .done(function (response) {
                    // Make sure that the formMessages div has the 'success' class.
                    formMessages.removeClass("error");
                    formMessages.addClass("success");
                    // Set the message text.
                    formMessages.text(response);
                    // Clear the form.
                    $(
                        form +
                            ' input:not([type="submit"]),' +
                            form +
                            " textarea"
                    ).val("");
                })
                .fail(function (data) {
                    // Make sure that the formMessages div has the 'error' class.
                    formMessages.removeClass("success");
                    formMessages.addClass("error");
                    // Set the message text.
                    if (data.responseText !== "") {
                        formMessages.html(data.responseText);
                    } else {
                        formMessages.html(
                            "Oops! An error occured and your message could not be sent."
                        );
                    }
                });
        }
    }

    function validateContact() {
        var valid = true;
        var formInput;

        function unvalid($validation) {
            $validation = $validation.split(",");
            for (var i = 0; i < $validation.length; i++) {
                formInput = form + " " + $validation[i];
                if (!$(formInput).val()) {
                    $(formInput).addClass(invalidCls);
                    valid = false;
                } else {
                    $(formInput).removeClass(invalidCls);
                    valid = true;
                }
            }
        }
        unvalid($validation);

        if (
            !$($email).val() ||
            !$($email)
                .val()
                .match(/^([\w-\.]+@([\w-]+\.)+[\w-]{2,4})?$/)
        ) {
            $($email).addClass(invalidCls);
            valid = false;
        } else {
            $($email).removeClass(invalidCls);
            valid = true;
        }
        return valid;
    }

    $(form).on("submit", function (element) {
        element.preventDefault();
        sendContact();
    });

    /*---------- 10. Popup Sidemenu ----------*/
    function popupSideMenu($sideMenu, $sideMunuOpen, $sideMenuCls, $toggleCls) {
        // Sidebar Popup
        $($sideMunuOpen).on("click", function (e) {
            e.preventDefault();
            $($sideMenu).addClass($toggleCls);
        });
        $($sideMenu).on("click", function (e) {
            e.stopPropagation();
            $($sideMenu).removeClass($toggleCls);
        });
        var sideMenuChild = $sideMenu + " > div";
        $(sideMenuChild).on("click", function (e) {
            e.stopPropagation();
            $($sideMenu).addClass($toggleCls);
        });
        $($sideMenuCls).on("click", function (e) {
            e.preventDefault();
            e.stopPropagation();
            $($sideMenu).removeClass($toggleCls);
        });
    }
    popupSideMenu(
        ".sidemenu-wrapper",
        ".sideMenuToggler",
        ".sideMenuCls",
        "show"
    );

    /*---------- 11. Search Box Popup ----------*/
    function popupSarchBox($searchBox, $searchOpen, $searchCls, $toggleCls) {
        $($searchOpen).on("click", function (e) {
            e.preventDefault();
            $($searchBox).addClass($toggleCls);
        });
        $($searchBox).on("click", function (e) {
            e.stopPropagation();
            $($searchBox).removeClass($toggleCls);
        });
        $($searchBox)
            .find("form")
            .on("click", function (e) {
                e.stopPropagation();
                $($searchBox).addClass($toggleCls);
            });
        $($searchCls).on("click", function (e) {
            e.preventDefault();
            e.stopPropagation();
            $($searchBox).removeClass($toggleCls);
        });
    }
    popupSarchBox(
        ".popup-search-box",
        ".searchBoxToggler",
        ".searchClose",
        "show"
    );

    /*----------- 12. Counter Up ----------*/
    $(".counter-number").counterUp({
        delay: 10,
        time: 1000,
    });

    /*----------- 13. Progress Bar Animation ----------*/
    $(".progress-bar").waypoint(
        function () {
            $(".progress-bar").css({
                animation: "animate-positive 1.8s",
                opacity: "1",
            });
        },
        { offset: "75%" }
    );

    /*---------- 14. Image to SVG Code ----------*/
    const cache = {};

    $.fn.inlineSvg = function fnInlineSvg() {
        this.each(imgToSvg);

        return this;
    };

    function imgToSvg() {
        const $img = $(this);
        const src = $img.attr("src");

        // fill cache by src with promise
        if (!cache[src]) {
            const d = $.Deferred();
            $.get(src, (data) => {
                d.resolve($(data).find("svg"));
            });
            cache[src] = d.promise();
        }

        // replace img with svg when cached promise resolves
        cache[src].then((svg) => {
            const $svg = $(svg).clone();

            if ($img.attr("id")) $svg.attr("id", $img.attr("id"));
            if ($img.attr("class")) $svg.attr("class", $img.attr("class"));
            if ($img.attr("style")) $svg.attr("style", $img.attr("style"));

            if ($img.attr("width")) {
                $svg.attr("width", $img.attr("width"));
                if (!$img.attr("height")) $svg.removeAttr("height");
            }
            if ($img.attr("height")) {
                $svg.attr("height", $img.attr("height"));
                if (!$img.attr("width")) $svg.removeAttr("width");
            }

            $svg.insertAfter($img);
            $img.trigger("svgInlined", $svg[0]);
            $img.remove();
        });
    }

    $(".svg-img").inlineSvg();

    /*---------- 15. Custom Cursor ----------*/
    const updateProperties = (elem, state) => {
        elem.style.setProperty("--x", `${state.x}px`);
        elem.style.setProperty("--y", `${state.y}px`);
        elem.style.setProperty("--width", `${state.width}px`);
        elem.style.setProperty("--height", `${state.height}px`);
        elem.style.setProperty("--radius", state.radius);
        elem.style.setProperty("--scale", state.scale);
    };

    document.querySelectorAll(".th-cursor").forEach((cursor) => {
        let onElement;

        const createState = (e) => {
            const defaultState = {
                x: e.clientX,
                y: e.clientY,
                width: 40,
                height: 40,
                radius: "50%",
            };

            const computedState = {};

            if (onElement != null) {
                const { top, left, width, height } =
                    onElement.getBoundingClientRect();
                const radius =
                    window.getComputedStyle(onElement).borderTopLeftRadius;

                computedState.x = left + width / 2;
                computedState.y = top + height / 2;
                computedState.width = width;
                computedState.height = height;
                computedState.radius = radius;
            }

            return {
                ...defaultState,
                ...computedState,
            };
        };

        document.addEventListener("mousemove", (e) => {
            const state = createState(e);
            updateProperties(cursor, state);
        });

        document
            .querySelectorAll(".cursor-btn")
            .forEach((elem) => {
                elem.addEventListener("mouseenter", () => (onElement = elem));
                elem.addEventListener(
                    "mouseleave",
                    () => (onElement = undefined)
                );
            });
    });

    /*----------- 16. WOW Js (Scroll Animation) ----------*/
    new WOW().init();

    /*----------- 17. Magnific Popup ----------*/
    /* magnificPopup img view */
    $(".popup-image").magnificPopup({
        type: "image",
        gallery: {
            enabled: true,
        },
    });

    /* magnificPopup video view */
    $(".popup-video").magnificPopup({
        type: "iframe",
    });

    /*----------- 18. Shape Mockup ----------*/
    $.fn.shapeMockup = function () {
        var $shape = $(this);
        $shape.each(function () {
            var $currentShape = $(this),
                shapeTop = $currentShape.data("top"),
                shapeRight = $currentShape.data("right"),
                shapeBottom = $currentShape.data("bottom"),
                shapeLeft = $currentShape.data("left");
            $currentShape
                .css({
                    top: shapeTop,
                    right: shapeRight,
                    bottom: shapeBottom,
                    left: shapeLeft,
                })
                .removeAttr("data-top")
                .removeAttr("data-right")
                .removeAttr("data-bottom")
                .removeAttr("data-left")
                .parent()
                .addClass("shape-mockup-wrap");
        });
    };

    if ($(".shape-mockup")) {
        $(".shape-mockup").shapeMockup();
    }

    // /*----------- 00. Right Click Disable ----------*/
    //   window.addEventListener('contextmenu', function (e) {
    //     // do something here...
    //     e.preventDefault();
    //   }, false);

    // /*----------- 00. Inspect Element Disable ----------*/
    //   document.onkeydown = function (e) {
    //     if (event.keyCode == 123) {
    //       return false;
    //     }
    //     if (e.ctrlKey && e.shiftKey && e.keyCode == 'I'.charCodeAt(0)) {
    //       return false;
    //     }
    //     if (e.ctrlKey && e.shiftKey && e.keyCode == 'C'.charCodeAt(0)) {
    //       return false;
    //     }
    //     if (e.ctrlKey && e.shiftKey && e.keyCode == 'J'.charCodeAt(0)) {
    //       return false;
    //     }
    //     if (e.ctrlKey && e.keyCode == 'U'.charCodeAt(0)) {
    //       return false;
    //     }
    //   }    
})(jQuery);
