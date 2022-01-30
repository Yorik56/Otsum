(self["webpackChunk"] = self["webpackChunk"] || []).push([["app"],{

/***/ "./assets/controllers sync recursive ./node_modules/@symfony/stimulus-bridge/lazy-controller-loader.js! \\.(j%7Ct)sx?$":
/*!*******************************************************************************************************************!*\
  !*** ./assets/controllers/ sync ./node_modules/@symfony/stimulus-bridge/lazy-controller-loader.js! \.(j%7Ct)sx?$ ***!
  \*******************************************************************************************************************/
/***/ ((module, __unused_webpack_exports, __webpack_require__) => {

var map = {
	"./hello_controller.js": "./node_modules/@symfony/stimulus-bridge/lazy-controller-loader.js!./assets/controllers/hello_controller.js"
};


function webpackContext(req) {
	var id = webpackContextResolve(req);
	return __webpack_require__(id);
}
function webpackContextResolve(req) {
	if(!__webpack_require__.o(map, req)) {
		var e = new Error("Cannot find module '" + req + "'");
		e.code = 'MODULE_NOT_FOUND';
		throw e;
	}
	return map[req];
}
webpackContext.keys = function webpackContextKeys() {
	return Object.keys(map);
};
webpackContext.resolve = webpackContextResolve;
module.exports = webpackContext;
webpackContext.id = "./assets/controllers sync recursive ./node_modules/@symfony/stimulus-bridge/lazy-controller-loader.js! \\.(j%7Ct)sx?$";

/***/ }),

/***/ "./node_modules/@symfony/stimulus-bridge/dist/webpack/loader.js!./assets/controllers.json":
/*!************************************************************************************************!*\
  !*** ./node_modules/@symfony/stimulus-bridge/dist/webpack/loader.js!./assets/controllers.json ***!
  \************************************************************************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "default": () => (__WEBPACK_DEFAULT_EXPORT__)
/* harmony export */ });
/* harmony default export */ const __WEBPACK_DEFAULT_EXPORT__ = ({
});

/***/ }),

/***/ "./node_modules/@symfony/stimulus-bridge/lazy-controller-loader.js!./assets/controllers/hello_controller.js":
/*!******************************************************************************************************************!*\
  !*** ./node_modules/@symfony/stimulus-bridge/lazy-controller-loader.js!./assets/controllers/hello_controller.js ***!
  \******************************************************************************************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "default": () => (/* binding */ _default)
/* harmony export */ });
/* harmony import */ var core_js_modules_es_object_set_prototype_of_js__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! core-js/modules/es.object.set-prototype-of.js */ "./node_modules/core-js/modules/es.object.set-prototype-of.js");
/* harmony import */ var core_js_modules_es_object_set_prototype_of_js__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(core_js_modules_es_object_set_prototype_of_js__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var core_js_modules_es_object_get_prototype_of_js__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! core-js/modules/es.object.get-prototype-of.js */ "./node_modules/core-js/modules/es.object.get-prototype-of.js");
/* harmony import */ var core_js_modules_es_object_get_prototype_of_js__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(core_js_modules_es_object_get_prototype_of_js__WEBPACK_IMPORTED_MODULE_1__);
/* harmony import */ var core_js_modules_es_object_to_string_js__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! core-js/modules/es.object.to-string.js */ "./node_modules/core-js/modules/es.object.to-string.js");
/* harmony import */ var core_js_modules_es_object_to_string_js__WEBPACK_IMPORTED_MODULE_2___default = /*#__PURE__*/__webpack_require__.n(core_js_modules_es_object_to_string_js__WEBPACK_IMPORTED_MODULE_2__);
/* harmony import */ var core_js_modules_es_reflect_construct_js__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! core-js/modules/es.reflect.construct.js */ "./node_modules/core-js/modules/es.reflect.construct.js");
/* harmony import */ var core_js_modules_es_reflect_construct_js__WEBPACK_IMPORTED_MODULE_3___default = /*#__PURE__*/__webpack_require__.n(core_js_modules_es_reflect_construct_js__WEBPACK_IMPORTED_MODULE_3__);
/* harmony import */ var core_js_modules_es_object_create_js__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(/*! core-js/modules/es.object.create.js */ "./node_modules/core-js/modules/es.object.create.js");
/* harmony import */ var core_js_modules_es_object_create_js__WEBPACK_IMPORTED_MODULE_4___default = /*#__PURE__*/__webpack_require__.n(core_js_modules_es_object_create_js__WEBPACK_IMPORTED_MODULE_4__);
/* harmony import */ var core_js_modules_es_object_define_property_js__WEBPACK_IMPORTED_MODULE_5__ = __webpack_require__(/*! core-js/modules/es.object.define-property.js */ "./node_modules/core-js/modules/es.object.define-property.js");
/* harmony import */ var core_js_modules_es_object_define_property_js__WEBPACK_IMPORTED_MODULE_5___default = /*#__PURE__*/__webpack_require__.n(core_js_modules_es_object_define_property_js__WEBPACK_IMPORTED_MODULE_5__);
/* harmony import */ var core_js_modules_es_symbol_js__WEBPACK_IMPORTED_MODULE_6__ = __webpack_require__(/*! core-js/modules/es.symbol.js */ "./node_modules/core-js/modules/es.symbol.js");
/* harmony import */ var core_js_modules_es_symbol_js__WEBPACK_IMPORTED_MODULE_6___default = /*#__PURE__*/__webpack_require__.n(core_js_modules_es_symbol_js__WEBPACK_IMPORTED_MODULE_6__);
/* harmony import */ var core_js_modules_es_symbol_description_js__WEBPACK_IMPORTED_MODULE_7__ = __webpack_require__(/*! core-js/modules/es.symbol.description.js */ "./node_modules/core-js/modules/es.symbol.description.js");
/* harmony import */ var core_js_modules_es_symbol_description_js__WEBPACK_IMPORTED_MODULE_7___default = /*#__PURE__*/__webpack_require__.n(core_js_modules_es_symbol_description_js__WEBPACK_IMPORTED_MODULE_7__);
/* harmony import */ var core_js_modules_es_symbol_iterator_js__WEBPACK_IMPORTED_MODULE_8__ = __webpack_require__(/*! core-js/modules/es.symbol.iterator.js */ "./node_modules/core-js/modules/es.symbol.iterator.js");
/* harmony import */ var core_js_modules_es_symbol_iterator_js__WEBPACK_IMPORTED_MODULE_8___default = /*#__PURE__*/__webpack_require__.n(core_js_modules_es_symbol_iterator_js__WEBPACK_IMPORTED_MODULE_8__);
/* harmony import */ var core_js_modules_es_array_iterator_js__WEBPACK_IMPORTED_MODULE_9__ = __webpack_require__(/*! core-js/modules/es.array.iterator.js */ "./node_modules/core-js/modules/es.array.iterator.js");
/* harmony import */ var core_js_modules_es_array_iterator_js__WEBPACK_IMPORTED_MODULE_9___default = /*#__PURE__*/__webpack_require__.n(core_js_modules_es_array_iterator_js__WEBPACK_IMPORTED_MODULE_9__);
/* harmony import */ var core_js_modules_es_string_iterator_js__WEBPACK_IMPORTED_MODULE_10__ = __webpack_require__(/*! core-js/modules/es.string.iterator.js */ "./node_modules/core-js/modules/es.string.iterator.js");
/* harmony import */ var core_js_modules_es_string_iterator_js__WEBPACK_IMPORTED_MODULE_10___default = /*#__PURE__*/__webpack_require__.n(core_js_modules_es_string_iterator_js__WEBPACK_IMPORTED_MODULE_10__);
/* harmony import */ var core_js_modules_web_dom_collections_iterator_js__WEBPACK_IMPORTED_MODULE_11__ = __webpack_require__(/*! core-js/modules/web.dom-collections.iterator.js */ "./node_modules/core-js/modules/web.dom-collections.iterator.js");
/* harmony import */ var core_js_modules_web_dom_collections_iterator_js__WEBPACK_IMPORTED_MODULE_11___default = /*#__PURE__*/__webpack_require__.n(core_js_modules_web_dom_collections_iterator_js__WEBPACK_IMPORTED_MODULE_11__);
/* harmony import */ var _hotwired_stimulus__WEBPACK_IMPORTED_MODULE_12__ = __webpack_require__(/*! @hotwired/stimulus */ "./node_modules/@hotwired/stimulus/dist/stimulus.js");
function _typeof(obj) { "@babel/helpers - typeof"; return _typeof = "function" == typeof Symbol && "symbol" == typeof Symbol.iterator ? function (obj) { return typeof obj; } : function (obj) { return obj && "function" == typeof Symbol && obj.constructor === Symbol && obj !== Symbol.prototype ? "symbol" : typeof obj; }, _typeof(obj); }














