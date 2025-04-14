// External dependencies
import { EventEmitter } from 'events';
import forEach from 'lodash/forEach';
import get from 'lodash/get';
import includes from 'lodash/includes';
import isEqual from 'lodash/isEqual';
import $ from 'jquery';

// Internal dependencies
import { top_window } from '@core-ui/utils/frame-helpers';
import ETScriptStickyStore from './sticky';
import {
  getContentAreaSelector,
  getTemplateEditorIframe,
} from '../../frontend-builder/gutenberg/utils/selectors';
import { isTemplateEditor } from '../../frontend-builder/gutenberg/utils/conditionals';
import {
  getBuilderUtilsParams,
  isBFB,
  isExtraTheme,
  isFE,
  isLBB,
  isLBP,
  isTB,
  isVB,
  maybeDecreaseEmitterMaxListeners,
  maybeIncreaseEmitterMaxListeners,
  registerFrontendComponent,
} from '../utils/utils';

// Builder window
const $window         = $(window);
const $topWindow      = top_window.jQuery(top_window);
const hasTopWindow    = ! isEqual(window, top_window);
const windowLocations = hasTopWindow ? ['app', 'top'] : ['app'];

// Event Constants
const HEIGHT_CHANGE              = 'height_change';
const WIDTH_CHANGE               = 'width_change';
const SCROLL_TOP_CHANGE          = 'scroll_top_change';
const BREAKPOINT_CHANGE          = 'breakpoint_change';
const SCROLL_LOCATION_CHANGE     = 'scroll_location_change';
const VERTICAL_SCROLL_BAR_CHANGE = 'vertical_scroll_bar_change';

// States.
// Private, limited to this module (ETScriptWindowStore class) only
const states = {
  breakpoint: 'desktop',
  extraMobileBreakpoint: false,
  isBuilderZoomed: false,
  scrollLocation: getBuilderUtilsParams().onloadScrollLocation, // app|top
  scrollTop: {
    app: 0,
    top: 0,
  },
  height: {
    app: 0,
    top: 0,
  },
  width: {
    app: 0,
    top: 0,
  },
  bfbIframeOffset: {
    top: 0,
    left: 0,
  },
  lbpIframeOffset: {
    top: 0,
    left: 0,
  },
  verticalScrollBar: {
    app: 0,
    top: 0,
  },
};

// Valid values.
// Retrieved from server, used for validating values
const validValues = {
  scrollLocation: [...getBuilderUtilsParams().scrollLocations],
};

// Variables
const builderScrollLocations = {
  ...getBuilderUtilsParams().builderScrollLocations,
};

// @todo need to change how this works since builder already have et_screen_sizes(), unless
// we prefer to add another breakpoint functions
const deviceMinimumBreakpoints = {
  desktop: 980,
  tablet: 767,
  phone: 0,
};
const bfbFrameId               = '#et-bfb-app-frame';

/**
 * Window store.
 *
 * This store listen to direct window's events; builder callback listen to this store's events
 * to avoid dom-based calculation whenever possible; use the property passed by this store.
 *
 * @since 4.6.0
 */
class ETScriptWindowStore extends EventEmitter {
  /**
   * ETScriptWindowStore constructor.
   *
   * @since 4.6.0
   */
  constructor() {
    super();

    // Set app window onload values
    const windowWidth     = $window.innerWidth();
    const windowHeight    = $window.innerHeight();
    const windowScrollTop = $window.scrollTop();

    this.setWidth('app', windowWidth).setHeight('app', windowHeight);
    this.setScrollTop('app', windowScrollTop);
    this.setVerticalScrollBarWidth('app', (window.outerWidth - windowWidth));

    // Set top window onload values (if top window exist)
    if (hasTopWindow) {
      const topWindowWidth     = $topWindow.innerWidth();
      const topWindowHeight    = $topWindow.innerHeight();
      const topWindowScrollTop = top_window.jQuery(top_window).scrollTop();

      this.setWidth('top', topWindowWidth).setHeight('top', topWindowHeight);
      this.setScrollTop('top', topWindowScrollTop);
      this.setVerticalScrollBarWidth('top', (top_window.outerWidth - topWindowWidth));
    }

    // Set iframe offset
    if (isBFB) {
      this.setBfbIframeOffset();
    }

    // Set Layout Block iframe offset
    if (isLBP) {
      this.setLayoutBlockPreviewIframeOffset();
    }
  }

