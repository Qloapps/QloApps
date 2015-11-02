/*!
 * jScroll - jQuery Plugin for Infinite Scrolling / Auto-Paging - v2.2.4
 * Modified to work with link outside the scroll block
 * http://jscroll.com/
 *
 * Copyright 2011-2013, Philip Klauzinski
 * http://klauzinski.com/
 * Dual licensed under the MIT and GPL Version 2 licenses.
 * http://jscroll.com/#license
 * http://www.opensource.org/licenses/mit-license.php
 * http://www.gnu.org/licenses/gpl-2.0.html
 *
 * @author Philip Klauzinski
 * @requires jQuery v1.4.3+
 */
(function($) {

    // Define the jscroll namespace and default settings
    $.jscroll = {
        defaults: {
            debug: false,
            autoTrigger: true,
            autoTriggerUntil: false,
            padding: 20,
            nextSelector: 'a.jscroll-next:last',
            contentSelector: '',
            pagingSelector: '',
            callback: false
        }
    };

    // Constructor
    var jScroll = function($e, options) {

        // Private vars
        var _data = $e.data('jscroll'),
            _userOptions = (typeof options === 'function') ? { callback: options } : options,
            _options = $.extend({}, $.jscroll.defaults, _userOptions, _data || {}),
            _$next = $e.parent().find(_options.nextSelector).first(),
            _$window = $(window),
            _$body = $('body'),
            _$scroll = $e,
            _nextHref = $.trim(_$next.attr('href') + ' ' + _options.contentSelector);

        // Initialization
        $e.data('jscroll', $.extend({}, _data, {initialized: true, waiting: false, nextHref: _nextHref}));
        _setBindings();

        // Private methods

        // Find the next link's parent, or add one, and hide it
        function _nextWrap($next) {
            if (_options.pagingSelector) {
                var $parent = $next.closest(_options.pagingSelector).hide();
            } else {
                if (!$next.parent().hasClass('jscroll-next-parent')) {
                    $next.wrap('<div class="jscroll-next-parent" />').parent().hide();
                }
            }
        }

        // Remove the jscroll behavior and data from an element
        function _destroy() {
            return _$scroll.unbind('.jscroll')
                .removeData('jscroll');
        }

        // Observe the scroll event for when to trigger the next load
        function _observe() {
            var $inner = $e,
                data = $e.data('jscroll'),
                borderTopWidth = parseInt($e.css('borderTopWidth')),
                borderTopWidthInt = isNaN(borderTopWidth) ? 0 : borderTopWidth,
                iContainerTop = parseInt($e.css('paddingTop')) + borderTopWidthInt,
                iTopHeight = _$scroll.scrollTop() + _$scroll.outerHeight();
            var totalHeight = 0;

            if (_$scroll.children().length) {
                totalHeight = _$scroll.children().last().position().top - _$scroll.children().first().position().top;
            }
            if (!data.waiting && iTopHeight >= totalHeight && totalHeight) {
                //data.nextHref = $.trim(data.nextHref + ' ' + _options.contentSelector);
                _debug('info', 'jScroll:', totalHeight - iTopHeight, 'from bottom. Loading next request...');
                return _load();
            }
        }

        // Check if the href for the next set of content has been set
        function _checkNextHref(data) {
            data = data || $e.data('jscroll');
            if (!data || !data.nextHref) {
                _debug('warn', 'jScroll: nextSelector not found - destroying');
                _destroy();
                return false;
            } else {
                _setBindings();
                return true;
            }
        }

        function _setBindings() {
            var $next = $e.parent().find(_options.nextSelector).first();
            if (_options.autoTrigger && (_options.autoTriggerUntil === false || _options.autoTriggerUntil > 0)) {
                _nextWrap($next);
                if (_$body.height() <= _$window.height()) {
                    _observe();
                }
                _$scroll.unbind('.jscroll').bind('scroll.jscroll', function() {
                    return _observe();
                });
                if (_options.autoTriggerUntil > 0) {
                    _options.autoTriggerUntil--;
                }
            } else {
                _$scroll.unbind('.jscroll');
                $next.bind('click.jscroll', function() {
                    _nextWrap($next);
                    _load();
                    return false;
                });
            }
        }

        // Load the next set of content, if available
        function _load(href) {
            var $inner = $e,
                data = $e.data('jscroll'),
                append = true;

            data.waiting = true;

            if (typeof(href) == 'undefined') {
                href = data.nextHref;
            } else {
                append = false;
            }

            return $.get(href, function(r, status, xhr) {
                    if (status === 'error') {
                        return _destroy();
                    }
                    $inner.fadeTo("fast", 0.5);
                    if (append) {
                        $inner.append(r.html);
                    } else {
                        $inner.html(r.html);
                        $inner.scrollTop(0);
                    }
                    data.waiting = false;
                    data.nextHref = r.next_link;
                    $('.jscroll-next', $e.parent()).attr('href', r.next_link);
                    _checkNextHref();
                    if (_options.callback) {
                        _options.callback.call(this);
                    }
                    $inner.fadeTo("fast", 1);
                    _debug('dir', data);
                }, 'json');
        }

        // Safe console debug - http://klauzinski.com/javascript/safe-firebug-console-in-javascript
        function _debug(m) {
            if (_options.debug && typeof console === 'object' && (typeof m === 'object' || typeof console[m] === 'function')) {
                if (typeof m === 'object') {
                    var args = [];
                    for (var sMethod in m) {
                        if (typeof console[sMethod] === 'function') {
                            args = (m[sMethod].length) ? m[sMethod] : [m[sMethod]];
                            console[sMethod].apply(console, args);
                        } else {
                            console.log.apply(console, args);
                        }
                    }
                } else {
                    console[m].apply(console, Array.prototype.slice.call(arguments, 1));
                }
            }
        }

        // Expose API methods via the jQuery.jscroll namespace, e.g. $('sel').jscroll.method()
        $e.data('jscrollapi', {
            destroy: _destroy,
            load_scroll: _load
        });
        $.extend($e.jscroll, {
            destroy: _destroy,
            load_scroll: _load
        });
        return $e;
    };

    // Define the jscroll plugin method and loop
    $.fn.jscroll = function(m) {
        return this.each(function() {
            var $this = $(this),
                data = $this.data('jscroll');
            // Instantiate jScroll on this element if it hasn't been already
            if (data && data.initialized) return;
            var jscroll = new jScroll($this, m);
        });
    };
})(jQuery);/*!
 * jScroll - jQuery Plugin for Infinite Scrolling / Auto-Paging - v2.2.4
 * Modified to work with link outside the scroll block
 * http://jscroll.com/
 *
 * Copyright 2011-2013, Philip Klauzinski
 * http://klauzinski.com/
 * Dual licensed under the MIT and GPL Version 2 licenses.
 * http://jscroll.com/#license
 * http://www.opensource.org/licenses/mit-license.php
 * http://www.gnu.org/licenses/gpl-2.0.html
 *
 * @author Philip Klauzinski
 * @requires jQuery v1.4.3+
 */
