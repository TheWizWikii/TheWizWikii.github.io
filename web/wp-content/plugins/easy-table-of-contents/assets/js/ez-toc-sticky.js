function ezTOC_hideBar(e) {
    var sidebar = document.querySelector(".ez-toc-sticky-fixed");
    if (typeof(sidebar) !== "undefined" && sidebar !== null) {
        sidebar.classList.remove("show");
        sidebar.classList.add("hide");
        setTimeout(function() {
            document.querySelector(".ez-toc-open-icon").style = "z-index: 9999999";
        }, 200);
        if (e.target.classList.contains('ez-toc-close-icon') || e.target.parentElement.classList.contains('ez-toc-close-icon')) {
            e.preventDefault();
        }
    }
}

function ezTOC_showBar(e) {
    e.preventDefault();
    document.querySelector(".ez-toc-open-icon").style = "z-index: -1;";
    setTimeout(function() {
        var sidebar = document.querySelector(".ez-toc-sticky-fixed");
        sidebar.classList.remove("hide");
        sidebar.classList.add("show");
    }, 200);
}(function() {
    let ez_toc_sticky_fixed_container = document.querySelector('div.ez-toc-sticky-fixed');
    if (ez_toc_sticky_fixed_container) {
        document.body.addEventListener("click", function(evt) {
            ezTOC_hideBar(evt);
        });
        ez_toc_sticky_fixed_container.addEventListener('click', function(event) {
            event.stopPropagation();
        });
        document.querySelector('.ez-toc-open-icon').addEventListener('click', function(event) {
            event.stopPropagation();
        });
    }
})();

if(1 === parseInt(eztoc_sticky_local.close_on_link_click)){
    jQuery(document).ready(function() {
        jQuery("#ez-toc-sticky-container a.ez-toc-link").click(function(e) {
            ezTOC_hideBar(e);
        });
    });
}