  /**
   * Set window height.
   *
   * @since 4.6.0
   *
   * @param {string} windowLocation App|top.
   * @param {number} height
   *
   * @returns {Window}
   */
  setHeight = (windowLocation = 'app', height) => {
    if (height === states.height[windowLocation]) {
      return this;
    }

    states.height[windowLocation] = height;

    this.emit(HEIGHT_CHANGE);

    return this;
  };

  /**
   * Set window width.
   *
   * @since 4.6.0
   *
   * @param {string} windowLocation App|top.
   * @param {number} width
   *
   * @returns {Window}
   */
  setWidth = (windowLocation = 'app', width) => {
    if (width === states.width[windowLocation]) {
      return this;
    }

    // Only app window could set breakpoint
    if ('app' === windowLocation) {
      this.setBreakpoint(width);

      // Extra theme has its own "mobile breakpoint" (below 1024px)
      if (isExtraTheme) {
        const outerWidth            = this.width + this.verticalScrollBar;
        const extraMobileBreakpoint = 1024;
        const fixedNavActivation    = ! states.extraMobileBreakpoint && outerWidth >= extraMobileBreakpoint;
        const fixedNavDeactivation  = states.extraMobileBreakpoint && outerWidth < extraMobileBreakpoint;

        // Re-set element props when Extra mobile breakpoint change happens
        if (fixedNavActivation || fixedNavDeactivation) {
          states.extraMobileBreakpoint = (outerWidth >= extraMobileBreakpoint);

          ETScriptStickyStore.setElementsProps();
        }
      }
    }

    states.width[windowLocation] = width;

    this.emit(WIDTH_CHANGE);

    return this;
  };

  /**
   * Set scroll location value.
   *
   * @since 4.6.0
   *
   * @param {string} scrollLocation App|top.
   *
   * @returns {ETScriptWindowStore}
   */
  setScrollLocation = scrollLocation => {
    // Prevent incorrect scroll location value from being saved
    if (! includes(validValues.scrollLocation, scrollLocation)) {
      return false;
    }

    if (scrollLocation === states.scrollLocation) {
      return this;
    }

    states.scrollLocation = scrollLocation;

    this.emit(SCROLL_LOCATION_CHANGE);

    return this;
  }

  /**
   * Set scroll top value.
   *
   * @since 4.6.0
   *
   * @param {string} windowLocation App|top.
   * @param {number} scrollTop
   *
   * @returns {ETScriptWindowStore}
   */
  setScrollTop = (windowLocation, scrollTop) => {
    if (scrollTop === states.scrollTop[windowLocation]) {
      return this;
    }

    states.scrollTop[windowLocation] = scrollTop;

    this.emit(SCROLL_TOP_CHANGE);

    return this;
  }

  /**
   * Set builder zoomed status (on builder only).
   *
   * @since 4.6.0
   *
   * @param {string} builderPreviewMode Desktop|tablet|phone|zoom|wireframe.
   */
  setBuilderZoomedStatus = builderPreviewMode => {
    const isBuilderZoomed = 'zoom' === builderPreviewMode;

    states.isBuilderZoomed = isBuilderZoomed;
  }

  /**
   * Set BFB iframe offset.
   *
   * @since 4.6.0
   */
  setBfbIframeOffset = () => {
    states.bfbIframeOffset = top_window.jQuery(bfbFrameId).offset();
  }

