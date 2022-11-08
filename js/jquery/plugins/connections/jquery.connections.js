/* jQuery Connections v1.0.1 https://github.com/musclesoft/jquery-connections */

(function($) {
    $.fn.connections = function(options) {
      if (options === "update") {
        return processConnections(update, this);
      } else if (options === "remove") {
        return processConnections(destroy, this);
      } else {
        options = $.extend(
          true,
          {
            borderClasses: {},
            class: "connection",
            css: {},
            from: this,
            tag: "connection",
            to: this,
            within: ":root"
          },
          options
        );
        connect(options);
        return this;
      }
    };
  
    $.event.special.connections = {
      teardown: function(namespaces) {
        processConnections(destroy, $(this));
      }
    };
  
    var connect = function(options) {
      var borderClasses = options.borderClasses;
      var tag = options.tag;
      var end1 = $(options.from);
      var end2 = $(options.to);
      var within = $(options.within);
      delete options.borderClasses;
      delete options.tag;
      delete options.from;
      delete options.to;
      delete options.within;
      within.each(function() {
        var container = this;
        var done = new Array();
        end1.each(function() {
          var node = this;
          done.push(this);
          end2.not(done).each(function() {
            createConnection(
              container,
              [node, this],
              tag,
              borderClasses,
              options
            );
          });
        });
      });
    };
  
    var createConnection = function(
      container,
      nodes,
      tag,
      borderClasses,
      options
    ) {
      var css = $.extend({ position: "absolute" }, options.css);
      var connection = $("<" + tag + "/>", options).css(css);
      connection.appendTo(container);
  
      var border_w = (connection.outerWidth() - connection.innerWidth()) / 2;
      var border_h = (connection.outerHeight() - connection.innerHeight()) / 2;
  
      if (border_w <= 0 && border_h <= 0) {
        border_w = border_h = 1;
      }
  
      var data = {
        borderClasses: borderClasses,
        border_h: border_h,
        border_w: border_w,
        node_from: $(nodes[0]),
        node_to: $(nodes[1]),
        nodes_dom: nodes,
        css: css
      };
  
      if ("none" === connection.css("border-top-style")) {
        data.css.borderStyle = "solid";
      }
      $.data(connection.get(0), "connection", data);
      $.data(connection.get(0), "connections", [connection.get(0)]);
      for (var i = 0; i < 2; i++) {
        var connections = connection.add($.data(nodes[i], "connections")).get();
        $.data(nodes[i], "connections", connections);
        if (connections.length == 1) {
          $(nodes[i]).on("connections.connections", false);
        }
      }
      update(connection.get(0));
    };
  
    var destroy = function(connection) {
      var nodes = $.data(connection, "connection").nodes_dom;
      for (var i = 0; i < 2; i++) {
        var connections = $($.data(nodes[i], "connections"))
          .not(connection)
          .get();
        $.data(nodes[i], "connections", connections);
      }
      $(connection).remove();
    };
  
    var getState = function(data) {
      data.rect_from = data.nodes_dom[0].getBoundingClientRect();
      data.rect_to = data.nodes_dom[1].getBoundingClientRect();
      var cached = data.cache;
      data.cache = [
        data.rect_from.top,
        data.rect_from.right,
        data.rect_from.bottom,
        data.rect_from.left,
        data.rect_to.top,
        data.rect_to.right,
        data.rect_to.bottom,
        data.rect_to.left
      ];
      data.hidden =
        0 === (data.cache[0] | data.cache[1] | data.cache[2] | data.cache[3]) ||
        0 === (data.cache[4] | data.cache[5] | data.cache[6] | data.cache[7]);
      data.unmodified = true;
      if (cached === undefined) {
        return (data.unmodified = false);
      }
      for (var i = 0; i < 8; i++) {
        if (cached[i] !== data.cache[i]) {
          return (data.unmodified = false);
        }
      }
    };
  
    var update = function(connection) {
      var data = $.data(connection, "connection");
      getState(data);
      if (data.unmodified) {
        return;
      }
      var border_h = data.border_h;
      var border_w = data.border_w;
      var from = data.rect_from;
      var to = data.rect_to;
      var b = (from.bottom + from.top) / 2;
      var r = (to.left + to.right) / 2;
      var t = (to.bottom + to.top) / 2;
      var l = (from.left + from.right) / 2;
  
      var h = ["right", "left"];
      if (l > r) {
        h = ["left", "right"];
        var x = Math.max(r - border_w / 2, Math.min(from.right, to.right));
        r = l + border_w / 2;
        l = x;
      } else {
        l -= border_w / 2;
        r = Math.min(r + border_w / 2, Math.max(from.left, to.left));
      }
      var v = ["bottom", "top"];
      if (t > b) {
        v = ["top", "bottom"];
        var x = Math.max(b - border_h / 2, Math.min(from.bottom, to.bottom));
        b = t + border_h / 2;
        t = x;
      } else {
        b = Math.min(b, Math.max(from.top, to.top));
        t -= border_h / 2;
      }
      var width = r - l;
      var height = b - t;
      if (width < border_w) {
        t = Math.max(t, Math.min(from.bottom, to.bottom));
        b = Math.min(b, Math.max(from.top, to.top));
        l = Math.max(from.left, to.left);
        r = Math.min(from.right, to.right);
        r = l = (l + r - border_w) / 2;
      }
      if (height < border_h) {
        l = Math.max(l, Math.min(from.right, to.right));
        r = Math.min(r, Math.max(from.left, to.left));
        t = Math.max(from.top, to.top);
        b = Math.min(from.bottom, to.bottom);
        b = t = (t + b - border_h) / 2;
      }
      width = r - l;
      height = b - t;
      width <= 0 && (border_h = 0);
      height <= 0 && (border_w = 0);
      var style =
        "border-" +
        v[0] +
        "-" +
        h[0] +
        "-radius: 0;" +
        "border-" +
        v[0] +
        "-" +
        h[1] +
        "-radius: 0;" +
        "border-" +
        v[1] +
        "-" +
        h[0] +
        "-radius: 0;";
      (border_h <= 0 || border_w <= 0) &&
        (style += "border-" + v[1] + "-" + h[1] + "-radius: 0;");
      if (data.hidden) {
        style += "display: none;";
      } else {
        data.css["border-" + v[0] + "-width"] = 0;
        data.css["border-" + h[0] + "-width"] = 0;
        data.css["border-" + v[1] + "-width"] = border_h;
        data.css["border-" + h[1] + "-width"] = border_w;
        var current_rect = connection.getBoundingClientRect();
        data.css.left = connection.offsetLeft + l - current_rect.left;
        data.css.top = connection.offsetTop + t - current_rect.top;
        data.css.width = width - border_w;
        data.css.height = height - border_h;
      }
      var bc = data.borderClasses;
      $(connection)
        .removeClass(bc[v[0]])
        .removeClass(bc[h[0]])
        .addClass(bc[v[1]])
        .addClass(bc[h[1]])
        .attr("style", style)
        .css(data.css);
    };
  
    var processConnections = function(method, elements) {
      return elements.each(function() {
        var connections = $.data(this, "connections");
        if (connections instanceof Array) {
          for (var i = 0, len = connections.length; i < len; i++) {
            method(connections[i]);
          }
        }
      });
    };
  })(jQuery);
  