/**
 * WP Captcha
 * Admin Functions
 * (c) WebFactory Ltd, 2022 - 2023, www.webfactoryltd.com
 */

var WPCaptcha = {};

WPCaptcha.init = function () {};

WPCaptcha.init3rdParty = function ($) {
  $("#wpcaptcha_tabs")
    .tabs({
      activate: function (event, ui) {
        window.localStorage.setItem("wpcaptcha_tabs", $("#wpcaptcha_tabs").tabs("option", "active"));
      },
      create: function (event, ui) {
        if (window.location.hash && $('a[href="' + location.hash + '"]').length) {
          $("#wpcaptcha_tabs").tabs(
            "option",
            "active",
            $('a[href="' + location.hash + '"]')
              .parent()
              .index()
          );
        }
        $('#wpcaptcha_tabs_sidebar').show();
      },
      active: window.localStorage.getItem("wpcaptcha_tabs"),
    })
    .show();

  // init 2nd level of tabs
  $(".wpcaptcha-tabs-2nd-level").each(function () {
    $(this).tabs({
      activate: function (event, ui) {
        window.localStorage.setItem($(this).attr("id"), $(this).tabs("option", "active"));
      },
      active: window.localStorage.getItem($(this).attr("id")),
    });
  });
}; // init3rdParty

WPCaptcha.initUI = function ($) {
  // universal button to close UI dialog in any dialog
  $(".wpcaptcha-close-ui-dialog").on("click", function (e) {
    e.preventDefault();

    parent = $(this).closest(".ui-dialog-content");
    $(parent).dialog("close");

    return false;
  }); // close-ui-dialog

  // autosize textareas
  $.each($("#wpcaptcha_tabs textarea[data-autoresize]"), function () {
    var offset = this.offsetHeight - this.clientHeight;

    var resizeTextarea = function (el) {
      $(el)
        .css("height", "auto")
        .css("height", el.scrollHeight + offset + 2);
    };
    $(this)
      .on("keyup input click", function () {
        resizeTextarea(this);
      })
      .removeAttr("data-autoresize");
  }); // autosize textareas
}; // initUI

WPCaptcha.fix_dialog_close = function (event, ui) {
  jQuery(".ui-widget-overlay").bind("click", function () {
    jQuery("#" + event.target.id).dialog("close");
  });
}; // fix_dialog_close

WPCaptcha.parse_form_html = function (form_html) {
  var $ = jQuery.noConflict();
  data = {
    action_url: "",
    email_field: "",
    name_field: "",
    extra_data: "",
    method: "",
    email_fields_extra: "",
  };

  html = $.parseHTML('<div id="parse-form-tmp" style="display: none;">' + form_html + "</div>");

  data.action_url = $("form", html).attr("action");
  if ($("form", html).attr("method")) {
    data.method = $("form", html).attr("method").toLowerCase();
  }

  email_fields = $("input[type=email]", html);
  if (email_fields.length == 1) {
    data.email_field = $("input[type=email]", html).attr("name");
  }

  inputs = "";
  $("input", html).each(function (ind, el) {
    type = $(el).attr("type");
    if (type == "email" || type == "button" || type == "reset" || type == "submit") {
      return;
    }

    name = $(el).attr("name");
    name_tmp = name.toLowerCase();

    if (!data.email_field && (name_tmp == "email" || name_tmp == "from" || name_tmp == "emailaddress")) {
      data.email_field = name;
    } else if (name_tmp == "name" || name_tmp == "fname" || name_tmp == "firstname") {
      data.name_field = name;
    } else {
      data.email_fields_extra += name + ", ";
      data.extra_data += name + "=" + $(el).attr("value") + "&";
    }
  }); // foreach

  data.email_fields_extra = data.email_fields_extra.replace(/\, $/g, "");
  data.extra_data = data.extra_data.replace(/&$/g, "");

  return data;
}; // parse_form_html

