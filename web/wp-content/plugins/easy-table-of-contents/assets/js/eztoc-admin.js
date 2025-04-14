function ezTocTabToggle(evt, idname, tabContentClass = 'eztoc-tabcontent', tabLinksClass = 'eztoc-tablinks') {
    var i, tabcontent, tablinks;
    evt.preventDefault();
    tabcontent = document.getElementsByClassName(tabContentClass);
    for (i = 0; i < tabcontent.length; i++) {
        tabcontent[i].style.display = "none";
    }
    tablinks = document.getElementsByClassName(tabLinksClass);
    for (i = 0; i < tablinks.length; i++) {
        tablinks[i].className = tablinks[i].className.replace(" active", "");
    }
    document.getElementById(idname).style.display = "block";

    evt.target.className += " active";
}

function eztocIsEmail(email) {
    var regex = /^([a-zA-Z0-9_.+-])+\@(([a-zA-Z0-9-])+\.)+([a-zA-Z0-9]{2,4})+$/;
    return regex.test(email);
}


//query form send starts here
jQuery(document).ready(function ($) {

    var url = window.location.href;
    if (url.indexOf('#technical-support') > -1) {
        $("#eztoc-technical").click();
    } else if (url.indexOf('#freevspro-support') > -1) {
        $("#eztoc-freevspro").click();
    } else if (url.indexOf('#welcome') > -1) {
        $("#eztoc-welcome").click();
    } else {
        $("#eztoc-default").click();
    }

    $(".eztoc-send-query").on("click", function (e) {
        e.preventDefault();
        var message = $("#eztoc_query_message").val();
        var email = $("#eztoc_query_email").val();
        var premium_cus = $("#saswp_query_premium_cus").val();

        if ($.trim(message) != '' && $.trim(email) != '' && eztocIsEmail(email) == true) {
            $.ajax({
                type: "POST",
                url: ajaxurl,
                dataType: "json",
                data: {
                    action: "eztoc_send_query_message",
                    message: message,
                    email: email,
                    eztoc_security_nonce: eztoc_admin_data.eztoc_security_nonce
                },
                success: function (response) {
                    if (response['status'] == 't') {
                        $(".eztoc-query-success").show();
                        $(".eztoc-query-error").hide();
                    } else {
                        $(".eztoc-query-success").hide();
                        $(".eztoc-query-error").show();
                    }
                },
                error: function (response) {
                    console.log(response);
                }
            });
        } else {

            if ($.trim(message) == '' && $.trim(email) == '') {
                alert('Please enter the message, email and select customer type');
            } else {

                if ($.trim(message) == '') {
                    alert('Please enter the message');
                }
                if ($.trim(email) == '') {
                    alert('Please enter the email');
                }
                if (eztocIsEmail(email) == false) {
                    alert('Please enter a valid email');
                }

            }

        }

    });

    $("#subscribe-newsletter-form").on('submit', function (e) {
        e.preventDefault();
        var $form = $("#subscribe-newsletter-form");
        var name = $form.find('input[name="name"]').val();
        var email = $form.find('input[name="email"]').val();
        var website = $form.find('input[name="company"]').val();
        $.post(ajaxurl, {action: 'eztoc_subscribe_newsletter', name: name, email: email, website: website, eztoc_security_nonce: eztoc_admin_data.eztoc_security_nonce},
            function (data) {
            }
        );
    });

    let stickyToggleCheckbox = $('#eztoc-sticky').find("input[name='ez-toc-settings[sticky-toggle]']");
let stickyToggleWidth = $('#eztoc-sticky').find("select[name='ez-toc-settings[sticky-toggle-width]']");
let stickyToggleWidthCustom = $('#eztoc-sticky').find("input[name='ez-toc-settings[sticky-toggle-width-custom]']");
let stickyToggleHeight = $('#eztoc-sticky').find("select[name='ez-toc-settings[sticky-toggle-height]']");
let stickyToggleHeightCustom = $('#eztoc-sticky').find("input[name='ez-toc-settings[sticky-toggle-height-custom]']");

if($(stickyToggleCheckbox).prop('checked') == false) {
    $('#eztoc-sticky').find('tr:not(:first-child)').hide(500);
}

$(document).on("change", "input[name='ez-toc-settings[sticky-toggle]']", function() {
    
    if($(stickyToggleCheckbox).prop('checked') == true) {

        $('#eztoc-sticky').find('tr:not(:first-child)').show(500);

        if($(stickyToggleWidth).val() == '' || $(stickyToggleWidth).val() != 'custom'){
            $(stickyToggleWidthCustom).parents('tr').hide();
        }
        if($(stickyToggleHeight).val() == '' || $(stickyToggleHeight).val() != 'custom'){
            $(stickyToggleHeightCustom).parents('tr').hide();
        }
    } else {
        $('#eztoc-sticky').find('tr:not(:first-child)').hide(500);
    }
    
});
update_sticky_width_field(stickyToggleWidth.val());
update_sticky_height_field(stickyToggleHeight.val());

$(document).on("change", "select[name='ez-toc-settings[sticky-toggle-width]']", function() {
    update_sticky_width_field($(this).val());
});

$(document).on("change", "select[name='ez-toc-settings[sticky-toggle-height]']", function() {
   update_sticky_height_field($(this).val());
});

});

function update_sticky_width_field(width){
    let stickyToggleWidthCustom = jQuery('#eztoc-sticky').find("input[name='ez-toc-settings[sticky-toggle-width-custom]']");
    if(width == 'custom') {
        jQuery(stickyToggleWidthCustom).parents('tr').show(500);
    } else {
        jQuery(stickyToggleWidthCustom).parents('tr').hide(500);
    }
}

function update_sticky_height_field(height){
    let stickyToggleHeightCustom = jQuery('#eztoc-sticky').find("input[name='ez-toc-settings[sticky-toggle-height-custom]']");
    if(height == 'custom') {
        jQuery(stickyToggleHeightCustom).parents('tr').show(500);
    } else {
        jQuery(stickyToggleHeightCustom).parents('tr').hide(500);
    }
}
