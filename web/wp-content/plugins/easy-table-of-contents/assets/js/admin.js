jQuery(document).ready(function ($) {

    var ez_toc_color_picker = $('.ez-toc-color-picker');

    if (ez_toc_color_picker.length) {
        ez_toc_color_picker.wpColorPicker();
    }

    var ezTocSettingsWidth = document.getElementById('ez-toc-settings[width]');
    var ezTocSettingsCustomWidth = document.getElementById('ez-toc-settings[width_custom]');

    if(ezTocSettingsCustomWidth) {
        if(ezTocSettingsWidth.value != 'custom')
            ezTocSettingsCustomWidth.parentNode.parentNode.style.display = "none";

        ezTocSettingsWidth.addEventListener('change', function () {
            if (document.getElementById('ez-toc-settings[width]').value == 'custom') {
                ezTocSettingsCustomWidth.parentNode.parentNode.style.display = "revert";
            } else {
                ezTocSettingsCustomWidth.parentNode.parentNode.style.display = "none";
            }
        });
    }

    $("#reset-options-to-default-button").click(function() {
        let text = "Do you want reset settings to default options?";
        if (confirm(text) == true) {
            $.post(ajaxurl, { action: 'eztoc_reset_options_to_default', eztoc_security_nonce: cn_toc_admin_data.eztoc_security_nonce },
                    function (data) {
                        alert('Default Options Reset Now!');
                        window.location.reload();
                    }
            );
        }

    });

    $("#subscribe-newsletter-form").on('submit', function (e) {
        e.preventDefault();
        var $form = $("#subscribe-newsletter-form");
        var name = $form.find('input[name="name"]').val();
        var email = $form.find('input[name="email"]').val();
        var website = $form.find('input[name="company"]').val();
        $.post(ajaxurl, {action: 'eztoc_subscribe_newsletter', name: name, email: email, website: website, eztoc_security_nonce: cn_toc_admin_data.eztoc_security_nonce},
                function (data) {
                    if(data === 'security_nonce_not_verified' ){
                        alert('Security nonce not verified');
                        return false;
                    } 
                    
                }
        );
    });

    let position = $('#eztoc-general').find("select[name='ez-toc-settings[position]']");
    let customParaNumber = $('#eztoc-general').find("input[name='ez-toc-settings[custom_para_number]']");
    let customImgNumber = $('#eztoc-general').find("input[name='ez-toc-settings[custom_img_number]']");
    let blockQCheckB = $('#eztoc-general').find("input[name='ez-toc-settings[blockqoute_checkbox]']");
    if($(position).val() == 'aftercustompara'){
        $(customParaNumber).parents('tr').show();
    }else{
        $(customParaNumber).parents('tr').hide();
    }
    if($(position).val() == 'afterpara' || $(position).val() == 'aftercustompara'){
        $(blockQCheckB).parents('tr').show();
    }else{
        $(blockQCheckB).parents('tr').hide();
    }
    if($(position).val() == 'aftercustomimg'){
        $(customImgNumber).parents('tr').show();
    }else{
        $(customImgNumber).parents('tr').hide();
    }
    $(document).on("change", "select[name='ez-toc-settings[position]']", function() {
        if($(this).val() == 'aftercustompara'){
            $(customParaNumber).parents('tr').show(500);    
        }else{
            $(customParaNumber).parents('tr').hide(500);
        }    
        if($(this).val() == 'afterpara' || $(this).val() == 'aftercustompara'){
            $(blockQCheckB).parents('tr').show(500);
        }else{
            $(blockQCheckB).parents('tr').hide(500);
        }
        if($(this).val() == 'aftercustomimg'){
            $(customImgNumber).parents('tr').show(500);    
        }else{
            $(customImgNumber).parents('tr').hide(500);
        }
    });
    let check_method = $('#eztoc-general').find("select[name='ez-toc-settings[toc_loading]']");
    let smoothCheck = $('#eztoc-general').find("input[name='ez-toc-settings[smooth_scroll]']");
    let anchsJump = $('#eztoc-general').find("input[name='ez-toc-settings[avoid_anch_jump]']");
    let js_where = $('#eztoc-advanced').find("select[name='ez-toc-settings[load_js_in]']");
    if($(check_method).val() == 'js'){
        $(smoothCheck).parents('tr').show();
        $(anchsJump).parents('tr').show();
        $(js_where).parents('tr').show();
    }else{
        $(smoothCheck).parents('tr').hide();
        $(anchsJump).parents('tr').hide();
        $(js_where).parents('tr').hide();
    }
    $(document).on("change", "select[name='ez-toc-settings[toc_loading]']", function() {
        if($(this).val() == 'js'){
            $(smoothCheck).parents('tr').show(500);    
            $(anchsJump).parents('tr').show(500);    
            $(js_where).parents('tr').show(500);    
        }else{
            $(smoothCheck).parents('tr').hide(500);
            $(anchsJump).parents('tr').hide(500);
            $(js_where).parents('tr').hide(500);
        }
    });

    let stickyHighlight = $('#eztoc-sticky').find("input[name='ez-toc-settings[sticky_highlight_heading]']");
    let stickyHighlightBg = $('#eztoc-sticky').find("input[name='ez-toc-settings[sticky_highlight_bg_colour]']");
    let stickyHighlightTitle = $('#eztoc-sticky').find("input[name='ez-toc-settings[sticky_highlight_title_colour]']");
    if($(stickyHighlight).prop('checked') == true){
        $(stickyHighlightBg).parents('tr').show();
        $(stickyHighlightTitle).parents('tr').show();
    }else{
        $(stickyHighlightBg).parents('tr').hide();
        $(stickyHighlightTitle).parents('tr').hide();
    }
    $(document).on("change", "input[name='ez-toc-settings[sticky_highlight_heading]']", function() {
        if($(this).prop('checked') == true){
            $(stickyHighlightBg).parents('tr').show(500);    
            $(stickyHighlightTitle).parents('tr').show(500);    
        }else{
            $(stickyHighlightBg).parents('tr').hide(500);
            $(stickyHighlightTitle).parents('tr').hide(500);
        }
    });

    let s_position = jQuery('#ez-toc').find("select[name='ez-toc-settings[position-specific]']");
    let s_customParaNumber = jQuery('#ez-toc').find("input[name='ez-toc-settings[s_custom_para_number]']");
    let s_customImgNumber = jQuery('#ez-toc').find("input[name='ez-toc-settings[s_custom_img_number]']");
    let s_blockQCheckB = jQuery('#ez-toc').find("input[name='ez-toc-settings[s_blockqoute_checkbox]']");

    if(jQuery(s_position).val() == 'aftercustompara'){
        jQuery(s_customParaNumber).parents('tr').show();
    }else{
        jQuery(s_customParaNumber).parents('tr').hide();
    }
    if(jQuery(s_position).val() == 'afterpara' || jQuery(s_position).val() == 'aftercustompara'){
        jQuery(s_blockQCheckB).parents('tr').show();
    }else{
        jQuery(s_blockQCheckB).parents('tr').hide();
    }
    if(jQuery(s_position).val() == 'aftercustomimg'){
        jQuery(s_customImgNumber).parents('tr').show();
    }else{
        jQuery(s_customImgNumber).parents('tr').hide();
    }
    jQuery('#ez-toc').on("change", "select[name='ez-toc-settings[position-specific]']", function() {
        if(jQuery(this).val() == 'aftercustompara'){
            jQuery(s_customParaNumber).parents('tr').show(500);    
        }else{
            jQuery(s_customParaNumber).parents('tr').hide(500);
        }    
        if(jQuery(this).val() == 'afterpara' || jQuery(this).val() == 'aftercustompara'){
            jQuery(s_blockQCheckB).parents('tr').show(500);
        }else{
            jQuery(s_blockQCheckB).parents('tr').hide(500);
        }
        if(jQuery(this).val() == 'aftercustomimg'){
            jQuery(s_customImgNumber).parents('tr').show(500);    
        }else{
            jQuery(s_customImgNumber).parents('tr').hide(500);
        }
    });

    
    

    

});

