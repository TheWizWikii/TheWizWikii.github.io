var snowflakes =
/******/ (function(modules) { // webpackBootstrap
/******/ 	// The module cache
/******/ 	var installedModules = {};

/******/ 	// The require function
/******/ 	function __webpack_require__(moduleId) {

/******/ 		// Check if module is in cache
/******/ 		if(installedModules[moduleId])
/******/ 			return installedModules[moduleId].exports;

/******/ 		// Create a new module (and put it into the cache)
/******/ 		var module = installedModules[moduleId] = {
/******/ 			exports: {},
/******/ 			id: moduleId,
/******/ 			loaded: false
/******/ 		};

/******/ 		// Execute the module function
/******/ 		modules[moduleId].call(module.exports, module, module.exports, __webpack_require__);

/******/ 		// Flag the module as loaded
/******/ 		module.loaded = true;

/******/ 		// Return the exports of the module
/******/ 		return module.exports;
/******/ 	}


/******/ 	// expose the modules object (__webpack_modules__)
/******/ 	__webpack_require__.m = modules;

/******/ 	// expose the module cache
/******/ 	__webpack_require__.c = installedModules;

/******/ 	// __webpack_public_path__
/******/ 	__webpack_require__.p = "dist/";

/******/ 	// Load entry module and return exports
/******/ 	return __webpack_require__(0);
/******/ })
/************************************************************************/
/******/ ([
/* 0 */
/***/ function(module, exports) {

	'use strict';

	var MAX_SNOWFLAKES = 200;
	var SNOWFLAKE_HORIZONTAL_MOVEMENT = 10;
	var SNOWFLAKE_HORIZONTAL_SPEED = 1;
	var SNOWFLAKE_VERTICAL_SPEED = 1;
	var ADD_SNOWFLAKE_FREQ = 800;
	var MOVE_SNOWFLAKE_FREQ = 50;
	var REMOVE_SNOWFLAKE_FREQ = 1000;
	var Z_INDEX = 10;

	var snowflakes = [];
	var snowflakeAdder = void 0;
	var snowflakeMover = void 0;
	var snowflakeRemover = void 0;
	var lastMoveUpdate = void 0;
	var lastAddUpdate = void 0;

	function initOptions(options) {
	    return Object.assign({
	        background: 'rgba(255,255,255,0.8)',
	        borderRadius: '10px',
	        width: '5px',
	        height: '5px',
	        boxShadow: '1px 1px .5px rgba(0,0,0,0.8)',
	        addSnowflakeFrequency: ADD_SNOWFLAKE_FREQ,
	        moveSnowflakeFrequency: MOVE_SNOWFLAKE_FREQ,
	        removeSnowFlakeFrequency: REMOVE_SNOWFLAKE_FREQ,
	        horizontalSpeed: SNOWFLAKE_HORIZONTAL_SPEED,
	        horizontalMovement: SNOWFLAKE_HORIZONTAL_MOVEMENT,
	        verticalSpeed: SNOWFLAKE_VERTICAL_SPEED,
	        maxSnowflakes: MAX_SNOWFLAKES,
	        zIndex: Z_INDEX
	    }, options);
	}

	function addSnowflake(options) {
	    if (snowflakes.length >= options.maxSnowflakes || isAddUpdaterInactive(options)) {
	        lastAddUpdate = Date.now();
	        return;
	    }

	    var snowflake = document.createElement('i');
	    initCustomOptions(snowflake, options);
	    initRandomOptions(snowflake, options);

	    document.body.appendChild(snowflake);
	    snowflakes.push(snowflake);
	    lastAddUpdate = Date.now();
	}

	function initCustomOptions(snowflake, options) {
	    snowflake.style.background = options.background;
	    snowflake.style.borderRadius = options.borderRadius;
	    snowflake.style.width = options.width;
	    snowflake.style.height = options.height;
	    snowflake.style.boxShadow = options.boxShadow;
	    snowflake.style.zIndex = options.zIndex;
	    snowflake.style.top = '-' + options.height;
	    snowflake.style.position = 'fixed';
	}

	function initRandomOptions(snowflake, options) {
	    snowflake.initLeft = window.innerWidth * Math.random();
	    snowflake.style.left = snowflake.initLeft + 'px';
	    snowflake.xOffset = (Math.random() < 0.5 ? -1 : 1) * Math.floor(options.horizontalMovement * Math.random());
	    if (snowflake.xOffset > 0) {
	        snowflake.direction = 'left';
	    } else {
	        snowflake.direction = 'right';
	    }
	}

	function moveSnowflakes(options) {
	    if (isMoveUpdaterInactive(options)) {
	        lastMoveUpdate = Date.now();
	        return;
	    }

	    var _iteratorNormalCompletion = true;
	    var _didIteratorError = false;
	    var _iteratorError = undefined;

	    try {
	        for (var _iterator = snowflakes[Symbol.iterator](), _step; !(_iteratorNormalCompletion = (_step = _iterator.next()).done); _iteratorNormalCompletion = true) {
	            var snowflake = _step.value;

	            moveSnowflakeVertically(snowflake, options);
	            moveSnowflakeHorizontally(snowflake, options);
	        }
	    } catch (err) {
	        _didIteratorError = true;
	        _iteratorError = err;
	    } finally {
	        try {
	            if (!_iteratorNormalCompletion && _iterator.return) {
	                _iterator.return();
	            }
	        } finally {
	            if (_didIteratorError) {
	                throw _iteratorError;
	            }
	        }
	    }

	    lastMoveUpdate = Date.now();
	}

	function isAddUpdaterInactive(options) {
	    var now = Date.now();

	    return now - lastAddUpdate > options.addSnowflakeFrequency;
	}

	function isMoveUpdaterInactive(options) {
	    var now = Date.now();

	    return now - lastMoveUpdate > options.addSnowflakeFrequency;
	}

	function moveSnowflakeVertically(snowflake, options) {
	    snowflake.style.top = parseInt(snowflake.style.top.replace('px', '')) + options.verticalSpeed + 'px';
	}

	function moveSnowflakeHorizontally(snowflake, options) {
	    if (options.horizontalMovement > 0) {
	        if (snowflake.direction === 'right') {
	            snowflake.xOffset += options.horizontalSpeed;

	            if (snowflake.xOffset >= options.horizontalMovement) {
	                snowflake.direction = 'left';
	            }
	        } else {
	            snowflake.xOffset -= options.horizontalSpeed;

	            if (snowflake.xOffset <= -options.horizontalMovement) {
	                snowflake.direction = 'right';
	            }
	        }

	        snowflake.style.left = snowflake.initLeft + snowflake.xOffset + 'px';
	    }
	}

	function removeSnowflakes() {
	    var activeSnowflakes = [];
	    var _iteratorNormalCompletion2 = true;
	    var _didIteratorError2 = false;
	    var _iteratorError2 = undefined;

	    try {
	        for (var _iterator2 = snowflakes[Symbol.iterator](), _step2; !(_iteratorNormalCompletion2 = (_step2 = _iterator2.next()).done); _iteratorNormalCompletion2 = true) {
	            var snowflake = _step2.value;

	            if (parseInt(snowflake.style.top.replace('px', '')) < window.innerHeight) {
	                activeSnowflakes.push(snowflake);
	            } else {
	                document.body.removeChild(snowflake);
	            }
	        }
	    } catch (err) {
	        _didIteratorError2 = true;
	        _iteratorError2 = err;
	    } finally {
	        try {
	            if (!_iteratorNormalCompletion2 && _iterator2.return) {
	                _iterator2.return();
	            }
	        } finally {
	            if (_didIteratorError2) {
	                throw _iteratorError2;
	            }
	        }
	    }

	    snowflakes = activeSnowflakes;
	}
	var init = function init(options) {
	    options = initOptions(options);

	    if (snowflakeAdder) {
	        clearInterval(snowflakeAdder);
	        clearInterval(snowflakeMover);
	        clearInterval(snowflakeRemover);
	    }

	    snowflakeAdder = setInterval(function () {
	        return addSnowflake(options);
	    }, options.addSnowflakeFrequency);
	    snowflakeMover = setInterval(function () {
	        return moveSnowflakes(options);
	    }, options.moveSnowflakeFrequency);
	    snowflakeRemover = setInterval(function () {
	        return removeSnowflakes(options);
	    }, options.removeSnowFlakeFrequency);
	};

	exports.init = init;

	exports.default = {
	    init: init
	};

/***/ }
/******/ ]);