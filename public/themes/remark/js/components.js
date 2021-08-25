$.components.register("scrollable", {
  mode: "init",
  defaults: {
    namespace: "scrollable",
    contentSelector: "> [data-role='content']",
    containerSelector: "> [data-role='container']"
  },
  init: function(context) {
    if (!$.fn.asScrollable) return;
    var defaults = $.components.getDefaults("scrollable");

    $('[data-plugin="scrollable"]', context).each(function() {
      var options = $.extend({}, defaults, $(this).data());

      $(this).asScrollable(options);
    });
  }
});

$.components.register("animsition", {
  mode: "manual",
  defaults: {
    inClass: 'fade-in',
    outClass: 'fade-out',
    inDuration: 800,
    outDuration: 500,
    linkElement: '.animsition-link',
    loading: true,
    loadingParentElement: "body",
    loadingClass: "loader",
    loadingType: "default",
    timeout: false,
    timeoutCountdown: 5000,
    onLoadEvent: true,
    browser: ['animation-duration', '-webkit-animation-duration'],
    overlay: false,
    // random: true,
    overlayClass: 'animsition-overlay-slide',
    overlayParentElement: "body",

    inDefaults: [
      'fade-in',
      'fade-in-up-sm', 'fade-in-up', 'fade-in-up-lg',
      'fade-in-down-sm', 'fade-in-down', 'fade-in-down-lg',
      'fade-in-left-sm', 'fade-in-left', 'fade-in-left-lg',
      'fade-in-right-sm', 'fade-in-right', 'fade-in-right-lg',
      // 'overlay-slide-in-top', 'overlay-slide-in-bottom', 'overlay-slide-in-left', 'overlay-slide-in-right',
      'zoom-in-sm', 'zoom-in', 'zoom-in-lg'
    ],
    outDefaults: [
      'fade-out',
      'fade-out-up-sm', 'fade-out-up', 'fade-out-up-lg',
      'fade-out-down-sm', 'fade-out-down', 'fade-out-down-lg',
      'fade-out-left-sm', 'fade-out-left', 'fade-out-left-lg',
      'fade-out-right-sm', 'fade-out-right', 'fade-out-right-lg',
      // 'overlay-slide-out-top', 'overlay-slide-out-bottom', 'overlay-slide-out-left', 'overlay-slide-out-right'
      'zoom-out-sm', 'zoom-out', 'zoom-out-lg'
    ]
  },

  init: function(context, callback) {
    var options = $.components.getDefaults("animsition");

    if (options.random) {
      var li = options.inDefaults.length,
        lo = options.outDefaults.length;

      var ni = parseInt(li * Math.random(), 0),
        no = parseInt(lo * Math.random(), 0);

      options.inClass = options.inDefaults[ni];
      options.outClass = options.outDefaults[no];
    }

    var $this = $(".animsition", context);

    $this.animsition(options);

    $("." + options.loadingClass).addClass('loader-' + options.loadingType);

    if ($this.animsition('supportCheck', options)) {
      if ($.isFunction(callback)) {
        $this.one('animsition.end', function() {
          callback.call();
        });
      }

      return true;
    } else {
      if ($.isFunction(callback)) {
        callback.call();
      }
      return false;
    }
  }
});

$.components.register("slidePanel", {
  mode: "manual",
  defaults: {
    closeSelector: '.slidePanel-close',
    mouseDragHandler: '.slidePanel-handler',
    loading: {
      template: function(options) {
        return '<div class="' + options.classes.loading + '">' +
          '<div class="loader loader-default"></div>' +
          '</div>';
      },
      showCallback: function(options) {
        this.$el.addClass(options.classes.loading + '-show');
      },
      hideCallback: function(options) {
        this.$el.removeClass(options.classes.loading + '-show');
      }
    }
  }
});

$.components.register("switchery", {
  mode: "init",
  defaults: {
    color: $.colors("primary", 600)
  },
  init: function(context) {
    if (typeof Switchery === "undefined") return;

    var defaults = $.components.getDefaults("switchery");

    $('[data-plugin="switchery"]', context).each(function() {
      var options = $.extend({}, defaults, $(this).data());

      new Switchery(this, options);
    });
  }
});

