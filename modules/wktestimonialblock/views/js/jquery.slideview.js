(function() {
  
  
  // History Shim
  var location = window.history.location || window.location;
    
    
  // ImageLoader
  var imageLoader = new ((function() {
    
    var
      instance = null,
      isImageComplete = function(img) {
        if (!img) return true;
        var src = img.getAttribute('src');
        if (src) {
          if (src.match(/^data:/)) {
            return true;
          }
          if (typeof(img.complete) !== "undefined") {
            return img.complete === true;
          }
          if (img.naturalHeight && img.naturalWidth) {
            return true;
          }
        }
        return false;
      },
      getImageURL = function(elem) {
        // Get Background Image URL
        var src = $(elem).attr('src') || $(elem).data('src');
        if (!src) {
          // Parse inline-style
          var bgImgStyle = elem.style.backgroundImage;
          if (bgImgStyle && bgImgStyle != 'none') {
            var match = /url\(["]?([^"]*)["]?\)/g.exec(bgImgStyle);
            if (match != null) {
              if (match[1] != window.location.href) {
                src = match[1];
              }
            }
          }
        }
        return src;
      },
      getDistanceToViewport = function(elem) {
        var $elem = $(elem), $window = $(window);
        var w = $window.innerWidth();
        var h = $window.innerHeight();
        var vp = {left: w / 2, top: h / 2};
        var ao = $elem.offset();
        var ap = {left: ao.left + $elem.outerWidth(true) / 2, top: ao.top + $elem.outerHeight(true) / 2};
        var n = {left: ap.left / w, top: ap.top / h};
        var ad = Math.sqrt((n.left -= 0.5) * n.left + (n.top -= 0.5) * n.top);
        return ad;
      },
      inView = function(elem) {
        var $elem = $(elem), $window = $(window);
        var rect = {left: 0, right: $window.innerWidth(), top: 0, bottom: $window.innerHeight() / 2};
        var offset = $elem.offset();
        if (offset.top + $elem.height() > rect.top && offset.left + $elem.width() > rect.left && offset.top < rect.bottom && offset.left < rect.right) {
          return true;
        }
        return false;
      };
    
    return function ImageLoader() {
      if (instance) {
        return instance;
      }
      instance = this;
      var
        emptyfunc = function() {},
        priority = function() {
          return Math.max(2 - getDistanceToViewport(this), 0);
        },
        defaults = {
          priority: priority,
          init: emptyfunc,
          start: emptyfunc,
          success: emptyfunc,
          error: emptyfunc,
          complete: emptyfunc
        },
        maxDownloads = 1,
        currentDownloads = 0,
        items = [];
        
      this.add = function(elem, options) {
        var
          item = {
            src: getImageURL(elem),
            elem: elem, 
            options: $.extend({}, defaults, options),
            complete: false
          };
        items.push(item);
      };
      
      function loadItems(items, complete) {
        
        complete = complete || function() {};
        var finished = [], started = [], itemComplete = function(item, img) {
          var elem = item.elem;
          item.loading = false;
          item.complete = true;
          var src = img && $(img).attr('src');
          if (!src) {
            // Error
            item.options.error.call(elem);
          } else {
            if (elem instanceof Image || elem.nodeName.toLowerCase() === 'img') {
              if (elem != img) {
                // Update original image with foreign source 
                $(elem).attr('src', src);
              }
            } else {
              // Background image
              elem.style.backgroundImage = "url('" + src + "')";
            }
            item.options.success.call(elem);
          }
          item.options.complete.call(elem);
          finished.push(item);
          if (finished.length === items.length) {
            complete();
          }
        };
        $(items).each(function(index, item) {
          item.loading = true;
          var src = item.src, elem = item.elem;
          started.push(item);
          var done = function(img) {
            itemComplete(item, img);
          };
          var img = item.options.init.call(elem, src, done);
          if (img instanceof Image || img === undefined || img === true) {
            img = img || item.elem instanceof Image ? item.elem : null;
            if (!(img instanceof Image)) {
              img = new Image();
            }
            $(img).attr('src', src);
            if (isImageComplete(img)) {
              done(img);
            } else {
              item.options.start.call(elem);
              $(img).on('load error', function(event) {
                // Delay completion for better performance
                window.setTimeout(function() {
                  // Item complete
                  done(event.type === 'error' ? null : img);
                }, 50);
              });
            }
            
          } else {
            // custom handled
          }
        });
      }
      
      this.update = function() {
        if (currentDownloads >= maxDownloads) {
          // Busy
          return;
        }
        var
          imageLoader = this,
          backgroundQueue = true,
          unsorted = $.grep(items.slice(), function(item, index) {
            return item.src && !item.complete && !item.loading;
          }),
          filtered = unsorted.filter(function(item, index) {
            return item.options.priority.call(item.elem);
          }),
          sorted = filtered.sort(function(a, b) {
            // Item priority
            var
              pa = a.options.priority.call(a.elem),
              pb = b.options.priority.call(b.elem);
            if (pa > pb) {
              return -1;
            } else if (pb > pa) {
              return 1;
            }
            return 0;
          }),
          downloads = sorted.slice(0, maxDownloads - currentDownloads);
        if (downloads.length) {
          currentDownloads+= downloads.length;
          loadItems(downloads, function() {
            currentDownloads-= downloads.length;
            imageLoader.update();
          });
        } else {
          // Nothing to do.
        }
      };
    };
  })());
  
  
  var
    /**
     * Camelize a string
     * @param string
     */
    camelize = (function() {
      var cache = {};
      return function(string) {
        return cache[string] = cache[string] || (function() {
          return string.replace(/(\-[a-z])/g, function($1){return $1.toUpperCase().replace('-','');});
        })();
      };
    })(),
  
    /**
     * Hyphenate a string
     * @param string
     */ 
    hyphenate = (function() {
      var cache = {};
      return function(string) {
        return cache[string] = cache[string] || (function() {
          return string.replace(/([A-Z])/g, function($1){return "-"+$1.toLowerCase();});
        })();
      };
    })(),
  
    /**
     * Humanize a string
     */
    humanize = (function() {
      var cache = {};
      return function(string) {
        return cache[string] = cache[string] || (function() {
          return string
            .replace(/.*\/|\.[^.]*$/g, '')
            .replace(/\.[^/.]+$/, "")
            .replace(/_/g, ' ')
            .replace(/(\w+)/g, function(match) {
              return match.charAt(0).toUpperCase() + match.slice(1);
            });
        })();
      };
    })(),
  
    /**
     * Slugify
     */
    slugify = (function() {
      var cache = {};
      return function(string) {
        return cache[string] = cache[string] || (function() {
          return text.toString().toLowerCase()
            .replace(/\s+/g, '-')           // Replace spaces with -
            .replace(/[^\w\-]+/g, '')       // Remove all non-word chars
            .replace(/\-\-+/g, '-')         // Replace multiple - with single -
            .replace(/^-+/, '')             // Trim - from start of text
            .replace(/-+$/, '');            // Trim - from end of text
        })();
      };
    })();
    
    /**
     * Retrieves a vendor prefixed style name for the given property
     * @param styleName
     * @param hyphenated
     */
    getVendorStyle = (function() {
      var
        cache = {},
        vendorPrefixes = ['Webkit', 'Moz', 'O', 'ms'], elem = document.createElement('div');
      return function (styleName, hyphenated) {
        hyphenated = typeof hyphenated === 'boolean' ? hyphenated : false;
        var
          camelized = camelize(styleName),
          result = cache[camelized] = typeof cache[camelized] !== 'undefined' ? cache[camelized] : (function(camelized) {
            var
              result = null,
              capitalized,
              prop;
            document.documentElement.appendChild(elem);
            if (typeof (elem.style[camelized]) === 'string') {
              result = camelized;
            }
            if (!result) {
              capitalized = camelized.substring(0, 1).toUpperCase() + camelized.substring(1);
              for (i = 0; i < vendorPrefixes.length; i++) {
                prop = vendorPrefixes[i] + capitalized;
                if (typeof elem.style[prop] === 'string') {
                  result = prop;
                  break;
                }
              }
            }
            elem.parentNode.removeChild(elem);
            return result;
          })(camelized);
        return result && hyphenated ? hyphenate(result) : result;
      };
    })(),
  
  
    /**
     * Compare arrays strictly
     */
    arrayStrictEqual = function(arr1, arr2){
      if (arr1.length !== arr2.length) return false;
      for (var i = 0, len = arr1.length; i < len; i++){
        if (arr1[i] !== arr2[i]){
            return false;
        }
      }
      return true; 
    },
    
    /**
     * Compare two arrays if they are equal even if they have different order.
     *
     * @link http://stackoverflow.com/a/7726509
     */
    arrayEqual = function(a, b) {
      return $(a).not(b).get().length === 0 && $(b).not(a).get().length === 0;
    },
    
    /**
     * Unsigned modulo
     */
    amod = function(x, m) {
      var r = x % m;
      r = r < 0 ? r + m : r;
      return r;
    },
    
    /**
     * Decodes html entities
     */
    decodeEntities = function(input) {
      var y = document.createElement('textarea');
      y.innerHTML = input;
      return y.value;
    };

  
    /**
     * Detects if element is in browser viewport
     */
    inViewport = function(element) {
      var
        $element = $(element),
        $win = $(window),
        viewport = {
          top : $win.scrollTop(),
          left : $win.scrollLeft()
        },
        bounds = $element.offset();
      viewport.right = viewport.left + $win.width();
      viewport.bottom = viewport.top + $win.height();
      bounds.right = bounds.left + $element.outerWidth();
      bounds.bottom = bounds.top + $element.outerHeight();
      return bounds.left >= viewport.left && bounds.right <= viewport.right;
    },
 
    /**
     * Parse css transform matrix
     */
    getTransformMatrix = function(string) {
      var result = { a: 1, b: 0, c: 0, d: 1, x: 0, y: 0 };
      var p = ['a', 'b', 'c', 'd', 'x', 'y'];
      if (typeof string == "string") {
        var match = string.match(/^matrix\(\s*(-?\d*),?\s*(-?\d*),?\s*(-?\d*),?\s*(-?\d*),?\s*(-?[\d\.]*)(?:px)?,?\s*(-?[\d\.]*)(?:px)?/);
        if (match) for (var i = 0; i < p.length; i++) result[p[i]] = parseInt(match[i + 1]);
      }
      return result;
    };
    
  // detects the element's position
  function getElementPosition(elem, style) {
    var $elem = $(elem), x = 0, y = 0;
    if (!$elem.parent().length) {
      return {
        x: 0, y: 0
      };
    }
    switch (style) {
      case 'position':
        x = parseFloat($elem.css('left'));
        y = parseFloat($elem.css('top'));
        break;

      case 'transform': 
      case 'transform3d': 

        var transformStyle = getVendorStyle('transform');
        var styleValue = $elem.css(transformStyle);
        var matrix = getTransformMatrix(styleValue);
        if (matrix) {
          x = matrix.x; 
          y = matrix.y;
        }
        break;
    }
    x = !isNaN(x) ? x : 0;
    y = !isNaN(y) ? y : 0;
    return {x: x, y: y};
  }
    
  // sets the elements position using the specified style
  function setElementPosition(elem, left, top, style) {
    switch (style) {
      case 'position': 
        // CSS Position
        elem.style.left = left;
        elem.style.top = top;
        break;
      case 'transform': 
      case 'transform3d': 
        // CSS Transform
        var transformStyle = getVendorStyle('transform');
        var translateMethod = style == 'transform3d' ? 'translate3d' : 'translate';
        var styleValue = translateMethod + "(" + left + ", " + top + ")";
        var styleValue;
        if (style == 'transform3d') {
           styleValue = "translate3d(" + left + ", " + top + ",0)";
        } else {
           styleValue = "translate(" + left + ", " + top + ")";
        }
        elem.style[transformStyle] = styleValue;
        break; 
    }
  }
  
  /* SlideView Plugin */
  var pluginName = "slideview";
  
  var defaults = {
    
    contentSelector: '> .slideview-content',
    scrollStyle: 'position', 
    
    transition: {
      type: 'swipe', 
      duration: 500, 
      easing: 'swing'
    },
    
    mouseDragging: true, 
    transitionInterruptable: false, 
    userInteraction: true, 
    endless: false, 
    maxTouchTime: 250,
    
    // playback
    autoStart: false, 
    showDuration: 4000, 
    
    // load management
    slideSelector: '.slide', 
    
    offset: 0, 
    limit: 2, 
    
    // callbacks
    slideLoaded: null, 
    slideBefore: null, 
    slideComplete: null, 
    slide: null,
    
    preloadImages: true,

    nextButton: '.slideview-next',
    prevButton: '.slideview-prev',
    buttonDisabledClass: 'slideview-button-disabled',
    
    pagination: '.slideview-pagination',
    paginationActiveClass: 'slideview-pagination-active',
    paginationItem: function(index) {
      return $('<a class="slideview-pagination-item"></a>');
    }
  };
  
  
  var isTouch = 'ontouchstart' in window;
  
  var pluginClass = function SlideView(element, opts) {
    // private local variables
    var options = $.extend(true, {}, defaults, opts);
    
    var slideView = this;
    var $element = $(element);
    
    var container = null;
    var $container = null;
 
    var invalidateFlag = true;
    
    var items = [];
    
    var scrollPosition = null;

    var currentTransition = null;
    var currentTransitions = {};
    
    var lastItem = null;
    var currentItem = null;
    
    var currentSlide = null;
    var queuedSlide = null;
    
    var _layoutItems = null;
    var _items = null;
    
    var animatePlugin = "animate";
    
    var _currentItem = 0;
    
    var slideIndex = 0;
    
    // local methods
    
    var optionsCache = {};
    var paginationItems = [];
    
    // Capture current dragging state
    var isDragging = false;
    
    // Prevent dragging during slide
    var isDraggable = true;
    
    function updateControls() {
      var $nextButton = $(getOptionElement('nextButton'));
      var $prevButton = $(getOptionElement('prevButton'));
      var $pagination = $(getOptionElement('pagination'));
      var slideIndex = this.getSlideIndex();
      var size = this.size();
      if ($nextButton.length) {
        if (slideIndex === size - 1) {
          $nextButton.addClass(options.buttonDisabledClass);
        } else {
          $nextButton.removeClass(options.buttonDisabledClass);
        }
        size > 1 ? $nextButton.show() : $nextButton.hide();
      }
      if ($prevButton.length) {
        if (slideIndex === 0 && !options.endless) {
          $prevButton.addClass(options.buttonDisabledClass);
        } else {
          $prevButton.removeClass(options.buttonDisabledClass);
        }
        size > 1 ? $prevButton.show() : $prevButton.hide();
      }
      if ($pagination.length) {
        if (paginationItems.length !== this.size()) {
          $(paginationItems).each(function() {
            $(this).remove();
          });
          paginationItems = (Array.apply(null, {length: this.size()}).map(Number.call, Number)).map(options.paginationItem);
          $pagination.append(paginationItems);
        }
        $(paginationItems).each(function(index) {
          if (index === slideIndex) {
            $(this).addClass(options.paginationActiveClass);
          } else {
            $(this).removeClass(options.paginationActiveClass);
          }
        });
        size > 1 ? $pagination.show() : $pagination.hide();
      }
    }
    
    function updateOptions() {
      updateControls.call(this);
    }
    
    function getOptionElement(name) {
      var value = options[name]; 
      var control = value ? $(value, element)[0] : null;
      var cached = optionsCache[name];
      if (cached && (cached.value !== control || cached.option !== value)) {
        if (cached.parentNode) {
          cached.parentNode.removeChild(cached);
        }
        delete optionsCache[name];
      } else if (cached) {
        control = cached.value;
      }
      if (!$element.has(control).length === 0) {
        $element.append(control);
      }
      optionsCache[name] = {value: control, option: value};
      return control;
    }
    
    
    // TODO: Make container dynamic option
    function getContainer() {
      return $(options.contentSelector)[0];
    }
    
    // collection view implementation
    
    // detects if the specified element is a valid item
    function isItem(elem) {
      return elem.nodeType == 1
        && $.inArray(elem.localName.toLowerCase(), ["br", "script", "link", "map"]) == -1;
    }
    
    // returns the item's index
    this.indexOf = function(item) {
      for (var i = 0; i < items.length; i++) if (items[i] == item) return i;
    };
    
    this.get = function(index) {
      return items[index];
    };
    
    this.size = function() {
      return items.length;
    };
    
    this.add = function(item, index) {
      item = item instanceof jQuery ? item.get(0) : item;
      // Import nodes
      if (item.ownerDocument !== element.ownerDocument) {
        item = element.ownerDocument.importNode(item, true);
      }
      if (item.parentNode && item.parentNode !== container) {
        item.parentNode.removeChild(item);
      }
      // Reuse existing items
      var existingIndex = $.inArray(item, items);
      if (existingIndex >= 0) {
        items.splice(existingIndex, 1);
      }
      
      // Insert
      if (typeof index === 'number') {
        items.splice(index, 0, item);
      } else {
        // Append
        items.push(item);
      }
      this.invalidate();
      itemsChanged.call(this);
      slideAdded.call(this, item);
    };
    
    function slideAdded(slide) {
      $(slide).each(function() {
        var images = $(this).find("*[src], *[data-src], *[style*='background']").toArray();
        if (images.length) {
          var finished = [];
          var initialized = [];
          var options = {
            priority: function() {
              var prio = 2 - Math.min(2, Math.abs(slideView.indexOf(slide) - slideView.getSlideIndex()));
              return prio;
            },
            init: function() {
              initialized.push(this);
            },
            complete: function() {
              finished.push(this);
              if (arrayEqual(initialized, finished)) {
                $(slide).removeClass('loading');
              }
            }
          };
          if (images.length && slide !== getCurrentItem()) {
            $(slide).addClass('loading');
          }
          $(images).each(function() {
            imageLoader.add(this, options);
          });
        }
      });
    }
    
    this.remove = function(item) {
      items.splice(this.indexOf(item), 1);
      this.invalidate();
      itemsChanged.call(this);
    };
    
    this.removeAll = function() {
      for (var i = 0; i < this.size(); i++) {
        invalidateFlag = false;
        this.remove(this.get(i));
        invalidateFlag = true;
        i--;
      }
      this.invalidate();
    };
    
    this.addAll = function(collection, index) {
      collection = collection instanceof jQuery ? collection.toArray() : collection;
      index = typeof index == 'number' ? index : this.size();
      invalidateFlag = false;
      for (var i = 0; i < collection.length; i++) {
        this.add(collection[i], index + i);
      }
      invalidateFlag = true;
      this.invalidate();
    };
    
    this.replaceAll = function(collection) {
      collection = collection instanceof jQuery ? collection.toArray() : collection;
      invalidateFlag = false;
      var slideIndex = this.getSlideIndex(), currentSlide = null;
      for (var i = 0; i < this.size(); i++) {
        var item = this.get(i);
        if (i === slideIndex) {
          currentSlide = item;
        }
        // Only remove the item if it's not already contained in this slideview
        if ($.inArray(item, collection) < 0) {
          this.remove(item);
          i--;
        }
      }
      for (var i = 0; i < collection.length; i++) {
        var item = collection[i];
        this.add(item);
        if (item === currentSlide) {
          setScrollPosition(i * element.clientWidth, 0, 0);
        }
      }
      invalidateFlag = true;
      this.invalidate();
    };
    
    this.invalidate = function() {
      if (!invalidateFlag) return;
      // Display element to validate view dimensions
      var hidden = element.style.display === 'none';
      if (hidden) {
        element.style.display = '';
      }
      layout.call(this);
      var lItems = getLayoutItems();
      if (!arrayStrictEqual(lItems, items)) {
        // TODO: irregular layout
      } else {
       invalidateScrollPosition.call(this);
       var s = getScrollPosition();
       var scrollIndex = getScrollPosition().x / element.clientWidth;
       var item = getItemAtScrollPosition(s.x, s.y);
       var itemIndex = this.indexOf(item);
       var itemDiff = itemIndex - scrollIndex;
       if (itemDiff != 0) {
         setScrollPosition((scrollIndex + itemDiff) * element.clientWidth, s.y);
       }
       layoutItems.call(this);
      }
      // Update Image Loader
      imageLoader.update();
      // Show the element again
      if (hidden) {
        element.style.display = 'none';
      }
    };
    
    this.setOptions = function(opts) {
      options = $.extend({}, options, opts);
      if (opts.items) {
        this.replaceAll(opts.items);
      }
      if (opts.slideIndex >= 0) {
        this.slideTo(opts.slideIndex, {duration: 0});
      }
    };
    
    this.getOptions = function() {
      return options;
    };
    
    function getOption(name) {
      return options[name];
    }

    function getItemAtScrollPosition(x, y) {
      for (var i = 0; i < items.length; i++) {
        var item = items[i];
        var p = getElementPosition(item, options.scrollStyle);
        if ($(item).is(":visible") && p.x >= x && p.x < x + element.clientWidth) {
          return item;
        }
      }
      return null;
    }

    function invalidateScrollPosition() {
      scrollPosition = null;
    }
    
    function getScrollPosition() {
      //if (!scrollPosition) {
        // compute scroll position
        var s = getElementPosition(container, options.scrollStyle);
        scrollPosition = {
          x: -s.x, y: -s.y
        };
      //}
      return scrollPosition;
    }

    var scrollDirection = 0;

    function setScrollPosition(x, y, duration) {
      
      var transitionOptions = options.transition;
      
      duration = typeof duration == "number" ? duration : typeof duration == "boolean" ? duration ? transitionOptions.duration : 0 : 0;
      
      var s = getScrollPosition();

      x = typeof x == "number" && !isNaN(x) ? x : 0;
      y = typeof y == "number" && !isNaN(y) ? y : 0;
      
      if (!options.endless) {
        if (x < 0) {
          x = 0;
        } else if (x > (slideView.size() - 1) * element.clientWidth) {
          x = (slideView.size() - 1) * element.clientWidth;
        }
      }
      
      scrollDirection = s.x > x ? -1 : s.x < x ? 1 : 0;

      var xp = -x / element.clientWidth * 100;
      var yp = -y / element.clientHeight * 100;
      
      if ($container.is(":animated")) {
        // TODO: do nothing if a transition to this position is already running
        $container.stop();
      }

      if (duration == 0 || s.x == x && s.y == y) {
        
        // No Transition
        if (s.x != x || s.y != y) {
          setElementPosition(container, xp + "%", yp + "%", options.scrollStyle);
          scrollPosition = {x: x, y: y};
        }
        
        if (s.x != x || s.y == y) {
          // Scroll Position has changed
        }
        
        scrollComplete();
        
      } else {
        
        // transition
        var
          properties = {},
          scrollStyle = options.scrollStyle;
          
        switch (scrollStyle) {
          
          case 'position': 
          
            properties = {
              left: xp + "%", 
              top: yp + "%"
            };
            break;
            
          case 'transform':
          case 'transform3d': 
            
            
            var transformStyle = getVendorStyle('transform');
            var transformValue = "translate(" + xp + "%" + "," + yp + "%)";
            if (options.scrollStyle == "transform3d") {
              transformValue = "translate3d(" + xp + "%" + "," + yp + "%,0)";
            }
            properties[transformStyle] = transformValue; 
        }
        
        animateScroll(properties, options.transition);
        scrollPosition = {x: x, y: y};
        layoutItems();
        
      }
      
      
    }
    
    
    function animateScroll(properties, opts) {

      var animationOptions = {};
      $.extend(animationOptions, opts, {
        complete: function() {
          if (opts.complete) opts.complete.apply(this, arguments);
          scrollComplete();
        }
      });
      
      animationOptions.queue = false;
      $container[animatePlugin](properties, animationOptions);
    }
    
    function getCurrentItem() {
      invalidateScrollPosition();
      var s = getScrollPosition();
      var elem = getItemAtScrollPosition(s.x, s.y);
      return elem;
    }
    
    function getCurrentIndex() {
      return slideView.indexOf(getCurrentItem());
    }
    
    function getVisibleItems(scrollPosition) {
      var s = typeof scrollPosition == 'number' ? scrollPosition : getScrollPosition();
      var vItems = [];
      var currentItem = null;
      for (var i = 0; i < items.length; i++) {
        var elem = items[i];
        var p = getElementPosition(elem, options.scrollStyle);
        if (elem.style.display != 'none' && p.x > s.x - element.clientWidth && p.x < s.x + element.clientWidth) {
          vItems.push({item: elem, scrollIndex: p.x / element.clientWidth});
        }
      }
      return vItems;
    }
    
    function swipeTo(item, transitionOptions) {
      
      var duration  = transitionOptions.duration;
      var direction = transitionOptions.direction;
      
      if (duration === 0) {
        setScrollPosition(slideView.indexOf(item) * element.clientWidth, 0, duration);
        return;
      }

      var items = getLayoutItems();
      
      var s = getScrollPosition();
      var p = getElementPosition(item, options.scrollStyle);
      
      var currentPage = Math.floor(s.x / element.clientWidth);
      
      var currentItem = getCurrentItem();
      var currentIndex = $.inArray(currentItem, items);
      
      var itemIndex = slideView.indexOf(item);
      
      // TODO: duration = 0
      // get view items 

      var vItems = getVisibleItems(s);
      if (vItems.length == 0) {
        invalidateLayoutItems();
        layoutItems();
        vItems = getVisibleItems(s);
        if (vItems.length == 0) {
          // error: at this point the view must have visible items
          return;
        }
      }

      var vMinScrollIndex = vItems[0].scrollIndex;
      var vMaxScrollIndex = vItems[vItems.length - 1].scrollIndex;
      
      // invisible views should scroll instantly
      duration = !inViewport(element) ? 0 : duration;
      
      direction = typeof direction == "number" ? direction : itemIndex < currentIndex ? -1 : itemIndex > currentIndex ? 1 : 0;
      
      
      // check for direct neighbors
      
      var currentNextIndex = (currentIndex + 1) % items.length;
      currentNextIndex = currentNextIndex < 0 ? currentNextIndex + items.length : currentNextIndex;
      var currentNextItem = items[currentNextIndex];
      
      var currentPrevIndex = (currentIndex - 1) % items.length;
      currentPrevIndex = currentPrevIndex < 0 ? currentPrevIndex + items.length : currentPrevIndex;
      var currentPrevItem = items[currentPrevIndex];
      
      var scrollOffset = s.x / element.clientWidth - currentPage;
       
      if (direction > 0 && currentNextItem == item) {
        setScrollPosition((currentPage + 1) * element.clientWidth, 0, duration);
        return;
      } else if (direction < 0 && currentPrevItem == item) {
        setScrollPosition((currentPage - 1) * element.clientWidth, 0, duration);
        return;
      }
     

      var lItems = [];
      var mItems = items.slice();
      if (direction == 0) {
         return;
      }
      
      if (direction > 0) {
        // forward

        for (var i = vMinScrollIndex; i <= vMinScrollIndex + items.length; i++) {

          var m = i % items.length;
          m = m < 0 ? m + items.length : m;
          elem = items[m];

          if (i > vMaxScrollIndex && i <= vMaxScrollIndex + 2) {

            var me = (itemIndex + i - vMaxScrollIndex - 1) % items.length;
            me = me < 0 ? me + items.length : me;
            elem = slideView.get(me);
            
          }
          
          while ($.inArray(elem, lItems) >= 0 && mItems.length > 0) {
            elem = mItems.shift();
          }
          if (lItems.indexOf(elem) === -1) {
            lItems[m] = elem;
          }
        }
 
      } else if (direction < 0) {
        
        // backward
        for (var i = currentPage; i > currentPage - items.length; i--) {

          var m = i % items.length;
          m = m < 0 ? m + items.length : m;
          elem = items[m];
            
          if (i < currentPage && i >= currentPage - 2) {
            
            mItems.push(elem);
            var me = (itemIndex + i - currentPage + 1) % items.length;
            var me = me < 0 ? me + items.length : me;
            elem = slideView.get(me);
            
          }
          while ($.inArray(elem, lItems) >= 0 && mItems.length > 0) {
            elem = mItems.shift();
          }
          if (lItems.indexOf(elem) === -1) {
            lItems[m] = elem;
          }
        }
        
      }
      
      setLayoutItems(lItems);
      
      setScrollPosition((currentPage + direction) * element.clientWidth, 0, duration);
     
    }
    
    
    function slideTo(item, transition) {
      
      var slideItem = slideView.get(slideIndex);
      
      if (slideItem === item) {
        // Same item, nothing to do
        return;
      }
      
      // reset playback timer
      window.clearTimeout(playTimeoutID);
      
      // merge options
      var opts = {};
      $.extend(opts, options.transition, transition);
      
      
      // callback
      slideBefore.call(this, item);
      
      // Update slideindex
      
      slideIndex = slideView.indexOf(item);
      
      isDraggable = false;
      
      // perform slide
      switch (opts.type) {

        case 'fade': 
          fadeTo.call(this, item, opts);
          break;
          
        case 'swipe': 
          
          swipeTo.call(this, item, opts);
          break;
        
        case 'scroll': 
          scrollTo.call(this, item, opts);
          break;
          
        case 'none': 
        default:
          // TODO: none
          //slideComplete.call(this);
      }
      
      
      slide.call(this, item);
      
            
    }
    

    function validateLayoutItems() {
      
      if (!_layoutItems) return;
      
      var s = getScrollPosition();
      var hasValidOrder = true;
      var items = getLayoutItems(); 
      var vItems = [];
      var vIndex = null;
        for (var i = 0; i < items.length; i++) {
          var elem = items[i];
          var p = getElementPosition(elem, options.scrollStyle);
          if ($(elem).is(":visible") && p.x > s.x - element.clientWidth && p.x < s.x + element.clientWidth) {
            var eIndex = amod(slideView.indexOf(elem), items.length);
            if (vIndex == null) {
            vIndex = eIndex;
            } else {
              vIndex = amod(vIndex++, items.length);
              if (vIndex == eIndex) {
                //hasValidOrder = true;
              } else {
                hasValidOrder = false;
                break;
              }
            }
          }
        }
        
        if (hasValidOrder) {
          
          // order is valid
          var scrollIndex = Math.floor(s.x / element.clientWidth);
          var offset = s.x / element.clientWidth - scrollIndex;
          
          var elem = getItemAtScrollPosition(s.x, 0);
          var itemIndex = slideView.indexOf(elem);
          var x = (itemIndex + offset) * element.clientWidth;
          
          //if (scrollIndex != itemIndex) {
            invalidateLayoutItems();
            setScrollPosition(x, 0, 0);
            
            //layoutItems();
          //}
          
          return true;
      
        } else {
          // order is invalid
          return false;
        }
    }
    
    function invalidateLayoutItems() {
      _layoutItems = null;
    }
    
    function setLayoutItems(items) {
      _layoutItems = items;
    }
    
    function getLayoutItems() {
      if (!_layoutItems) {
        return items;
      }
      return _layoutItems;
    }
    
    function layoutItems() {
      invalidateScrollPosition();
      
      var s = getScrollPosition();
      
      var scrollIndex = Math.floor(s.x / element.clientWidth);
      var scrollOffset = s.x / element.clientWidth - scrollIndex;
      
      var lItems = getLayoutItems();
      // maximum number of displayed items is 3
      var minScrollIndex = scrollIndex - 1;
      var maxScrollIndex = scrollIndex + 1;
     
      if (lItems.length == 1) {
        minScrollIndex = scrollIndex;
        maxScrollIndex = scrollIndex;
      } else if (lItems.length == 2) {
        var sd = scrollOffset != 0 ? scrollOffset : scrollDirection;
        minScrollIndex = sd < 0 ? scrollIndex - 1 : scrollIndex;
        maxScrollIndex = sd < 0 ? scrollIndex : scrollIndex + 1;
      } else {
        minScrollIndex = scrollIndex - 1;
        maxScrollIndex = scrollIndex + 1;
      }
      
      for (var x = minScrollIndex; x < minScrollIndex + lItems.length; x++) {

        var m = x % slideView.size();
        m = m < 0 ? m + slideView.size() : m;
        
        var item = lItems[m];
        var $item = $(item);
        
        var y = 0;
        
        if (x >= minScrollIndex && x <= maxScrollIndex) {
            if (item.parentNode != container) {
              container.appendChild(item);
            }

            $item.css({
              position: 'absolute', 
              width: '100%',
              height: '100%',
              display: ''
            });
            
            var p = getElementPosition(item, options.scrollStyle);
            
            setElementPosition(item, x * 100 + "%", y + "px", options.scrollStyle);
            
            
            // load slide
            /*
            if (!isSlideLoaded(item)) {
              loadSlide(item);
            } else {
            }*/
            item.style.display = "";
            
        } else {
          // hide
          $item.css('display', 'none');
        }
        
        // TODO: option resetScroll
        // if item is currently not visible reset content scroll
        var isVisibleAtScrollPosition = x + element.clientWidth > s.x && x < s.x + element.clientWidth;
        isVisibleAtScrollPosition = x + 1 > s.x / element.clientWidth && x < s.x / element.clientWidth + 1;
        
        if (!isVisibleAtScrollPosition) {
          item.scrollTop = 0;
          // TODO: reset content scroll on nested slideviews
        }
        
      }
      
    
    }
  
    // CSS LAYOUT
    function layout() {
      // Setup styles for element
      
      var elemCSS = {
        // Element should hide overflow content
        overflow: 'hidden'
      };
      
      // Slideview should be in position context
      $element.css('position', '');
      if ($element.css('position') === 'static') {
        $element.css('position', 'relative');
      }
      
      // Apply element styles
      $element.css(elemCSS);
    }
    
    // Callbacks
    
    var slideCallback = false;
    
    function transitionStart(type, inItem, outItem) {
      currentTransitions[type] = {
        inItem: inItem, 
        outItem: outItem
      };
    }
    
    function transitionEnd(type) {
      var f = currentTransitions[type];
      var i = slideView.indexOf(f.inItem);
      delete currentTransitions[type];
      validateLayoutItems.call(slideView);
      setScrollPosition(i * element.clientWidth, 0, 0);
      // layoutItems.call(this);
    }
    
    function scrollComplete() {
      // Enable dragging
      isDraggable = true;
      
      if (typeof options.scrollComplete == 'function') {
        options.scrollComplete.call(slideView);
      }
      scrollDirection = 0;
      validateLayoutItems();
      var s = getScrollPosition();
      if (element.clientWidth === 0 || s.x % element.clientWidth === 0) {
        layoutItems.call(this);
        var currentItem = getCurrentItem();
        slideComplete();
      }
    }
    
    
    function itemsChanged() {
      updateControls.call(this);
    }
    
    
    function slide(item) {
      slideCallback = true;
      updateControls.call(slideView);
      if (typeof options.slide == "function") {
        options.slide.call(slideView, item);
      }
    }
    
    function slideBefore(item) {
      if (slideCallback && typeof options.slideBefore == "function") {
        options.slideBefore.call(slideView, item);
      }
    }
   
    function slideComplete() {
      
      var currentItem = getCurrentItem();
      _currentItem = currentItem;
      
      // playback
      if (slideView.isPlaying()) {
        window.clearTimeout(playTimeoutID);
        playTimeoutID = window.setTimeout(function() {
          slideView.next();
        }, options.showDuration);
      }
      
      currentSlide = currentItem;
      
      // Queued Slides
      if (queuedSlide && queuedSlide.item != currentItem) {
        var item = queuedSlide.item, transition = queuedSlide.transition;
        queuedSlide = null;
        slideTo(item, transition);
      } else {
        
        // All complete
        
        // Update location
        updateLocation(currentItem);
        
        // Slide Change
        slideChange.call(this, currentSlide);
        
        // Options Slide Complete
        if (slideCallback && typeof options.slideComplete == "function") {
          options.slideComplete.call(slideView, currentItem);
        }
      }
      
    }
    
    function slideChange(slide) {
      // Update loader
      imageLoader.update();
      if (slideCallback && typeof options.slideChange == "function") {
        options.slideChange.call(slideView, slide);
      }
      // Update location
      //updateLocation(slide);
    }
    
    function getSlideState(slide) {
      return slide && (typeof options.pushState === 'function' ? options.pushState.call(this, slide) : (function() {
        var titleNode = $(slide).find("[itemprop='title']", "h2");
        var title = titleNode.attr('content') || titleNode.text();
        var urlNode = $(slide).find("[itemprop='url']").addBack('[data-url], [itemid]');
        var url = urlNode.attr('data-url') || urlNode.attr('itemprop') && urlNode.attr('content') || urlNode.attr('itemid');
        return {
          title: title,
          url: url
        };
      })());
    }
    
    function pushState(url, title) {
      if (title && title !== document.title) {
        document.title = title;
      }
      if (url && url !== location.href) {
        history.pushState({ url: url, title: title}, title, url);
      }
    }
    
    function jumpToSlide(url, clicked) {
      clicked = typeof clicked === 'undefined' ? false : clicked;
      var slide = $(items).filter(function(index, item) {
        var state = getSlideState(item);
        return state.url === url;
      }).get(0);
      
      if (slide) {
        if (!isDragging || !clicked) {
          var transitionOptions = {};
          if (!$(element).is(':visible') || !inViewport(element)) {
            transitionOptions.duration = 0;
          }
          // slideChange may not be called if no change has occurred, but may be a location switch
          updateLocation(slide);
          // Actually slide
          window.setTimeout(function() {
            slideView.slideTo(slide, transitionOptions);
          }, 0);
        }
        return true;
      }
      return false;
    }
    
    $(window).on('popstate', function(event) {
      var eventState = event.originalEvent.state;
      if (eventState) {
        document.title = eventState.title;
        jumpToSlide(eventState.url);
      }
    });
    
    $(window).on('click', function(e) {
      var a = $(e.target).is('a[href]') ? e.target : $(e.target).parents('a[href]').get(0);
      if (a) {
        var $a = $(a);
        var href = $(a).attr('href');
        var handled = jumpToSlide(href, true);
        if (handled) {
          e.preventDefault();
        }
      }
    });
    
    $(window).on('click', function(event) {
      var eventState = event.originalEvent.state;
      if (eventState) {
        document.title = eventState.title;
        jumpToSlide(eventState.url, clicked);
      }
    });
    
    var updateLocationTimeout = null;
    
    function updateLocation(slide) {
      options.pushState = true;
      if (options.pushState) {
        //inViewport(element) &&
        clearTimeout(updateLocationTimeout); 
        updateLocationTimeout = window.setTimeout(function() {
          var state = getSlideState(slide);
          if ($(element).is(':visible') && inViewport(element) && state) {
            // PUSH STATE
            pushState(state.url, state.title);
          }
        }, 0);
      }
    }

    // public methods
    this.getCurrentIndex = function() {
      return getCurrentIndex();
    };
    
    this.getCurrentItem = function() {
      return getCurrentItem();
    };
    
    this.getSlideIndex = function() {
      return slideIndex;
    };
    
    
    
    this.slideTo = function(item, transition) {
      
      if (typeof item == "number") {
        var index = item;
        if (options.endless) {
          index = amod(item, slideView.size());
        }
        item = slideView.get(index);
      }

      if (!item) return;
      
      if ($container.is(':animated')) {
        // QUEUED
        queuedSlide = {item: item, transition: transition};
      } else {
        slideTo(item, transition);
      }
      
    };

    this.next = function() {
      this.slideTo(this.getSlideIndex() + 1, {
        direction: 1
      });
    };
    
    this.previous = function() {
      this.slideTo(this.getSlideIndex() - 1, {
        direction: -1
      });
    };
    
    var playTimeoutID = null;
    var playing = false;
    
    this.start = function() {
      playing = true;
    };
    
    this.stop = function() {
      playing = true;
      window.clearTimeout(playTimeoutID);
    };
    
    this.isPlaying = function() {
      return playing;
    };
  
    /* User Interaction */

    function initKeyboardInteraction() {
      
      // keyboard navigation
      
      var hasMouseFocus = false;
      
      $(element).bind('mouseenter', function(event) {
        hasMouseFocus = true;
      });
      
      $(element).bind('mouseleave', function(event) {
        hasMouseFocus = false;
      });
      
      $(document).bind('keydown', function(event) {
        
        if (event.target.tabIndex >= 0) {
          return;
        }
        
        if (hasMouseFocus) {
          switch (event.which) {
            case 39: 
              slideView.next();
              // event.preventDefault();
              break;
            case 37: 
              slideView.previous();
              // event.preventDefault();
              break;
          }
        }
      });
 
    }

    function initMouseWheelInteraction() {

      $element = $(element);
      
      var mouseWheelEndTimeout = null;
      var mouseWheelCount = 0;
      
      
      $element.bind('onmousewheel' in window ? 'mousewheel' : 'DOMMouseScroll', function(event) {

        var preventDefault = false;
        
        if (event.target, $(container).has($(event.target)).length === 0) {
          return;
        }
    
        // get mouse wheel vector
        var oEvent = event.originalEvent;
        var wheelDeltaX;
        var wheelDeltaY;
        if (!window.opera && 'wheelDeltaX' in oEvent) {
          wheelDeltaX = oEvent.wheelDeltaX;
          wheelDeltaY = oEvent.wheelDeltaY;
            
        } else if (!window.opera && 'detail' in oEvent) {
          if (oEvent.axis === 2) { 
            // Vertical
            wheelDeltaY = -oEvent.detail * 12;
            wheelDeltaX = 0;
          } else { 
            // Horizontal
            wheelDeltaX = -oEvent.detail * 12;
            wheelDeltaY = 0;
          }
        } else if ('wheelDelta' in oEvent) {
          // ie / opera
          wheelDeltaX = 0;
          wheelDeltaY = sh > 0 ? oEvent.wheelDelta : 0;
        }
        
        var dx = wheelDeltaX ? - wheelDeltaX / 12 : 0;
        var dy = - wheelDeltaY / 12;
  
        var o = dy == 0 ? Math.abs(dx) : Math.abs(dx) / Math.abs(dy);
        
        
        if (mouseWheelEndTimeout != null) {
          
            window.clearTimeout(mouseWheelEndTimeout);
            mouseWheelEndTimeout = null;
            
        } else {
           
           
           if (o > 0 && dx != 0) {
            
                if (dx > 0) {

                  slideView.next({
                    type: 'swipe'
                  });
                  
                } else if (dx < 0) {

                  slideView.previous({
                    type: 'swipe'
                  });
                  
                }
            }

        }
        
        if (o > 0 && dx != 0) {
          preventDefault = true;
        }
        
        mouseWheelEndTimeout = window.setTimeout(function() {
          mouseWheelEndTimeout = null;
        }, 100);

      
        if (currentTransition) {
            preventDefault = true;
        }
    
        if (preventDefault) {
            
          if (event.preventDefault) {
            event.preventDefault();
            event.stopPropagation();
          }
          
          event.returnValue = false;
          event.cancelBubbles = true;
          return false;     
        }
      
      });
    }
  
  
    function initTouchInteraction() {
      
      var mouseDragging = options.mouseDragging;
  
      var touchStartPos = null, touchStartTime = null, touchCurrentPos = null, touchInitialVector = null, cancelClicks;
      var initialDirection;
     
      var touchStartEvent = isTouch ? 'touchstart' : mouseDragging ? ' mousedown' : null;
      var touchMoveEvent = isTouch ? 'touchmove' : mouseDragging ? ' mousemove' : null;
      var touchEndEvent = isTouch ? 'touchend' : mouseDragging ? ' mouseup' : null;
      
      isDragging = false;
      
      $(window).bind(touchStartEvent, function(event) {
        // Stop Dragging
        isDragging = false;
      });
      
      $element.bind(touchStartEvent, function(event) {
        
        if (!isDraggable) {
          return;
        }
        
        if (event.target, $(container).has($(event.target)).length === 0) {
          return;
        }

        if ($container.is(':animated')) {
          $container.stop();
        }
        
        var touchEvent = event.originalEvent;
        var touch = event.type == 'touchstart' ? touchEvent.changedTouches[0] : touchEvent;
        touchStartPos = touchCurrentPos = {x: touch.clientX, y: touch.clientY};
        touchStartTime = new Date().getTime();
        initialDirection = null;
        
      
        if (event.type == 'mousedown') {
          window.setTimeout(function() {
            if (typeof $(element).attr('tabindex') !== 'undefined') {
              document.activeElement.blur();
              $(event.target).focus();
            }
          }, 1);
          event.preventDefault();
          /*window.setTimeout(function() {
            $(event.target).focus();
          }, 100);*/
          //event.stopPropagation();
        }
        
        // reset playback timer
        window.clearTimeout(playTimeoutID);
  
      });
      
      $element.bind(touchMoveEvent, function(event) {
        // touch move
  
        if (touchCurrentPos != null) {
          
          isDragging = true;
          
          var touchEvent = event.originalEvent;
          
          var touch = event.type == 'touchmove' ? touchEvent.changedTouches[0] : touchEvent;
          var touchX = touch.clientX;
          var touchY = touch.clientY;
          
          var dx = (touchX - touchCurrentPos.x) * -1;
          var dy = (touchY - touchCurrentPos.y) * -1;
  
          touchCurrentPos = {x: touchX, y: touchY};
          
          var o = dy == 0 ? Math.abs(dx) : Math.abs(dx) / Math.abs(dy);
  
          if (initialDirection == null) {
            initialDirection = o;
          }
          
          if (initialDirection >= 1) {
  
            if (dx != 0) {
  
                var s = getScrollPosition();
  
                setScrollPosition(s.x + dx, 0, 0);
                
                layoutItems();
                
                // Prevent default actions, e.g. history swipe
                event.preventDefault();
                touchEvent.preventDefault();
            }
    
          }
        }
  
      });
      
      
      $(document).bind(touchEndEvent, function(event) {
      //$element.bind(touchEndEvent, function(event) {
        
        // touch end
        if (touchCurrentPos != null) {   
      
          var touchEvent = event.originalEvent;
  
          var currentIndex = slideView.indexOf(currentItem);
          var newIndex = currentIndex;
            
          var dx = touchCurrentPos.x - touchStartPos.x;
          var dy = touchCurrentPos.y - touchStartPos.y;
  
          var maxTouchTime = options.maxTouchTime;
          
          var touchTime = new Date().getTime() - touchStartTime;
      
          var clw = element.clientWidth;
          var clh = element.clientHeight;
          
          var v = touchInitialVector;
          
          var o = Math.abs(dx) / Math.abs(dy);
          
          touchCurrentPos = null;
          
          // TODO: dynamic duration based on momentum
          var duration = options.transition.duration;
          
          invalidateScrollPosition();
  
          if (o >= 1 && dx != 0) {
  
            var s = getScrollPosition();
            var ns = {x: s.x, y: s.y};
            
            if (touchTime < maxTouchTime) {
              
              ns.x = dx < 0 ? 
                Math.ceil(s.x / clw) * clw : dx > 0 ?
                Math.floor(s.x / clw) * clw : Math.round(s.x / clw) * clw;
  
            } else {
              
              var scrollOffset = s.x / clw - Math.round(s.x / clw);
              
              ns.x = dx > 0 && scrollOffset > 0.5 < 0 ? 
                Math.ceil(s.x / clw) * clw : dx < 0 && scrollOffset < -0.5 ?
                Math.floor(s.x / clw) * clw : Math.round(s.x / clw) * clw;
  
            }
            
            isDraggable = false;
            // TODO: call slideTo
            //var e = getItemAtScrollPosition(ns.x, ns.y);
            //slideView.slideTo(e, {transition: 'swipe', duration: duration});
            var item = getItemAtScrollPosition(ns.x, ns.y);
            slideIndex = slideView.indexOf(item);
            slide(item);
            setScrollPosition(ns.x, ns.y, duration);
            
          }
          
        }
        
      });  
  
    }
    
   
    function initControls() {
      var touchEndEvent = isTouch ? 'touchend' : 'click';
      $element.bind(touchEndEvent, function(event) {
        var nextButton = getOptionElement('nextButton');
        var prevButton = getOptionElement('prevButton');
        var pagination = getOptionElement('pagination');
        if ($(nextButton).is(event.target) || $(nextButton).has(event.target).length) {
          slideView.next();
        }
        if ($(prevButton).is(event.target) || $(prevButton).has(event.target).length) {
          slideView.previous();
        }
        if (pagination) {
          var slideIndex = $(paginationItems).index($(paginationItems).filter(function(index) {
            if ($(this).is(event.target) || $(this).has(event.target).length) {
              return true;
            };
          }));
          if (slideIndex !== -1) {
            slideView.slideTo(slideIndex);
          }
        }
      });
    }
    
    
    function initHistory() {
      
    }
    
    /* INIT */
  
    function init() {
      
      items = [];
      
      animatePlugin = 'animate';
      
      // css transform fallback
      if (options.scrollStyle.indexOf('transform') >= 0) {
        var transformStyle = getVendorStyle('transform');
        if (!transformStyle) {
          options.scrollStyle = 'position';
        }
      }
      
      $element.css({
        overflow: 'hidden'
      });
      
      if (isTouch) {
        $element.addClass('slideview-touch');
      }
      
      // init container
      $container = $(options.contentSelector, $element);
      container = $container[0];
      if (!container) {
        container = element.ownerDocument.createElement('div');
        $container = $(container);
        $element.append(container);
      }
      
      $container.css({
        position: 'absolute', 
        left: 0, 
        top: 0, 
        width: '100%', 
        height: '100%'
      });
      
      // Collect initial items
      var initialItems = options.items || (function(parent) {
        var items = [];
        for (var i = 0; i < parent.childNodes.length; i++) {
          var child = parent.childNodes[i];
          if (isItem(child)) {
            items.push(child);
          }/* else {
            parent.removeChild(child);
            i--;
          }*/
        }
        return items;
      })(container || element);
      
      
      // add items to container
    
      // init interaction
      initTouchInteraction();
      initKeyboardInteraction();
      initMouseWheelInteraction();
      initControls();
      
      // add items
      invalidateFlag = false;
      for (var i = 0; i < initialItems.length; i++) {
        this.add(initialItems[i]);
      }
      
      invalidateFlag = true;
      this.invalidate();
      this.slideTo(options.slideIndex >= 0 ? options.slideIndex : 0, {duration: 0});
      
    }
    // init plugin
    init.call(this);
    
  };
  
  // bootstrap plugin
 
  $.fn[pluginName] = function(options) {
    return this.each(function() {
        var instance = $(this).data(pluginName);
        if (!instance) {
          instance = $(this).data(pluginName, new pluginClass(this, options));
        } else {
          instance.setOptions(options);
        }
        
        return $(this);
    });
  };
  
  
})(jQuery, window);