/**
 * DisableScrolling Function
 * @since 2.0.33
 */
function disableScrolling() {
    var x=window.scrollX;
    var y=window.scrollY;
    window.onscroll=function(){window.scrollTo(x, y);};
}
/**
 * EnableScrolling Function
 * @since 2.0.33
 */
function enableScrolling(){
    ezTocSettingsTabsFixed();
}

/**
 * unsecuredCopyToClipboard Function
 * Clipboard JS
 * @since 2.0.33
 */
const unsecuredCopyToClipboard = (text) => {
    const textArea = document.createElement("textarea");
    textArea.value = text;
    document.body.appendChild(textArea);
    textArea.focus();
    textArea.select();
    try {
        document.execCommand('copy')
    } catch (err) {
        console.error('Unable to copy to clipboard', err)
    }
    document.body.removeChild(textArea)
};
/**
 * ez_toc_clipboard Function
 * Clipboard JS
 * @since 2.0.33
 */
function ez_toc_clipboard(id, tooltipId, $this, event) {
    event.preventDefault();
    disableScrolling();
    var copyText = $this.parentNode.parentNode.querySelectorAll("#" + id)[0];
    copyText.select();
    copyText.setSelectionRange(0, 99999);
    // unsecuredCopyToClipboard(copyText.value);
    navigator.clipboard.writeText(copyText.value);

    var tooltip = $this.querySelectorAll('span.' + tooltipId)[0];
    tooltip.innerHTML = "Copied: " + copyText.value;
}
/**
 * ez_toc_outFunc Function
 * Clipboard JS
 * @since 2.0.33
 */
