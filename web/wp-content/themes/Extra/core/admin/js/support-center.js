(function($) {
  'use strict';

  var docPlayer;
  var resizeTimer;
  var showHideDelay                    = 300;
  var removeDelay                      = 500;
  var logViewers                       = [];
  var supportUserID                    = etSupportCenter.supportUserID || null;
  var debugLogViewer                   = window.wp && window.wp.codeEditor;
  var $save_message                    = $('#epanel-ajax-saving');
  var $etSystemStatusTable             = $('.et_system_status');
  var $etSupportUserToggle             = $('.et_support_user_toggle .et_pb_yes_no_button');
  var $et_documentation_videos_list_li = $('.et_documentation_videos_list li');
  var $modalSafeModeWarningTemplate    = $('#et-ajax-safe-mode-template').html();

  function confirmClipboardCopy() {
    $save_message.addClass('success-animation').fadeIn('fast');
    $save_message.fadeOut('slow');
  }

  // Remote Access: Toggle ET Support User On/Off
  function supportUserActivationToggle($toggle, newState, silentMode) {

    // If Silent Mode is `true` then we'll run AJAX without rendering display changes
    silentMode = silentMode || false;

    if (typeof newState === 'undefined') {
      return;
    }

    var postData = {
      action: 'et_support_user_update',
      nonce:  etSupportCenter.nonce
    };

    switch (newState) {
      case 'activate':
        postData.support_update = 'activate';
        break;
      case 'deactivate':
        postData.support_update = 'deactivate';
        break;
      default:
        return;
    }

    // Ajax toggle ET Support User
    jQuery.ajax({
      type:       'POST',
      data:       postData,
      dataType:   'json',
      url:        etSupportCenter.ajaxURL,
      action:     'support_user_update_via_ajax',
      beforeSend: function(xhr) {
        // Don't execute DOM changes in Silent Mode
        if (silentMode) {
          return;
        }

        $('.et-remote-access-error').first().hide(showHideDelay);
        $save_message.addClass('et_loading').removeClass('success-animation');
        $save_message.fadeIn('fast');
      },
      success:    function(response) {
        // Don't execute DOM changes in Silent Mode
        if (silentMode) {
          return;
        }

        $save_message.removeClass('et_loading').removeClass('success-animation');

        setTimeout(function() {
          $save_message.fadeOut('slow');
        }, removeDelay);
        var $msgExpiry = $('.et-support-user-expiry').first();
        if ('activate' === postData.support_update) {
          if (response.error) {
            $('.et-remote-access-error').first().text(response.error).show(showHideDelay);
            return;
          }
          $('#et-remote-access-error').remove();
          $toggle.removeClass('et_pb_off_state').addClass('et_pb_on_state');
          $msgExpiry.attr('data-expiry', response.expiry);
          supportUserTimeToExpiry();
          $msgExpiry.show(showHideDelay);
          $('.et-support-user-elevated').show(showHideDelay);
          $('.card.et_remote_access .et_card_cta').append(
            $('<a>')
              .attr({
                'class':      'copy_support_token',
                'data-token': response.token
              })
              .text('Copy Support Token')
          );
        } else if ('deactivate' === postData.support_update) {
          // First switch & hide the "elevated" toggle
          // (not a click event because we don't need to trigger AJAX)
          $('.et-support-user-elevated').hide(showHideDelay);
          $('.et_support_user_elevated_toggle .et_pb_yes_no_button').removeClass('et_pb_on_state').addClass('et_pb_off_state');
          // Now clean up the Remote Access toggle
          $msgExpiry.hide(showHideDelay);
          $toggle.removeClass('et_pb_on_state').addClass('et_pb_off_state');
          $('.copy_support_token').fadeOut('slow');
          setTimeout(function() {
            $('.copy_support_token').remove();
          }, removeDelay);
        }
        $save_message.addClass('success-animation');
      }
    }).fail(function(data) {
      console.log(data.responseText);
    });
  }

  // Remote Access: Calculate of Time To Auto-Deactivation
  function supportUserTimeToExpiry() {
    if (! $('.et_support_user_toggle .et_pb_on_state').length) {
      return;
    }

    var $supportUserExpiry = $('.et-support-user-expiry').first();
    var expiry             = parseInt($supportUserExpiry.attr('data-expiry'));
    var timeToExpiry       = (expiry - (new Date().getTime() / 1000));
    var $timer             = $supportUserExpiry.find('.support-user-time-to-expiry').first();
    var timerContent       = '';
    var days               = 0;
    var hours              = 0;
    var minutes            = 0;

    if (30 >= timeToExpiry) {
      // Don't bother calculating; expiration will happen before the next check, so let's trigger deactivation now.
      $timer.html('0 minutes');
      // Go ahead and turn off the user (don't need to wait for WP Cron)
      $etSupportUserToggle.trigger('click');
      return;
    }

    days         = parseInt(timeToExpiry / 86400);
    days         = days > 0 ? days : 0;
    timeToExpiry = timeToExpiry % 86400;

    hours        = parseInt(timeToExpiry / 3600);
    hours        = hours > 0 ? hours : 0;
    timeToExpiry = timeToExpiry % 3600;

    minutes = parseInt(timeToExpiry / 60);
    minutes = minutes > 0 ? minutes : 0;

    if (0 < days) {
      timerContent = timerContent + days + (1 < days ? ' days, ' : ' day, ');
    }

    if (0 < hours) {
      timerContent = timerContent + hours + (1 < hours ? ' hours, ' : ' hour, ');
    }

    timerContent = timerContent + minutes + (1 !== minutes ? ' minutes' : ' minute');

    $timer.html(timerContent);
  }

  // Documentation: Recalculate video dimensions (typically on viewport resize)
  function et_core_correct_video_proportions() {
    var parentHeight = (parseInt($('.et_docs_videos').first().width()) * .5625) + 'px';
    $('.et_docs_videos .wrapper').css('max-height', parentHeight);
    $('.et_docs_videos iframe').css('max-height', parentHeight);
  }

  // Documentation: Initialize YouTube Iframe player
  function loadYouTubeIframe() {
    if (('undefined' !== typeof YT) && YT && YT.Player) {
      // Default video: 'Getting Started With The Divi Builder'
      var firstVideo      = 'T-Oe01_J62c';
      var $firstVideoItem = $('.et_docs_videos li:first-of-type');

      // If the Documentation videos list has YouTube IDs, grab the first one
      if ($firstVideoItem.length > 0 && $firstVideoItem[0].hasAttribute('data-ytid')) {
        firstVideo = $firstVideoItem.attr('data-ytid');
      }

      docPlayer = new YT.Player('et_documentation_player', {
        videoId:  firstVideo,
        height:   '360',
        width:    '640',
        showinfo: 0,
        controls: 0,
        rel:      0
      });
      et_core_correct_video_proportions();
    } else {
      setTimeout(loadYouTubeIframe, 100);
    }
  }

  // Safe Mode: Activate/Deactivate
  function toggleETSafeMode($toggle) {
    var postData = {
      action: 'et_safe_mode_update',
      nonce:  etSupportCenter.nonce
    };

    if ($toggle.hasClass('et_pb_off_state')) {
      postData.support_update = 'activate';
    } else if ($toggle.hasClass('et_pb_on_state') || $toggle.hasClass('et-safe-mode-indicator') || $toggle.hasClass('et-core-modal-action')) {
      postData.support_update = 'deactivate';
    } else {
      return;
    }

    if ('activate' === postData.support_update) {
      var safeModeProduct = $toggle.parents('#et_card_safe_mode').data('et-product');

      // Continue only if the product is in our allowlist
      switch (safeModeProduct) {
        case 'divi_builder_plugin':
        case 'divi_theme':
        case 'extra_theme':
        case 'monarch_plugin':
        case 'bloom_plugin':
          postData.product = safeModeProduct;
          break;
        default:
          return;
      }
    }

    // Ajax toggle Safe Mode
    jQuery.ajax({
      type:       'POST',
      data:       postData,
      dataType:   'json',
      url:        etSupportCenter.ajaxURL,
      action:     'safe_mode_update_via_ajax',
      beforeSend: function(xhr) {
        $('.et-core-safe-mode-block-modal').removeClass('et-core-active');
        $save_message.addClass('et_loading').removeClass('success-animation');
        $save_message.fadeIn('fast');
      },
      success:    function(response) {
        $save_message.removeClass('et_loading').addClass('success-animation');
        var $msgExpiry = $('.et-support-user-expiry').first();
        if ('activate' === postData.support_update) {
          $('.et_safe_mode_toggle .et_pb_yes_no_button').removeClass('et_pb_off_state').addClass('et_pb_on_state');
        } else if ('deactivate' === postData.support_update) {
          $('.et_safe_mode_toggle .et_pb_yes_no_button').removeClass('et_pb_on_state').addClass('et_pb_off_state');
          $('.et-safe-mode-indicator').fadeOut('slow');
          setTimeout(function() {
            $('.et-safe-mode-indicator').remove();
            $('.wp-admin').removeClass('et-safe-mode-active');
          }, removeDelay);
        }
        setTimeout(function() {
          $save_message.fadeOut('slow');
          window.location.reload(true);
        }, removeDelay);
      }
    }).fail(function(data) {
      console.log(data.responseText);
      $save_message.fadeOut('slow');
    });
  }

  // Safe Mode: Interrupt Actions when Safe Mode is Active
  function preventActionWhenSafeModeActive() {
    $('body').append($modalSafeModeWarningTemplate);
    $('.et-core-safe-mode-block-modal').addClass('et-core-active');
    $(window).trigger('et-core-modal-active');
  }

  // Logs: Add CodeMirror Instance with Custom Formatting Rules
  function addLogViewerInstance(codeEditor, $element, config) {
    if (! $element || $element.length === 0) {
      return;
    }
    var instance = codeEditor.initialize($element, {
      codemirror: config,
    });
    if (instance && instance.codemirror) {
      logViewers.push(instance.codemirror);
    }
  }

  // Dismiss Card in the Support Center
  function dismissCard($button) {
    const postData = {
      action:   'et_dismiss_support_center_card',
      nonce:    etSupportCenter.nonce,
      product:  $button.data('product'),
      card_key: $button.data('key'),
    };

    // Dismiss the Card via AJAX
    jQuery.ajax({
      type:       'POST',
      data:       postData,
      dataType:   'json',
      url:        etSupportCenter.ajaxURL,
      beforeSend: function(xhr) {
        $button.prop('disabled', true);
        $save_message.addClass('et_loading').removeClass('success-animation');
        $save_message.fadeIn('fast');
      },
      success:    function(response) {
        $button.parent().remove();
        $save_message.removeClass('et_loading').addClass('success-animation');

        setTimeout(function() {
          $save_message.fadeOut('slow');
        }, removeDelay);
      },
    }).fail(function(data) {
      $button.prop('disabled', false);
      console.log(data.responseText);
      $save_message.fadeOut('slow');
    });
  }

  $(window).on('resize', function() {
    resizeTimer = _.debounce(et_core_correct_video_proportions(), showHideDelay);
  });

  $(function() {
    /**
     * Support Center :: System Status
     */

    // System Status: display message if all checks passed
    if (0 === $('.et-system-status-report').children(':not(.et_system_status_pass)').length) {
      $('.et-system-status-congratulations').show(showHideDelay);
    }

    // System Status: Show Full Report
    $('.full_report_show').on('click', function() {
      $etSystemStatusTable.find('.et_system_status_pass').show(showHideDelay);
      $etSystemStatusTable.removeClass('summary').addClass('full');
    });

    // System Status: Show Summary Report
    $('.full_report_hide').on('click', function() {
      $etSystemStatusTable.find('.et_system_status_pass').hide(showHideDelay);
      $etSystemStatusTable.addClass('summary').removeClass('full');
    });

    // System Status: Copy Full Report to Clipboard
    $('.full_report_copy').on('click', function() {
      $('#et_system_status_plain').trigger('select');
      document.execCommand('copy');
      confirmClipboardCopy();
    });

    /**
     * Support Center :: Remote Access
     */
    if ($('.card.et_remote_access').length > 0) {
      // Remote Access: Initial Calculation of Time To Auto-Deactivation
      supportUserTimeToExpiry();

      // Remote Access: Recalculate Time To Auto-Deactivation (every 30 seconds)
      setInterval(supportUserTimeToExpiry, 30000);

      // Remote Access: Display Auto-Deactivation Countdown
      if ($etSupportUserToggle.hasClass('et_pb_on_state')) {
        $('.et-support-user-expiry').first().show(0);
      } else {
        // If the Support User account toggle is off, send a quick AJAX request to verify the account is deactivated
        supportUserActivationToggle($etSupportUserToggle, 'deactivate', true);
      }

      // Remote Access: Activate/Deactivate
      $etSupportUserToggle.on('click', function(e) {
        e.preventDefault();

        if ($etSupportUserToggle.hasClass('et_pb_off_state')) {
          supportUserActivationToggle($(this), 'activate');
        } else if ($etSupportUserToggle.hasClass('et_pb_on_state')) {
          supportUserActivationToggle($(this), 'deactivate');
        }
      });

      // Remote Access: Elevate/Reset Divi Support user role
      $('.et_support_user_elevated_toggle .et_pb_yes_no_button').on('click', function(e) {
        e.preventDefault();

        var $toggle = $(this);

        var postData = {
          action: 'et_support_user_update',
          nonce:  etSupportCenter.nonce
        };

        if ($toggle.hasClass('et_pb_off_state')) {
          postData.support_update = 'elevate';
        } else if ($toggle.hasClass('et_pb_on_state')) {
          postData.support_update = 'activate';
        } else {
          return;
        }

        // Ajax toggle ET Support User Admin Mode
        jQuery.ajax({
          type:       'POST',
          data:       postData,
          dataType:   'json',
          url:        etSupportCenter.ajaxURL,
          action:     'support_user_update_via_ajax',
          beforeSend: function(xhr) {
            $save_message.addClass('et_loading').removeClass('success-animation');
            $save_message.fadeIn('fast');
          },
          success:    function(response) {
            $save_message.removeClass('et_loading').removeClass('success-animation');

            setTimeout(function() {
              $save_message.fadeOut('slow');
            }, removeDelay);
            if ('elevate' === postData.support_update) {
              $toggle.removeClass('et_pb_off_state').addClass('et_pb_on_state');
            } else if ('activate' === postData.support_update) {
              $toggle.removeClass('et_pb_on_state').addClass('et_pb_off_state');
            }
            $save_message.addClass('success-animation');
          }
        }).fail(function(data) {
          console.log(data.responseText);
        });
      });

      // Remote Access: Copy Support Token to clipboard
      $('body').on('click', '.copy_support_token', function() {
        var token = $(this).attr('data-token');
        var $temp = $('<input>');
        $('body').append($temp);
        $temp.val(token).trigger('select');
        document.execCommand('copy');
        $temp.remove();
        confirmClipboardCopy();
      });
    }

    /**
     * Support Center :: Documentation & Help
     */
    if ($('body').find('[data-et-page="wp-admin-support-center"]').length > 0) {
      // Load the IFrame Player API code asynchronously.
      var tag            = document.createElement('script');
      tag.src            = 'https://www.youtube.com/iframe_api';
      var firstScriptTag = document.getElementsByTagName('script')[0];
      firstScriptTag.parentNode.insertBefore(tag, firstScriptTag);
      loadYouTubeIframe();
    }

    // Documentation & Help: YouTube Video Navigation
    $et_documentation_videos_list_li.on('click', function() {
      var $active = $(this);

      $et_documentation_videos_list_li.removeClass('active');
      $active.addClass('active');

      docPlayer.cueVideoById($active.attr('data-ytid'), 0, 'large');
    });

    /**
     * Support Center :: Safe Mode
     */

    if ($save_message.length === 0) {
      $('body.wp-admin').append(
        $('<div>').attr({ 'id': 'et-ajax-saving', 'class': 'et_loading' }).append(
          $('<img>').attr({ 'src': etSupportCenter.ajaxLoaderImg, 'alt': 'loading', 'id': 'loading' })
        )
      );
      $save_message = $('#et-ajax-saving');
    }

    // Safe Mode: Activate/Deactivate
    $('body').on('click', '.et-safe-mode-indicator', function(e) {
      e.preventDefault();
      var $toggle = $(this);
      toggleETSafeMode($toggle);
    });
    $('body').on('click', '.et_safe_mode_toggle .et_pb_yes_no_button', function(e) {
      e.preventDefault();
      var $toggle = $(this);
      toggleETSafeMode($toggle);
    });
    $('body').on('click', '.et-core-safe-mode-block-modal .et-core-modal-action', function(e) {
      e.preventDefault();
      var $toggle = $(this);
      toggleETSafeMode($toggle);
    });

    // Safe Mode: Interrupt Plugin/Theme Toggles
    $('body.et-safe-mode-active').on('click', '.theme .activate', function(e) {
      e.preventDefault();
      preventActionWhenSafeModeActive();
    });
    $('body.et-safe-mode-active').on('click', '.plugins .activate a', function(e) {
      e.preventDefault();
      preventActionWhenSafeModeActive();
    });
    $('body.et-safe-mode-active').on('click', '.plugins .deactivate a', function(e) {
      e.preventDefault();
      preventActionWhenSafeModeActive();
    });
    $('body.et-safe-mode-active.plugins-php').on('click', '.page-title-action', function(e) {
      e.preventDefault();
      preventActionWhenSafeModeActive();
    });

    // Safe Mode: Close Interrupt
    $('body').on('click', '>.et-core-safe-mode-block-modal .et-core-modal-close', function(e) {
      e.preventDefault();
      $('body>.et-core-safe-mode-block-modal').remove();
    });

    /**
     * Support Center :: Logs
     */

    // Logs: Initialize CodeMirror Rendering of Log File
    if (debugLogViewer && debugLogViewer.initialize && debugLogViewer.defaultSettings && debugLogViewer.defaultSettings.codemirror) {

      // User ET CodeMirror theme
      var configDebugLog = $.extend({}, debugLogViewer.defaultSettings.codemirror, {
        indentUnit:     2,
        tabSize:        2,
        mode:           'nginx',
        theme:          'et',
        scrollbarStyle: 'native',
        readOnly:       true,
        lineWrapping:   true
      });

      if ($('#et_logs_display').length > 0) {
        // Divi Theme
        addLogViewerInstance(debugLogViewer, $('#et_logs_display'), configDebugLog);
      }
    }

    // Logs: Copy Full WP_DEBUG Log to Clipboard
    $('.copy_debug_log').on('click', function() {
      $('#et_logs_recent').trigger('select');
      document.execCommand('copy');
      confirmClipboardCopy();
    });

    /**
     * Support Center :: Divi Hosting Card
     */

    // Dismiss Card from the Support Center
    $('.card.has-dismiss-button').on('click', '.et-dismiss-button', function(e) {
      const $toggle = $(this);

      dismissCard($toggle);
    });

    // Initialize Tippy when it's available
    if (typeof tippy !== 'undefined') {
      tippy('[data-tippy-content]', {
        arrow: tippy.roundArrow,
        theme: 'et-tippy',
      });
    }
  });
})(jQuery);