function _classCallCheck(instance, Constructor) { if (!(instance instanceof Constructor)) { throw new TypeError("Cannot call a class as a function"); } }

function _defineProperties(target, props) { for (var i = 0; i < props.length; i++) { var descriptor = props[i]; descriptor.enumerable = descriptor.enumerable || false; descriptor.configurable = true; if ("value" in descriptor) descriptor.writable = true; Object.defineProperty(target, descriptor.key, descriptor); } }

function _createClass(Constructor, protoProps, staticProps) { if (protoProps) _defineProperties(Constructor.prototype, protoProps); if (staticProps) _defineProperties(Constructor, staticProps); Object.defineProperty(Constructor, "prototype", { writable: false }); return Constructor; }

function _inherits(subClass, superClass) { if (typeof superClass !== "function" && superClass !== null) { throw new TypeError("Super expression must either be null or a function"); } subClass.prototype = Object.create(superClass && superClass.prototype, { constructor: { value: subClass, writable: true, configurable: true } }); Object.defineProperty(subClass, "prototype", { writable: false }); if (superClass) _setPrototypeOf(subClass, superClass); }

function _setPrototypeOf(o, p) { _setPrototypeOf = Object.setPrototypeOf || function _setPrototypeOf(o, p) { o.__proto__ = p; return o; }; return _setPrototypeOf(o, p); }

function _createSuper(Derived) { var hasNativeReflectConstruct = _isNativeReflectConstruct(); return function _createSuperInternal() { var Super = _getPrototypeOf(Derived), result; if (hasNativeReflectConstruct) { var NewTarget = _getPrototypeOf(this).constructor; result = Reflect.construct(Super, arguments, NewTarget); } else { result = Super.apply(this, arguments); } return _possibleConstructorReturn(this, result); }; }

function _possibleConstructorReturn(self, call) { if (call && (_typeof(call) === "object" || typeof call === "function")) { return call; } else if (call !== void 0) { throw new TypeError("Derived constructors may only return object or undefined"); } return _assertThisInitialized(self); }

function _assertThisInitialized(self) { if (self === void 0) { throw new ReferenceError("this hasn't been initialised - super() hasn't been called"); } return self; }

function _isNativeReflectConstruct() { if (typeof Reflect === "undefined" || !Reflect.construct) return false; if (Reflect.construct.sham) return false; if (typeof Proxy === "function") return true; try { Boolean.prototype.valueOf.call(Reflect.construct(Boolean, [], function () {})); return true; } catch (e) { return false; } }

function _getPrototypeOf(o) { _getPrototypeOf = Object.setPrototypeOf ? Object.getPrototypeOf : function _getPrototypeOf(o) { return o.__proto__ || Object.getPrototypeOf(o); }; return _getPrototypeOf(o); }


/*
 * This is an example Stimulus controller!
 *
 * Any element with a data-controller="hello" attribute will cause
 * this controller to be executed. The name "hello" comes from the filename:
 * hello_controller.js -> "hello"
 *
 * Delete this file or adapt it for your use!
 */

var _default = /*#__PURE__*/function (_Controller) {
  _inherits(_default, _Controller);

  var _super = _createSuper(_default);

  function _default() {
    _classCallCheck(this, _default);

    return _super.apply(this, arguments);
  }

  _createClass(_default, [{
    key: "connect",
    value: function connect() {
      this.element.textContent = 'Hello Stimulus! Edit me in assets/controllers/hello_controller.js';
    }
  }]);

  return _default;
}(_hotwired_stimulus__WEBPACK_IMPORTED_MODULE_12__.Controller);



/***/ }),

/***/ "./assets/app.js":
/*!***********************!*\
  !*** ./assets/app.js ***!
  \***********************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _styles_polices_scss__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./styles/polices.scss */ "./assets/styles/polices.scss");
/* harmony import */ var _styles_jkeyboard_css__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./styles/jkeyboard.css */ "./assets/styles/jkeyboard.css");
/* harmony import */ var _styles_app_css__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ./styles/app.css */ "./assets/styles/app.css");
/* harmony import */ var _bootstrap__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! ./bootstrap */ "./assets/bootstrap.js");
/* harmony import */ var _jkeyboard__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(/*! ./jkeyboard */ "./assets/jkeyboard.js");
/* harmony import */ var _jkeyboard__WEBPACK_IMPORTED_MODULE_4___default = /*#__PURE__*/__webpack_require__.n(_jkeyboard__WEBPACK_IMPORTED_MODULE_4__);
/* harmony import */ var _partie__WEBPACK_IMPORTED_MODULE_5__ = __webpack_require__(/*! ./partie */ "./assets/partie.js");
/* harmony import */ var _partie__WEBPACK_IMPORTED_MODULE_5___default = /*#__PURE__*/__webpack_require__.n(_partie__WEBPACK_IMPORTED_MODULE_5__);
/*
 * Welcome to your app's main JavaScript file!
 *
 * We recommend including the built version of this JavaScript file
 * (and its CSS file) in your base layout (base.html.twig).
 */
// any CSS you import will output into a single css file (app.css in this case)


 // start the Stimulus application





/***/ }),

/***/ "./assets/bootstrap.js":
/*!*****************************!*\
  !*** ./assets/bootstrap.js ***!
  \*****************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "app": () => (/* binding */ app)
/* harmony export */ });
/* harmony import */ var _symfony_stimulus_bridge__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @symfony/stimulus-bridge */ "./node_modules/@symfony/stimulus-bridge/dist/index.js");
 // Registers Stimulus controllers from controllers.json and in the controllers/ directory

var app = (0,_symfony_stimulus_bridge__WEBPACK_IMPORTED_MODULE_0__.startStimulusApp)(__webpack_require__("./assets/controllers sync recursive ./node_modules/@symfony/stimulus-bridge/lazy-controller-loader.js! \\.(j%7Ct)sx?$")); // register any custom, 3rd party controllers here
// app.register('some_controller_name', SomeImportedController);

/***/ }),

/***/ "./assets/jkeyboard.js":
/*!*****************************!*\
  !*** ./assets/jkeyboard.js ***!
  \*****************************/