function ez_toc_outFunc(tooltipId, $this, event) {
    event.preventDefault();
    var tooltip = $this.querySelectorAll('span.' + tooltipId)[0];
    tooltip.innerHTML = "Copy to clipboard";
    enableScrolling();
}

/**
 * ezTocSettingsTabsFixed Function
 * Apply Fixed CSS & JS for General Settings Tabs
 * @since 2.0.38
 */
function ezTocSettingsTabsFixed() {
    var ezTocProSettingsContainer = '<span class="general-pro-settings-container"> | <a href="#eztoc-prosettings" id="eztoc-link-prosettings">Pro Settings</a></span>';

    var ezTocGeneralTabs = document.querySelector("#general #eztoc-tabs");
    var ezTocGeneralForm = document.querySelector("#general form");

    if(ezTocGeneralTabs !== null) {
        window.onscroll = function () {
            var y = window.scrollY;

            var ez_toc_pro_settings_link_paid = document.getElementsByClassName('ez-toc-pro-settings-link-paid');
            var ezTocElementProSettingsContainer = document.getElementsByClassName("general-pro-settings-container");

            var ezTocGeneralTabsLinkGeneral = document.querySelector("#general #eztoc-tabs #eztoc-link-general");
            var ezTocGeneralTabsLinkAppearance = document.querySelector("#general #eztoc-tabs #eztoc-link-appearance");
            var ezTocGeneralTabsLinkAdvanced = document.querySelector("#general #eztoc-tabs #eztoc-link-advanced");
            var ezTocGeneralTabsLinkShortcode = document.querySelector("#general #eztoc-tabs #eztoc-link-shortcode");
            var ezTocGeneralTabsLinkSticky = document.querySelector("#general #eztoc-tabs #eztoc-link-sticky");
            var ezTocGeneralTabsLinkCompatibility = document.querySelector("#general #eztoc-tabs #eztoc-link-compatibility");
            var ezTocGeneralTabsLinkIeSettings = document.querySelector("#general #eztoc-tabs #eztoc-link-iesettings");
            var ezTocGeneralTabsLinkProSettings = document.querySelector("#general #eztoc-tabs #eztoc-link-prosettings");

            var minusOffsetTop = 100;

            var ezTocGeneralContainerGeneral = document.querySelector("#general div#eztoc-general").offsetTop - minusOffsetTop;
            var ezTocGeneralContainerAppearance = document.querySelector("#general div#eztoc-appearance").offsetTop - minusOffsetTop;
            var ezTocGeneralContainerAdvanced = document.querySelector("#general div#eztoc-advanced").offsetTop - minusOffsetTop;
            var ezTocGeneralContainerShortcode = document.querySelector("#general div#eztoc-shortcode").offsetTop - minusOffsetTop;
            var ezTocGeneralContainerCompatibility = document.querySelector("#general div#eztoc-compatibility").offsetTop - minusOffsetTop;
            var ezTocGeneralContainerIeSettings = document.querySelector("#general div#eztoc-iesettings").offsetTop - minusOffsetTop;
            var ezTocGeneralContainerSticky = document.querySelector("#eztoc-sticky").offsetTop - minusOffsetTop;
           
            if(ez_toc_pro_settings_link_paid !== null && ez_toc_pro_settings_link_paid.length > 0) {
                var ezTocGeneralContainerProSettings = document.querySelector("#general div#eztoc-prosettings").offsetTop - minusOffsetTop - 150;
            } else {
                ezTocGeneralContainerCompatibility -= 150;
                ezTocGeneralContainerIeSettings -= 150;
            }
            ezTocGeneralTabsLinkGeneral.classList.add('active');
            ezTocGeneralTabsLinkAppearance.classList.remove('active');
            ezTocGeneralTabsLinkAdvanced.classList.remove('active');
            ezTocGeneralTabsLinkShortcode.classList.remove('active');
            ezTocGeneralTabsLinkCompatibility.classList.remove('active');
            ezTocGeneralTabsLinkIeSettings.classList.remove('active');
            ezTocGeneralTabsLinkSticky.classList.remove('active');
            if (ezTocElementProSettingsContainer.length > 0 && ezTocGeneralTabsLinkProSettings !== null)
                ezTocGeneralTabsLinkProSettings.classList.remove('active');

            if (y >= 100) {
                ezTocGeneralTabs.classList.remove('stay');
                ezTocGeneralTabs.classList.add('moving');
                ezTocGeneralForm.classList.add('moving');
                if(ez_toc_pro_settings_link_paid !== null && ez_toc_pro_settings_link_paid.length > 0 && ezTocElementProSettingsContainer.length == 0)
                    ezTocGeneralTabs.innerHTML += ezTocProSettingsContainer;
            } else {
                ezTocGeneralTabs.classList.remove('moving');
                ezTocGeneralTabs.classList.add('stay');
                ezTocGeneralForm.classList.remove('moving');
                if(ez_toc_pro_settings_link_paid !== null && ez_toc_pro_settings_link_paid.length > 0 && ezTocElementProSettingsContainer.length > 0)
                    document.querySelector(".general-pro-settings-container").remove();
            }

            if (y >= ezTocGeneralContainerGeneral) {
                ezTocGeneralTabsLinkGeneral.classList.add('active');
                ezTocGeneralTabsLinkAppearance.classList.remove('active');
                ezTocGeneralTabsLinkAdvanced.classList.remove('active');
                ezTocGeneralTabsLinkShortcode.classList.remove('active');
                ezTocGeneralTabsLinkCompatibility.classList.remove('active');
                ezTocGeneralTabsLinkIeSettings.classList.remove('active');
                ezTocGeneralTabsLinkSticky.classList.remove('active');
                if(ez_toc_pro_settings_link_paid !== null && ez_toc_pro_settings_link_paid.length > 0 && ezTocElementProSettingsContainer.length > 0 && ezTocGeneralTabsLinkProSettings !== null)
                    ezTocGeneralTabsLinkProSettings.classList.remove('active');
            }
            if (y >= ezTocGeneralContainerAppearance) {
                ezTocGeneralTabsLinkGeneral.classList.remove('active');
                ezTocGeneralTabsLinkAppearance.classList.add('active');
                ezTocGeneralTabsLinkAdvanced.classList.remove('active');
                ezTocGeneralTabsLinkShortcode.classList.remove('active');
                ezTocGeneralTabsLinkCompatibility.classList.remove('active');
                ezTocGeneralTabsLinkIeSettings.classList.remove('active');
                ezTocGeneralTabsLinkSticky.classList.remove('active');
               if(ez_toc_pro_settings_link_paid !== null && ez_toc_pro_settings_link_paid.length > 0 && ezTocElementProSettingsContainer.length > 0 && ezTocGeneralTabsLinkProSettings !== null)
                    ezTocGeneralTabsLinkProSettings.classList.remove('active');
            }
            if (y >= ezTocGeneralContainerAdvanced) {
                ezTocGeneralTabsLinkGeneral.classList.remove('active');
                ezTocGeneralTabsLinkAppearance.classList.remove('active');
                ezTocGeneralTabsLinkAdvanced.classList.add('active');
                ezTocGeneralTabsLinkShortcode.classList.remove('active');
                ezTocGeneralTabsLinkCompatibility.classList.remove('active');
                ezTocGeneralTabsLinkIeSettings.classList.remove('active');
                ezTocGeneralTabsLinkSticky.classList.remove('active');
                if(ez_toc_pro_settings_link_paid !== null && ez_toc_pro_settings_link_paid.length > 0 && ezTocElementProSettingsContainer.length > 0 && ezTocGeneralTabsLinkProSettings !== null)
                    ezTocGeneralTabsLinkProSettings.classList.remove('active');
            }
            if (y >= ezTocGeneralContainerShortcode) {
                ezTocGeneralTabsLinkGeneral.classList.remove('active');
                ezTocGeneralTabsLinkAppearance.classList.remove('active');
                ezTocGeneralTabsLinkAdvanced.classList.remove('active');
                ezTocGeneralTabsLinkShortcode.classList.add('active');
                ezTocGeneralTabsLinkCompatibility.classList.remove('active');
                ezTocGeneralTabsLinkIeSettings.classList.remove('active');
                ezTocGeneralTabsLinkSticky.classList.remove('active');
                if(ez_toc_pro_settings_link_paid !== null && ez_toc_pro_settings_link_paid.length > 0 && ezTocElementProSettingsContainer.length > 0 && ezTocGeneralTabsLinkProSettings !== null)
                    ezTocGeneralTabsLinkProSettings.classList.remove('active');
            }
            if (y >= ezTocGeneralContainerSticky) {
                ezTocGeneralTabsLinkGeneral.classList.remove('active');
                ezTocGeneralTabsLinkAppearance.classList.remove('active');
                ezTocGeneralTabsLinkAdvanced.classList.remove('active');
                ezTocGeneralTabsLinkShortcode.classList.remove('active');
                ezTocGeneralTabsLinkCompatibility.classList.remove('active');
                ezTocGeneralTabsLinkIeSettings.classList.remove('active');
                ezTocGeneralTabsLinkSticky.classList.add('active');
                
                if(ez_toc_pro_settings_link_paid !== null && ez_toc_pro_settings_link_paid.length > 0 && ezTocElementProSettingsContainer.length > 0 && ezTocGeneralTabsLinkProSettings !== null)
                    ezTocGeneralTabsLinkProSettings.classList.remove('active');
            }
            if (y >= ezTocGeneralContainerCompatibility) {
                ezTocGeneralTabsLinkGeneral.classList.remove('active');
                ezTocGeneralTabsLinkAppearance.classList.remove('active');
                ezTocGeneralTabsLinkAdvanced.classList.remove('active');
                ezTocGeneralTabsLinkShortcode.classList.remove('active');
                ezTocGeneralTabsLinkCompatibility.classList.add('active');
                ezTocGeneralTabsLinkIeSettings.classList.remove('active');
                ezTocGeneralTabsLinkSticky.classList.remove('active');
                if(ez_toc_pro_settings_link_paid !== null && ez_toc_pro_settings_link_paid.length > 0 && ezTocElementProSettingsContainer.length > 0 && ezTocGeneralTabsLinkProSettings !== null)
                    ezTocGeneralTabsLinkProSettings.classList.remove('active');
            }
            if (y >= ezTocGeneralContainerIeSettings) {
                ezTocGeneralTabsLinkGeneral.classList.remove('active');
                ezTocGeneralTabsLinkAppearance.classList.remove('active');
                ezTocGeneralTabsLinkAdvanced.classList.remove('active');
                ezTocGeneralTabsLinkShortcode.classList.remove('active');
                ezTocGeneralTabsLinkCompatibility.classList.remove('active');
                ezTocGeneralTabsLinkIeSettings.classList.add('active');
                ezTocGeneralTabsLinkSticky.classList.remove('active');
                if(ez_toc_pro_settings_link_paid !== null && ez_toc_pro_settings_link_paid.length > 0 && ezTocElementProSettingsContainer.length > 0 && ezTocGeneralTabsLinkProSettings !== null)
                    ezTocGeneralTabsLinkProSettings.classList.remove('active');
            }
            if (ezTocElementProSettingsContainer.length > 0 && y >= ezTocGeneralContainerProSettings) {
                ezTocGeneralTabsLinkGeneral.classList.remove('active');
                ezTocGeneralTabsLinkAppearance.classList.remove('active');
                ezTocGeneralTabsLinkAdvanced.classList.remove('active');
                ezTocGeneralTabsLinkShortcode.classList.remove('active');
                ezTocGeneralTabsLinkCompatibility.classList.remove('active');
                ezTocGeneralTabsLinkIeSettings.classList.remove('active');
                ezTocGeneralTabsLinkSticky.classList.remove('active');
                if(ez_toc_pro_settings_link_paid !== null && ez_toc_pro_settings_link_paid.length > 0 && ezTocElementProSettingsContainer.length > 0 && ezTocGeneralTabsLinkProSettings !== null)
                    ezTocGeneralTabsLinkProSettings.classList.add('active');
            }
        };
    } else {
        window.onscroll = function () {}
    }
}
ezTocSettingsTabsFixed();