(function($) {

    // Define the jscroll namespace and default settings
    $.jscroll = {
        defaults: {
            debug: false,
            autoTrigger: true,
            autoTriggerUntil: false,
            padding: 20,
            nextSelector: 'a.jscroll-next:last',
            contentSelector: '',
            pagingSelector: '',
            callback: false
        }
    };

    // Constructor
    var jScroll = function($e, options) {

        // Private vars
        var _data = $e.data('jscroll'),
            _userOptions = (typeof options === 'function') ? { callback: options } : options,
            _options = $.extend({}, $.jscroll.defaults, _userOptions, _data || {}),
            _$next = $e.parent().find(_options.nextSelector).first(),
            _$window = $(window),
            _$body = $('body'),
            _$scroll = $e,
            _nextHref = $.trim(_$next.attr('href') + ' ' + _options.contentSelector);

        // Initialization
        $e.data('jscroll', $.extend({}, _data, {initialized: true, waiting: false, nextHref: _nextHref}));
        _setBindings();

        // Private methods

        // Find the next link's parent, or add one, and hide it
        function _nextWrap($next) {
            if (_options.pagingSelector) {
                var $parent = $next.closest(_options.pagingSelector).hide();
            } else {
                if (!$next.parent().hasClass('jscroll-next-parent')) {
                    $next.wrap('<div class="jscroll-next-parent" />').parent().hide();
                }
            }
        }

        // Remove the jscroll behavior and data from an element
        function _destroy() {
            return _$scroll.unbind('.jscroll')
                .removeData('jscroll');
        }

        // Observe the scroll event for when to trigger the next load
        function _observe() {
            var $inner = $e,
                data = $e.data('jscroll'),
                borderTopWidth = parseInt($e.css('borderTopWidth')),
                borderTopWidthInt = isNaN(borderTopWidth) ? 0 : borderTopWidth,
                iContainerTop = parseInt($e.css('paddingTop')) + borderTopWidthInt,
                iTopHeight = _$scroll.scrollTop() + _$scroll.outerHeight();
            var totalHeight = 0;

            if (_$scroll.children().length) {
                totalHeight = _$scroll.children().last().position().top - _$scroll.children().first().position().top;
            }
            if (!data.waiting && iTopHeight >= totalHeight && totalHeight) {
                //data.nextHref = $.trim(data.nextHref + ' ' + _options.contentSelector);
                _debug('info', 'jScroll:', totalHeight - iTopHeight, 'from bottom. Loading next request...');
                return _load();
            }
        }

        // Check if the href for the next set of content has been set
        function _checkNextHref(data) {
            data = data || $e.data('jscroll');
            if (!data || !data.nextHref) {
                _debug('warn', 'jScroll: nextSelector not found - destroying');
                _destroy();
                return false;
            } else {
                _setBindings();
                return true;
            }
        }

        function _setBindings() {
            var $next = $e.parent().find(_options.nextSelector).first();
            if (_options.autoTrigger && (_options.autoTriggerUntil === false || _options.autoTriggerUntil > 0)) {
                _nextWrap($next);
                if (_$body.height() <= _$window.height()) {
                    _observe();
                }
                _$scroll.unbind('.jscroll').bind('scroll.jscroll', function() {
                    return _observe();
                });
                if (_options.autoTriggerUntil > 0) {
                    _options.autoTriggerUntil--;
                }
            } else {
                _$scroll.unbind('.jscroll');
                $next.bind('click.jscroll', function() {
                    _nextWrap($next);
                    _load();
                    return false;
                });
            }
        }

        // Load the next set of content, if available
        function _load(href) {
            var $inner = $e,
                data = $e.data('jscroll'),
                append = true;

            data.waiting = true;

            if (typeof(href) == 'undefined') {
                href = data.nextHref;
            } else {
                append = false;
            }

            return $.get(href, function(r, status, xhr) {
                    if (status === 'error') {
                        return _destroy();
                    }
                    $inner.fadeTo("fast", 0.5);
                    if (append) {
                        $inner.append(r.html);
                    } else {
                        $inner.html(r.html);
                        $inner.scrollTop(0);
                    }
                    data.waiting = false;
                    data.nextHref = r.next_link;
                    $('.jscroll-next', $e.parent()).attr('href', r.next_link);
                    _checkNextHref();
                    if (_options.callback) {
                        _options.callback.call(this);
                    }
                    $inner.fadeTo("fast", 1);
                    _debug('dir', data);
                }, 'json');
        }

        // Safe console debug - http://klauzinski.com/javascript/safe-firebug-console-in-javascript
        function _debug(m) {
            if (_options.debug && typeof console === 'object' && (typeof m === 'object' || typeof console[m] === 'function')) {
                if (typeof m === 'object') {
                    var args = [];
                    for (var sMethod in m) {
                        if (typeof console[sMethod] === 'function') {
                            args = (m[sMethod].length) ? m[sMethod] : [m[sMethod]];
                            console[sMethod].apply(console, args);
                        } else {
                            console.log.apply(console, args);
                        }
                    }
                } else {
                    console[m].apply(console, Array.prototype.slice.call(arguments, 1));
                }
            }
        }

        // Expose API methods via the jQuery.jscroll namespace, e.g. $('sel').jscroll.method()
        $e.data('jscrollapi', {
            destroy: _destroy,
            load_scroll: _load
        });
        $.extend($e.jscroll, {
            destroy: _destroy,
            load_scroll: _load
        });
        return $e;
    };

    // Define the jscroll plugin method and loop
    $.fn.jscroll = function(m) {
        return this.each(function() {
            var $this = $(this),
                data = $this.data('jscroll');
            // Instantiate jScroll on this element if it hasn't been already
            if (data && data.initialized) return;
            var jscroll = new jScroll($this, m);
        });
    };
})(jQuery);