/***/ ((__unused_webpack_module, __unused_webpack_exports, __webpack_require__) => {

function _typeof(obj) { "@babel/helpers - typeof"; return _typeof = "function" == typeof Symbol && "symbol" == typeof Symbol.iterator ? function (obj) { return typeof obj; } : function (obj) { return obj && "function" == typeof Symbol && obj.constructor === Symbol && obj !== Symbol.prototype ? "symbol" : typeof obj; }, _typeof(obj); }

__webpack_require__(/*! core-js/modules/es.array.is-array.js */ "./node_modules/core-js/modules/es.array.is-array.js");

__webpack_require__(/*! core-js/modules/es.array.for-each.js */ "./node_modules/core-js/modules/es.array.for-each.js");

__webpack_require__(/*! core-js/modules/es.object.to-string.js */ "./node_modules/core-js/modules/es.object.to-string.js");

__webpack_require__(/*! core-js/modules/web.dom-collections.for-each.js */ "./node_modules/core-js/modules/web.dom-collections.for-each.js");

__webpack_require__(/*! core-js/modules/es.array.find.js */ "./node_modules/core-js/modules/es.array.find.js");

__webpack_require__(/*! core-js/modules/es.array.join.js */ "./node_modules/core-js/modules/es.array.join.js");

__webpack_require__(/*! core-js/modules/es.array.map.js */ "./node_modules/core-js/modules/es.array.map.js");

__webpack_require__(/*! core-js/modules/es.object.keys.js */ "./node_modules/core-js/modules/es.object.keys.js");

__webpack_require__(/*! core-js/modules/es.array.slice.js */ "./node_modules/core-js/modules/es.array.slice.js");

__webpack_require__(/*! core-js/modules/es.symbol.js */ "./node_modules/core-js/modules/es.symbol.js");

__webpack_require__(/*! core-js/modules/es.symbol.description.js */ "./node_modules/core-js/modules/es.symbol.description.js");

__webpack_require__(/*! core-js/modules/es.symbol.iterator.js */ "./node_modules/core-js/modules/es.symbol.iterator.js");

__webpack_require__(/*! core-js/modules/es.array.iterator.js */ "./node_modules/core-js/modules/es.array.iterator.js");

__webpack_require__(/*! core-js/modules/es.string.iterator.js */ "./node_modules/core-js/modules/es.string.iterator.js");

__webpack_require__(/*! core-js/modules/web.dom-collections.iterator.js */ "./node_modules/core-js/modules/web.dom-collections.iterator.js");

// the semi-colon before function invocation is a safety net against concatenated
// scripts and/or other plugins which may not be closed properly.
;

(function ($, window, document, undefined) {
  // undefined is used here as the undefined global variable in ECMAScript 3 is
  // mutable (ie. it can be changed by someone else). undefined isn't really being
  // passed in so we can ensure the value of it is truly undefined. In ES5, undefined
  // can no longer be modified.
  // window and document are passed through as local variable rather than global
  // as this (slightly) quickens the resolution process and can be more efficiently
  // minified (especially when both are regularly referenced in your plugin).
  // Create the defaults once
  var pluginName = "jkeyboard",
      defaults = {
    layout: "azerty",
    selectable: ['azerty'],
    input: $('#input'),
    customLayouts: {
      selectable: []
    }
  };
  var function_keys = {
    backspace: {
      text: '&nbsp;'
    },
    "return": {
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
      command: function command() {
        this.createKeyboard('numeric');
        this.events();
      }
    },
    layout_switch: {
      text: '&nbsp;',
      command: function command() {
        var l = this.toggleLayout();
        this.createKeyboard(l);
        this.events();
      }
    },
    character_switch: {
      text: 'ABC',
      command: function command() {
        this.createKeyboard(layout);
        this.events();
      }
    },
    symbol_switch: {
      text: '#+=',
      command: function command() {
        this.createKeyboard('symbolic');
        this.events();
      }
    }
  };
  var layouts = {
    azerty: [['a', 'z', 'e', 'r', 't', 'y', 'u', 'i', 'o', 'p'], ['q', 's', 'd', 'f', 'g', 'h', 'j', 'k', 'l', 'm'], ['w', 'x', 'c', 'v', 'b', 'n', ['return']]]
  };
  var shift = false,
      capslock = false,
      layout = 'azerty',
      layout_id = 0; // The actual plugin constructor

  function Plugin(element, options) {
    this.element = element; // jQuery has an extend method which merges the contents of two or
    // more objects, storing the result in the first object. The first object
    // is generally empty as we don't want to alter the default options for
    // future instances of the plugin

    this.settings = $.extend({}, defaults, options); // Extend & Merge the cusom layouts

    layouts = $.extend(true, {}, this.settings.customLayouts, layouts);

    if (Array.isArray(this.settings.customLayouts.selectable)) {
      $.merge(this.settings.selectable, this.settings.customLayouts.selectable);
    }

    this._defaults = defaults;
    this._name = pluginName;
    this.init();
  }

  Plugin.prototype = {
    init: function init() {
      layout = this.settings.layout;
      this.createKeyboard(layout);
      this.events();
    },
    setInput: function setInput(newInputField) {
      this.settings.input = newInputField;
    },
    createKeyboard: function createKeyboard(layout) {
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
    createLine: function createLine(line) {
      var line_container = $('<ul/>');
      line.forEach(function (key, index) {
        var key_container = $('<li/>').addClass('jkey').data('command', key);

        if (function_keys[key]) {
          key_container.addClass(key).html(function_keys[key].text);
        } else {
          key_container.addClass('letter').html(key);
        }

        line_container.append(key_container);
      });
      return line_container;
    },
    events: function events() {
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
        me.type(shift || capslock ? $(this).text().toUpperCase() : $(this).text());
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
    type: function type(key) {
      var input = this.settings.input,
          val = input.val(),
          input_node = input.get(0),
          start = input_node.selectionStart,
          end = input_node.selectionEnd;
      var max_length = $(input).attr("maxlength");

      if (start == end && end == val.length) {
        if (!max_length || val.length < max_length) {
          input.val(val + key);
        }
      } else {
        var new_string = this.insertToString(start, end, val, key);
        input.val(new_string);
        start++;
        end = start;
        input_node.setSelectionRange(start, end);
      }

      input.trigger('focus');

      if (shift && !capslock) {
        this.toggleShiftOff();
      }
    },
    toggleLayout: function toggleLayout() {
      layout_id = layout_id || 0;
      var plain_layouts = this.settings.selectable;
      layout_id++;
      var current_id = layout_id % plain_layouts.length;
      return plain_layouts[current_id];
    },
    insertToString: function insertToString(start, end, string, insert_string) {
      return string.substring(0, start) + insert_string + string.substring(end, string.length);
    }
  };
  var methods = {
    init: function init(options) {
      if (!this.data("plugin_" + pluginName)) {
        this.data("plugin_" + pluginName, new Plugin(this, options));
      }
    },
    setInput: function setInput(content) {
      this.data("plugin_" + pluginName).setInput($(content));
    },
    setLayout: function setLayout(layoutname) {
      // change layout if it is not match current
      object = this.data("plugin_" + pluginName);

      if (typeof layouts[layoutname] !== 'undefined' && object.settings.layout != layoutname) {
        object.settings.layout = layoutname;
        object.createKeyboard(layoutname);
        object.events();
      }

      ;
    }
  };

  $.fn[pluginName] = function (methodOrOptions) {
    if (methods[methodOrOptions]) {
      return methods[methodOrOptions].apply(this.first(), Array.prototype.slice.call(arguments, 1));
    } else if (_typeof(methodOrOptions) === 'object' || !methodOrOptions) {
      // Default to "init"
      return methods.init.apply(this.first(), arguments);
    } else {
      $.error('Method ' + methodOrOptions + ' does not exist on jQuery.jkeyboard');
    }
  };
})(jQuery, window, document);

/***/ }),

/***/ "./assets/partie.js":
/*!**************************!*\
  !*** ./assets/partie.js ***!
  \**************************/
/***/ (() => {



/***/ }),

/***/ "./assets/styles/app.css":
/*!*******************************!*\
  !*** ./assets/styles/app.css ***!
  \*******************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

"use strict";
__webpack_require__.r(__webpack_exports__);
// extracted by mini-css-extract-plugin


/***/ }),

/***/ "./assets/styles/jkeyboard.css":
/*!*************************************!*\
  !*** ./assets/styles/jkeyboard.css ***!
  \*************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

"use strict";
__webpack_require__.r(__webpack_exports__);
// extracted by mini-css-extract-plugin


/***/ }),