function no_heading_text(params) {
    if(jQuery("input[name='ez-toc-settings[no_heading_text]']").prop('checked') == true) {
        jQuery("input[name='ez-toc-settings[no_heading_text_value]']").parents('tr').show(200);
    } else {
        jQuery("input[name='ez-toc-settings[no_heading_text_value]']").parents('tr').hide(200);
    }
}
jQuery(document).on("change", "input[name='ez-toc-settings[no_heading_text]']", function() {
    no_heading_text();
});
no_heading_text();


 /* Newletters js starts here */      
        
 if(cn_toc_admin_data.do_tour){
                
    var  content = '<h3>'+cn_toc_admin_data.translable_txt.using_eztoc+'</h3>';
         content += '<p>'+cn_toc_admin_data.translable_txt.do_you_want+' <b>'+cn_toc_admin_data.translable_txt.sd_update+'</b> '+cn_toc_admin_data.translable_txt.before_others+'</p>';
         content += '<style type="text/css">';
         content += '.wp-pointer-buttons{ padding:0; overflow: hidden; }';
         content += '.wp-pointer-content .button-secondary{  left: -25px;background: transparent;top: 5px; border: 0;position: relative; padding: 0; box-shadow: none;margin: 0;color: #0085ba;} .wp-pointer-content .button-primary{ display:none}  #saswp_mc_embed_signup{background:#fff; clear:left; font:14px Helvetica,Arial,sans-serif; }';
         content += '</style>';                        
         content += '<div id="saswp_mc_embed_signup">';
         content += '<form method="POST" accept-charset="utf-8" id="eztoc-news-letter-form">';
         content += '<div id="saswp_mc_embed_signup_scroll">';
         content += '<div class="eztoc-mc-field-group" style="    margin-left: 15px;    width: 195px;    float: left;">';
         content += '<input type="text" name="eztoc_subscriber_name" class="form-control" placeholder="Name" hidden value="'+cn_toc_admin_data.current_user_name+'" style="display:none">';
         content += '<input type="text" value="'+cn_toc_admin_data.current_user_email+'" name="eztoc_subscriber_email" class="form-control" placeholder="Email*"  style="      width: 180px;    padding: 6px 5px;">';                        
         content += '<input type="text" name="eztoc_subscriber_website" class="form-control" placeholder="Website" hidden style=" display:none; width: 168px; padding: 6px 5px;" value="'+cn_toc_admin_data.get_home_url+'">';
         content += '<input type="hidden" name="ml-submit" value="1" />';
         content += '</div>';
         content += '<div id="mce-responses">';                                                
         content += '</div>';
         content += '<div style="position: absolute; left: -5000px;" aria-hidden="true"><input type="text" name="b_a631df13442f19caede5a5baf_c9a71edce6" tabindex="-1" value=""></div>';
         content += '<input type="submit" value="Subscribe" name="subscribe" id="pointer-close" class="button mc-newsletter-sent" style=" background: #0085ba; border-color: #006799; padding: 0px 16px; text-shadow: 0 -1px 1px #006799,1px 0 1px #006799,0 1px 1px #006799,-1px 0 1px #006799; height: 30px; margin-top: 1px; color: #fff; box-shadow: 0 1px 0 #006799;">';
         content += '<p id="eztoc-news-letter-status"></p>';
         content += '</div>';
         content += '</form>';
         content += '</div>';

         jQuery(document).on("submit", "#eztoc-news-letter-form", function(e){
           e.preventDefault(); 
           
           var $form = jQuery(this),
           name = $form.find('input[name="eztoc_subscriber_name"]').val(),
           email = $form.find('input[name="eztoc_subscriber_email"]').val();
           website = $form.find('input[name="eztoc_subscriber_website"]').val();                          
           
           jQuery.post(cn_toc_admin_data.ajax_url,
                      {action:'eztoc_subscribe_newsletter',
                        eztoc_security_nonce:cn_toc_admin_data.eztoc_security_nonce,
                      name:name, email:email, website:website },
             function(data) {
               
                 if(data)
                 {
                   if(data=="Some fields are missing.")
                   {
                    jQuery("#eztoc-news-letter-status").text("");
                    jQuery("#eztoc-news-letter-status").css("color", "red");
                   }
                   else if(data=="Invalid email address.")
                   {
                    jQuery("#eztoc-news-letter-status").text("");
                    jQuery("#eztoc-news-letter-status").css("color", "red");
                   }
                   else if(data=="Invalid list ID.")
                   {
                    jQuery("#eztoc-news-letter-status").text("");
                    jQuery("#eztoc-news-letter-status").css("color", "red");
                   }
                   else if(data=="Already subscribed.")
                   {
                    jQuery("#eztoc-news-letter-status").text("");
                    jQuery("#eztoc-news-letter-status").css("color", "red");
                   }
                   else
                   {
                    jQuery("#eztoc-news-letter-status").text("You're subscribed!");
                    jQuery("#eztoc-news-letter-status").css("color", "green");
                   }
                 }
                 else
                 {
                   alert("Sorry, unable to subscribe. Please try again later!");
                 }
             }
           );
         });      
 

         (function ($) {
 var setup;                
 var wp_pointers_tour_opts = {
     content:content,
     position:{
         edge:"top",
         align:"left"
     }
 };
                 
 wp_pointers_tour_opts = $.extend (wp_pointers_tour_opts, {
         buttons: function (event, t) {
                 button= $ ('<a id="pointer-close" class="button-secondary">' + cn_toc_admin_data.button1 + '</a>');
                 button_2= $ ('#pointer-close.button');
                 button.bind ('click.pointer', function () {
                         t.element.pointer ('close');
                 });
                 button_2.on('click', function() {
                   setTimeout(function(){ 
                       t.element.pointer ('close');
                  }, 3000);
                       
                 } );
                 return button;
         },
         close: function () {
                 $.post (cn_toc_admin_data.ajax_url, {
                         pointer: 'eztoc_subscribe_pointer',
                         action: 'dismiss-wp-pointer'
                 });
         },
         show: function(event, t){
          t.pointer.css({'left':'170px', 'top':'160px'});
       }                                               
 });
 setup = function () {
         $(cn_toc_admin_data.displayID).pointer(wp_pointers_tour_opts).pointer('open');
          if (cn_toc_admin_data.button2) {
            $ ('#pointer-close').after ('<a id="pointer-primary" class="button-primary">' + cn_toc_admin_data.button2+ '</a>');
            $ ('#pointer-primary').click (function () {
                         cn_toc_admin_data.function_name;
                 });
            $ ('#pointer-close').click (function () {
                         $.post (cn_toc_admin_data.ajax_url, {
                                 pointer: 'eztoc_subscribe_pointer',
                                 action: 'dismiss-wp-pointer'
                         });
                 });
          }
 };
 if (wp_pointers_tour_opts.position && wp_pointers_tour_opts.position.defer_loading) {
         $(window).bind('load.wp-pointers', setup);
 }
 else {
         setup ();
 }
}) (jQuery);
}
 