  /**
   * Set Layout Block iframe offset.
   *
   * @since 4.6.0
   */
  setLayoutBlockPreviewIframeOffset = () => {
    const blockId          = get(window.ETBlockLayoutModulesScript, 'blockId', '');
    const previewIframeId  = `#divi-layout-iframe-${blockId}`;
    const $block           = top_window.jQuery(previewIframeId).closest('.wp-block[data-type="divi/layout"]');
    const blockPosition    = $block.position();
    const contentSelectors = [
      // WordPress 5.4
      'block-editor-editor-skeleton__content',

      // WordPress 5.5
      'interface-interface-skeleton__content',
    ];

    let blockOffsetTop = parseInt(get(blockPosition, 'top', 0));

    // Since WordPress 5.4, blocks list position to its parent somehow is not considered
    // Previous inserted DOM are also gone + Block item now has collapsing margin top/bottom
    // These needs to be manually calculated here since the result is no longer identical
    if (includes(contentSelectors, getContentAreaSelector(top_window, false))) {
      // Find Block List Layout. By default, it's located on editor of top window.
      // When Template Editor is active, it's "moved" to editor of iframe window.
      const $blockEditorLayout = isTemplateEditor() ? getTemplateEditorIframe(top_window).find('.block-editor-block-list__layout.is-root-container') : top_window.jQuery('.block-editor-block-list__layout');

      // Blocks list position to its parent (title + content wrapper)
      // WordPress 5.4 = 183px
      // WordPress 5.5 = 161px
      if ($blockEditorLayout.length) {
        blockOffsetTop += $blockEditorLayout.position().top;
      }

      // Compensating collapsing block item margin top
      blockOffsetTop += parseInt($block.css('marginTop')) || 0;
    }

    // Admin bar in less than 600 width window uses absolute positioning which stays on top of
    // document and affecting iframe top offset
    if (600 > this.width && ETScriptStickyStore.getElementProp('wpAdminBar', 'exist', false)) {
      blockOffsetTop += ETScriptStickyStore.getElementProp('wpAdminBar', 'height', 0);
    }

    states.lbpIframeOffset.top = blockOffsetTop;
  }

  /**
   * Set vertical scrollbar width.
   *
   * @since 4.6.0
   *
   * @param {string} windowLocation
   * @param {number} width
   */
  setVerticalScrollBarWidth = (windowLocation = 'app', width) => {
    if (width === states.verticalScrollBar[windowLocation]) {
      return this;
    }

    states.verticalScrollBar[windowLocation] = width;

    this.emit(VERTICAL_SCROLL_BAR_CHANGE);

    return this;
  }

  /**
   * Get current window width.
   *
   * @since 4.6.0
   *
   * @returns {number}
   */
  get width() {
    return states.width[this.scrollLocation];
  }

  /**
   * Get current window height.
   *
   * @since 4.6.0
   *
   * @returns {number}
   */
  get height() {
    return states.height[this.scrollLocation];
  }

  /**
   * Get current window scroll location.
   *
   * @since 4.6.0
   *
   * @returns {string} App|top.
   */
  get scrollLocation() {
    return states.scrollLocation;
  }

  /**
   * Get current window scroll top / distance to document.
   *
   * @since 4.6.0
   *
   * @returns {number}
   */
  get scrollTop() {
    const multiplier = this.isBuilderZoomed ? 2 : 1;

    let appFrameOffset = 0;

    // Add app iframe offset on scrollTop calculation in BFB
    if (isBFB) {
      appFrameOffset += states.bfbIframeOffset.top;
    }

    // Add Layout Block preview iframe on scrollTop calculation
    if (isLBP) {
      appFrameOffset += states.lbpIframeOffset.top;
    }

    return (states.scrollTop[this.scrollLocation] - appFrameOffset) * multiplier;
  }

  /**
   * Get current app window breakpoint (by device).
   *
   * @since 4.6.0
   *
   * @returns {string}
   */
  get breakpoint() {
    return states.breakpoint;
  }

  /**
   * Get builder zoomed status.
   *
   * @since 4.6.0
   *
   * @returns {bool}
   */
  get isBuilderZoomed() {
    return states.isBuilderZoomed;
  }

  /**
   * Get current window vertical scrollbar width.
   *
   * @since 4.6.0
   *
   * @returns {number}
   */
  get verticalScrollBar() {
    return states.verticalScrollBar[this.scrollLocation];
  }

  /**
   * Get builder scroll location of builder context + preview mode.
   *
   * @since 4.6.0
   *
   * @param {string} previewMode Desktop|tablet|phone|zoom|wireframe.
   *
   * @returns {string} App|top.
   */
  getBuilderScrollLocation = previewMode => get(builderScrollLocations, previewMode, 'app')

  /**
   * Add width change event listener.
   *
   * @since 4.6.0
   *
   * @param {Function} callback
   *
   * @returns {Window}
   */
  addWidthChangeListener = callback => {
    maybeIncreaseEmitterMaxListeners(this, WIDTH_CHANGE);
    this.on(WIDTH_CHANGE, callback);
    return this;
  };

  /**
   * Remove width change event listener.
   *
   * @since 4.6.0
   *
   * @param {Function} callback
   *
   * @returns {Window}
   */
  removeWidthChangeListener = callback => {
    this.removeListener(WIDTH_CHANGE, callback);
    maybeDecreaseEmitterMaxListeners(this, WIDTH_CHANGE);
    return this;
  };