jQuery(document).ready(function ($) {
  // helper for linking anchors in different tabs
  $(".settings_page_wpcaptcha").on("click", ".change_tab", function (e) {
    e.preventDefault();

    tab_name = "wpcaptcha_" + $(this).data("tab");
    tab_id = $('#wpcaptcha_tabs ul.ui-tabs-nav li[aria-controls="' + tab_name + '"]')
      .attr("aria-labelledby")
      .replace("ui-id-", "");
    if (!tab_id) {
      return false;
    }

    $("#wpcaptcha_tabs").tabs("option", "active", tab_id - 1);

    if ($(this).data("tab2")) {
      tab_name2 = "tab_" + $(this).data("tab2");
      tmp = $("#" + tab_name + ' ul.ui-tabs-nav li[aria-controls="' + tab_name2 + '"]');
      tab_id = $("#" + tab_name + " ul.ui-tabs-nav li").index(tmp);
      if (tab_id == -1) {
        return false;
      }

      $("#" + tab_name + " .wpcaptcha-tabs-2nd-level").tabs("option", "active", tab_id);
    } // if secondary tab

    // get the link anchor and scroll to it
    target = this.href.split("#")[1];

    return false;
  }); // change tab

  // helper for linking anchors in different tabs
  $(".settings_page_wpcaptcha").on("click", ".confirm_action", function (e) {
    message = $(this).data("confirm");

    if (!message || confirm(message)) {
      return true;
    } else {
      e.preventDefault();
      return false;
    }
  }); // confirm action before link click

  $(window).on("hashchange", function () {
    $("#wpcaptcha_tabs").tabs(
      "option",
      "active",
      $("a[href=\\" + location.hash + "]")
        .parent()
        .index()
    );
  });

  var selectedTab = getUrlParameter("tab");

  if (selectedTab) {
    $("#wpcaptcha_tabs").tabs(
      "option",
      "active",
      $("a[href=\\#" + selectedTab + "]")
        .parent()
        .index()
    );
  }

  WPCaptcha.initUI($);
  WPCaptcha.init3rdParty($);

  $("#wpcaptcha-locks-log-table").one("preInit.dt", function () {
    $("#wpcaptcha-locks-log-table_filter").append('<div id="wpcaptcha-locks-log-toggle-chart" title="' + (window.localStorage.getItem("wpcaptcha_locks_chart") == "disabled" ? "Show" : "Hide") + ' locks Chart" class="tooltip wpcaptcha-locks-log-toggle-chart wpcaptcha-locks-log-toggle-chart-' + window.localStorage.getItem("wpcaptcha_locks_chart") + '"><i class="wpcaptcha-icon wpcaptcha-graph"></i></a>');

    $("#wpcaptcha-locks-log-table_filter").append('<div id="wpcaptcha-locks-log-toggle-stats" title="' + (window.localStorage.getItem("wpcaptcha_locks_stats") == "disabled" ? "Show" : "Hide") + ' locks Stats" class="tooltip wpcaptcha-locks-log-toggle-stats wpcaptcha-locks-log-toggle-stats-' + window.localStorage.getItem("wpcaptcha_locks_stats") + '"><i class="wpcaptcha-icon wpcaptcha-pie"></i></a>');

    $(".tooltip").tooltipster();
  });

  $("#wpcaptcha-fails-log-table").one("preInit.dt", function () {
    $("#wpcaptcha-fails-log-table_filter").append('<div id="wpcaptcha-fails-log-toggle-chart" title="' + (window.localStorage.getItem("wpcaptcha_fails_chart") == "disabled" ? "Show" : "Hide") + ' fails Chart" class="tooltip wpcaptcha-fails-log-toggle-chart wpcaptcha-fails-log-toggle-chart-' + window.localStorage.getItem("wpcaptcha_fails_chart") + '"><i class="wpcaptcha-icon wpcaptcha-graph"></i></a>');
    $("#wpcaptcha-fails-log-table_filter").append('<div id="wpcaptcha-fails-log-toggle-stats" title="' + (window.localStorage.getItem("wpcaptcha_fails_stats") == "disabled" ? "Show" : "Hide") + ' fails Stats" class="tooltip wpcaptcha-fails-log-toggle-stats wpcaptcha-fails-log-toggle-stats-' + window.localStorage.getItem("wpcaptcha_fails_stats") + '"><i class="wpcaptcha-icon wpcaptcha-pie"></i></a>');

    $(".tooltip").tooltipster();
  });

  $("#wpcaptcha_tabs").on("click", ".wpcaptcha-fails-log-toggle-chart", function () {
    if ($(this).hasClass("wpcaptcha-fails-log-toggle-chart-enabled")) {
      $("#tab_log_full .wpcaptcha-chart-placeholder").fadeOut(300);
      $(".wpcaptcha-chart-fails").hide(
        "blind",
        {
          direction: "vertical",
          complete: function () {
            center_locks_placeholder("full");
          },
        },
        500
      );
      $(this).removeClass("wpcaptcha-fails-log-toggle-chart-enabled");
      $(this).addClass("wpcaptcha-fails-log-toggle-chart-disabled");
      $(this).attr("title", "Show Failed Attempts Chart");
      window.localStorage.setItem("wpcaptcha_fails_chart", "disabled");
    } else {
      $(this).removeClass("wpcaptcha-fails-log-toggle-chart-disabled");
      $(this).addClass("wpcaptcha-fails-log-toggle-chart-enabled");
      $(this).attr("title", "Hide Failed Attempts Chart");
      window.localStorage.setItem("wpcaptcha_fails_chart", "enabled");
      $(".wpcaptcha-chart-fails").show();
      create_fails_chart();
      $(".wpcaptcha-chart-fails").hide();
      $("#wpcaptcha_fails_log .wpcaptcha-chart-placeholder").fadeOut(300);
      $(".wpcaptcha-chart-fails").show(
        "blind",
        {
          direction: "vertical",
          complete: function () {
            center_locks_placeholder("full");
          },
        },
        500
      );
    }

    $(this).tooltipster("destroy");
    $(".tooltip").tooltipster();
  });

  $("#wpcaptcha_tabs").on("click", ".wpcaptcha-locks-log-toggle-chart", function () {
    if ($(this).hasClass("wpcaptcha-locks-log-toggle-chart-enabled")) {
      $("#tab_log_locks .wpcaptcha-chart-placeholder").fadeOut(300);
      $(".wpcaptcha-chart-locks").hide(
        "blind",
        {
          direction: "vertical",
          complete: function () {
            center_locks_placeholder("locks");
          },
        },
        500
      );
      $(this).removeClass("wpcaptcha-locks-log-toggle-chart-enabled");
      $(this).addClass("wpcaptcha-locks-log-toggle-chart-disabled");
      $(this).attr("title", "Show Failed Attempts Chart");
      window.localStorage.setItem("wpcaptcha_locks_chart", "disabled");
    } else {
      $(this).removeClass("wpcaptcha-locks-log-toggle-chart-disabled");
      $(this).addClass("wpcaptcha-locks-log-toggle-chart-enabled");
      $(this).attr("title", "Hide Access Locks Chart");
      window.localStorage.setItem("wpcaptcha_locks_chart", "enabled");
      $(".wpcaptcha-chart-locks").show();
      create_locks_chart();
      $(".wpcaptcha-chart-locks").hide();
      $("#wpcaptcha_locks_log .wpcaptcha-chart-placeholder").fadeOut(300);
      $(".wpcaptcha-chart-locks").show(
        "blind",
        {
          direction: "vertical",
          complete: function () {
            center_locks_placeholder("locks");
          },
        },
        500
      );
    }

    $(this).tooltipster("destroy");
    $(".tooltip").tooltipster();
  });

  $("body")
    .on("input", 'input[type="range"]', function (e) {
      $(this).parents("td").find(".range_value").html(this.value);
    })
    .trigger("change");

  function center_locks_placeholder(type) {
    var placeholder_top = 0;

    if ($("#tab_log_" + type + " .wpcaptcha-chart-" + type + "").is(":visible")) {
      placeholder_top = placeholder_top + 70;
    }
    if ($("#tab_log_" + type + " .wpcaptcha-stats-" + type + "").is(":visible")) {
      placeholder_top = placeholder_top + 120;
    }

    $("#tab_log_" + type + " .wpcaptcha-chart-placeholder").css("top", placeholder_top + "px");
    if (placeholder_top == 0) {
      $("#tab_log_" + type + " .wpcaptcha-chart-placeholder").hide();
    } else {
      $("#tab_log_" + type + " .wpcaptcha-chart-placeholder").fadeIn(300);
      $("#tab_log_" + type + " .wpcaptcha-chart-placeholder").css("top", placeholder_top + "px");
    }
  }

  if (wpcaptcha_vars.stats_locks.total == 0) {
    var placeholder_top = 0;
    if (window.localStorage.getItem("wpcaptcha_locks_stats") == "enabled") {
      placeholder_top = placeholder_top + 70;
    }
    if (window.localStorage.getItem("wpcaptcha_locks_chart") == "enabled") {
      placeholder_top = placeholder_top + 120;
    }
    $(".wpcaptcha-chart-locks").css("filter", "blur(3px)");
    $(".wpcaptcha-stats-locks").css("filter", "blur(3px)");
    $("#tab_log_locks").append('<div class="wpcaptcha-chart-placeholder">' + wpcaptcha_vars.stats_unavailable + "</div>");

    if (placeholder_top == 0) {
      $("#tab_log_locks .wpcaptcha-chart-placeholder").hide();
    } else {
      $("#tab_log_locks .wpcaptcha-chart-placeholder").css("top", placeholder_top + "px");
      $("#wpcaptcha_locks_log .wpcaptcha-chart-placeholder").fadeIn(300);
    }
  }

  if (wpcaptcha_vars.stats_fails.total == 0) {
    var placeholder_top = 0;
    if (window.localStorage.getItem("wpcaptcha_fails_stats") == "enabled") {
      placeholder_top = placeholder_top + 70;
    }
    if (window.localStorage.getItem("wpcaptcha_fails_chart") == "enabled") {
      placeholder_top = placeholder_top + 120;
    }
    $(".wpcaptcha-chart-fails").css("filter", "blur(3px)");
    $(".wpcaptcha-stats-fails").css("filter", "blur(3px)");
    $("#tab_log_full").append('<div class="wpcaptcha-chart-placeholder">' + wpcaptcha_vars.stats_unavailable + "</div>");

    if (placeholder_top == 0) {
      $("#tab_log_full .wpcaptcha-chart-placeholder").hide();
    } else {
      $("#tab_log_full .wpcaptcha-chart-placeholder").css("top", placeholder_top + "px");
      $("#wpcaptcha_fails_log .wpcaptcha-chart-placeholder").fadeIn(300);
    }
  }

  $("#wpcaptcha_tabs").on("click", ".wpcaptcha-fails-log-toggle-stats", function () {
    if ($(this).hasClass("wpcaptcha-fails-log-toggle-stats-enabled")) {
      $("#wpcaptcha_fails_log .wpcaptcha-chart-placeholder").fadeOut(300);
      $(".wpcaptcha-stats-fails").hide(
        "blind",
        {
          direction: "vertical",
          complete: function () {
            center_locks_placeholder("full");
          },
        },
        500
      );
      $(this).removeClass("wpcaptcha-fails-log-toggle-stats-enabled");
      $(this).addClass("wpcaptcha-fails-log-toggle-stats-disabled");
      $(this).attr("title", "Show Failed Attempts Stats");
      window.localStorage.setItem("wpcaptcha_fails_stats", "disabled");
    } else {
      $(this).removeClass("wpcaptcha-fails-log-toggle-stats-disabled");
      $(this).addClass("wpcaptcha-fails-log-toggle-stats-enabled");
      $(this).attr("title", "Hide fails Stats");
      window.localStorage.setItem("wpcaptcha_fails_stats", "enabled");
      $(".wpcaptcha-stats-fails").show();
      $(".wpcaptcha-stats-fails").hide();
      $("#wpcaptcha_fails_log .wpcaptcha-chart-placeholder").fadeOut(300);
      $(".wpcaptcha-stats-fails").show(
        "blind",
        {
          direction: "vertical",
          complete: function () {
            center_locks_placeholder("full");
          },
        },
        500
      );
    }

    $(this).tooltipster("destroy");
    $(".tooltip").tooltipster();
  });

  $("#wpcaptcha_tabs").on("click", ".wpcaptcha-locks-log-toggle-stats", function () {
    if ($(this).hasClass("wpcaptcha-locks-log-toggle-stats-enabled")) {
      $("#wpcaptcha_locks_log .wpcaptcha-chart-placeholder").fadeOut(300);
      $(".wpcaptcha-stats-locks").hide(
        "blind",
        {
          direction: "vertical",
          complete: function () {
            center_locks_placeholder("locks");
          },
        },
        500
      );
      $(this).removeClass("wpcaptcha-locks-log-toggle-stats-enabled");
      $(this).addClass("wpcaptcha-locks-log-toggle-stats-disabled");
      $(this).attr("title", "Show Access Locks Stats");
      window.localStorage.setItem("wpcaptcha_locks_stats", "disabled");
    } else {
      $(this).removeClass("wpcaptcha-locks-log-toggle-stats-disabled");
      $(this).addClass("wpcaptcha-locks-log-toggle-stats-enabled");
      $(this).attr("title", "Hide Access Locks Stats");
      window.localStorage.setItem("wpcaptcha_locks_stats", "enabled");
      $(".wpcaptcha-stats-locks").show();
      $(".wpcaptcha-stats-locks").hide();
      $("#wpcaptcha_locks_log .wpcaptcha-chart-placeholder").fadeOut(300);
      $(".wpcaptcha-stats-locks").show(
        "blind",
        {
          direction: "vertical",
          complete: function () {
            center_locks_placeholder("locks");
          },
        },
        500
      );
    }

    $(this).tooltipster("destroy");
    $(".tooltip").tooltipster();
  });

  $(".settings_page_wpcaptcha").on("click", ".unlock_accesslock", function (e) {
    e.preventDefault();
    $.post({
      url: ajaxurl,
      data: {
        action: "wpcaptcha_run_tool",
        _ajax_nonce: wpcaptcha_vars.run_tool_nonce,
        tool: "unlock_accesslock",
        lock_id: $(this).data("lock-id"),
      },
    })
      .always(function (response) {})
      .done(function (response) {
        location.reload();
      });
  });

  $(".settings_page_wpcaptcha").on("click", ".delete_lock_entry", function (e) {
    e.preventDefault();
    uid = $(this).data("lock-uid");
    button = $(this);

    wpcaptcha_swal
      .fire({
        title: $(button).data("title"),
        type: "question",
        text: $(button).data("text"),
        heightAuto: false,
        showCancelButton: true,
        focusConfirm: false,
        confirmButtonText: $(button).data("btn-confirm"),
        cancelButtonText: wpcaptcha_vars.cancel_button,
        width: 600,
      })
      .then((result) => {
        if (typeof result.value != "undefined") {
          block = block_ui($(button).data("msg-wait"));
          $.post({
            url: ajaxurl,
            data: {
              action: "wpcaptcha_run_tool",
              _ajax_nonce: wpcaptcha_vars.run_tool_nonce,
              tool: "delete_lock_log",
              lock_id: $(button).data("lock-id"),
            },
          })
            .always(function (response) {
              wpcaptcha_swal.close();
            })
            .done(function (response) {
              if (response.success) {
                $("#wpcaptcha-locks-log-table tr#" + response.data.id).remove();
                wpcaptcha_swal.fire({
                  type: "success",
                  heightAuto: false,
                  title: $(button).data("msg-success"),
                });
              } else {
                wpcaptcha_swal.fire({
                  type: "error",
                  heightAuto: false,
                  title: wpcaptcha_vars.documented_error + " " + data.data,
                });
              }
            })
            .fail(function (response) {
              wpcaptcha_swal.fire({
                type: "error",
                heightAuto: false,
                title: wpcaptcha_vars.undocumented_error,
              });
            });
        } // if confirmed
      });
  });

  $(".settings_page_wpcaptcha").on("click", ".empty_log", function (e) {
    e.preventDefault();
    button = $(this);

    wpcaptcha_swal
      .fire({
        title: $(button).data("title"),
        type: "question",
        text: $(button).data("text"),
        heightAuto: false,
        showCancelButton: true,
        focusConfirm: false,
        confirmButtonText: $(button).data("btn-confirm"),
        cancelButtonText: wpcaptcha_vars.cancel_button,
        width: 600,
      })
      .then((result) => {
        if (typeof result.value != "undefined") {
          block = block_ui($(button).data("msg-wait"));
          $.post({
            url: ajaxurl,
            data: {
              action: "wpcaptcha_run_tool",
              _ajax_nonce: wpcaptcha_vars.run_tool_nonce,
              tool: "empty_log",
              log: $(button).data("log"),
            },
          })
            .always(function (response) {
              wpcaptcha_swal.close();
            })
            .done(function (response) {
              location.reload();
            })
            .fail(function (response) {
              wpcaptcha_swal.fire({
                type: "error",
                heightAuto: false,
                title: wpcaptcha_vars.undocumented_error,
              });
            });
        } // if confirmed
      });
  });

  $("#toggle_firewall_rules").on("change", function () {
    $(".firewall_rule_toggle").prop("checked", $(this).is(":checked"));
    $(".firewall_rule_toggle").trigger("change");
  });

  jQuery(document).ready(function ($) {
    $(".wpcaptcha-color").wpColorPicker();
  });

  $(".settings_page_wpcaptcha").on("click", ".captcha-box-wrapper img", function (e) {
    $("#captcha").val($(this).parent().data("captcha"));
    $("#captcha").trigger("change");
    $(".captcha-box-wrapper").removeClass("captcha-selected");
    $(this).parent().addClass("captcha-selected");
  });

  $(".settings_page_wpcaptcha").on("blur change keyup", "#captcha,#captcha_site_key,#captcha_secret_key", function (e) {
    if ($("#captcha").val() != "disabled" && $(this).val() != $(this).data("old")) {
      $(".captcha_verify_wrapper").show();
    } else {
      $(".captcha_verify_wrapper").hide();
    }
  });

  var icon_captcha = false;

  $(".settings_page_wpcaptcha").on("click", "#verify-captcha", function (e) {
    e.preventDefault();
    var captcha_response;

    wpcaptcha_swal
      .fire({
        title: "Verify Captcha",
        type: "",
        icon: "",
        html: '<div class="wpcaptcha-swal-captcha-wrapper"><div class="wpcaptcha-captcha-loader"><img width="64" src="' + wpcaptcha_vars.icon_url + '" /></div><div id="wpcaptcha_captcha_box" style="margin: 0 auto; display: inline-block;"></div></div>',
        onOpen: () => {
          window.wpcaptcha_captcha_script = document.createElement("script");
          if ($("#captcha").val() == "recaptchav2") {
            window.wpcaptcha_captcha_script.src = "https://www.google.com/recaptcha/api.js?onload=wpcaptcha_captchav2_test&render=explicit";
          }

          if ($("#captcha").val() == "recaptchav3") {
            window.wpcaptcha_captcha_script.src = "https://www.google.com/recaptcha/api.js?onload=wpcaptcha_captchav3_test&render=" + $("#captcha_site_key").val();
          }

          if ($("#captcha").val() == "builtin") {
            $(".wpcaptcha-captcha-loader").remove();

            var captcha_html = "";
            captcha_html += '<p><label for="wpcaptcha_captcha">Are you human? Please solve:';
            captcha_html += '<img class="wpcaptcha-captcha-img" style="vertical-align: text-top;" src="' + wpcaptcha_vars.plugin_url + "libs/captcha.php?wpcaptcha-generate-image=true&color=#FFFFFF&noise=1&rnd=" + Math.floor(Math.random() * 1000) + '" alt="Captcha" />';
            captcha_html += '<input class="input" type="text" size="3" name="wpcaptcha_captcha" id="wpcaptcha_builtin_captcha" />';
            captcha_html += "</label></p>";

            $("#wpcaptcha_captcha_box").html(captcha_html);

            $("#wpcaptcha_builtin_captcha").on("blur change keyup", function () {
              captcha_response = $(this).val();
            });
          }

          window.wpcaptcha_captcha_script.onerror = function () {
            wpcaptcha_swal.close();
            wpcaptcha_swal.fire({
              type: "error",
              heightAuto: false,
              title: "An error occured loading the captcha, please check your Captcha Site Key",
            });
          };

          window.wpcaptcha_captchav2_test = function () {
            $(".wpcaptcha-captcha-loader").remove();
            window.wpcaptcha_captcha_box = grecaptcha.render("wpcaptcha_captcha_box", {
              sitekey: $("#captcha_site_key").val(),
              theme: "light",
              callback: () => {
                captcha_response = grecaptcha.getResponse(window.wpcaptcha_captcha_box);
              },
            });
          };

          window.wpcaptcha_captchav3_test = function () {
            grecaptcha.execute($("#captcha_site_key").val(), { action: "submit" }).then(function (token) {
              $(".wpcaptcha-swal-captcha-wrapper").html();
              captcha_response = token;
              $(".wpcaptcha-swal-captcha-wrapper").html("Captcha token ready, click Submit Captcha to verify it");
            });
          };

          document.head.appendChild(window.wpcaptcha_captcha_script);
        },
        heightAuto: false,
        showCancelButton: true,
        focusConfirm: false,
        confirmButtonText: "Submit Captcha",
        cancelButtonText: "Cancel",
        width: 600,
      })
      .then((result) => {
        if (typeof result.value != "undefined") {
          block = block_ui("Verifying captcha");

          $.post({
            url: ajaxurl,
            data: {
              action: "wpcaptcha_run_tool",
              _ajax_nonce: wpcaptcha_vars.run_tool_nonce,
              tool: "verify_captcha",
              captcha_type: $("#captcha").val(),
              captcha_site_key: $("#captcha_site_key").val(),
              captcha_secret_key: $("#captcha_secret_key").val(),
              captcha_response: captcha_response,
            },
          })
            .always(function (response) {
              wpcaptcha_swal.close();
              document.head.removeChild(window.wpcaptcha_captcha_script);
              window.wpcaptcha_captcha_script = null;
              window.wpcaptcha_captchav2_test = null;
              window.wpcaptcha_captchav3_test = null;
              window.wpcaptcha_hcaptcha_test = null;
            })
            .done(function (response) {
              if (response.success) {
                $("#captcha_site_key").data("old", $("#captcha_site_key").val());
                $("#captcha_secret_key").data("old", $("#captcha_secret_key").val());
                $(".captcha_verify_wrapper").hide();
                $("#captcha_verified").val("1");
                wpcaptcha_swal.fire({
                  type: "success",
                  heightAuto: false,
                  title: "Captcha has been verified successfully",
                });
              } else {
                wpcaptcha_swal
                  .fire({
                    type: "error",
                    heightAuto: false,
                    title: response.data,
                  })
                  .then((result) => {
                    if ($("#captcha").val() == "icons") {
                      location.reload();
                    }
                  });
              }
            })
            .fail(function (response) {
              wpcaptcha_swal.fire({
                type: "error",
                heightAuto: false,
                title: wpcaptcha_vars.undocumented_error,
              });
            });
        } // if confirmed
      });
  });

  $(".settings_page_wpcaptcha").on("click", ".delete_failed_entry", function (e) {
    e.preventDefault();
    uid = $(this).data("failed-uid");
    button = $(this);

    wpcaptcha_swal
      .fire({
        title: $(button).data("title"),
        type: "question",
        text: $(button).data("text"),
        heightAuto: false,
        showCancelButton: true,
        focusConfirm: false,
        confirmButtonText: $(button).data("btn-confirm"),
        cancelButtonText: wpcaptcha_vars.cancel_button,
        width: 600,
      })
      .then((result) => {
        if (typeof result.value != "undefined") {
          block = block_ui($(button).data("msg-wait"));
          $.post({
            url: ajaxurl,
            data: {
              action: "wpcaptcha_run_tool",
              _ajax_nonce: wpcaptcha_vars.run_tool_nonce,
              tool: "delete_fail_log",
              fail_id: $(button).data("failed-id"),
            },
          })
            .always(function (response) {
              wpcaptcha_swal.close();
            })
            .done(function (response) {
              if (response.success) {
                $("#wpcaptcha-fails-log-table tr#" + response.data.id).remove();
                wpcaptcha_swal.fire({
                  type: "success",
                  heightAuto: false,
                  title: $(button).data("msg-success"),
                });
              } else {
                wpcaptcha_swal.fire({
                  type: "error",
                  heightAuto: false,
                  title: wpcaptcha_vars.documented_error + " " + data.data,
                });
              }
            })
            .fail(function (response) {
              wpcaptcha_swal.fire({
                type: "error",
                heightAuto: false,
                title: wpcaptcha_vars.undocumented_error,
              });
            });
        } // if confirmed
      });
  });

  // display a message while an action is performed
  function block_ui(message) {
    tmp = wpcaptcha_swal.fire({
      text: message,
      type: false,
      imageUrl: wpcaptcha_vars.icon_url,
      onOpen: () => {},
      imageWidth: 58,
      imageHeight: 58,
      imageAlt: message,
      allowOutsideClick: false,
      allowEscapeKey: false,
      allowEnterKey: false,
      showConfirmButton: false,
      heightAuto: false,
    });

    return tmp;
  } // block_ui

  function getUrlParameter(sParam) {
    var sPageURL = window.location.search.substring(1),
      sURLVariables = sPageURL.split("&"),
      sParameterName,
      i;

    for (i = 0; i < sURLVariables.length; i++) {
      sParameterName = sURLVariables[i].split("=");

      if (sParameterName[0] === sParam) {
        return sParameterName[1] === undefined ? true : decodeURIComponent(sParameterName[1]);
      }
    }
  }

  table_locks_logs = $("#wpcaptcha-locks-log-table").dataTable({
    bProcessing: true,
    bServerSide: true,
    bLengthChange: 1,
    bProcessing: true,
    bStateSave: 0,
    bAutoWidth: 0,
    columnDefs: [
      {
        targets: [1],
        className: "dt-body-center",
        orderable: false,
      },
      {
        targets: [2],
        className: "dt-body-center",
        orderable: false,
      },
      {
        targets: [3],
        className: "dt-body-center",
        orderable: false,
      },
      {
        targets: [4],
        className: "dt-body-center",
        orderable: false,
      },
      {
        targets: [5],
        className: "dt-body-right",
        orderable: false,
      },
    ],
    drawCallback: function () {
      $(".tooltip").tooltipster();
    },
    initComplete: function () {
      $(".tooltip").tooltipster();
    },
    language: {
      loadingRecords: "&nbsp;",
      processing: '<div class="wpcaptcha-datatables-loader"><img width="64" src="' + wpcaptcha_vars.icon_url + '" /></div>',
      emptyTable: "No Access Locks exist yet",
      searchPlaceholder: "Type something to search ...",
      search: "",
    },
    order: [[0, "desc"]],
    iDisplayLength: 25,
    sPaginationType: "full_numbers",
    dom: '<"settings_page_wpcaptcha_top"f>rt<"bottom"lp><"clear">',
    sAjaxSource: ajaxurl + "?action=wpcaptcha_run_tool&tool=locks_logs&_ajax_nonce=" + wpcaptcha_vars.run_tool_nonce,
  });

  table_activity_logs = $("#wpcaptcha-fails-log-table").dataTable({
    bProcessing: true,
    bServerSide: true,
    bLengthChange: 1,
    bProcessing: true,
    bStateSave: 0,
    bAutoWidth: 0,
    columnDefs: [
      {
        targets: [3],
        className: "dt-body-center",
        orderable: false,
      },
      {
        targets: [4],
        className: "dt-body-right",
        orderable: false,
      },
    ],
    drawCallback: function () {
      $(".tooltip").tooltipster();
    },
    initComplete: function () {
      $(".tooltip").tooltipster();
    },
    language: {
      loadingRecords: "&nbsp;",
      processing: '<div class="wpcaptcha-datatables-loader"><img width="64" src="' + wpcaptcha_vars.icon_url + '" /></div>',
      emptyTable: "No failed attempts exist yet",
      searchPlaceholder: "Type something to search ...",
      search: "",
    },
    order: [[0, "desc"]],
    iDisplayLength: 25,
    sPaginationType: "full_numbers",
    dom: '<"settings_page_wpcaptcha_top"f>rt<"bottom"lp><"clear">',
    sAjaxSource: ajaxurl + "?action=wpcaptcha_run_tool&tool=activity_logs&_ajax_nonce=" + wpcaptcha_vars.run_tool_nonce,
  });

  if ($("#captcha").val() != "disabled" && $("#captcha").val() != "builtin" && $("#captcha").val() != "icons") {
    $(".captcha_keys_wrapper").show();
  } else {
    $(".captcha_keys_wrapper").hide();
  }

  $("#captcha").on("change", function () {
    if ($("#captcha").val() != "disabled" && $("#captcha").val() != "builtin" && $("#captcha").val() != "icons") {
      $(".captcha_keys_wrapper").show();
    } else {
      $(".captcha_keys_wrapper").hide();
    }
  });

  Chart.defaults.global.defaultFontColor = "#23282d";
  Chart.defaults.global.defaultFontFamily = '-apple-system,BlinkMacSystemFont,"Segoe UI",Roboto,Oxygen-Sans,Ubuntu,Cantarell,"Helvetica Neue",sans-serif';
  Chart.defaults.global.defaultFontSize = 12;
  var wpcaptcha_fails_chart;
  var wpcaptcha_locks_chart;
  var wpcaptcha_fails_device_chart;
  var wpcaptcha_locks_device_chart;

  function create_locks_chart() {
    if (!wpcaptcha_vars.stats_locks || !wpcaptcha_vars.stats_locks.days.length) {
      $("#wpcaptcha-locks-chart").remove();
      return;
    } else {
      if (wpcaptcha_locks_chart) {
        wpcaptcha_locks_chart.destroy();
      }

      var chartlockscanvas = document.getElementById("wpcaptcha-locks-chart").getContext("2d");
      var gradient = chartlockscanvas.createLinearGradient(0, 0, 0, 200);
      gradient.addColorStop(0, "#f9f9f9");
      gradient.addColorStop(1, "#ffffff");

      wpcaptcha_locks_chart = new Chart(chartlockscanvas, {
        type: "line",

        data: {
          labels: wpcaptcha_vars.stats_locks.days,
          datasets: [
            {
              label: "Locks",
              yAxisID: "yleft",
              xAxisID: "xdown",
              data: wpcaptcha_vars.stats_locks.count,
              backgroundColor: gradient,
              borderColor: wpcaptcha_vars.chart_colors[0],
              hoverBackgroundColor: wpcaptcha_vars.chart_colors[0],
              borderWidth: 0,
            },
          ],
        },
        options: {
          animation: false,
          legend: false,
          maintainAspectRatio: false,
          tooltips: {
            mode: "index",
            intersect: false,
            callbacks: {
              title: function (value, values) {
                index = value[0].index;
                return moment(values.labels[index], "YYYY-MM-DD").format("dddd, MMMM Do");
              },
            },
            displayColors: false,
          },
          scales: {
            xAxes: [
              {
                display: false,
                id: "xdown",
                stacked: true,
                ticks: {
                  callback: function (value, index, values) {
                    return moment(value, "YYYY-MM-DD").format("MMM Do");
                  },
                },
                categoryPercentage: 0.85,
                time: {
                  unit: "day",
                  displayFormats: { day: "MMM Do" },
                  tooltipFormat: "dddd, MMMM Do",
                },
                gridLines: { display: false },
              },
            ],
            yAxes: [
              {
                display: false,
                id: "yleft",
                position: "left",
                type: "linear",
                scaleLabel: {
                  display: true,
                  labelString: "Hits",
                },
                gridLines: { display: false },
                stacked: false,
                ticks: {
                  beginAtZero: false,
                  maxTicksLimit: 12,
                  callback: function (value, index, values) {
                    return Math.round(value);
                  },
                },
              },
            ],
          },
        },
      });
    }
  }

  function create_fails_chart() {
    if (!wpcaptcha_vars.stats_fails || !wpcaptcha_vars.stats_fails.days.length) {
      $("#wpcaptcha-fails-chart").remove();
      return;
    } else {
      if (wpcaptcha_fails_chart) wpcaptcha_fails_chart.destroy();

      var chartfailscanvas = document.getElementById("wpcaptcha-fails-chart").getContext("2d");
      var gradient = chartfailscanvas.createLinearGradient(0, 0, 0, 200);
      gradient.addColorStop(0, "#f9f9f9");
      gradient.addColorStop(1, "#ffffff");

      wpcaptcha_fails_chart = new Chart(chartfailscanvas, {
        type: "line",
        data: {
          labels: wpcaptcha_vars.stats_fails.days,
          datasets: [
            {
              label: "Fails",
              yAxisID: "yleft",
              xAxisID: "xdown",
              data: wpcaptcha_vars.stats_fails.count,
              backgroundColor: gradient,
              borderColor: wpcaptcha_vars.chart_colors[0],
              hoverBackgroundColor: wpcaptcha_vars.chart_colors[0],
              borderWidth: 0,
            },
          ],
        },
        options: {
          animation: false,
          legend: false,
          maintainAspectRatio: false,
          tooltips: {
            mode: "index",
            intersect: false,
            callbacks: {
              title: function (value, values) {
                index = value[0].index;
                return moment(values.labels[index], "YYYY-MM-DD").format("dddd, MMMM Do");
              },
            },
            displayColors: false,
          },

          scales: {
            xAxes: [
              {
                display: false,
                id: "xdown",
                stacked: true,
                ticks: {
                  callback: function (value, index, values) {
                    return moment(value, "YYYY-MM-DD").format("MMM Do");
                  },
                },
                categoryPercentage: 0.85,
                time: {
                  unit: "day",
                  displayFormats: { day: "MMM Do" },
                  tooltipFormat: "dddd, MMMM Do",
                },
                gridLines: { display: false },
              },
            ],
            yAxes: [
              {
                display: false,
                id: "yleft",
                position: "left",
                type: "linear",
                scaleLabel: {
                  display: true,
                  labelString: "Hits",
                },
                gridLines: { display: false },
                stacked: false,
                ticks: {
                  beginAtZero: false,
                  maxTicksLimit: 12,
                  callback: function (value, index, values) {
                    return Math.round(value);
                  },
                },
              },
            ],
          },
        },
      });
    }
  }

  Chart.defaults.doughnutLabels = Chart.helpers.clone(Chart.defaults.doughnut);
  var wpcaptcha_doughnut_helpers = Chart.helpers;
  Chart.controllers.doughnutLabels = Chart.controllers.doughnut.extend({
    updateElement: function (arc, index, reset) {
      var _this = this;
      var chart = _this.chart,
        chartArea = chart.chartArea,
        opts = chart.options,
        animationOpts = opts.animation,
        arcOpts = opts.elements.arc,
        centerX = (chartArea.left + chartArea.right) / 2,
        centerY = (chartArea.top + chartArea.bottom) / 2,
        startAngle = opts.rotation, // non reset case handled later
        endAngle = opts.rotation, // non reset case handled later
        dataset = _this.getDataset(),
        circumference = reset && animationOpts.animateRotate ? 0 : arc.hidden ? 0 : _this.calculateCircumference(dataset.data[index]) * (opts.circumference / (2.0 * Math.PI)),
        innerRadius = reset && animationOpts.animateScale ? 0 : _this.innerRadius,
        outerRadius = reset && animationOpts.animateScale ? 0 : _this.outerRadius,
        custom = arc.custom || {},
        valueAtIndexOrDefault = wpcaptcha_doughnut_helpers.getValueAtIndexOrDefault;

      wpcaptcha_doughnut_helpers.extend(arc, {
        // Utility
        _datasetIndex: _this.index,
        _index: index,

        // Desired view properties
        _model: {
          x: centerX + chart.offsetX,
          y: centerY + chart.offsetY,
          startAngle: startAngle,
          endAngle: endAngle,
          circumference: circumference,
          outerRadius: outerRadius,
          innerRadius: innerRadius,
          label: valueAtIndexOrDefault(dataset.label, index, chart.data.labels[index]),
        },

        draw: function () {
          var ctx = this._chart.ctx,
            vm = this._view,
            sA = vm.startAngle,
            eA = vm.endAngle,
            opts = this._chart.config.options;

          var labelPos = this.tooltipPosition();
          var segmentLabel = (vm.circumference / opts.circumference) * 100;

          ctx.beginPath();

          ctx.arc(vm.x, vm.y, vm.outerRadius, sA, eA);
          ctx.arc(vm.x, vm.y, vm.innerRadius, eA, sA, true);

          ctx.closePath();
          ctx.strokeStyle = vm.borderColor;
          ctx.lineWidth = vm.borderWidth;

          ctx.fillStyle = vm.backgroundColor;

          ctx.fill();
          ctx.lineJoin = "bevel";

          if (vm.circumference > 0.15) {
            // Trying to hide label when it doesn't fit in segment
            ctx.beginPath();
            ctx.font = wpcaptcha_doughnut_helpers.fontString(opts.defaultFontSize, opts.defaultFontStyle, opts.defaultFontFamily);
            ctx.fillStyle = "#fff";
            ctx.textBaseline = "top";
            ctx.textAlign = "center";

            // Round percentage in a way that it always adds up to 100%
            ctx.fillText(segmentLabel.toFixed(0) + "%", labelPos.x, labelPos.y);
          }
        },
      });

      var model = arc._model;
      model.backgroundColor = custom.backgroundColor ? custom.backgroundColor : valueAtIndexOrDefault(dataset.backgroundColor, index, arcOpts.backgroundColor);
      model.hoverBackgroundColor = custom.hoverBackgroundColor ? custom.hoverBackgroundColor : valueAtIndexOrDefault(dataset.hoverBackgroundColor, index, arcOpts.hoverBackgroundColor);
      model.borderWidth = custom.borderWidth ? custom.borderWidth : valueAtIndexOrDefault(dataset.borderWidth, index, arcOpts.borderWidth);
      model.borderColor = custom.borderColor ? custom.borderColor : valueAtIndexOrDefault(dataset.borderColor, index, arcOpts.borderColor);

      // Set correct angles if not resetting
      if (!reset || !animationOpts.animateRotate) {
        if (index === 0) {
          model.startAngle = opts.rotation;
        } else {
          model.startAngle = _this.getMeta().data[index - 1]._model.endAngle;
        }

        model.endAngle = model.startAngle + model.circumference;
      }

      arc.pivot();
    },
  });

  if ($(".wpcaptcha-chart-locks").length && window.localStorage.getItem("wpcaptcha_locks_chart") == "enabled") {
    $(".wpcaptcha-chart-locks").show();
    create_locks_chart();
  }

  if ($(".wpcaptcha-chart-fails").length && window.localStorage.getItem("wpcaptcha_fails_chart") == "enabled") {
    $(".wpcaptcha-chart-fails").show();
    create_fails_chart();
  }

  if (window.localStorage.getItem("wpcaptcha_fails_stats") == "enabled") {
    $(".wpcaptcha-stats-fails").show();
  }

  if ($(".wpcaptcha-chart-locks").length && window.localStorage.getItem("wpcaptcha_locks_chart") == "enabled") {
    $(".wpcaptcha-chart-locks").show();
    create_locks_chart();
  }

  if (window.localStorage.getItem("wpcaptcha_locks_stats") == "enabled") {
    $(".wpcaptcha-stats-locks").show();
  }

  $("#wpcaptcha_tabs").on("tabsactivate", function (event, ui) {

    var active_index = $("#wpcaptcha_tabs").tabs("option", "active");
    var active_id = $("#wpcaptcha_tabs > ul > li").eq(active_index).find("a").attr("href").replace("#", "");

    if (active_id == "wpcaptcha_activity") {
      if (window.localStorage.getItem("wpcaptcha_locks_chart") == "enabled") {
        create_locks_chart();
        create_fails_chart();
      }
    }
  });

  if (window.localStorage.getItem("wpcaptcha_locks_chart") == null) {
    window.localStorage.setItem("wpcaptcha_locks_chart", "enabled");
  }

  if (window.localStorage.getItem("wpcaptcha_fails_chart") == null) {
    window.localStorage.setItem("wpcaptcha_fails_chart", "enabled");
  }

  if (window.localStorage.getItem("wpcaptcha_locks_stats") == null) {
    window.localStorage.setItem("wpcaptcha_locks_stats", "enabled");
  }

  if (window.localStorage.getItem("wpcaptcha_fails_stats") == null) {
    window.localStorage.setItem("wpcaptcha_fails_stats", "enabled");
  }

  if ($("#country_blocking_mode").val() != "none") {
    $(".country-blocking-wrapper").show();
    if ($("#country_blocking_mode").val() == "whitelist") {
      $(".country-blocking-label").html("Allowed Countries");
    } else {
      $(".country-blocking-label").html("Blocked Countries");
    }
  } else {
    $(".country-blocking-wrapper").hide();
  }

  $("#country_blocking_mode").on("change", function () {
    if ($("#country_blocking_mode").val() != "none") {
      $(".country-blocking-wrapper").show();
      if ($("#country_blocking_mode").val() == "whitelist") {
        $(".country-blocking-label").html("Allowed Countries");
      } else {
        $(".country-blocking-label").html("Blocked Countries");
      }
    } else {
      $(".country-blocking-wrapper").hide();
    }
  });

  $("#wpcaptcha_run_tests").on("click", function (e) {
    e.preventDefault();
    $(this).blur();

    wpcaptcha_swal.fire({
      title: "Running tests",
      text: " ",
      type: false,
      allowOutsideClick: false,
      allowEscapeKey: false,
      allowEnterKey: false,
      showConfirmButton: false,
      imageUrl: wpcaptcha_vars.icon_url,
      onOpen: () => {
        $(wpcaptcha_swal.getImage()).addClass("wpcaptcha_rotating");
      },
      imageWidth: 58,
      imageHeight: 58,
      imageAlt: "Running Tests",
    });

    $.ajax({
      url: ajaxurl,
      data: {
        action: "wpcaptcha_run_tool",
        _ajax_nonce: wpcaptcha_vars.run_tool_nonce,
        tool: "login_tests",
      },
    })
      .done(function (data) {
        if (data.success) {
          wpcaptcha_swal.fire({
            title: "Test Completed",
            text: data.data.message,
            type: data.data.pass ? "success" : "error",
            showConfirmButton: true,
          });
        } else {
          wpcaptcha_swal.fire({
            type: "error",
            title: wpcaptcha_vars.undocumented_error,
          });
        }
      })
      .fail(function (data) {
        wpcaptcha_swal.fire({
          type: "error",
          title: wpcaptcha_vars.undocumented_error,
        });
      });
  });

  $("#wpcaptcha_recovery_url_show").on("click", function (e) {
    e.preventDefault();
    $(this).blur();

    wpcaptcha_swal.fire({
      title: "Recovery URL",
      html: "<strong id='wpcaptcha_recovery_url'></strong><br /><br /><button class='button button-primary' id='wpcaptcha_recovery_url_reset'>Reset Recovery URL</button>",
      type: false,
      allowOutsideClick: true,
      allowEscapeKey: true,
      allowEnterKey: true,
      showConfirmButton: true,
    });

    get_recovery_url(false);
  });

  $(".settings_page_wpcaptcha").on("click", "#wpcaptcha_recovery_url_reset", function (e) {
    $(this).blur();
    $("#wpcaptcha_recovery_url").html('<img src="' + wpcaptcha_vars.icon_url + '" />');
    get_recovery_url(true);
  });

  function get_recovery_url(reset) {
    $.post({
      url: ajaxurl,
      data: {
        action: "wpcaptcha_run_tool",
        _ajax_nonce: wpcaptcha_vars.run_tool_nonce,
        tool: "recovery_url",
        reset: reset,
      },
    })
      .done(function (data) {
        $("#wpcaptcha_recovery_url").html(data.data.url);
      })
      .fail(function (data) {
        wpcaptcha_swal.fire({
          type: "error",
          title: wpcaptcha_vars.undocumented_error,
        });
      });
  }

  $(document).on("click", ".wpcaptcha-upload", function (e) {
    e.preventDefault();
    if ($(this).hasClass("wpcaptcha-free-images")) {
      getUploader("Select Image", $(this), true);
    } else {
      getUploader("Select Image", $(this), false);
    }
  });

  // pro dialog
  $('a.nav-tab-pro').on('click', function (e) {
    e.preventDefault();
    open_upsell('tab');
    return false;
  });

  $('#wpwrap').on('change', 'select', function(e) {
    option_class = $('#' + $(this).attr('id') + ' :selected').attr('class');
    if(option_class == 'pro-option'){
        option_text = $('#' + $(this).attr('id') + ' :selected').text();
        value = $('#' + $(this).attr('id') + ' :selected').attr('value');
        $(this).val('disabled');
        $(this).trigger('change');
        open_upsell($(this).attr('id') + '-' + value);
        $('.show_if_' + $(this).attr('id')).hide();
    }
  });

  $('#wpwrap').on('click', '.open-upsell', function(e) {
    e.preventDefault();
    feature = $(this).data('feature');
    $(this).blur();
    open_upsell(feature);

    return false;
  });

  $('#wpwrap').on('click', '.open-pro-dialog', function (e) {
    e.preventDefault();
    $(this).blur();

    pro_feature = $(this).data('pro-feature');
    if (!pro_feature) {
      pro_feature = $(this).parent('label').attr('for');
    }
    open_upsell(pro_feature);

    return false;
  });

  $('#wpcaptcha-pro-dialog').dialog({
    dialogClass: 'wp-dialog wpcaptcha-pro-dialog',
    modal: true,
    resizable: false,
    width: 850,
    height: 'auto',
    show: 'fade',
    hide: 'fade',
    close: function (event, ui) {},
    open: function (event, ui) {
      $(this).siblings().find('span.ui-dialog-title').html('WP Captcha PRO is here!');
      wpcaptcha_fix_dialog_close(event, ui);
    },
    autoOpen: false,
    closeOnEscape: true,
  });

  function clean_feature(feature) {
    feature = feature || 'free-plugin-unknown';
    feature = feature.toLowerCase();
    feature = feature.replace(' ', '-');

    return feature;
  }

  function open_upsell(feature) {
    feature = clean_feature(feature);

    $('#wpcaptcha-pro-dialog').dialog('open');

    $('#wpcaptcha-pro-table .button-buy').each(function (ind, el) {
      tmp = $(el).data('href-org');
      tmp = tmp.replace('pricing-table', feature);
      $(el).attr('href', tmp);
    });
  } // open_upsell

  if (window.localStorage.getItem('wpcaptcha_upsell_shown') != 'true') {
    open_upsell('welcome');

    window.localStorage.setItem('wpcaptcha_upsell_shown', 'true');
    window.localStorage.setItem('wpcaptcha_upsell_shown_timestamp', new Date().getTime());
  }

  if (window.location.hash == '#open-pro-dialog') {
    open_upsell('url-hash');
    window.location.hash = '';
  }

  $('.install-wp301').on('click',function(e){
    e.preventDefault();

    if (!confirm('The free WP 301 Redirects plugin will be installed & activated from the official WordPress repository. Click OK to proceed.')) {
      return false;
    }

    jQuery('body').append('<div style="width:550px;height:450px; position:fixed;top:10%;left:50%;margin-left:-275px; color:#444; background-color: #fbfbfb;border:1px solid #DDD; border-radius:4px;box-shadow: 0px 0px 0px 4000px rgba(0, 0, 0, 0.85);z-index: 9999999;"><iframe src="' + wpcaptcha_vars.wp301_install_url + '" style="width:100%;height:100%;border:none;" /></div>');
    jQuery('#wpwrap').css('pointer-events', 'none');

    e.preventDefault();
    return false;
  });

  function wpcaptcha_fix_dialog_close(event, ui) {
    jQuery('.ui-widget-overlay').bind('click', function () {
      jQuery('#' + event.target.id).dialog('close');
    });
  } // wpcaptcha_fix_dialog_close
});
