(function(window, document, $) {
  'use strict';

  $.site.menu = {
    speed: 250,
    accordion: true, // A setting that changes the collapsible behavior to expandable instead of the default accordion style

    init: function() {
      this.$instance = $('.site-menu');

      if (this.$instance.length === 0) {
        return;
      }

      this.bind();
    },

    bind: function() {
      var self = this;

      this.$instance.on('mouseenter.site.menu', '.site-menu-item', function() {
        var $item = $(this);
        if ($.site.menubar.folded === true && $item.is('.has-sub') && $item.parent('.site-menu').length > 0) {
          var $sub = $item.children('.site-menu-sub');
          self.position($item, $sub);
        }

        $item.addClass('hover');
      }).on('mouseleave.site.menu', '.site-menu-item', function() {
        var $item = $(this);
        if ($.site.menubar.folded === true && $item.is('.has-sub') && $item.parent('.site-menu').length > 0) {
          $item.children('.site-menu-sub').css("max-height", "");
        }

        $item.removeClass('hover');
      }).on('deactive.site.menu', '.site-menu-item.active', function(e) {
        var $item = $(this);

        $item.removeClass('active');

        e.stopPropagation();
      }).on('active.site.menu', '.site-menu-item', function(e) {
        var $item = $(this);

        $item.addClass('active');

        e.stopPropagation();
      }).on('open.site.menu', '.site-menu-item', function(e) {
        var $item = $(this);

        self.expand($item, function() {
          $item.addClass('open');
        });

        if (self.accordion) {
          $item.siblings('.open').trigger('close.site.menu');
        }

        e.stopPropagation();
      }).on('close.site.menu', '.site-menu-item.open', function(e) {
        var $item = $(this);

        self.collapse($item, function() {
          $item.removeClass('open');
        });

        e.stopPropagation();
      }).on('click.site.menu ', '.site-menu-item', function(e) {
        if ($(this).is('.has-sub') && $(e.target).closest('.site-menu-item').is(this)) {
          if ($(this).is('.open')) {
            $(this).trigger('close.site.menu');
          } else {
            $(this).trigger('open.site.menu');
          }
        } else {
          if (!$(this).is('.active')) {
            $(this).siblings('.active').trigger('deactive.site.menu');
            $(this).trigger('active.site.menu');
          }
        }

        e.stopPropagation();
      }).on('tap.site.menu', '> .site-menu-item > a', function() {
        var link = $(this).attr('href');

        if (link) {
          window.location = link;
        }
      }).on('touchend.site.menu', '> .site-menu-item > a', function(e) {
        var $item = $(this).parent('.site-menu-item');

        if ($.site.menubar.folded === true) {
          if ($item.is('.has-sub') && $item.parent('.site-menu').length > 0) {
            $item.siblings('.hover').removeClass('hover');

            if ($item.is('.hover')) {
              $item.removeClass('hover');
            } else {
              $item.addClass('hover');
            }
          }
        }
      }).on('scroll.site.menu', '.site-menu-sub', function(e) {
        e.stopPropagation();
      });
    },

    collapse: function($item, callback) {
      var self = this;
      var $sub = $item.children('.site-menu-sub');

      $sub.show().slideUp(this.speed, function() {
        $(this).css('display', '');

        $(this).find('> .site-menu-item').removeClass('is-shown');

        if (callback) {
          callback();
        }

        self.$instance.trigger('collapsed.site.menu');
      });
    },

    expand: function($item, callback) {
      var self = this;
      var $sub = $item.children('.site-menu-sub');
      var $children = $sub.children('.site-menu-item').addClass('is-hidden');

      $sub.hide().slideDown(this.speed, function() {
        $(this).css('display', '');

        if (callback) {
          callback();
        }

        self.$instance.trigger('expanded.site.menu');
      });

      setTimeout(function() {
        $children.addClass('is-shown');
        $children.removeClass('is-hidden');
      }, 0);
    },

    refresh: function() {
      this.$instance.find('.open').filter(':not(.active)').removeClass('open');
    },

    position: function($item, $dropdown) {
      var offsetTop = $item.position().top,
        dropdownHeight = $dropdown.outerHeight(),
        menubarHeight = $.site.menubar.$instance.outerHeight(),
        itemHeight = $item.find("> a").outerHeight();

      $dropdown.removeClass('site-menu-sub-up').css('max-height', "");

      //if (offsetTop + dropdownHeight > menubarHeight) {
      if (offsetTop > menubarHeight / 2) {
        $dropdown.addClass('site-menu-sub-up');

        if ($.site.menubar.foldAlt) {
          offsetTop = offsetTop - itemHeight;
        }
        //if(dropdownHeight > offsetTop + itemHeight) {
        $dropdown.css('max-height', offsetTop + itemHeight);
        //}
      } else {
        if ($.site.menubar.foldAlt) {
          offsetTop = offsetTop + itemHeight;
        }
        $dropdown.removeClass('site-menu-sub-up');
        $dropdown.css('max-height', menubarHeight - offsetTop);
      }
      //}
    }
  };
})(window, document, jQuery);