/* Newletters js ends here */ 

jQuery(function($) {

    /* AMP Support Option js starts here */
    if( cn_toc_admin_data.is_amp_activated == 0 ){
    let tocAMPSupportOption = $('input[name="ez-toc-settings[toc-run-on-amp-pages]"]');
        if (tocAMPSupportOption.length > 0) {
            tocAMPSupportOption.attr('disabled', true);
        }
    }
    /* AMP Support Option js ends here */

});

    /* Headings Padding js starts here */
    jQuery(function($) {
        let $appearance = $('#eztoc-appearance');
        let headingsPaddingCheckbox = $appearance.find("input[name='ez-toc-settings[headings-padding]']");
        let paddingDirections = ['top', 'bottom', 'left', 'right'];
    
        paddingDirections.forEach(direction => {
            let input = $appearance.find(`input[name='ez-toc-settings[headings-padding-${direction}]']`);
            let inputHTML = input.parent();
            input.attr('type', 'number');
            input.parents('tr').remove();
            headingsPaddingCheckbox.parent().append(`&nbsp;&nbsp;&nbsp;<span id='headings-padding-${direction}-container'><label for='ez-toc-settings[headings-padding-${direction}]'><strong>${capitalize(direction)}</strong></label>&nbsp;&nbsp;&nbsp;${inputHTML.html()}</span>`);
            $appearance.find(`select[name='ez-toc-settings[headings-padding-${direction}_units]']`).html('<option value="px" selected="selected">px</option>');
        });
    
        let paddingContainers = {};
        paddingDirections.forEach(direction => {
            paddingContainers[direction] = $appearance.find(`span#headings-padding-${direction}-container`);
        });
    
        if (!headingsPaddingCheckbox.prop('checked')) {
            hidePaddingContainers();
        }
    
        $(document).on('change click', "input[name='ez-toc-settings[headings-padding]']", function() {
            if (headingsPaddingCheckbox.prop('checked')) {
                showPaddingContainers();
            } else {
                hidePaddingContainers();
            }
        });
    
        function hidePaddingContainers() {
            paddingDirections.forEach(direction => {
                paddingContainers[direction].hide(500);
                $appearance.find(`input[name='ez-toc-settings[headings-padding-${direction}]']`).val(0);
            });
        }
    
        function showPaddingContainers() {
            paddingDirections.forEach(direction => {
                paddingContainers[direction].show(500);
            });
        }
    
        function capitalize(str) {
            return str.charAt(0).toUpperCase() + str.slice(1);
        }
    });
      /* Headings Padding js ends here */

      /* Display Header Label js starts here */

      jQuery(function($) {
        let $generalSettings = $('#eztoc-general');
        let showHeadingText = $generalSettings.find("input[name='ez-toc-settings[show_heading_text]']");
        let visibilityOnHeaderText = $generalSettings.find("input[name='ez-toc-settings[visibility_on_header_text]']");
        let headerText = $generalSettings.find("input[name='ez-toc-settings[heading_text]']");
    
        function toggleHeaderTextVisibility() {
            if (showHeadingText.prop('checked')) {
                visibilityOnHeaderText.parents('tr').show(500);
                headerText.parents('tr').show(500);
            } else {
                visibilityOnHeaderText.parents('tr').hide(500);
                headerText.parents('tr').hide(500);
            }
        }
    
        // Initial check on page load
        toggleHeaderTextVisibility();
    
        // Event listener for changes
        $(document).on('change click', "input[name='ez-toc-settings[show_heading_text]']", toggleHeaderTextVisibility);
    });
    
    /* Display Header Label js ends here */

    /* Admin Initial View js starts here */
    jQuery(function($) {
        let $generalSettings = $('#eztoc-general');
        let visibility = $generalSettings.find("input[name='ez-toc-settings[visibility]']");
        let visibilityHideByDefault = $generalSettings.find("input[name='ez-toc-settings[visibility_hide_by_default]']");
    
        function toggleVisibility() {
            if (visibility.prop('checked')) {
                visibilityHideByDefault.parents('tr').show(500);
            } else {
                visibilityHideByDefault.parents('tr').hide(500);
            }
        }
    
        // Initial check on page load
        toggleVisibility();
    
        // Event listener for changes
        $(document).on('change click', "input[name='ez-toc-settings[visibility]']", toggleVisibility);
    });

    /* Admin Initial View js ends here */
    
    
