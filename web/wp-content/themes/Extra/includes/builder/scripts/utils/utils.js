/**
 * IMPORTANT: Keep external dependencies as low as possible since this utils might be
 * imported by various frontend scripts; need to keep frontend script size low.
 */

// External dependencies
import includes from 'lodash/includes';
import get from 'lodash/get';
import $ from 'jquery';

// Internal dependencies
import { top_window } from '@core/admin/js/frame-helpers';

export const getBuilderUtilsParams = () => {
  if (window.et_builder_utils_params) {
    return window.et_builder_utils_params;
  }

  if (top_window.et_builder_utils_params) {
    return top_window.et_builder_utils_params;
  }

  return {};
};

export const getBuilderType = () => get(getBuilderUtilsParams(), 'builderType', '');

/**
 * Check current page's builder Type.
 *
 * @since 4.6.0
 *
 * @param {string} builderType Fe|vb|bfb|tb|lbb|lbp.
 *
 * @returns {bool}
 */
export const isBuilderType = (builderType) => builderType === getBuilderType();

/**
 * Return condition value.
 *
 * @since 4.6.0
 *
 * @param {string} conditionName
 *
 * @returns {bool}
 */
export const is = conditionName => get(getBuilderUtilsParams(), `condition.${conditionName}`);

/**
 * Is current page Frontend.
 *
 * @since 4.6.0
 *
 * @type {bool}
 */
export const isFE = isBuilderType('fe');

/**
 * Is current page Visual Builder.
 *
 * @since 4.6.0
 *
 * @type {bool}
 */
export const isVB = isBuilderType('vb');

/**
 * Is current page BFB / New Builder Experience.
 *
 * @since 4.6.0
 *
 * @type {bool}
 */
export const isBFB = isBuilderType('bfb');

/**
 * Is current page Theme Builder.
 *
 * @since 4.6.0
 *
 * @type {bool}
 */
export const isTB = isBuilderType('tb');

/**
 * Is current page Layout Block Builder.
 *
 * @type {bool}
 */
export const isLBB = isBuilderType('lbb');

/**
 * Is current page uses Divi Theme.
 *
 * @since 4.6.0
 *
 * @type {bool}
 */
export const isDiviTheme = is('diviTheme');

/**
 * Is current page uses Extra Theme.
 *
 * @since 4.6.0
 *
 * @type {bool}
 */
export const isExtraTheme = is('extraTheme');

/**
 * Is current page Layout Block Preview.
 *
 * @since 4.6.0
 *
 * @type {bool}
 */
export const isLBP = isBuilderType('lbp');

/**
 * Check if current window is block editor window (gutenberg editing page).
 *
 * @since 4.6.0
 *
 * @type {bool}
 */
export const isBlockEditor = 0 < $(top_window.document).find('.edit-post-layout__content').length;

/**
 * Check if current window is builder window (VB, BFB, TB, LBB).
 *
 * @since 4.6.0
 *
 * @type {bool}
 */
export const isBuilder = includes(['vb', 'bfb', 'tb', 'lbb'], getBuilderType());

/**
 * Get offsets value of all sides.
 *
 * @since 4.6.0
 *
 * @param {object} $selector JQuery selector instance.
 * @param {number} height
 * @param {number} width
 *
 * @returns {object}
 */
