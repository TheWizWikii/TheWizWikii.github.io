// Sticky Elements specific utils, used accross files

// External dependencies
import filter from 'lodash/filter';
import forEach from 'lodash/forEach';
import get from 'lodash/get';
import head from 'lodash/head';
import includes from 'lodash/includes';
import isEmpty from 'lodash/isEmpty';
import isString from 'lodash/isString';
import $ from 'jquery';

// Internal dependencies
import {
  getOffsets,
} from './utils';

/**
 * Get top / bottom limit attributes.
 *
 * @since 4.6.0
 * @param {object} $selector
 * @param limit
 * @param {string}
 * @returns {object}
 * @returns {string} Object.limit.
 * @returns {number} Object.height.
 * @returns {number} Object.width.
 * @return {object} object.offsets
 * @return {number} object.offsets.top
 * @return {number} object.offsets.right
 * @return {number} object.offsets.bottom
 * @return {number} object.offsets.left
 */
export const getLimit = ($selector, limit) => {
  // @todo update valid limits based on selector
  const validLimits = ['body', 'section', 'row', 'column'];

  if (! includes(validLimits, limit)) {
    return false;
  }

  // Limit selector
  const $limitSelector = getLimitSelector($selector, limit);

  if (! $limitSelector) {
    return false;
  }

  const height = $limitSelector.outerHeight();
  const width  = $limitSelector.outerWidth();

  return {
    limit,
    height,
    width,
    offsets: getOffsets($limitSelector, width, height),
  };
};

/**
 * Get top / bottom limit selector based on given name.
 *
 * @since 4.6.0
 *
 * @param {object} $selector
 * @param {string} limit
 *
 * @returns {bool|object}
 */
export const getLimitSelector = ($selector, limit) => {
  let parentSelector = false;

  switch (limit) {
    case 'body':
      parentSelector = '.et_builder_inner_content';
      break;
    case 'section':
      parentSelector = '.et_pb_section';
      break;
    case 'row':
      parentSelector = '.et_pb_row';
      break;
    case 'column':
      parentSelector = '.et_pb_column';
      break;
    default:
      break;
  }

  return parentSelector ? $selector.closest(parentSelector) : false;
};

/**
 * Filter invalid sticky modules
 * 1. Sticky module inside another sticky module.
 *
 * @param {object} modules
 * @param {object} currentModules
 *
 * @since 4.6.0
 */
export const filterInvalidModules = (modules, currentModules = {}) => {
  const filteredModules = {};

  forEach(modules, (module, key) => {
    // If current sticky module is inside another sticky module, ignore current module
    if ($(module.selector).parents('.et_pb_sticky_module').length > 0) {
      return;
    }

    // Repopulate the module list
    if (! isEmpty(currentModules) && currentModules[key]) {
      // Keep props that isn't available on incoming modules intact
      filteredModules[key] = {
        ...currentModules[key],
        ...module,
      };
    } else {
      filteredModules[key] = module;
    }
  });

  return filteredModules;
};

/**
 * Get sticky style of given module by cloning, adding sticky state classname, appending DOM,
 * retrieving value, then immediately the cloned DOM. This is needed for property that is most
 * likely to be affected by transition if the sticky value is retrieved on the fly, thus it needs
 * to be retrieved ahead its time by this approach.
 *
 * @since 4.6.0
 *
 * @param {string} id
 * @param {object} $module
 * @param {object} $placeholder
 *
 * @returns {object}
 */
