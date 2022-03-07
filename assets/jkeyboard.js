// the semi-colon before function invocation is a safety net against concatenated
// scripts and/or other plugins which may not be closed properly.
; (function ($, window, document, undefined) {

  // undefined is used here as the undefined global variable in ECMAScript 3 is
  // mutable (ie. it can be changed by someone else). undefined isn't really being
  // passed in so we can ensure the value of it is truly undefined. In ES5, undefined
  // can no longer be modified.

  // window and document are passed through as local variable rather than global
  // as this (slightly) quickens the resolution process and can be more efficiently
  // minified (especially when both are regularly referenced in your plugin).

  // Create the defaults once
  let pluginName = "jkeyboard",
    defaults = {
      layout: "azerty",
      selectable: ['azerty'],
      input: $('#input'),
      customLayouts: {
        selectable: []
      },
    };


  var function_keys = {
    backspace: {
      text: '&nbsp;',
    },
    return: {
      text: 'Enter'
    },
    shift: {
      text: '&nbsp;'
    },
    space: {
      text: '&nbsp;'
    },
    numeric_switch: {
      text: '123',
      command: function () {
        this.createKeyboard('numeric');
        this.events();
      }
    },
    layout_switch: {
      text: '&nbsp;',
      command: function () {
        var l = this.toggleLayout();
        this.createKeyboard(l);
        this.events();
      }
    },
    character_switch: {
      text: 'ABC',
      command: function () {
        this.createKeyboard(layout);
        this.events();
      }
    },
    symbol_switch: {
      text: '#+=',
      command: function () {
        this.createKeyboard('symbolic');
        this.events();
      }
    }
  };


  var layouts = {
    azerty: [
      ['a', 'z', 'e', 'r', 't', 'y', 'u', 'i', 'o', 'p',],
      ['q', 's', 'd', 'f', 'g', 'h', 'j', 'k', 'l','m'],
      ['backspace', 'w', 'x', 'c', 'v', 'b', 'n', ['return']]
    ]
  }

  var shift = false, capslock = false, layout = 'azerty', layout_id = 0;

  // The actual plugin constructor
  function Plugin(element, options) {
    this.element = element;
    // jQuery has an extend method which merges the contents of two or
    // more objects, storing the result in the first object. The first object
    // is generally empty as we don't want to alter the default options for
    // future instances of the plugin
    this.settings = $.extend({}, defaults, options);
    // Extend & Merge the cusom layouts
    layouts = $.extend(true, {}, this.settings.customLayouts, layouts);
    if (Array.isArray(this.settings.customLayouts.selectable)) {
      $.merge(this.settings.selectable, this.settings.customLayouts.selectable);
    }
    this._defaults = defaults;
    this._name = pluginName;
    this.init();
  }

  Plugin.prototype = {
    init: function () {
      layout = this.settings.layout;
      this.createKeyboard(layout);
      this.events();
    },

    setInput: function (newInputField) {
      this.settings.input = newInputField;
    },

    createKeyboard: function (layout) {
      shift = false;
      capslock = false;

      var keyboard_container = $('<ul/>').addClass('jkeyboard'),
        me = this;

      layouts[layout].forEach(function (line, index) {
        var line_container = $('<li/>').addClass('jline');
        line_container.append(me.createLine(line));
        keyboard_container.append(line_container);
      });

      $(this.element).html('').append(keyboard_container);
    },

    createLine: function (line) {
      var line_container = $('<ul/>');

      line.forEach(function (key, index) {
        var key_container = $('<li/>').addClass('jkey').data('command', key);

        if (function_keys[key]) {
          key_container.addClass(key).html(function_keys[key].text);
        }
        else {
          key_container.addClass('letter').html(key);
        }

        line_container.append(key_container);
      })

      return line_container;
    },

    events: function () {
      var letters = $(this.element).find('.letter'),
        shift_key = $(this.element).find('.shift'),
        space_key = $(this.element).find('.space'),
        backspace_key = $(this.element).find('.backspace'),
        return_key = $(this.element).find('.return'),

        me = this,
        fkeys = Object.keys(function_keys).map(function (k) {
          return '.' + k;
        }).join(',');

      letters.on('click', function () {
        me.type((shift || capslock) ? $(this).text().toUpperCase() : $(this).text());
      });

      space_key.on('click', function () {
        me.type(' ');
      });

      return_key.on('click', function () {
        me.type("\n");
        me.settings.input.parents('form').submit();
      });

      backspace_key.on('click', function () {
        me.backspace();
      });

      shift_key.on('click', function () {
        if (shift) {
          me.toggleShiftOff();
        } else {
          me.toggleShiftOn();
        }
      }).on('dblclick', function () {
        me.toggleShiftOn(true);
      });


      $(fkeys).on('click', function (e) {
        //prevent bubbling to avoid side effects when used as floating keyboard which closes on click outside of keyboard container
        e.stopPropagation();

        var command = function_keys[$(this).data('command')].command;
        if (!command) return;

        command.call(me);
      });
    },

    type: function (key) {
      var input = this.settings.input,
        val = input.val(),
        input_node = input.get(0),
        start = input_node.selectionStart,
        end = input_node.selectionEnd;
      input.val("");
      input.val(key);
      if(key !== "\n"){
        let current_cell = $('.current_cell');
        current_cell.html("<div>" + key + "</div>")
        if(!current_cell.hasClass('last_cell')){
          current_cell.next().addClass('current_cell').html("<div>_</div>");
        }
        current_cell.toggleClass('current_cell');
      }

    },

    toggleLayout: function () {
      layout_id = layout_id || 0;
      var plain_layouts = this.settings.selectable;
      layout_id++;

      var current_id = layout_id % plain_layouts.length;
      return plain_layouts[current_id];
    },

    backspace: function () {
      var input = this.settings.input,
        input_node = input.get(0),
        start = input_node.selectionStart,
        val = input.val();
        let current_cell = $('.current_cell');
        let last_cell = $('.last_cell');
        if(current_cell.length > 0){
          if(!current_cell.prev().hasClass('first_cell')){
            current_cell.prev().addClass('current_cell').html("<div>_</div>");
            current_cell.removeClass('current_cell').html("<div>.</div>");
          }
        } else {
          last_cell.prev().addClass('current_cell').html("<div>_</div>");
          last_cell.html("<div>.</div>");
        }
    },
    insertToString: function (start, end, string, insert_string) {
      return string.substring(0, start) + insert_string + string.substring(end, string.length);
    }
  };


  var methods = {
    init: function(options) {
      if (!this.data("plugin_" + pluginName)) {
        this.data("plugin_" + pluginName, new Plugin(this, options));
      }
    },
    setInput: function(content) {
      this.data("plugin_" + pluginName).setInput($(content));
    },
    setLayout: function(layoutname) {
      // change layout if it is not match current
      object = this.data("plugin_" + pluginName);
      if (typeof(layouts[layoutname]) !== 'undefined' && object.settings.layout != layoutname) {
        object.settings.layout = layoutname;
        object.createKeyboard(layoutname);
        object.events();
      };
    },
  };

  $.fn[pluginName] = function (methodOrOptions) {
    if (methods[methodOrOptions]) {
      return methods[methodOrOptions].apply(this.first(), Array.prototype.slice.call( arguments, 1));
    } else if (typeof methodOrOptions === 'object' || ! methodOrOptions) {
      // Default to "init"
      return methods.init.apply(this.first(), arguments);
    } else {
      $.error('Method ' +  methodOrOptions + ' does not exist on jQuery.jkeyboard');
    }
  };

})(jQuery, window, document);