export const getOffsets = ($selector, width = 0, height = 0) => {
  // Return previously saved offset if sticky tab is active; retrieving actual offset contain risk
  // of incorrect offsets if sticky horizontal / vertical offset of relative position is modified.
  const isStickyTabActive = isBuilder && $selector.hasClass('et_pb_sticky') && 'fixed' !== $selector.css('position');
  const cachedOffsets     = $selector.data('et-offsets');
  const cachedDevice      = $selector.data('et-offsets-device');
  const currentDevice     = get(window.ET_FE, 'stores.window.breakpoint', '');

  // Only return cachedOffsets if sticky tab is active and cachedOffsets is not undefined and
  // cachedDevice equal to currentDevice.
  if (isStickyTabActive && cachedOffsets !== undefined && cachedDevice === currentDevice) {
    return cachedOffsets;
  }

  // Get top & left offsets
  const offsets = $selector.offset();

  // If no offsets found, return empty object
  if ('undefined' === typeof offsets) {
    return {};
  }

  // FE sets the flag for sticky module which uses transform as classname on module wrapper while
  // VB, BFB, TB, and LB sets the flag on CSS output's <style> element because it can't modify
  // its parent. This compromises avoids the needs to extract transform rendering logic
  const hasTransform = isBuilder
    ? $selector.children('.et-fb-custom-css-output[data-sticky-has-transform="on"]').length > 0
    : $selector.hasClass('et_pb_sticky--has-transform');

  let top  = 'undefined' === typeof offsets.top ? 0 : offsets.top;
  let left = 'undefined' === typeof offsets.left ? 0 : offsets.left;

  // If module is sticky module that uses transform, its offset calculation needs to be adjusted
  // because transform tends to modify the positioning of the module
  if (hasTransform) {
    // Calculate offset (relative to selector's parent) AFTER it is affected by transform
    // NOTE: Can't use jQuery's position() because it considers margin-left `auto` which causes issue
    // on row thus this manually calculate the difference between element and its parent's offset
    // @see https://github.com/jquery/jquery/blob/1.12-stable/src/offset.js#L149-L155
    const parentOffsets = $selector.parent().offset();

    const transformedPosition = {
      top: offsets.top - parentOffsets.top,
      left: offsets.left - parentOffsets.left,
    };

    // Calculate offset (relative to selector's parent) BEFORE it is affected by transform
    const preTransformedPosition = {
      top: $selector[0].offsetTop,
      left: $selector[0].offsetLeft,
    };

    // Update offset's top value
    top        += (preTransformedPosition.top - transformedPosition.top);
    offsets.top = top;

    // Update offset's left value
    left        += (preTransformedPosition.left - transformedPosition.left);
    offsets.left = left;
  }

  // Manually calculate right & bottom offsets
  offsets.right  = left + width;
  offsets.bottom = top + height;

  // Save copy of the offset on element's .data() in case of scenario where retrieving actual
  // offset value will lead to incorrect offset value (eg. sticky tab active with position offset)
  $selector.data('et-offsets', offsets);

  // Add current device to cache
  if ('' !== currentDevice) {
    $selector.data('et-offsets-device', offsets);
  }

  return offsets;
};

/**
 * Increase EventEmitter's max listeners if lister count is about to surpass the max listeners limit
 * IMPORTANT: Need to be placed BEFORE `.on()`.
 *
 * @since 4.6.0
 * @param {EventEmitter} emitter
 * @param eventName
 * @param {string} EventName
 */
export const maybeIncreaseEmitterMaxListeners = (emitter, eventName) => {
  const currentCount = emitter.listenerCount(eventName);
  const maxListeners = emitter.getMaxListeners();

  if (currentCount === maxListeners) {
    emitter.setMaxListeners(maxListeners + 1);
  }
};

/**
 * Decrease EventEmitter's max listeners if listener count is less than max listener limit and above
 * 10 (default max listener limit). If listener count is less than 10, max listener limit will
 * remain at 10
 * IMPORTANT: Need to be placed AFTER `.removeListener()`.
 *
 * @since 4.6.0
 *
 * @param {EventEmitter} emitter
 * @param {string} eventName
 */
export const maybeDecreaseEmitterMaxListeners = (emitter, eventName) => {
  const currentCount = emitter.listenerCount(eventName);
  const maxListeners = emitter.getMaxListeners();

  if (maxListeners > 10) {
    emitter.setMaxListeners(currentCount);
  }
};

/**
 * Expose frontend (FE) component via global object so it can be accessed and reused externally
 * Note: window.ET_Builder is for builder app's component; window.ET_FE is for frontend component.
 *
 * @since 4.6.0
 *
 * @param {string} type
 * @param {string} name
 * @param {mixed} component
 */
export const registerFrontendComponent = (type, name, component) => {
  // Make sure that ET_FE is available
  if ('undefined' === typeof window.ET_FE) {
    window.ET_FE = {};
  }

  if ('object' !== typeof window.ET_FE[type]) {
    window.ET_FE[type] = {};
  }

  window.ET_FE[type][name] = component;
};

/**
 * Set inline style with !important tag. JQuery's .css() can't set value with `!important` tag so
 * here it is.
 *
 * @since 4.6.2
 *
 * @param {object} $element
 * @param {string} cssProp
 * @param {string} value
 */
export const setImportantInlineValue = ($element, cssProp, value) => {
  // Remove prop from current inline style in case the prop is already exist
  $element.css(cssProp, '');

  // Get current inline style
  const inlineStyle = $element.attr('style');

  // Re-insert inline style + property with important tag
  $element.attr('style', `${inlineStyle} ${cssProp}: ${value} !important;`);
};