(function(window, document, $) {
  'use strict';

  var $body = $('body'),
    $html = $('html');

  $.site.menubar = {
    opened: null,
    folded: null,
    top: false,
    foldAlt: false,
    $instance: null,
    auto: true,

    init: function() {
      $html.removeClass('css-menubar').addClass('js-menubar');

      this.$instance = $(".site-menubar");

      if (this.$instance.length === 0) {
        return;
      }

      var self = this;

      if ($body.is('.site-menubar-top')) {
        this.top = true;
      }

      if ($body.is('.site-menubar-fold-alt')) {
        this.foldAlt = true;
      }

      if ($body.data('autoMenubar') === false || $body.is('.site-menubar-keep')) {
        if ($body.hasClass('site-menubar-fold')) {
          this.auto = 'fold';
        } else if ($body.hasClass('site-menubar-unfold')) {
          this.auto = 'unfold';
        }
      }

      this.$instance.on('changed.site.menubar', function() {
        self.update();
      });

      this.change();
    },

    change: function() {
      var breakpoint = Breakpoints.current();
      if (this.auto !== true) {
        switch (this.auto) {
          case 'fold':
            this.reset();
            if (breakpoint.name == 'xs') {
              this.hide();
            } else {
              this.fold();
            }
            return;
          case 'unfold':
            this.reset();
            if (breakpoint.name == 'xs') {
              this.hide();
            } else {
              this.unfold();
            }
            return;
        }
      }

      this.reset();

      if (breakpoint) {
        switch (breakpoint.name) {
          case 'lg':
            this.unfold();
            break;
          case 'md':
          case 'sm':
            this.fold();
            break;
          case 'xs':
            this.hide();
            break;
        }
      }
    },

    animate: function(doing, callback) {
      var self = this;
      $body.addClass('site-menubar-changing');

      doing.call(self);
      this.$instance.trigger('changing.site.menubar');

      setTimeout(function() {
        callback.call(self);
        $body.removeClass('site-menubar-changing');

        self.$instance.trigger('changed.site.menubar');
      }, 500);
    },

    reset: function() {
      this.opened = null;
      this.folded = null;
      $body.removeClass('site-menubar-hide site-menubar-open site-menubar-fold site-menubar-unfold');
      $html.removeClass('disable-scrolling');
    },

    open: function() {
      if (this.opened !== true) {
        this.animate(function() {
          $body.removeClass('site-menubar-hide').addClass('site-menubar-open site-menubar-unfold');
          this.opened = true;

          $html.addClass('disable-scrolling');

        }, function() {
          this.scrollable.enable();
        });
      }
    },

    hide: function() {
      this.hoverscroll.disable();

      if (this.opened !== false) {
        this.animate(function() {

          $html.removeClass('disable-scrolling');
          $body.removeClass('site-menubar-open').addClass('site-menubar-hide site-menubar-unfold');
          this.opened = false;

        }, function() {
          this.scrollable.enable();
        });
      }
    },

    unfold: function() {
      this.hoverscroll.disable();

      if (this.folded !== false) {
        this.animate(function() {
          $body.removeClass('site-menubar-fold').addClass('site-menubar-unfold');
          this.folded = false;

        }, function() {
          this.scrollable.enable();

          if (this.folded !== null) {
            $.site.resize();
          }
        });
      }
    },

    fold: function() {
      this.scrollable.disable();

      if (this.folded !== true) {
        this.animate(function() {

          $body.removeClass('site-menubar-unfold').addClass('site-menubar-fold');
          this.folded = true;

        }, function() {
          this.hoverscroll.enable();

          if (this.folded !== null) {
            $.site.resize();
          }
        });
      }
    },

    toggle: function() {
      var breakpoint = Breakpoints.current();
      var folded = this.folded;
      var opened = this.opened;

      switch (breakpoint.name) {
        case 'lg':
          if (folded === null || folded === false) {
            this.fold();
          } else {
            this.unfold();
          }
          break;
        case 'md':
        case 'sm':
          if (folded === null || folded === true) {
            this.unfold();
          } else {
            this.fold();
          }
          break;
        case 'xs':
          if (opened === null || opened === false) {
            this.open();
          } else {
            this.hide();
          }
          break;
      }
    },

    update: function() {
      this.scrollable.update();
      this.hoverscroll.update();
    },

    scrollable: {
      api: null,
      native: false,
      init: function() {
        // if (/Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent)) {
        //   this.native = true;
        //   $body.addClass('site-menubar-native');
        //   return;
        // }

        if ($body.is('.site-menubar-native')) {
          this.native = true;
          return;
        }

        this.api = $.site.menubar.$instance.children('.site-menubar-body').asScrollable({
          namespace: 'scrollable',
          skin: 'scrollable-inverse',
          direction: 'vertical',
          contentSelector: '>',
          containerSelector: '>'
        }).data('asScrollable');
      },

      update: function() {
        if (this.api) {
          this.api.update();
        }
      },

      enable: function() {
        if (this.native) {
          return;
        }
        if (!this.api) {
          this.init();
        }
        if (this.api) {
          this.api.enable();
        }
      },

      disable: function() {
        if (this.api) {
          this.api.disable();
        }
      }
    },

    hoverscroll: {
      api: null,

      init: function() {
        this.api = $.site.menubar.$instance.children('.site-menubar-body').asHoverScroll({
          namespace: 'hoverscorll',
          direction: 'vertical',
          list: '.site-menu',
          item: '> li',
          exception: '.site-menu-sub',
          fixed: false,
          boundary: 100,
          onEnter: function() {
            //$(this).siblings().removeClass('hover');
            //$(this).addClass('hover');
          },
          onLeave: function() {
            //$(this).removeClass('hover');
          }
        }).data('asHoverScroll');
      },

      update: function() {
        if (this.api) {
          this.api.update();
        }
      },

      enable: function() {
        if (!this.api) {
          this.init();
        }
        if (this.api) {
          this.api.enable();
        }
      },

      disable: function() {
        if (this.api) {
          this.api.disable();
        }
      }
    }
  };
})(window, document, jQuery);