export const getStickyStyles = (id, $module, $placeholder) => {
  // Sticky state classname to be added; these will make cloned module to have fixed position and
  // make sticky style take effect
  const stickyStyleClassname = 'et_pb_sticky et_pb_sticky_style_dom';

  // Cloned the module add sticky state classname; set the opacity to 0 and remove the transition
  // so the dimension can be immediately retrieved
  const $stickyStyleDom = $module.clone().addClass(stickyStyleClassname).attr({
    'data-sticky-style-dom-id': id,

    // Remove inline styles so on-page styles works. Especially needed if module is in sticky state
    style: '',
  }).css({
    opacity: 0,
    transition: 'none',
    animation: 'none',
  });

  // Cloned module might contain image. However the image might take more than a milisecond to be
  // loaded on the cloned module after the module is appended to the layout EVEN IF the image on
  // the $module has been loaded. This might load to inaccurate sticky style calculation. To avoid
  // it, recreate the image by getting actual width and height then recreate the image using SVG
  $stickyStyleDom.find('img').each(function(index) {
    const $img           = $(this);
    const $measuredImg   = $module.find('img').eq(index);
    const measuredWidth  = get($measuredImg, [0, 'naturalWidth'], $module.find('img').eq(index).outerWidth());
    const measuredHeight = get($measuredImg, [0, 'naturalHeight'], $module.find('img').eq(index).outerHeight());

    $img.attr({
      // Remove scrse to force DOM to use src
      scrset: '',

      // Recreate svg to use image's actual width so the image reacts appropriately when sticky
      // style modifies image dimension (eg image has 100% and padding in sticky style is larger;
      // this will resulting in image being smaller because the wrapper dimension is smaller)
      src: `data:image/svg+xml;utf8,<svg xmlns="http://www.w3.org/2000/svg" width="${measuredWidth}" height="${measuredHeight}"><rect width="${measuredWidth}" height="${measuredHeight}" /></svg>`,
    });
  });

  // Append the cloned DOM
  $module.after($stickyStyleDom);

  // Get inline margin style value that is substraction of sticky style - style due to position
  // relative to fixed change
  const getMarginStyle = corner => {
    const marginPropName = `margin${corner}`;
    const $normalModule  = $module.hasClass('et_pb_sticky') ? $placeholder : $module;

    return parseFloat($stickyStyleDom.css(marginPropName)) - parseFloat($normalModule.css(marginPropName));
  };

  /**
   * Equalize Column Heights :: If the parent container is an equal column(Flexbox), temporary hide
   * the placeholder module and the original modules to restore(expand) the width of the $stickyStyleDom.
   * We insert two clones i.e data-sticky-style-dom-id and data-sticky-placeholder-id of the module at the
   * original location of the module which causes columns(flex items) to shrink to fit in the row 
   * i.e .et_pb_equal_columns flex container.
   */
  const isEqualColumns = $module.parent().hasClass('et_pb_equal_columns');

  if(isEqualColumns) {
    $module.hide();
    $placeholder.hide();
  }

  // Measure sticky style DOM properties
  const styles = {
    height: $stickyStyleDom.outerHeight(),
    width: $stickyStyleDom.outerWidth(),
    marginRight: getMarginStyle('Right'),
    marginLeft: getMarginStyle('Left'),
    padding: $stickyStyleDom.css('padding'),
  };

  // display module and placeholder.
  if(isEqualColumns) {
    $module.show();
    $placeholder.show();
  }

  // Immediately remove the cloned DOM
  $(`.et_pb_sticky_style_dom[data-sticky-style-dom-id="${id}"]`).remove();

  return styles;
};

/**
 * Remove given property's transition from transition property's value. To make some properties
 * (eg. Width, top, left) transition smoothly when entering / leaving sticky state, its property
 * and transition need to be removed then re-added 50ms later. This is mostly happened because the
 * module positioning changed from relative to fixed when entering/leaving sticky state.
 *
 * @since 4.6.0
 *
 * @param {string} transitionValue
 * @param {Array} trimmedProperties
 *
 * @returns {string}
 */
export const trimTransitionValue = (transitionValue, trimmedProperties) => {
  // Make sure that transitionValue is string. Otherwise split will throw error
  if (! isString(transitionValue)) {
    transitionValue = '';
  }

  const transitions  = transitionValue.split(', ');
  const trimmedValue = filter(transitions, transition => ! includes(trimmedProperties, head(transition.split(' '))));

  return isEmpty(trimmedValue) ? 'none' : trimmedValue.join(', ');
};