  /**
   * Add height change event listener.
   *
   * @since 4.6.0
   *
   * @param {Function} callback
   *
   * @returns {Window}
   */
  addHeightChangeListener = callback => {
    maybeIncreaseEmitterMaxListeners(this, HEIGHT_CHANGE);
    this.on(HEIGHT_CHANGE, callback);
    return this;
  };

  /**
   * Remove height change event listener.
   *
   * @since 4.6.0
   *
   * @param {Function} callback
   *
   * @returns {Window}
   */
  removeHeightChangeListener = callback => {
    this.removeListener(HEIGHT_CHANGE, callback);
    maybeDecreaseEmitterMaxListeners(this, HEIGHT_CHANGE);
    return this;
  };

  /**
   * Add scroll location change event listener.
   *
   * @param callback
   * @since 4.6.0
   * @returns {ETScriptWindowStore}
   */
  addScrollLocationChangeListener = callback => {
    maybeIncreaseEmitterMaxListeners(this, SCROLL_LOCATION_CHANGE);
    this.on(SCROLL_LOCATION_CHANGE, callback);
    return this;
  }

  /**
   * Remove scroll location change event listener.
   *
   * @param callback
   * @since 4.6.0
   * @returns {ETScriptWindowStore}
   */
  removeScrollLocationChangeListener = callback => {
    this.removeListener(SCROLL_LOCATION_CHANGE, callback);
    maybeDecreaseEmitterMaxListeners(this, SCROLL_LOCATION_CHANGE);
    return this;
  }

  /**
   * Add scroll top change event listener.
   *
   * @param callback
   * @since 4.6.0
   * @returns {ETScriptWindowStore}
   */
  addScrollTopChangeListener = callback => {
    maybeIncreaseEmitterMaxListeners(this, SCROLL_TOP_CHANGE);
    this.on(SCROLL_TOP_CHANGE, callback);
    return this;
  }

  /**
   * Remove scroll top change event listener.
   *
   * @param callback
   * @since 4.6.0
   * @returns {ETScriptWindowStore}
   */
  removeScrollTopChangeListener = callback => {
    this.removeListener(SCROLL_TOP_CHANGE, callback);
    maybeDecreaseEmitterMaxListeners(this, SCROLL_TOP_CHANGE);
    return this;
  }

  /**
   * Set breakpoint (by device) based on window width.
   *
   * @since 4.6.0
   *
   * @todo Update breakpoint setting mechanic so this won't need to define another screen size definition
   *       and able to reuse (et_screen_size()).
   *
   * @param {number} windowWidth
   *
   * @returns {ETScriptWindowStore}
   */
  setBreakpoint = windowWidth => {
    let newBreakpoint = '';

    forEach(deviceMinimumBreakpoints, (minWidth, device) => {
      if (windowWidth > minWidth) {
        newBreakpoint = device;

        // equals to "break"
        return false;
      }
    });

    // No need to update breakpoint property if it is unchanged
    if (this.breakpoint === newBreakpoint) {
      return;
    }

    states.breakpoint = newBreakpoint;

    this.emit(BREAKPOINT_CHANGE);

    return this;
  }

  /**
   * Add breakpoint change event listener.
   *
   * @since 4.6.0
   *
   * @param {Function} callback
   */
  addBreakpointChangeListener = callback => {
    maybeIncreaseEmitterMaxListeners(this, BREAKPOINT_CHANGE);
    this.on(BREAKPOINT_CHANGE, callback);
    return this;
  }

  /**
   * Remove breakpoint change event listener.
   *
   * @since 4.6.0
   *
   * @param {Function} callback
   */
  removeBreakpointChangeListener = callback => {
    this.removeListener(BREAKPOINT_CHANGE, callback);
    maybeDecreaseEmitterMaxListeners(this, BREAKPOINT_CHANGE);
    return this;
  }
}

// initiate window store instance
const windowStoreInstance = new ETScriptWindowStore();


/**
 * Listen for (app/top) window events, and update store's value
 * store is listener free; it only hold / set / get values.
 */