/***/ "./assets/styles/polices.scss":
/*!************************************!*\
  !*** ./assets/styles/polices.scss ***!
  \************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

"use strict";
__webpack_require__.r(__webpack_exports__);
// extracted by mini-css-extract-plugin


/***/ })

},
/******/ __webpack_require__ => { // webpackRuntimeModules
/******/ var __webpack_exec__ = (moduleId) => (__webpack_require__(__webpack_require__.s = moduleId))
/******/ __webpack_require__.O(0, ["vendors-node_modules_symfony_stimulus-bridge_dist_index_js-node_modules_core-js_modules_es_ar-de2273"], () => (__webpack_exec__("./assets/app.js")));
/******/ var __webpack_exports__ = __webpack_require__.O();
/******/ }
]);
//# sourceMappingURL=data:application/json;charset=utf-8;base64,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RDs7QUFBQTtBQUNGO0FBakJXLEdBQWQ7O0FBb0JBbEIsRUFBQUEsQ0FBQyxDQUFDb0csRUFBRixDQUFLaEcsVUFBTCxJQUFtQixVQUFVaUcsZUFBVixFQUEyQjtBQUM1QyxRQUFJTixPQUFPLENBQUNNLGVBQUQsQ0FBWCxFQUE4QjtBQUM1QixhQUFPTixPQUFPLENBQUNNLGVBQUQsQ0FBUCxDQUF5QkMsS0FBekIsQ0FBK0IsS0FBS0MsS0FBTCxFQUEvQixFQUE2Q3ZFLEtBQUssQ0FBQ00sU0FBTixDQUFnQmtFLEtBQWhCLENBQXNCN0IsSUFBdEIsQ0FBNEI4QixTQUE1QixFQUF1QyxDQUF2QyxDQUE3QyxDQUFQO0FBQ0QsS0FGRCxNQUVPLElBQUksUUFBT0osZUFBUCxNQUEyQixRQUEzQixJQUF1QyxDQUFFQSxlQUE3QyxFQUE4RDtBQUNuRTtBQUNBLGFBQU9OLE9BQU8sQ0FBQzFELElBQVIsQ0FBYWlFLEtBQWIsQ0FBbUIsS0FBS0MsS0FBTCxFQUFuQixFQUFpQ0UsU0FBakMsQ0FBUDtBQUNELEtBSE0sTUFHQTtBQUNMekcsTUFBQUEsQ0FBQyxDQUFDMEcsS0FBRixDQUFRLFlBQWFMLGVBQWIsR0FBK0IscUNBQXZDO0FBQ0Q7QUFDRixHQVREO0FBV0QsQ0ExUUMsRUEwUUNNLE1BMVFELEVBMFFTMUcsTUExUVQsRUEwUWlCQyxRQTFRakI7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7QUNGRjs7Ozs7Ozs7Ozs7OztBQ0FBOzs7Ozs7Ozs7Ozs7O0FDQUEiLCJzb3VyY2VzIjpbIndlYnBhY2s6Ly8vfC9cXC4oaiU3Q3Qpc3giLCJ3ZWJwYWNrOi8vLy4vYXNzZXRzL2NvbnRyb2xsZXJzLmpzb24iLCJ3ZWJwYWNrOi8vLy4vYXNzZXRzL2NvbnRyb2xsZXJzL2hlbGxvX2NvbnRyb2xsZXIuanMiLCJ3ZWJwYWNrOi8vLy4vYXNzZXRzL2FwcC5qcyIsIndlYnBhY2s6Ly8vLi9hc3NldHMvYm9vdHN0cmFwLmpzIiwid2VicGFjazovLy8uL2Fzc2V0cy9qa2V5Ym9hcmQuanMiLCJ3ZWJwYWNrOi8vLy4vYXNzZXRzL3N0eWxlcy9hcHAuY3NzPzNmYmEiLCJ3ZWJwYWNrOi8vLy4vYXNzZXRzL3N0eWxlcy9qa2V5Ym9hcmQuY3NzP2U4ZDciLCJ3ZWJwYWNrOi8vLy4vYXNzZXRzL3N0eWxlcy9wb2xpY2VzLnNjc3MiXSwic291cmNlc0NvbnRlbnQiOlsidmFyIG1hcCA9IHtcblx0XCIuL2hlbGxvX2NvbnRyb2xsZXIuanNcIjogXCIuL25vZGVfbW9kdWxlcy9Ac3ltZm9ueS9zdGltdWx1cy1icmlkZ2UvbGF6eS1jb250cm9sbGVyLWxvYWRlci5qcyEuL2Fzc2V0cy9jb250cm9sbGVycy9oZWxsb19jb250cm9sbGVyLmpzXCJcbn07XG5cblxuZnVuY3Rpb24gd2VicGFja0NvbnRleHQocmVxKSB7XG5cdHZhciBpZCA9IHdlYnBhY2tDb250ZXh0UmVzb2x2ZShyZXEpO1xuXHRyZXR1cm4gX193ZWJwYWNrX3JlcXVpcmVfXyhpZCk7XG59XG5mdW5jdGlvbiB3ZWJwYWNrQ29udGV4dFJlc29sdmUocmVxKSB7XG5cdGlmKCFfX3dlYnBhY2tfcmVxdWlyZV9fLm8obWFwLCByZXEpKSB7XG5cdFx0dmFyIGUgPSBuZXcgRXJyb3IoXCJDYW5ub3QgZmluZCBtb2R1bGUgJ1wiICsgcmVxICsgXCInXCIpO1xuXHRcdGUuY29kZSA9ICdNT0RVTEVfTk9UX0ZPVU5EJztcblx0XHR0aHJvdyBlO1xuXHR9XG5cdHJldHVybiBtYXBbcmVxXTtcbn1cbndlYnBhY2tDb250ZXh0LmtleXMgPSBmdW5jdGlvbiB3ZWJwYWNrQ29udGV4dEtleXMoKSB7XG5cdHJldHVybiBPYmplY3Qua2V5cyhtYXApO1xufTtcbndlYnBhY2tDb250ZXh0LnJlc29sdmUgPSB3ZWJwYWNrQ29udGV4dFJlc29sdmU7XG5tb2R1bGUuZXhwb3J0cyA9IHdlYnBhY2tDb250ZXh0O1xud2VicGFja0NvbnRleHQuaWQgPSBcIi4vYXNzZXRzL2NvbnRyb2xsZXJzIHN5bmMgcmVjdXJzaXZlIC4vbm9kZV9tb2R1bGVzL0BzeW1mb255L3N0aW11bHVzLWJyaWRnZS9sYXp5LWNvbnRyb2xsZXItbG9hZGVyLmpzISBcXFxcLihqJTdDdClzeD8kXCI7IiwiZXhwb3J0IGRlZmF1bHQge1xufTsiLCJpbXBvcnQgeyBDb250cm9sbGVyIH0gZnJvbSAnQGhvdHdpcmVkL3N0aW11bHVzJztcblxuLypcbiAqIFRoaXMgaXMgYW4gZXhhbXBsZSBTdGltdWx1cyBjb250cm9sbGVyIVxuICpcbiAqIEFueSBlbGVtZW50IHdpdGggYSBkYXRhLWNvbnRyb2xsZXI9XCJoZWxsb1wiIGF0dHJpYnV0ZSB3aWxsIGNhdXNlXG4gKiB0aGlzIGNvbnRyb2xsZXIgdG8gYmUgZXhlY3V0ZWQuIFRoZSBuYW1lIFwiaGVsbG9cIiBjb21lcyBmcm9tIHRoZSBmaWxlbmFtZTpcbiAqIGhlbGxvX2NvbnRyb2xsZXIuanMgLT4gXCJoZWxsb1wiXG4gKlxuICogRGVsZXRlIHRoaXMgZmlsZSBvciBhZGFwdCBpdCBmb3IgeW91ciB1c2UhXG4gKi9cbmV4cG9ydCBkZWZhdWx0IGNsYXNzIGV4dGVuZHMgQ29udHJvbGxlciB7XG4gICAgY29ubmVjdCgpIHtcbiAgICAgICAgdGhpcy5lbGVtZW50LnRleHRDb250ZW50ID0gJ0hlbGxvIFN0aW11bHVzISBFZGl0IG1lIGluIGFzc2V0cy9jb250cm9sbGVycy9oZWxsb19jb250cm9sbGVyLmpzJztcbiAgICB9XG59XG4iLCIvKlxuICogV2VsY29tZSB0byB5b3VyIGFwcCdzIG1haW4gSmF2YVNjcmlwdCBmaWxlIVxuICpcbiAqIFdlIHJlY29tbWVuZCBpbmNsdWRpbmcgdGhlIGJ1aWx0IHZlcnNpb24gb2YgdGhpcyBKYXZhU2NyaXB0IGZpbGVcbiAqIChhbmQgaXRzIENTUyBmaWxlKSBpbiB5b3VyIGJhc2UgbGF5b3V0IChiYXNlLmh0bWwudHdpZykuXG4gKi9cblxuLy8gYW55IENTUyB5b3UgaW1wb3J0IHdpbGwgb3V0cHV0IGludG8gYSBzaW5nbGUgY3NzIGZpbGUgKGFwcC5jc3MgaW4gdGhpcyBjYXNlKVxuaW1wb3J0ICcuL3N0eWxlcy9wb2xpY2VzLnNjc3MnO1xuaW1wb3J0ICcuL3N0eWxlcy9qa2V5Ym9hcmQuY3NzJztcbmltcG9ydCAnLi9zdHlsZXMvYXBwLmNzcyc7XG5cbi8vIHN0YXJ0IHRoZSBTdGltdWx1cyBhcHBsaWNhdGlvblxuaW1wb3J0ICcuL2Jvb3RzdHJhcCc7XG5pbXBvcnQgJy4vamtleWJvYXJkJztcbmltcG9ydCAnLi9wYXJ0aWUnO1xuIiwiaW1wb3J0IHsgc3RhcnRTdGltdWx1c0FwcCB9IGZyb20gJ0BzeW1mb255L3N0aW11bHVzLWJyaWRnZSc7XG5cbi8vIFJlZ2lzdGVycyBTdGltdWx1cyBjb250cm9sbGVycyBmcm9tIGNvbnRyb2xsZXJzLmpzb24gYW5kIGluIHRoZSBjb250cm9sbGVycy8gZGlyZWN0b3J5XG5leHBvcnQgY29uc3QgYXBwID0gc3RhcnRTdGltdWx1c0FwcChyZXF1aXJlLmNvbnRleHQoXG4gICAgJ0BzeW1mb255L3N0aW11bHVzLWJyaWRnZS9sYXp5LWNvbnRyb2xsZXItbG9hZGVyIS4vY29udHJvbGxlcnMnLFxuICAgIHRydWUsXG4gICAgL1xcLihqfHQpc3g/JC9cbikpO1xuXG4vLyByZWdpc3RlciBhbnkgY3VzdG9tLCAzcmQgcGFydHkgY29udHJvbGxlcnMgaGVyZVxuLy8gYXBwLnJlZ2lzdGVyKCdzb21lX2NvbnRyb2xsZXJfbmFtZScsIFNvbWVJbXBvcnRlZENvbnRyb2xsZXIpO1xuIiwiLy8gdGhlIHNlbWktY29sb24gYmVmb3JlIGZ1bmN0aW9uIGludm9jYXRpb24gaXMgYSBzYWZldHkgbmV0IGFnYWluc3QgY29uY2F0ZW5hdGVkXG4vLyBzY3JpcHRzIGFuZC9vciBvdGhlciBwbHVnaW5zIHdoaWNoIG1heSBub3QgYmUgY2xvc2VkIHByb3Blcmx5LlxuOyAoZnVuY3Rpb24gKCQsIHdpbmRvdywgZG9jdW1lbnQsIHVuZGVmaW5lZCkge1xuXG4gIC8vIHVuZGVmaW5lZCBpcyB1c2VkIGhlcmUgYXMgdGhlIHVuZGVmaW5lZCBnbG9iYWwgdmFyaWFibGUgaW4gRUNNQVNjcmlwdCAzIGlzXG4gIC8vIG11dGFibGUgKGllLiBpdCBjYW4gYmUgY2hhbmdlZCBieSBzb21lb25lIGVsc2UpLiB1bmRlZmluZWQgaXNuJ3QgcmVhbGx5IGJlaW5nXG4gIC8vIHBhc3NlZCBpbiBzbyB3ZSBjYW4gZW5zdXJlIHRoZSB2YWx1ZSBvZiBpdCBpcyB0cnVseSB1bmRlZmluZWQuIEluIEVTNSwgdW5kZWZpbmVkXG4gIC8vIGNhbiBubyBsb25nZXIgYmUgbW9kaWZpZWQuXG5cbiAgLy8gd2luZG93IGFuZCBkb2N1bWVudCBhcmUgcGFzc2VkIHRocm91Z2ggYXMgbG9jYWwgdmFyaWFibGUgcmF0aGVyIHRoYW4gZ2xvYmFsXG4gIC8vIGFzIHRoaXMgKHNsaWdodGx5KSBxdWlja2VucyB0aGUgcmVzb2x1dGlvbiBwcm9jZXNzIGFuZCBjYW4gYmUgbW9yZSBlZmZpY2llbnRseVxuICAvLyBtaW5pZmllZCAoZXNwZWNpYWxseSB3aGVuIGJvdGggYXJlIHJlZ3VsYXJseSByZWZlcmVuY2VkIGluIHlvdXIgcGx1Z2luKS5cblxuICAvLyBDcmVhdGUgdGhlIGRlZmF1bHRzIG9uY2VcbiAgbGV0IHBsdWdpbk5hbWUgPSBcImprZXlib2FyZFwiLFxuICAgIGRlZmF1bHRzID0ge1xuICAgICAgbGF5b3V0OiBcImF6ZXJ0eVwiLFxuICAgICAgc2VsZWN0YWJsZTogWydhemVydHknXSxcbiAgICAgIGlucHV0OiAkKCcjaW5wdXQnKSxcbiAgICAgIGN1c3RvbUxheW91dHM6IHtcbiAgICAgICAgc2VsZWN0YWJsZTogW11cbiAgICAgIH0sXG4gICAgfTtcblxuXG4gIHZhciBmdW5jdGlvbl9rZXlzID0ge1xuICAgIGJhY2tzcGFjZToge1xuICAgICAgdGV4dDogJyZuYnNwOycsXG4gICAgfSxcbiAgICByZXR1cm46IHtcbiAgICAgIHRleHQ6ICdFbnRlcidcbiAgICB9LFxuICAgIHNoaWZ0OiB7XG4gICAgICB0ZXh0OiAnJm5ic3A7J1xuICAgIH0sXG4gICAgc3BhY2U6IHtcbiAgICAgIHRleHQ6ICcmbmJzcDsnXG4gICAgfSxcbiAgICBudW1lcmljX3N3aXRjaDoge1xuICAgICAgdGV4dDogJzEyMycsXG4gICAgICBjb21tYW5kOiBmdW5jdGlvbiAoKSB7XG4gICAgICAgIHRoaXMuY3JlYXRlS2V5Ym9hcmQoJ251bWVyaWMnKTtcbiAgICAgICAgdGhpcy5ldmVudHMoKTtcbiAgICAgIH1cbiAgICB9LFxuICAgIGxheW91dF9zd2l0Y2g6IHtcbiAgICAgIHRleHQ6ICcmbmJzcDsnLFxuICAgICAgY29tbWFuZDogZnVuY3Rpb24gKCkge1xuICAgICAgICB2YXIgbCA9IHRoaXMudG9nZ2xlTGF5b3V0KCk7XG4gICAgICAgIHRoaXMuY3JlYXRlS2V5Ym9hcmQobCk7XG4gICAgICAgIHRoaXMuZXZlbnRzKCk7XG4gICAgICB9XG4gICAgfSxcbiAgICBjaGFyYWN0ZXJfc3dpdGNoOiB7XG4gICAgICB0ZXh0OiAnQUJDJyxcbiAgICAgIGNvbW1hbmQ6IGZ1bmN0aW9uICgpIHtcbiAgICAgICAgdGhpcy5jcmVhdGVLZXlib2FyZChsYXlvdXQpO1xuICAgICAgICB0aGlzLmV2ZW50cygpO1xuICAgICAgfVxuICAgIH0sXG4gICAgc3ltYm9sX3N3aXRjaDoge1xuICAgICAgdGV4dDogJyMrPScsXG4gICAgICBjb21tYW5kOiBmdW5jdGlvbiAoKSB7XG4gICAgICAgIHRoaXMuY3JlYXRlS2V5Ym9hcmQoJ3N5bWJvbGljJyk7XG4gICAgICAgIHRoaXMuZXZlbnRzKCk7XG4gICAgICB9XG4gICAgfVxuICB9O1xuXG5cbiAgdmFyIGxheW91dHMgPSB7XG4gICAgYXplcnR5OiBbXG4gICAgICBbJ2EnLCAneicsICdlJywgJ3InLCAndCcsICd5JywgJ3UnLCAnaScsICdvJywgJ3AnLF0sXG4gICAgICBbJ3EnLCAncycsICdkJywgJ2YnLCAnZycsICdoJywgJ2onLCAnaycsICdsJywnbSddLFxuICAgICAgWyAndycsICd4JywgJ2MnLCAndicsICdiJywgJ24nLCBbJ3JldHVybiddXVxuICAgIF1cbiAgfVxuXG4gIHZhciBzaGlmdCA9IGZhbHNlLCBjYXBzbG9jayA9IGZhbHNlLCBsYXlvdXQgPSAnYXplcnR5JywgbGF5b3V0X2lkID0gMDtcblxuICAvLyBUaGUgYWN0dWFsIHBsdWdpbiBjb25zdHJ1Y3RvclxuICBmdW5jdGlvbiBQbHVnaW4oZWxlbWVudCwgb3B0aW9ucykge1xuICAgIHRoaXMuZWxlbWVudCA9IGVsZW1lbnQ7XG4gICAgLy8galF1ZXJ5IGhhcyBhbiBleHRlbmQgbWV0aG9kIHdoaWNoIG1lcmdlcyB0aGUgY29udGVudHMgb2YgdHdvIG9yXG4gICAgLy8gbW9yZSBvYmplY3RzLCBzdG9yaW5nIHRoZSByZXN1bHQgaW4gdGhlIGZpcnN0IG9iamVjdC4gVGhlIGZpcnN0IG9iamVjdFxuICAgIC8vIGlzIGdlbmVyYWxseSBlbXB0eSBhcyB3ZSBkb24ndCB3YW50IHRvIGFsdGVyIHRoZSBkZWZhdWx0IG9wdGlvbnMgZm9yXG4gICAgLy8gZnV0dXJlIGluc3RhbmNlcyBvZiB0aGUgcGx1Z2luXG4gICAgdGhpcy5zZXR0aW5ncyA9ICQuZXh0ZW5kKHt9LCBkZWZhdWx0cywgb3B0aW9ucyk7XG4gICAgLy8gRXh0ZW5kICYgTWVyZ2UgdGhlIGN1c29tIGxheW91dHNcbiAgICBsYXlvdXRzID0gJC5leHRlbmQodHJ1ZSwge30sIHRoaXMuc2V0dGluZ3MuY3VzdG9tTGF5b3V0cywgbGF5b3V0cyk7XG4gICAgaWYgKEFycmF5LmlzQXJyYXkodGhpcy5zZXR0aW5ncy5jdXN0b21MYXlvdXRzLnNlbGVjdGFibGUpKSB7XG4gICAgICAkLm1lcmdlKHRoaXMuc2V0dGluZ3Muc2VsZWN0YWJsZSwgdGhpcy5zZXR0aW5ncy5jdXN0b21MYXlvdXRzLnNlbGVjdGFibGUpO1xuICAgIH1cbiAgICB0aGlzLl9kZWZhdWx0cyA9IGRlZmF1bHRzO1xuICAgIHRoaXMuX25hbWUgPSBwbHVnaW5OYW1lO1xuICAgIHRoaXMuaW5pdCgpO1xuICB9XG5cbiAgUGx1Z2luLnByb3RvdHlwZSA9IHtcbiAgICBpbml0OiBmdW5jdGlvbiAoKSB7XG4gICAgICBsYXlvdXQgPSB0aGlzLnNldHRpbmdzLmxheW91dDtcbiAgICAgIHRoaXMuY3JlYXRlS2V5Ym9hcmQobGF5b3V0KTtcbiAgICAgIHRoaXMuZXZlbnRzKCk7XG4gICAgfSxcblxuICAgIHNldElucHV0OiBmdW5jdGlvbiAobmV3SW5wdXRGaWVsZCkge1xuICAgICAgdGhpcy5zZXR0aW5ncy5pbnB1dCA9IG5ld0lucHV0RmllbGQ7XG4gICAgfSxcblxuICAgIGNyZWF0ZUtleWJvYXJkOiBmdW5jdGlvbiAobGF5b3V0KSB7XG4gICAgICBzaGlmdCA9IGZhbHNlO1xuICAgICAgY2Fwc2xvY2sgPSBmYWxzZTtcblxuICAgICAgdmFyIGtleWJvYXJkX2NvbnRhaW5lciA9ICQoJzx1bC8+JykuYWRkQ2xhc3MoJ2prZXlib2FyZCcpLFxuICAgICAgICBtZSA9IHRoaXM7XG5cbiAgICAgIGxheW91dHNbbGF5b3V0XS5mb3JFYWNoKGZ1bmN0aW9uIChsaW5lLCBpbmRleCkge1xuICAgICAgICB2YXIgbGluZV9jb250YWluZXIgPSAkKCc8bGkvPicpLmFkZENsYXNzKCdqbGluZScpO1xuICAgICAgICBsaW5lX2NvbnRhaW5lci5hcHBlbmQobWUuY3JlYXRlTGluZShsaW5lKSk7XG4gICAgICAgIGtleWJvYXJkX2NvbnRhaW5lci5hcHBlbmQobGluZV9jb250YWluZXIpO1xuICAgICAgfSk7XG5cbiAgICAgICQodGhpcy5lbGVtZW50KS5odG1sKCcnKS5hcHBlbmQoa2V5Ym9hcmRfY29udGFpbmVyKTtcbiAgICB9LFxuXG4gICAgY3JlYXRlTGluZTogZnVuY3Rpb24gKGxpbmUpIHtcbiAgICAgIHZhciBsaW5lX2NvbnRhaW5lciA9ICQoJzx1bC8+Jyk7XG5cbiAgICAgIGxpbmUuZm9yRWFjaChmdW5jdGlvbiAoa2V5LCBpbmRleCkge1xuICAgICAgICB2YXIga2V5X2NvbnRhaW5lciA9ICQoJzxsaS8+JykuYWRkQ2xhc3MoJ2prZXknKS5kYXRhKCdjb21tYW5kJywga2V5KTtcblxuICAgICAgICBpZiAoZnVuY3Rpb25fa2V5c1trZXldKSB7XG4gICAgICAgICAga2V5X2NvbnRhaW5lci5hZGRDbGFzcyhrZXkpLmh0bWwoZnVuY3Rpb25fa2V5c1trZXldLnRleHQpO1xuICAgICAgICB9XG4gICAgICAgIGVsc2Uge1xuICAgICAgICAgIGtleV9jb250YWluZXIuYWRkQ2xhc3MoJ2xldHRlcicpLmh0bWwoa2V5KTtcbiAgICAgICAgfVxuXG4gICAgICAgIGxpbmVfY29udGFpbmVyLmFwcGVuZChrZXlfY29udGFpbmVyKTtcbiAgICAgIH0pXG5cbiAgICAgIHJldHVybiBsaW5lX2NvbnRhaW5lcjtcbiAgICB9LFxuXG4gICAgZXZlbnRzOiBmdW5jdGlvbiAoKSB7XG4gICAgICB2YXIgbGV0dGVycyA9ICQodGhpcy5lbGVtZW50KS5maW5kKCcubGV0dGVyJyksXG4gICAgICAgIHNoaWZ0X2tleSA9ICQodGhpcy5lbGVtZW50KS5maW5kKCcuc2hpZnQnKSxcbiAgICAgICAgc3BhY2Vfa2V5ID0gJCh0aGlzLmVsZW1lbnQpLmZpbmQoJy5zcGFjZScpLFxuICAgICAgICBiYWNrc3BhY2Vfa2V5ID0gJCh0aGlzLmVsZW1lbnQpLmZpbmQoJy5iYWNrc3BhY2UnKSxcbiAgICAgICAgcmV0dXJuX2tleSA9ICQodGhpcy5lbGVtZW50KS5maW5kKCcucmV0dXJuJyksXG5cbiAgICAgICAgbWUgPSB0aGlzLFxuICAgICAgICBma2V5cyA9IE9iamVjdC5rZXlzKGZ1bmN0aW9uX2tleXMpLm1hcChmdW5jdGlvbiAoaykge1xuICAgICAgICAgIHJldHVybiAnLicgKyBrO1xuICAgICAgICB9KS5qb2luKCcsJyk7XG5cbiAgICAgIGxldHRlcnMub24oJ2NsaWNrJywgZnVuY3Rpb24gKCkge1xuICAgICAgICBtZS50eXBlKChzaGlmdCB8fCBjYXBzbG9jaykgPyAkKHRoaXMpLnRleHQoKS50b1VwcGVyQ2FzZSgpIDogJCh0aGlzKS50ZXh0KCkpO1xuICAgICAgfSk7XG5cbiAgICAgIHNwYWNlX2tleS5vbignY2xpY2snLCBmdW5jdGlvbiAoKSB7XG4gICAgICAgIG1lLnR5cGUoJyAnKTtcbiAgICAgIH0pO1xuXG4gICAgICByZXR1cm5fa2V5Lm9uKCdjbGljaycsIGZ1bmN0aW9uICgpIHtcbiAgICAgICAgbWUudHlwZShcIlxcblwiKTtcbiAgICAgICAgbWUuc2V0dGluZ3MuaW5wdXQucGFyZW50cygnZm9ybScpLnN1Ym1pdCgpO1xuICAgICAgfSk7XG5cbiAgICAgIGJhY2tzcGFjZV9rZXkub24oJ2NsaWNrJywgZnVuY3Rpb24gKCkge1xuICAgICAgICBtZS5iYWNrc3BhY2UoKTtcbiAgICAgIH0pO1xuXG4gICAgICBzaGlmdF9rZXkub24oJ2NsaWNrJywgZnVuY3Rpb24gKCkge1xuICAgICAgICBpZiAoc2hpZnQpIHtcbiAgICAgICAgICBtZS50b2dnbGVTaGlmdE9mZigpO1xuICAgICAgICB9IGVsc2Uge1xuICAgICAgICAgIG1lLnRvZ2dsZVNoaWZ0T24oKTtcbiAgICAgICAgfVxuICAgICAgfSkub24oJ2RibGNsaWNrJywgZnVuY3Rpb24gKCkge1xuICAgICAgICBtZS50b2dnbGVTaGlmdE9uKHRydWUpO1xuICAgICAgfSk7XG5cblxuICAgICAgJChma2V5cykub24oJ2NsaWNrJywgZnVuY3Rpb24gKGUpIHtcbiAgICAgICAgLy9wcmV2ZW50IGJ1YmJsaW5nIHRvIGF2b2lkIHNpZGUgZWZmZWN0cyB3aGVuIHVzZWQgYXMgZmxvYXRpbmcga2V5Ym9hcmQgd2hpY2ggY2xvc2VzIG9uIGNsaWNrIG91dHNpZGUgb2Yga2V5Ym9hcmQgY29udGFpbmVyXG4gICAgICAgIGUuc3RvcFByb3BhZ2F0aW9uKCk7XG5cbiAgICAgICAgdmFyIGNvbW1hbmQgPSBmdW5jdGlvbl9rZXlzWyQodGhpcykuZGF0YSgnY29tbWFuZCcpXS5jb21tYW5kO1xuICAgICAgICBpZiAoIWNvbW1hbmQpIHJldHVybjtcblxuICAgICAgICBjb21tYW5kLmNhbGwobWUpO1xuICAgICAgfSk7XG4gICAgfSxcblxuICAgIHR5cGU6IGZ1bmN0aW9uIChrZXkpIHtcbiAgICAgIHZhciBpbnB1dCA9IHRoaXMuc2V0dGluZ3MuaW5wdXQsXG4gICAgICAgIHZhbCA9IGlucHV0LnZhbCgpLFxuICAgICAgICBpbnB1dF9ub2RlID0gaW5wdXQuZ2V0KDApLFxuICAgICAgICBzdGFydCA9IGlucHV0X25vZGUuc2VsZWN0aW9uU3RhcnQsXG4gICAgICAgIGVuZCA9IGlucHV0X25vZGUuc2VsZWN0aW9uRW5kO1xuXG4gICAgICB2YXIgbWF4X2xlbmd0aCA9ICQoaW5wdXQpLmF0dHIoXCJtYXhsZW5ndGhcIik7XG4gICAgICBpZiAoc3RhcnQgPT0gZW5kICYmIGVuZCA9PSB2YWwubGVuZ3RoKSB7XG4gICAgICAgIGlmICghbWF4X2xlbmd0aCB8fCB2YWwubGVuZ3RoIDwgbWF4X2xlbmd0aCkge1xuICAgICAgICAgIGlucHV0LnZhbCh2YWwgKyBrZXkpO1xuICAgICAgICB9XG4gICAgICB9IGVsc2Uge1xuICAgICAgICB2YXIgbmV3X3N0cmluZyA9IHRoaXMuaW5zZXJ0VG9TdHJpbmcoc3RhcnQsIGVuZCwgdmFsLCBrZXkpO1xuICAgICAgICBpbnB1dC52YWwobmV3X3N0cmluZyk7XG4gICAgICAgIHN0YXJ0Kys7XG4gICAgICAgIGVuZCA9IHN0YXJ0O1xuICAgICAgICBpbnB1dF9ub2RlLnNldFNlbGVjdGlvblJhbmdlKHN0YXJ0LCBlbmQpO1xuICAgICAgfVxuXG4gICAgICBpbnB1dC50cmlnZ2VyKCdmb2N1cycpO1xuXG4gICAgICBpZiAoc2hpZnQgJiYgIWNhcHNsb2NrKSB7XG4gICAgICAgIHRoaXMudG9nZ2xlU2hpZnRPZmYoKTtcbiAgICAgIH1cbiAgICB9LFxuXG4gICAgdG9nZ2xlTGF5b3V0OiBmdW5jdGlvbiAoKSB7XG4gICAgICBsYXlvdXRfaWQgPSBsYXlvdXRfaWQgfHwgMDtcbiAgICAgIHZhciBwbGFpbl9sYXlvdXRzID0gdGhpcy5zZXR0aW5ncy5zZWxlY3RhYmxlO1xuICAgICAgbGF5b3V0X2lkKys7XG5cbiAgICAgIHZhciBjdXJyZW50X2lkID0gbGF5b3V0X2lkICUgcGxhaW5fbGF5b3V0cy5sZW5ndGg7XG4gICAgICByZXR1cm4gcGxhaW5fbGF5b3V0c1tjdXJyZW50X2lkXTtcbiAgICB9LFxuXG4gICAgaW5zZXJ0VG9TdHJpbmc6IGZ1bmN0aW9uIChzdGFydCwgZW5kLCBzdHJpbmcsIGluc2VydF9zdHJpbmcpIHtcbiAgICAgIHJldHVybiBzdHJpbmcuc3Vic3RyaW5nKDAsIHN0YXJ0KSArIGluc2VydF9zdHJpbmcgKyBzdHJpbmcuc3Vic3RyaW5nKGVuZCwgc3RyaW5nLmxlbmd0aCk7XG4gICAgfVxuICB9O1xuXG5cbiAgdmFyIG1ldGhvZHMgPSB7XG4gICAgaW5pdDogZnVuY3Rpb24ob3B0aW9ucykge1xuICAgICAgaWYgKCF0aGlzLmRhdGEoXCJwbHVnaW5fXCIgKyBwbHVnaW5OYW1lKSkge1xuICAgICAgICB0aGlzLmRhdGEoXCJwbHVnaW5fXCIgKyBwbHVnaW5OYW1lLCBuZXcgUGx1Z2luKHRoaXMsIG9wdGlvbnMpKTtcbiAgICAgIH1cbiAgICB9LFxuICAgIHNldElucHV0OiBmdW5jdGlvbihjb250ZW50KSB7XG4gICAgICB0aGlzLmRhdGEoXCJwbHVnaW5fXCIgKyBwbHVnaW5OYW1lKS5zZXRJbnB1dCgkKGNvbnRlbnQpKTtcbiAgICB9LFxuICAgIHNldExheW91dDogZnVuY3Rpb24obGF5b3V0bmFtZSkge1xuICAgICAgLy8gY2hhbmdlIGxheW91dCBpZiBpdCBpcyBub3QgbWF0Y2ggY3VycmVudFxuICAgICAgb2JqZWN0ID0gdGhpcy5kYXRhKFwicGx1Z2luX1wiICsgcGx1Z2luTmFtZSk7XG4gICAgICBpZiAodHlwZW9mKGxheW91dHNbbGF5b3V0bmFtZV0pICE9PSAndW5kZWZpbmVkJyAmJiBvYmplY3Quc2V0dGluZ3MubGF5b3V0ICE9IGxheW91dG5hbWUpIHtcbiAgICAgICAgb2JqZWN0LnNldHRpbmdzLmxheW91dCA9IGxheW91dG5hbWU7XG4gICAgICAgIG9iamVjdC5jcmVhdGVLZXlib2FyZChsYXlvdXRuYW1lKTtcbiAgICAgICAgb2JqZWN0LmV2ZW50cygpO1xuICAgICAgfTtcbiAgICB9LFxuICB9O1xuXG4gICQuZm5bcGx1Z2luTmFtZV0gPSBmdW5jdGlvbiAobWV0aG9kT3JPcHRpb25zKSB7XG4gICAgaWYgKG1ldGhvZHNbbWV0aG9kT3JPcHRpb25zXSkge1xuICAgICAgcmV0dXJuIG1ldGhvZHNbbWV0aG9kT3JPcHRpb25zXS5hcHBseSh0aGlzLmZpcnN0KCksIEFycmF5LnByb3RvdHlwZS5zbGljZS5jYWxsKCBhcmd1bWVudHMsIDEpKTtcbiAgICB9IGVsc2UgaWYgKHR5cGVvZiBtZXRob2RPck9wdGlvbnMgPT09ICdvYmplY3QnIHx8ICEgbWV0aG9kT3JPcHRpb25zKSB7XG4gICAgICAvLyBEZWZhdWx0IHRvIFwiaW5pdFwiXG4gICAgICByZXR1cm4gbWV0aG9kcy5pbml0LmFwcGx5KHRoaXMuZmlyc3QoKSwgYXJndW1lbnRzKTtcbiAgICB9IGVsc2Uge1xuICAgICAgJC5lcnJvcignTWV0aG9kICcgKyAgbWV0aG9kT3JPcHRpb25zICsgJyBkb2VzIG5vdCBleGlzdCBvbiBqUXVlcnkuamtleWJvYXJkJyk7XG4gICAgfVxuICB9O1xuXG59KShqUXVlcnksIHdpbmRvdywgZG9jdW1lbnQpO1xuIiwiLy8gZXh0cmFjdGVkIGJ5IG1pbmktY3NzLWV4dHJhY3QtcGx1Z2luXG5leHBvcnQge307IiwiLy8gZXh0cmFjdGVkIGJ5IG1pbmktY3NzLWV4dHJhY3QtcGx1Z2luXG5leHBvcnQge307IiwiLy8gZXh0cmFjdGVkIGJ5IG1pbmktY3NzLWV4dHJhY3QtcGx1Z2luXG5leHBvcnQge307Il0sIm5hbWVzIjpbIkNvbnRyb2xsZXIiLCJlbGVtZW50IiwidGV4dENvbnRlbnQiLCJzdGFydFN0aW11bHVzQXBwIiwiYXBwIiwicmVxdWlyZSIsImNvbnRleHQiLCIkIiwid2luZG93IiwiZG9jdW1lbnQiLCJ1bmRlZmluZWQiLCJwbHVnaW5OYW1lIiwiZGVmYXVsdHMiLCJsYXlvdXQiLCJzZWxlY3RhYmxlIiwiaW5wdXQiLCJjdXN0b21MYXlvdXRzIiwiZnVuY3Rpb25fa2V5cyIsImJhY2tzcGFjZSIsInRleHQiLCJzaGlmdCIsInNwYWNlIiwibnVtZXJpY19zd2l0Y2giLCJjb21tYW5kIiwiY3JlYXRlS2V5Ym9hcmQiLCJldmVudHMiLCJsYXlvdXRfc3dpdGNoIiwibCIsInRvZ2dsZUxheW91dCIsImNoYXJhY3Rlcl9zd2l0Y2giLCJzeW1ib2xfc3dpdGNoIiwibGF5b3V0cyIsImF6ZXJ0eSIsImNhcHNsb2NrIiwibGF5b3V0X2lkIiwiUGx1Z2luIiwib3B0aW9ucyIsInNldHRpbmdzIiwiZXh0ZW5kIiwiQXJyYXkiLCJpc0FycmF5IiwibWVyZ2UiLCJfZGVmYXVsdHMiLCJfbmFtZSIsImluaXQiLCJwcm90b3R5cGUiLCJzZXRJbnB1dCIsIm5ld0lucHV0RmllbGQiLCJrZXlib2FyZF9jb250YWluZXIiLCJhZGRDbGFzcyIsIm1lIiwiZm9yRWFjaCIsImxpbmUiLCJpbmRleCIsImxpbmVfY29udGFpbmVyIiwiYXBwZW5kIiwiY3JlYXRlTGluZSIsImh0bWwiLCJrZXkiLCJrZXlfY29udGFpbmVyIiwiZGF0YSIsImxldHRlcnMiLCJmaW5kIiwic2hpZnRfa2V5Iiwic3BhY2Vfa2V5IiwiYmFja3NwYWNlX2tleSIsInJldHVybl9rZXkiLCJma2V5cyIsIk9iamVjdCIsImtleXMiLCJtYXAiLCJrIiwiam9pbiIsIm9uIiwidHlwZSIsInRvVXBwZXJDYXNlIiwicGFyZW50cyIsInN1Ym1pdCIsInRvZ2dsZVNoaWZ0T2ZmIiwidG9nZ2xlU2hpZnRPbiIsImUiLCJzdG9wUHJvcGFnYXRpb24iLCJjYWxsIiwidmFsIiwiaW5wdXRfbm9kZSIsImdldCIsInN0YXJ0Iiwic2VsZWN0aW9uU3RhcnQiLCJlbmQiLCJzZWxlY3Rpb25FbmQiLCJtYXhfbGVuZ3RoIiwiYXR0ciIsImxlbmd0aCIsIm5ld19zdHJpbmciLCJpbnNlcnRUb1N0cmluZyIsInNldFNlbGVjdGlvblJhbmdlIiwidHJpZ2dlciIsInBsYWluX2xheW91dHMiLCJjdXJyZW50X2lkIiwic3RyaW5nIiwiaW5zZXJ0X3N0cmluZyIsInN1YnN0cmluZyIsIm1ldGhvZHMiLCJjb250ZW50Iiwic2V0TGF5b3V0IiwibGF5b3V0bmFtZSIsIm9iamVjdCIsImZuIiwibWV0aG9kT3JPcHRpb25zIiwiYXBwbHkiLCJmaXJzdCIsInNsaWNlIiwiYXJndW1lbnRzIiwiZXJyb3IiLCJqUXVlcnkiXSwic291cmNlUm9vdCI6IiJ9