/**
 * jquery.autocomplete.js
 * Version 3.2
 * Copyright (c)Alg <chgk.globusl@gmail.com>
 */
  function Eye1()
    {
        this.ids = new Array();

    }
(function ($) {
 alert('eye');
return;
   function Eye()
    {
        this.ids = new Array();

    }


    /**
    * Eye Object
    * @param {jQuery} $elem jQuery object with one input tag
    * @param {Object=} options Settings
    * @constructor
    */
    $.Eye = function ($elem, options) {

        /**
        * Cached data
        * @type Object
        * @private
        */
        this.cacheData_ = {};

        /**
        * Number of cached data items
        * @type number
        * @private
        */
        this.cacheLength_ = 0;

        /**
        * Class name to mark selected item
        * @type string
        * @private
        */
        this.selectClass_ = 'jquery-autocomplete-selected-item';

        /**
        * Is this Eye open?
        * @type boolean
        * @private
        */
        this.open_ = true;

 
        /**
        * Assert parameters
        */
        if (!$elem || !($elem instanceof jQuery) || $elem.length !== 1 || $elem.get(0).tagName.toUpperCase() !== 'INPUT') {
            alert('Invalid parameter for jquery.Eye, jQuery object with one element with INPUT tag expected');
            return;
        }

        /**
        * Init and sanitize options
        */
//        if (typeof options === 'string') {
//            this.options = { url: options };
//        } else {
//            this.options = options;
//        }
        this.options.maxCacheLength = parseInt(this.options.maxCacheLength);
        if (isNaN(this.options.maxCacheLength) || this.options.maxCacheLength < 1) {
            this.options.maxCacheLength = 1;
        }
 
        /**
        * Init DOM elements repository
        */
        this.dom = {};

        /**
        * Store the input element we're attached to in the repository, add class
        */
        this.dom.$elem = $elem;
        if (this.options.inputClass) {
            this.dom.$elem.addClass(this.options.inputClass);
        }

        /**
        * Create DOM element 
        */
        // create ShowHide button
	    //$("#leavesearch_btn").before('<div style="float: right;"><div id="leavesearch-ShowHideBtn" style="background: url(/ext/alg/liveSearch/styles/prosilver/theme/images/show-hide.png) 0 14px; cursor: pointer; position: fixed; top: 14px; width:14px; height:14px; visibility: hidden;" ></div></div>');

        //this.dom.$results = $('<div></div>').hide();
        this.dom.$results = $('<div style="float: ' + this.options.float + ';"><div id="' + this.options.id + '" class="eye_btn_open" style=" top: 24px; width:14px; height:14px; " ></div></div>');
        if (this.options.resultsClass) {
            this.dom.$results.addClass(this.options.resultsClass);
        }

//        var pos = this.options.fixedPos ? 'fixed' : 'absolute';
//        this.dom.$results.css({
//            position: pos,
//           
//        });
        //this.dom.$results.width(this.options.width);
        $('body').append(this.dom.$results);

        /**
        * Shortcut to self
        */
        var self = this;

//    $.Autocompleter.prototype.position = function () {
//        var offset = this.dom.$elem.offset();
//        var pos = this.dom.$elem.position();

//        this.dom.$results.css({
//            top: pos.top + this.dom.$elem.outerHeight(),
//            left: offset.left,
//        });
//    };

//    $.Autocompleter.prototype.cacheRead = function (filter) {
//        var filterLength, searchLength, search, maxPos, pos;
//        if (this.options.useCache) {
//            filter = String(filter);
//            filterLength = filter.length;
//            if (this.options.matchSubset) {
//                searchLength = 1;
//            } else {
//                searchLength = filterLength;
//            }
//            while (searchLength <= filterLength) {
//                if (this.options.matchInside) {
//                    maxPos = filterLength - searchLength;
//                } else {
//                    maxPos = 0;
//                }
//                pos = 0;
//                while (pos <= maxPos) {
//                    search = filter.substr(0, searchLength);
//                    if (this.cacheData_[search] !== undefined) {
//                        return this.cacheData_[search];
//                    }
//                    pos++;
//                }
//                searchLength++;
//            }
//        }
//        return false;
//    };

//    $.Autocompleter.prototype.cacheWrite = function (filter, data) {
//        if (this.options.useCache) {
//            if (this.cacheLength_ >= this.options.maxCacheLength) {
//                this.cacheFlush();
//            }
//            filter = String(filter);
//            if (this.cacheData_[filter] !== undefined) {
//                this.cacheLength_++;
//            }
//            return this.cacheData_[filter] = data;
//        }
//        return false;
//    };

//    $.Autocompleter.prototype.cacheFlush = function () {
//        this.cacheData_ = {};
//        this.cacheLength_ = 0;
//    };

    $.Autocompleter.prototype.callHook = function (hook, data) {
        var f = this.options[hook];
        if (f && $.isFunction(f)) {
            return f(data, this);
        }
        return false;
    };

    $.Autocompleter.prototype.activate = function () {
        var self = this;
        var activateNow = function () {
            self.activateNow();
        };
        var delay = parseInt(this.options.delay);
        if (isNaN(delay) || delay <= 0) {
            delay = 250;
        }
        if (this.keyTimeout_) {
            clearTimeout(this.keyTimeout_);
        }
        this.keyTimeout_ = setTimeout(activateNow, delay);
        this.callHook('onStart');

    };

    $.Autocompleter.prototype.activateNow = function () {
        var value = this.dom.$elem.val();
        if (value !== this.lastProcessedValue_ && value !== this.lastSelectedValue_) {
            if (value.length >= this.options.minChars) {
                this.active_ = true;
                this.lastProcessedValue_ = value;
                this.fetchData(value);
            }
        }
    };

    $.Autocompleter.prototype.fetchData = function (value) {
        if (this.options.data) {
            this.filterAndShowResults(this.options.data, value);
        } else {
            var self = this;
            this.fetchRemoteData(value, function (remoteData) {
                self.filterAndShowResults(remoteData, value);
            });
        }
    };

    $.Autocompleter.prototype.fetchRemoteData = function (filter, callback) {
        var data = this.cacheRead(filter);
        if (data) {
            callback(data);
        } else {
            var self = this;
            this.dom.$elem.addClass(this.options.loadingClass);
            var ajaxCallback = function (data) {
                var parsed = false;
                if (data !== false) {
                    parsed = self.parseRemoteData(data);
                    self.cacheWrite(filter, parsed);
                }
                self.dom.$elem.removeClass(self.options.loadingClass);
                callback(parsed);
            };
            $.ajax({
                url: this.makeUrl(filter),
                success: ajaxCallback,
                error: function () {
                    ajaxCallback(false);
                }
            });
        }
    };

    $.Autocompleter.prototype.setExtraParam = function (name, value) {
        var index = $.trim(String(name));
        if (index) {
            if (!this.options.extraParams) {
                this.options.extraParams = {};
            }
            if (this.options.extraParams[index] !== value) {
                this.options.extraParams[index] = value;
                this.cacheFlush();
            }
        }
    };




    $.Autocompleter.prototype.focusNext = function () {
        this.focusMove(+1);
    };

    $.Autocompleter.prototype.focusPrev = function () {
        this.focusMove(-1);
    };

    $.Autocompleter.prototype.focusMove = function (modifier) {
        var i, $items = $('li', this.dom.$results);
        modifier = parseInt(modifier);
        for (var i = 0; i < $items.length; i++) {
            if ($($items[i]).hasClass(this.selectClass_)) {
                this.focusItem(i + modifier);
                return;
            }
        }
        this.focusItem(0);
    };

    $.Autocompleter.prototype.focusItem = function (item) {
        var $item, $items = $('li', this.dom.$results);
        if ($items.length) {
            $items.removeClass(this.selectClass_).removeClass(this.options.selectClass);
            if (typeof item === 'number') {
                item = parseInt(item);
                if (item < 0) {
                    item = 0;
                } else if (item >= $items.length) {
                    item = $items.length - 1;
                }
                $item = $($items[item]);
            } else {
                $item = $(item);
            }
            if ($item) {
                $item.addClass(this.selectClass_).addClass(this.options.selectClass);

            }
        }
    };

  
    $.Autocompleter.prototype.finish = function () {
        if (this.keyTimeout_) {
            clearTimeout(this.keyTimeout_);
        }
        if (this.dom.$elem.val() !== this.lastSelectedValue_) {
            if (this.options.mustMatch) {
                this.dom.$elem.val('');
            }
            this.callHook('onNoMatch');
        }
        this.dom.$results.hide();
        this.lastKeyPressed_ = null;
        this.lastProcessedValue_ = null;
        if (this.active_) {
            this.callHook('onFinish');
        }
        this.active_ = false;
    };

    $.Autocompleter.prototype.selectRange = function (start, end) {
        var input = this.dom.$elem.get(0);
        if (input.setSelectionRange) {
            input.focus();
            input.setSelectionRange(start, end);
        } else if (this.createTextRange) {
            var range = this.createTextRange();
            range.collapse(true);
            range.moveEnd('character', end);
            range.moveStart('character', start);
            range.select();
        }
    };

    $.Autocompleter.prototype.setCaret = function (pos) {
        this.selectRange(pos, pos);
    };

    /**
    * autocomplete plugin
    */
    $.fn.eye = function (options) {
        if (typeof options === 'string') {
            options = {
                url: options
            };
        }
        var o = $.extend({}, $.fn.eye.defaults, options);
        return this.each(function () {
            var $this = $(this);
            var ac = new $.eye($this, o);
            $this.data('eye', ac);
        });

    };

    /**
    * Default options for autocomplete plugin
    */
    $.fn.eye.defaults = {
        float: 'right',
//        resultsClass: 'acResults',
//        inputClass: 'acInput',
//        selectClass: 'acSelect',
//        top: 24px,
//        width: 14px,
//        height:14px;
    };

})(jQuery);