forEach(windowLocations, windowLocation => {
  const isTop          = 'top' === windowLocation;
  const isApp          = 'app' === windowLocation;
  const currentWindow  = isApp ? window : top_window;
  const $currentWindow = currentWindow.jQuery(currentWindow);

  // Scroll in Theme Builder & Layout Block Builder happens on element; adjustment needed
  // const scrollWindow   = isTop && (isTB || isLBB) ? currentWindow.document.getElementById('et-fb-app') : currentWindow;
  const scrollWindow = () => {
    // Theme Builder & Layout Block Builder
    if (isTop && (isTB || isLBB)) {
      return currentWindow.document.getElementById('et-fb-app');
    }

    // Layout Block Preview / Gutenberg
    if (isTop && isLBP) {
      return currentWindow.document.getElementsByClassName(getContentAreaSelector(currentWindow, false))[0];
    }

    return currentWindow;
  };

  // listen to current (app/top) window resize event
  currentWindow.addEventListener('resize', () => {
    const width  = currentWindow.jQuery(currentWindow).innerWidth();
    const height = currentWindow.jQuery(currentWindow).innerHeight();

    windowStoreInstance.setWidth(windowLocation, width).setHeight(windowLocation, height);
    windowStoreInstance.setVerticalScrollBarWidth(windowLocation, (currentWindow.outerWidth - width));

    if ((windowStoreInstance.width > 782 && height <= 782) || (windowStoreInstance.width <= 782 && height > 782)) {
      // Wait until admin bar's viewport style kicks in
      setTimeout(() => {
        ETScriptStickyStore.setElementHeight('wpAdminBar');

        windowStoreInstance.emit(SCROLL_TOP_CHANGE);
      }, 300);
    }
  });

  // listen to current (app/top) window scroll event
  scrollWindow().addEventListener('scroll', () => {
    const scrollTop = isTop && (isTB || isLBB || isLBP) ? scrollWindow().scrollTop : scrollWindow().pageYOffset;

    windowStoreInstance.setScrollTop(windowLocation, scrollTop);
  });

  // Top window listener only
  if (isTop) {
    // Listen to builder's preview mode change that is passed via top window event
    $currentWindow.on('et_fb_preview_mode_changed', (event, screenMode, builderMode) => {
      const scrollLocation = windowStoreInstance.getBuilderScrollLocation(builderMode);

      windowStoreInstance.setBuilderZoomedStatus(builderMode);
      windowStoreInstance.setScrollLocation(scrollLocation);
    });

    // Update iframe offset if any metabox is moved
    if (isBFB) {
      currentWindow.addEventListener('ETBFBMetaboxSortStopped', () => {
        windowStoreInstance.setBfbIframeOffset();
      });
    }

    // Gutenberg moves the scroll back to window if window's width is less than 600px
    if (isLBP) {
      currentWindow.addEventListener('scroll', () => {
        if (windowStoreInstance.width > 600) {
          return;
        }

        const scrollTop = currentWindow.pageYOffset;

        windowStoreInstance.setScrollTop(windowLocation, scrollTop);
      });
    }

    // When scroll is located on top window, there is a chance that the top window actually scrolls
    // before the builder is loaded which means initial scroll top value actually has changed
    // to avoid issue caused by it, when app window that carries this script is loaded, trigger
    // scroll event on the top window's scrolling element
    scrollWindow().dispatchEvent(new CustomEvent('scroll'));
  }

  // App window listener only
  if (isApp) {
    // Update known element props when breakpoint changes. Breakpoint change is basically less
    // aggressive resize event, happened between known window's width
    if (isFE || isVB) {
      windowStoreInstance.addBreakpointChangeListener(() => {
        ETScriptStickyStore.setElementsProps();
      });
    }

    // Update iframe offset if layout block is moved
    if (isLBP) {
      currentWindow.addEventListener('ETBlockGbBlockOrderChange', () => {
        // Need to wait at least 300ms until GB animation is done
        setTimeout(() => {
          windowStoreInstance.setLayoutBlockPreviewIframeOffset();

          windowStoreInstance.emit(SCROLL_TOP_CHANGE);
        }, 300);
      });

      // Update iframe offset if notice size is changed
      currentWindow.addEventListener('ETGBNoticeSizeChange', () => {
        if (ETScriptStickyStore.getElementProp('gbComponentsNoticeList', 'exist', false)) {
          ETScriptStickyStore.setElementHeight('gbComponentsNoticeList');

          windowStoreInstance.emit(SCROLL_TOP_CHANGE);
        }
      });
    }
  }
});

// Register store instance as component to be exposed via global object
registerFrontendComponent('stores', 'window', windowStoreInstance);

// Export store instance
// IMPORTANT: For uniformity, import this as ETScriptWindowStore
export default windowStoreInstance;
