(function($) {
"use strict";

window.etCore.versionRollback = {
  boot: function() {
    var _this = this;

    $(document).on('click', '.et-core-version-rollback-confirm', function (e) {
      var $a = $(this);

      e.preventDefault();

      _this.rollback($a.attr('href'));
    });
  },

  disableActions: function() {
    $('.et-core-version-rollback-modal .et-core-modal-action').addClass('et-core-disabled');
  },

  enableActions: function() {
    $('.et-core-version-rollback-modal .et-core-modal-action').removeClass('et-core-disabled');
  },

  removeActions: function() {
    $('.et-core-version-rollback-modal .et-core-modal-action').remove();
    $('.et-core-version-rollback-modal').addClass('et-core-modal-actionless');
  },

  rollback: function(url) {
    var success = (function(response) {
      etCore.modalContent( '<div class="et-core-loader et-core-loader-success"></div>', false, false, '#et-core-version-rollback-modal-content' );
      setTimeout(function() {
        window.location.reload();
      }, 2000);
    }).bind(this);

    var error = (function(response) {
      var data;
      if ( response.responseJSON !== undefined ) {
        data = response.responseJSON.data;
      } else {
        data = {
          errorIsUnrecoverable: false,
          error: etCoreVersionRollbackI18n.unknownError
        };
      }

      var removeTempContent = true;
      etCore.modalContent( '<div class="et-core-loader et-core-loader-fail"></div>', false, 2000, '#et-core-version-rollback-modal-content' );

      if (undefined !== typeof data.errorIsUnrecoverable && data.errorIsUnrecoverable) {
        removeTempContent = false;
        this.removeActions();
      }

      setTimeout(function() {
        var content = $('<div></div>').append($('<p></p>').html(data.error)).html();
        etCore.modalContent(content, true, removeTempContent, '#et-core-version-rollback-modal-content');
      }, 2000)
    }).bind(this);

    var complete = (function(response) {
      this.enableActions();
    }).bind(this);

    this.disableActions();
    etCore.modalContent( '<div class="et-core-loader"></div>', false, false, '#et-core-version-rollback-modal-content' );

    // The URL already includes action and nonce.
    $.ajax({
      type: 'POST',
      url: url,
      dataType: 'json',
      success: success,
      error: error,
      complete: complete
    });
  }
};

$(function() {
  window.etCore.versionRollback.boot();
});

})(jQuery);