(function(window, document, $) {
  'use strict';

  var $body = $('body'),
    $html = $('html');

  $.site.gridmenu = {
    opened: false,

    init: function() {
      this.$instance = $('.site-gridmenu');

      if (this.$instance.length === 0) {
        return;
      }

      this.bind();
    },

    bind: function() {
      var self = this;

      $(document).on('click', '[data-toggle="gridmenu"]', function() {
        var $this = $(this);

        if (self.opened) {
          self.close();

          $this.removeClass('active')
            .attr('aria-expanded', false);

        } else {
          self.open();

          $this.addClass('active')
            .attr('aria-expanded', true);
        }
      });
    },

    open: function() {
      var self = this;

      if (this.opened !== true) {
        this.animate(function() {
          self.opened = true;


          self.$instance.addClass('active');

          $('[data-toggle="gridmenu"]').addClass('active')
            .attr('aria-expanded', true);

          $body.addClass('site-gridmenu-active');
          $html.addClass('disable-scrolling');
        }, function() {
          this.scrollable.enable();
        });
      }
    },

    close: function() {
      var self = this;

      if (this.opened === true) {
        this.animate(function() {
          self.opened = false;

          self.$instance.removeClass('active');

          $('[data-toggle="gridmenu"]').addClass('active')
            .attr('aria-expanded', true);

          $body.removeClass('site-gridmenu-active');
          $html.removeClass('disable-scrolling');
        }, function() {
          this.scrollable.disable();
        });
      }
    },

    toggle: function() {
      if (this.opened) {
        this.close();
      } else {
        this.open();
      }
    },

    animate: function(doing, callback) {
      var self = this;

      doing.call(self);
      this.$instance.trigger('changing.site.gridmenu');

      setTimeout(function() {
        callback.call(self);

        self.$instance.trigger('changed.site.gridmenu');
      }, 500);
    },

    scrollable: {
      api: null,
      init: function() {
        this.api = $.site.gridmenu.$instance.asScrollable({
          namespace: 'scrollable',
          skin: 'scrollable-inverse',
          direction: 'vertical',
          contentSelector: '>',
          containerSelector: '>'
        }).data('asScrollable');
      },

      update: function() {
        if (this.api) {
          this.api.update();
        }
      },

      enable: function() {
        if (!this.api) {
          this.init();
        }
        if (this.api) {
          this.api.enable();
        }
      },

      disable: function() {
        if (this.api) {
          this.api.disable();
        }
      }
    },
  };
})(window, document, jQuery);