$.components.register("panel", {
  api: function() {
    $(document).on('click.site.panel', '[data-toggle="panel-fullscreen"]', function(e) {
      e.preventDefault();
      var $this = $(this),
        $panel = $this.closest('.panel');

      var api = $panel.data('panel-api');
      api.toggleFullscreen();
    });

    $(document).on('click.site.panel', '[data-toggle="panel-collapse"]', function(e) {
      e.preventDefault();
      var $this = $(this),
        $panel = $this.closest('.panel');

      var api = $panel.data('panel-api');
      api.toggleContent();
    });

    $(document).on('click.site.panel', '[data-toggle="panel-close"]', function(e) {
      e.preventDefault();
      var $this = $(this),
        $panel = $this.closest('.panel');

      var api = $panel.data('panel-api');
      api.close();
    });

    $(document).on('click.site.panel', '[data-toggle="panel-refresh"]', function(e) {
      e.preventDefault();
      var $this = $(this);
      var $panel = $this.closest('.panel');

      var api = $panel.data('panel-api');
      var callback = $this.data('loadCallback');

      if ($.isFunction(window[callback])) {
        api.load(e.target, window[callback]);
      } else {
        api.load(e.target);
      }
    });
  },

  init: function(context) {
    $('.panel', context).each(function() {
      var $this = $(this);

      var isFullscreen = false;
      var isClose = false;
      var isCollapse = false;
      var isLoading = false;

      var $fullscreen = $this.find('[data-toggle="panel-fullscreen"]');
      var $collapse = $this.find('[data-toggle="panel-collapse"]');
      var $loading;
      var self = this;

      if ($this.hasClass('is-collapse')) {
        isCollapse = true;
      }

      var api = {
        load: function(elem, callback) {
          var type = $this.data('loader-type');
          if (!type) {
            type = 'default';
          }

          $loading = $('<div class="panel-loading">' +
            '<div class="loader loader-' + type + '"></div>' +
            '</div>');

          $loading.appendTo($this);

          $this.addClass('is-loading');
          $this.trigger('loading.uikit.panel');
          isLoading = true;

          if ($.isFunction(callback)) {
            callback.call(self, elem, this.done);
          }
        },
        done: function() {
          if (isLoading === true) {
            $loading.remove();
            $this.removeClass('is-loading');
            $this.trigger('loading.done.uikit.panel');
          }
        },
        toggleContent: function() {
          if (isCollapse) {
            this.showContent();
          } else {
            this.hideContent();
          }
        },

        showContent: function() {
          if (isCollapse !== false) {
            $this.removeClass('is-collapse');

            if ($collapse.hasClass('wb-plus')) {
              $collapse.removeClass('wb-plus').addClass('wb-minus');
            }

            $this.trigger('shown.uikit.panel');

            isCollapse = false;
          }
        },

        hideContent: function() {
          if (isCollapse !== true) {
            $this.addClass('is-collapse');

            if ($collapse.hasClass('wb-minus')) {
              $collapse.removeClass('wb-minus').addClass('wb-plus');
            }

            $this.trigger('hidden.uikit.panel');
            isCollapse = true;
          }
        },

        toggleFullscreen: function() {
          if (isFullscreen) {
            this.leaveFullscreen();
          } else {
            this.enterFullscreen();
          }
        },
        enterFullscreen: function() {
          if (isFullscreen !== true) {
            $this.addClass('is-fullscreen');

            if ($fullscreen.hasClass('wb-expand')) {
              $fullscreen.removeClass('wb-expand').addClass('wb-contract');
            }

            $this.trigger('enter.fullscreen.uikit.panel');
            isFullscreen = true;
          }
        },
        leaveFullscreen: function() {
          if (isFullscreen !== false) {
            $this.removeClass('is-fullscreen');

            if ($fullscreen.hasClass('wb-contract')) {
              $fullscreen.removeClass('wb-contract').addClass('wb-expand');
            }

            $this.trigger('leave.fullscreen.uikit.panel');
            isFullscreen = false;
          }
        },
        toggle: function() {
          if (isClose) {
            this.open();
          } else {
            this.close();
          }
        },
        open: function() {
          if (isClose !== false) {
            $this.removeClass('is-close');
            $this.trigger('open.uikit.panel');

            isClose = false;
          }
        },
        close: function() {
          if (isClose !== true) {

            $this.addClass('is-close');
            $this.trigger('close.uikit.panel');

            isClose = true;
          }
        }
      };

      $this.data('panel-api', api);
    });
  }
});

var panelRefreshCallback = function (elem, panelCallback) {
    var url = elem.getAttribute('data-url');
    if ( ! url) {
        panelCallback();
        return false;
    }
    $.ajax({
        url: url,
        type: 'GET',
        success: function(result) {
            if (result.response) {
                $(elem).parents('.panel').children('.panel-body').html(result.response);
            }
            panelCallback();
        },
        error: function(result) {
            alert('Smth went wrong...');
            panelCallback();
        }
    });
}
$.components.register("toastr", {
  mode: "api",
  api: function() {
    if (typeof toastr === "undefined") return;
    var defaults = $.components.getDefaults("toastr");

    $(document).on('click.site.toastr', '[data-plugin="toastr"]', function(e) {
      e.preventDefault();

      var $this = $(this);
      var options = $.extend(true, {}, defaults, $this.data());
      var message = options.message || '';
      var type = options.type || "info";
      var title = options.title || undefined;

      switch (type) {
        case "success":
          toastr.success(message, title, options);
          break;
        case "warning":
          toastr.warning(message, title, options);
          break;
        case "error":
          toastr.error(message, title, options);
          break;
        case "info":
          toastr.info(message, title, options);
          break;
        default:
          toastr.info(message, title, options);
      }
    });
  }
});

toastr.options = {
  "closeButton": false,
  "debug": false,
  "newestOnTop": true,
  "progressBar": false,
  "positionClass": "toast-top-center",
  "preventDuplicates": true,
  "onclick": null,
  "showDuration": "100",
  "hideDuration": "100",
  "timeOut": "5000",
  "extendedTimeOut": "1000",
  "showEasing": "swing",
  "hideEasing": "linear",
  "showMethod": "slideDown",
  "hideMethod": "fadeOut"
}
//# sourceMappingURL=components.js.map
