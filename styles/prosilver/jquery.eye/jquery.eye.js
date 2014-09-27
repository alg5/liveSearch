var el_open = 1;
var el_close = 0;
//*************************************
//*************************************
//*************************************
/**
 * jquery.eye.js
 * Version 1.0.0
 * Copyright (c) Algl <chgk.globusl@gmail.com>
 */
(function ($) {

var prefix = '_eye_';
    /**
    * eye Object
    * @param {jQuery} $elem jQuery object 
    * @param {Object=} options Settings
    * @constructor
    */

    $.cls_eye = function ($elem, options) {

    this.options = options;
        /**
        * Init and sanitize options
        */
     if (options.name != undefined)
        this.options.name = prefix + options.name;
    if (options.float != undefined)
        this.options.float = options.float;
    if (options.title_open != undefined)
        this.options.title_open = options.title_open;
    if (options.title_close != undefined)
        this.options.title_close = options.title_close;
    if (options.id != undefined)
        this.options.id_arr = options.id;
    if (options.class != undefined)
        this.options.class_arr = options.class;

        this.options.visibleStatus = el_open;
        if (this.options.name) {
            this.options.visibleStatus = localStorage.getItem(this.options.name);
            if (this.options.visibleStatus == null || isNaN(this.options.visibleStatus)) { this.options.visibleStatus = el_open; }
        }


 
        /**
        * Init DOM elements repository
        */
        this.dom = {};

        /**
        * Store the input element we're attached to in the repository, add class
        */
        this.dom.$elem = $elem;
  
        /**
        * Create DOM element  - button "eye"
        */
    var btn = '<div style="float:' + this.options.float + ';">';
    btn = btn + '<div ';
    if (this.options.name != undefined) {
        btn = btn + ' id="' + this.options.name + '"';

    }
    if (this.options.visibleStatus == el_open) {
        btn = btn + 'class="eye_btn eye_btn_open"  ';
        if (this.options.title_close)
            btn = btn + 'title="' + this.options.title_close + '"';
    }
    else {
        btn = btn + 'class="eye_btn eye_btn_close"  ';
        if (this.options.title_open)
            btn = btn + 'title="' + this.options.title_open + '"';
    }
    btn = btn + '> </div> </div>';
       var new_content = $('#wrap').html() + btn;
   $('#wrap').html(new_content) ;
    this.dom.$btn_eye = $('#' +  this.options.name);

        /**
        * Shortcut to self
        */
        var self = this;

    //Init  
    if (this.options.visibleStatus == el_close ) {
    self.change_visible();
        }

   
   //fire click event
   $(this.dom.$btn_eye).on('click', function (e) {
    var visibleStatus = localStorage.getItem(self.options.name);
        if (visibleStatus == null || isNaN(visibleStatus)) { visibleStatus = el_open; }
        if (parseInt(visibleStatus) === parseInt(el_open)) {
            self.options.visibleStatus =  el_close;
            localStorage.setItem(self.options.name, el_close);
            $(this).removeClass('eye_btn_open').addClass('eye_btn_close');
            $(this).attr('title', self.options.title_open);
        }
        else {
            self.options.visibleStatus = el_open;
            localStorage.setItem(self.options.name, el_open);
            $(this).removeClass('eye_btn_close').addClass('eye_btn_open');
            $(this).attr('title', self.options.title_close);
        }
         self.change_visible();
    }); //end event click


    };
  
     $.cls_eye.prototype.change_visible = function ()
    {
    var visibleStatus = localStorage.getItem(this.options.name);
        if (visibleStatus == null || isNaN(visibleStatus)) { visibleStatus = el_open; }
        if (parseInt(visibleStatus) === parseInt(el_open))
        {
            this.show();
        }
        else
        {
            this.hide();
        }

    }

    $.cls_eye.prototype.show = function () {
        if (this.options.id_arr && this.options.id_arr.length > 0) {
            $.each(this.options.id_arr, function (i, val) {
                $('#' + val).show();
            });
        }
        if (this.options.class_arr && this.options.class_arr.length > 0) {
            $.each(this.options.class_arr, function (i, val) {
                $('.' + val).show();
            });
        }

    };
    $.cls_eye.prototype.hide = function () {
        if (this.options.id_arr && this.options.id_arr.length > 0) {
            $.each(this.options.id_arr, function (i, val) {
                $('#' + val).hide();
            });
        }
        if (this.options.class_arr && this.options.class_arr.length > 0) {
            $.each(this.options.class_arr, function (i, val) {
                $('.' + val).hide();
            });
        }
    };



//    alert('jquery');
      /**
    * eye plugin
    */
    $.fn.eye = function (options) {
        var o = $.extend({}, $.fn.eye.defaults, options);
        return this.each(function () {
            var $this = $(this);
            var ac = new $.cls_eye($this, o);

            $this.data('cls_eye', ac);

        });

    }; 

   /**
    * Default options for autocomplete plugin
    */
    $.fn.eye.defaults = {

        float : 'right',
        top : '14px',
        title_open : '',
        title_close : '',

    };



})(jQuery);