/**
 * Calculate automatic offset that should be given based on sum of heights of all sticky modules
 * that are currently in sticky state when window reaches $target's offset.
 *
 * @since 4.6.0
 *
 * @param {object} $target
 *
 * @returns {number}
 */
export const getClosestStickyModuleOffsetTop = $target => {
  const offset = $target.offset();
  offset.right = offset.left + $target.outerWidth();

  let closestStickyElement   = null;
  let closestStickyOffsetTop = 0;

  // Get all sticky module data from store. NOTE: this util might be used on various output build
  // so it needs to get sticky store value via global object instead of importing it
  const stickyModules = get(window.ET_FE, 'stores.sticky.modules', {});

  // Loop sticky module data to get the closest sticky module to given y offset. Sticky module
  // already has map of valid modules it needs to consider as automatic offset due to
  // adjacent-column situation.
  // @see https://github.com/elegantthemes/Divi/issues/19432
  forEach(stickyModules, stickyModule => {
    // Ignore sticky module if it is stuck to bottom
    if (! includes(['top_bottom', 'top'], stickyModule.position)) {
      return;
    }

    // Ignore if $target is sticky module (that sticks to top; stuck to bottom check above has
    // made sure of it) - otherwise the auto-generate offset will subtract the element's offset
    // and causing the scroll never reaches $target location.
    // @see https://github.com/elegantthemes/Divi/issues/23240
    if ($target.is(get(stickyModule, 'selector'))) {
      return;
    }

    // Ignore if sticky module's right edge doesn't collide with target's left edge
    if (get(stickyModule, 'offsets.right', 0) < offset.left) {
      return;
    }

    // Ignore if sticky module's left edge doesn't collide with target's right edge
    if (get(stickyModule, 'offsets.left', 0) > offset.right) {
      return;
    }

    // Ignore sticky module if it is located below given y offset
    if (get(stickyModule, 'offsets.top', 0) > offset.top) {
      return;
    }

    // Ignore sticky module if its bottom limit is higher than given y offset
    const bottomLimitBottom = get(stickyModule, 'bottomLimitSettings.offsets.bottom');

    if (bottomLimitBottom && bottomLimitBottom < offset.top) {
      return;
    }

    closestStickyElement = stickyModule;
  });

  // Once closest sticky module to given y offset has been found, loop its topOffsetModules, get
  // each module's heightSticky and return the sum of their heights
  if (get(closestStickyElement, 'topOffsetModules', false)) {
    forEach(get(closestStickyElement, 'topOffsetModules', []), stickyId => {
      // Get sticky module's height on sticky state; fallback to height just to be safe
      const stickyModuleHeight = get(stickyModules, [stickyId, 'heightSticky'], get(stickyModules, [stickyId, 'height'], 0));

      // Sum up top offset module's height
      closestStickyOffsetTop += stickyModuleHeight;
    });

    // Get closest-to-y-offset's sticky module's height on sticky state;
    const closestStickyElementHeight = get(stickyModules, [closestStickyElement.id, 'heightSticky'], get(stickyModules, [closestStickyElement.id, 'height'], 0));

    // Sum up top offset module's height
    closestStickyOffsetTop += closestStickyElementHeight;
  }

  return closestStickyOffsetTop;
};

/**
 * Determine if the target is in sticky state.
 *
 * @since 4.9.5
 *
 * @param {object} $target
 *
 * @returns {bool}
 */
export const isTargetStickyState = $target => {
  const stickyModules = get(window.ET_FE, 'stores.sticky.modules', {});

  let isStickyState = false;

  forEach(stickyModules, stickyModule => {
    const isTarget             = $target.is(get(stickyModule, 'selector'));
    const {isSticky, isPaused} = stickyModule;

    // If the target is in sticky state and not paused, set isStickyState to true and exit iteration.
    // Elements can have a sticky limit (ex: section) in which case they can be sticky but paused.
    if (isTarget && isSticky && !isPaused) {
      isStickyState = true;

      return false; // Exit iteration.
    }
  });

  return isStickyState;
};