(function(window, document, $) {
  'use strict';

  $.site.sidebar = {
    init: function() {
      if (typeof $.slidePanel === 'undefined') return;

      $(document).on('click', '[data-toggle="site-sidebar"]', function() {
        var $this = $(this);

        var direction = 'right';
        if ($('body').hasClass('site-menubar-flipped')) {
          direction = 'left';
        }

        var defaults = $.components.getDefaults("slidePanel");
        var options = $.extend({}, defaults, {
          direction: direction,
          skin: 'site-sidebar',
          dragTolerance: 80,
          template: function(options) {
            return '<div class="' + options.classes.base + ' ' + options.classes.base + '-' + options.direction + '">' +
              '<div class="' + options.classes.content + ' site-sidebar-content"></div>' +
              '<div class="slidePanel-handler"></div>' +
              '</div>';
          },
          afterLoad: function() {
            var self = this;
            this.$panel.find('.tab-pane').asScrollable({
              namespace: 'scrollable',
              contentSelector: "> div",
              containerSelector: "> div"
            });

            $.components.init('switchery', self.$panel);

            this.$panel.on('shown.bs.tab', function() {
              self.$panel.find(".tab-pane.active").asScrollable('update');
            });
          },
          beforeShow: function() {
            if (!$this.hasClass('active')) {
              $this.addClass('active');
            }
          },
          afterHide: function() {
            if ($this.hasClass('active')) {
              $this.removeClass('active');
            }
          }
        });

        if ($this.hasClass('active')) {
          $.slidePanel.hide();
        } else {
          var url = $this.data('url');
          if (!url) {
            url = $this.attr('href');
            url = url && url.replace(/.*(?=#[^\s]*$)/, '');
          }

          $.slidePanel.show({
            url: url
          }, options);
        }
      });

      $(document).on('click', '[data-toggle="show-chat"]', function() {
        $('#conversation').addClass('active');
      });


      $(document).on('click', '[data-toggle="close-chat"]', function() {
        $('#conversation').removeClass('active');
      });
    }
  };

})(window, document, jQuery);

//# sourceMappingURL=sections.js.map
