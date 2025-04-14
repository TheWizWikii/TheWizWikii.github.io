// External dependencies
import { EventEmitter } from 'events';
import assign from 'lodash/assign';
import cloneDeep from 'lodash/cloneDeep';
import compact from 'lodash/compact';
import filter from 'lodash/filter';
import forEach from 'lodash/forEach';
import get from 'lodash/get';
import has from 'lodash/has';
import head from 'lodash/head';
import includes from 'lodash/includes';
import isEqual from 'lodash/isEqual';
import isFunction from 'lodash/isFunction';
import isObject from 'lodash/isObject';
import isUndefined from 'lodash/isUndefined';
import keys from 'lodash/keys';
import last from 'lodash/last';
import map from 'lodash/map';
import mapKeys from 'lodash/mapKeys';
import set from 'lodash/set';
import size from 'lodash/size';
import slice from 'lodash/slice';
import sortBy from 'lodash/sortBy';
import $ from 'jquery';

// Internal dependencies
import {
  isOrHasValue,
} from '@frontend-builder/utils/responsive-options-pure';
import {
  top_window,
} from '@core-ui/utils/frame-helpers';
import ETScriptDocumentStore from './document';
import ETScriptWindowStore from './window';
import {
  getOffsets,
  isBFB,
  isBuilder,
  isDiviTheme,
  isExtraTheme,
  isLBB,
  isTB,
  isVB,
  maybeDecreaseEmitterMaxListeners,
  maybeIncreaseEmitterMaxListeners,
  registerFrontendComponent,
} from '../utils/utils';

import {
  filterInvalidModules,
  getLimit,
} from '../utils/sticky';

// Event Constants
const SETTINGS_CHANGE = 'settings_change';

// Variables
const $body       = $('body');
const hasFixedNav = $body.hasClass('et_fixed_nav');

/**
 * Saved sticky elements. In FE, this means all the sticky settings that exist on current page.
 * In VB (and other builder context) this means sticky settings that exist on current page but
 * is rendered outside current builder type. Removed nested sticky module (sticky inside another
 * sticky module) from the module list.
 *
 * @since 4.6.0
 *
 * @type {object}
 */
const savedStickyElements = filterInvalidModules(cloneDeep(window.et_pb_sticky_elements));

/**
 * Defaults of known non module elements which its stickiness needs to be considered.
 *
 * @since 4.6.0
 *
 * @type {object}
 */
const elementsDefaults = {
  wpAdminBar: {
    id: 'wpAdminBar',
    selector: '#wpadminbar',
    exist: false,
    height: 0,
    window: 'top',
    condition: () => {
      // Admin bar doesn't have fixed position in smaller breakpoint
      const isPositionFixed = 'fixed' === top_window.jQuery(elements.wpAdminBar.selector).css('position');

      // When Responsive View's control is visible, admin bar offset becomes irrelevant. Note:
      // At this point the `height` value might not be updated yet, so manually get the height
      // value via `getHeight()` method.
      const hasVbAppFramePaddingTop = elements.builderAppFramePaddingTop.getHeight() > 0;

      return ! hasVbAppFramePaddingTop && ! isTB && ! isLBB && isPositionFixed;
    },
  },
  diviFixedPrimaryNav: {
    id: 'diviPrimaryNav',
    selector: '#main-header',
    exist: false,
    height: 0,
    window: 'app',
    condition: () => {
      // Divi Theme has fixed nav. Note: vertical header automatically removes .et_fixed_nav
      // classname so it is fine just to test fixed nav state against .et_fixed_nav classname only
      const hasFixedNavBodyClass = isDiviTheme && hasFixedNav;

      // Check for element's existence
      const isNavExist = $(elements.diviFixedPrimaryNav.selector).length > 0;

      // Primary nav is doesn't have fixed position in smaller breakpoint
      const isPositionFixed = 'fixed' === $(elements.diviFixedPrimaryNav.selector).css('position');

      return hasFixedNavBodyClass && isNavExist && isPositionFixed;
    },
    getHeight: () => {
      const $mainHeader = $(elementsDefaults.diviFixedPrimaryNav.selector);

      // Bail if this isn't Divi
      if (! isDiviTheme && 1 > $mainHeader.length) {
        return 0;
      }

      // Clone header
      const $clone = $mainHeader.clone();

      // Emulate fixed header state. Fixed header state is emulated as soon as the window is
      // scrolled so it is safe to assume that any sticky module on its sticky state will "meet"
      // header on its fixed state; this will avoid unwanted "jump" effect that happens because
      // fixed header has 400ms transition which could be slower than scroll speed; The fixed header
      // state also adds negative margin top state to #page-container which triggers document
      // dimension change event. Also add classname which will ensure that this clone won't
      // be visible to end user even if we only render it for a split second to avoid issues
      $clone.addClass('et-fixed-header et-script-temporary-measurement');

      // Add it to layout so its dimension can be measured
      $mainHeader.parent().append($clone);

      // Measure the fixed header height
      const height = $clone.outerHeight();

      // Immediately remove the cloned DOM from layout
      $clone.remove();

      return parseFloat(height);
    },
  },
  diviFixedSecondaryNav: {
    id: 'diviPrimaryNav',
    selector: '#top-header',
    exist: false,
    height: 0,
    window: 'app',
    condition: () => {
      // Divi Theme has fixed nav. Note: vertical header automatically removes .et_fixed_nav
      // classname so it is fine just to test fixed nav state against .et_fixed_nav classname only
      const hasFixedNavBodyClass = isDiviTheme && hasFixedNav;

      // Check for element's existence
      const isNavExist = $(elements.diviFixedSecondaryNav.selector).length > 0;

      // Primary nav is doesn't have fixed position in smaller breakpoint
      const isPositionFixed = 'fixed' === $(elements.diviFixedSecondaryNav.selector).css('position');

      return hasFixedNavBodyClass && isNavExist && isPositionFixed;
    },
  },
  extraFixedPrimaryNav: {
    id: 'extraFixedPrimaryNav',
    selector: '#main-header',
    exist: false,
    height: 0,
    window: 'app',
    condition: () => {
      if (! isObject(ETScriptWindowStore) || ! isExtraTheme) {
        return false;
      }

      // Extra Theme has fixed nav.
      const hasFixedNavBodyClass = isExtraTheme && hasFixedNav;

      // Check for element's existence.
      const isNavExist = $(elements.extraFixedPrimaryNav.selector).length > 0;

      // Extra has its own breakpoint for fixed nav. Detecting computed style is most likely fail
      // because retrieved value is always one step behind before the computed style result is retrieved
      const isPositionFixed = 1024 <= (ETScriptWindowStore.width + ETScriptWindowStore.verticalScrollBar);

      return hasFixedNavBodyClass && isNavExist && isPositionFixed;
    },
    getHeight: () => {
      const $mainHeader = $(elementsDefaults.extraFixedPrimaryNav.selector);

      // Bail if this isn't Extra
      if (! isExtraTheme && 1 > $mainHeader.length) {
        return 0;
      }

      // Clone header
      const $clone = $mainHeader.clone();

      // Emulate fixed header state. Fixed header state is emulated as soon as the window is
      // scrolled so it is safe to assume that any sticky module on its sticky state will "meet"
      // header on its fixed state; this will avoid unwanted "jump" effect that happens because
      // fixed header has 500ms transition which could be slower than scroll speed; The fixed header
      // state also adds negative margin top state to #page-container which triggers document
      // dimension change event. Also add classname which will ensure that this clone won't
      // be visible to end user even if we only render it for a split second to avoid issues
      $clone.addClass('et-fixed-header et-script-temporary-measurement');

      // Add it to layout so its dimension can be measured
      $mainHeader.parent().append($clone);

      // Measure the fixed header height
      const height = $clone.outerHeight();

      // Immediately remove the cloned DOM from layout
      $clone.remove();

      return parseFloat(height);
    },
  },
  builderAppFramePaddingTop: {
    id: 'builderAppFramePaddingTop',
    selector: isBFB ? '#et-bfb-app-frame' : '#et-fb-app-frame',
    exist: false,
    height: 0,
    window: 'top',
    getHeight: () => {
      const selector = elements.builderAppFramePaddingTop.selector;
      const cssProperty = isBFB ? 'marginTop' : 'paddingTop';
      const paddingTop = top_window.jQuery(selector).css(cssProperty);

      return parseFloat(paddingTop);
    }
  },
  tbHeader: {
    id: 'et-tb-branded-modal__header',
    selector: '.et-tb-branded-modal__header',
    exist: false,
    height: 0,
    window: 'top',
  },
  lbbHeader: {
    id: 'et-block-builder-modal--header',
    selector: '.et-block-builder-modal--header',
    exist: false,
    height: 0,
    window: 'top',
  },
  gbHeader: {
    id: 'edit-post-header',

    // This selector exist on WP 5.4 and below; hence these are used instead of `.block-editor-editor-skeleton__header`
    selector: '.edit-post-header',
    exist: false,
    height: 0,
    window: 'top',
  },
  gbFooter: {
    id: 'block-editor-editor-skeleton__footer',
    selector: '.block-editor-editor-skeleton__footer',
    exist: false,
    height: 0,
    window: 'top',
  },
  gbComponentsNoticeList: {
    id: 'components-notice-list',
    selector: '.components-notice-list',
    exist: false,
    height: 0,
    window: 'top',
    multiple: true,
  },
};

/**
 * Known non module elements which its stickiness needs to be considered.
 *
 * @since 4.6.0
 *
 * @type {object}
 */
const elements = cloneDeep(elementsDefaults);

// States
/**
 * Hold all sticky elements modules' properties.
 *
 * @since 4.6.0
 *
 * @type {object}
 */
let modules = {};


/**
 * Sticky Elements store.
 *
 * This store stores selected properties of all sticky elements on the page so a sticky element
 * can use other sticky element's calculated value quickly.
 *
 * @since 4.6.0
 */
class ETScriptStickyStore extends EventEmitter {
  /**
   * ETScriptStickyStore constructor.
   *
   * @since 4.6.0
   */
  constructor() {
    super();

    // Load modules passed via global variable from server via wp_localize_script()
    assign(modules, savedStickyElements);

    // Caculate top/bottom offsetModules which are basically list of sticky elements that need
    // to be considered for additional offset calculation when `Offset From Surrounding Sticky Elements`
    // option is toggled `on`
    this.generateOffsetModules();

    // Calculate known elements' properties. This needs to be done after DOM is ready
    if (isVB) {
      $(window).on('et_fb_init_app_after', () => {
        this.setElementsProps();
      });
    } else {
      $(() => {
        this.setElementsProps();
      });
    }

    // Some props need to be updated when document height is changed (eg. fixed nav's height)
    ETScriptDocumentStore.addHeightChangeListener(this.onDocumentHeightChange);

    // Builder specific event callback
    if (isBuilder) {
      // Event callback once the builder has been mounted
      $(window).on('et_fb_root_did_mount', this.onBuilderDidMount);

      // Listen to builder change if current window is builder window
      window.addEventListener('ETBuilderStickySettingsSyncs', this.onBuilderSettingsChange);
    }
  }

  /**
   * Get registered modules.
   *
   * @since 4.6.0
   *
   * @type {object}
   */
  get modules() {
    return modules;
  }

  /**
   * List of builder options (that is used by sticky elements) that has responsive mode.
   *
   * @since 4.6.0
   *
   * @returns {Array}
   */
  get responsiveOptions() {
    const options = [
      'position',
      'topOffset',
      'bottomOffset',
      'topLimit',
      'bottomLimit',
      'offsetSurrounding',
      'transition',
      'topOffsetModules',
      'bottomOffsetModules',
    ];

    return options;
  }

  /**
   * Update selected module / elements prop on document height change.
   *
   * @since 4.6.0
   */
  onDocumentHeightChange = () => {
    // Update Divi fixed nav height property. Divi fixed nav height change when it enters its sticky state
    // thus making it having different height when sits on top of viewport and during window scroll
    if (this.getElementProp('diviFixedPrimaryNav', 'exist', false)) {
      const getHeight = this.getElementProp('diviFixedPrimaryNav', 'getHeight');

      this.setElementProp('diviFixedPrimaryNav', 'height', getHeight());
    }

    // Update Extra's fixed height property. Extra fixed nav height changes as the window is scrolled
    if (this.getElementProp('extraFixedPrimaryNav', 'exist', false)) {
      const getExtraFixedMainHeaderHeight = this.getElementProp('extraFixedPrimaryNav', 'getHeight');

      this.setElementProp('extraFixedPrimaryNav', 'height', getExtraFixedMainHeaderHeight());
    }

    if (this.getElementProp('builderAppFramePaddingTop', 'exist', false)) {
      this.setElementHeight('builderAppFramePaddingTop');
    }
  }

  /**
   * Builder did mount listener callback.
   *
   * @since 4.6.0
   */
  onBuilderDidMount = () => {
    const stickyOnloadModuleKeys  = keys(window.et_pb_sticky_elements);
    const stickyMountedModuleKeys = keys(this.modules);

    // Has sticky elements but builder has no saved sticky module; sticky element on current
    // page is outside current builder (eg. page builder has with no sticky element saved but
    // TB header of current page has sticky element). Need to emit change to kickstart the stick
    // element initialization and generating offset modules
    if (stickyOnloadModuleKeys.length > 0 && isEqual(stickyOnloadModuleKeys, stickyMountedModuleKeys)) {
      this.onBuilderSettingsChange(undefined, true);
    }
  }

  /**
   * Builder settings change listener callback.
   *
   * @since 4.6.0
   *
   * @param {object} event
   * @param {bool}   forceUpdate
   */
  onBuilderSettingsChange = (event, forceUpdate = false) => {
    const settings = get(event, 'detail.settings');

    if (isEqual(settings, this.modules) && ! forceUpdate) {
      return;
    }

    // Update sticky settings. Removed nested sticky module (sticky inside another
    // sticky module) from the module list.
    modules = filterInvalidModules(cloneDeep(settings), modules);

    // Append saved sticky elements settings which is rendered outside of current builder
    // type because it won't be generated by current builder's components
    assign(modules, savedStickyElements);

    // Generate offset modules
    this.generateOffsetModules();

    this.emit(SETTINGS_CHANGE);
  }

  /**
   * Get id of all modules.
   *
   * @since 4.6.0
   *
   * @type {object} modules
   *
   * @returns {Array}
   */
  getModulesId = modules => map(modules, module => module.id)

  /**
   * Get modules based on its rendering position; also consider its offset surrounding setting if needed.
   *
   * @since 4.6.0
   * @param {string} top|bottom
   * @param position
   * @param offsetSurrounding
   * @param {string|bool} on|off|false When false, ignore offset surrounding value.
   * @returns {bool}
   */
  getModulesByPosition = (position, offsetSurrounding = false) => filter(modules, (module, id) => {
    // Check offset surrounding value; if param set to `false`, ignore it. If `on`|`off`, only
    // pass module that has matching value
    const isOffsetSurrounding = ! offsetSurrounding ? true : isOrHasValue(module.offsetSurrounding, offsetSurrounding);

    return includes(['top_bottom', position], this.getProp(id, 'position')) && isOffsetSurrounding;
  })

  /**
   * Sort modules from top to down based on offset prop. Passed module has no id or index prop so
   * offset which visually indicate module's position in the page will do.
   *
   * @since 4.6.0
   */
  sortModules = () => {
    const storeModules = this.modules;
    const modulesSize  = size(storeModules);

    // Return modules as-is if it is less than two modules; no need to sort it
    if (modulesSize < 2) {
      return storeModules;
    }

    // There's no index whatsoever, but offset's top and left indicates module's position
    const sortedModules = sortBy(storeModules, [
      module => module.offsets.top,
      module => module.offsets.left,
    ]);

    // sortBy returns array type value; remap id as object key
    const remappedModules = mapKeys(sortedModules, module => module.id);

    modules = cloneDeep(remappedModules);
  }

  /**
   * Set prop value.
   *
   * @since 4.6.0
   *
   * @param {string} id Need to be unique.
   * @param {string} name
   * @param {string} value
   */
  setProp = (id, name, value) => {
    // Skip updating if the id isn't exist
    if (! has(modules, id) || isUndefined(id)) {
      return;
    }

    const currentValue = this.getProp(id, name);

    // Skip updating prop if the value is the same
    if (currentValue === value) {
      return;
    }

    set(modules, `${id}.${name}`, value);
  }

  /**
   * Get prop.
   *
   * @since 4.6.0
   * @param {string} id
   * @param {string} name
   * @param {mixed} defaultValue
   * @param returnCurrentBreakpoint
   * @param {bool} return
   * @returns {mixed}
   */
  getProp = (id, name, defaultValue, returnCurrentBreakpoint = true) => {
    const value        = get(modules, `${id}.${name}`, defaultValue);
    const isResponsive = returnCurrentBreakpoint
      && isObject(value)
      && has(value, 'desktop')
      && includes(this.responsiveOptions, name);

    return isResponsive ? get(value, get(ETScriptWindowStore, 'breakpoint', 'desktop'), defaultValue) : value;
  }

  /**
   * Set known elements' props.
   *
   * @since 4.6.0
   */
  setElementsProps = () => {
    forEach(elements, (settings, name) => {
      if (! has(settings, 'window')) {
        return;
      }

      if (has(settings, 'condition') && isFunction(settings.condition) && ! settings.condition()) {
        // Reset props if it fails on condition check
        this.setElementProp(name, 'exist', get(elementsDefaults, `${name}.exist`, false));
        this.setElementProp(name, 'height', get(elementsDefaults, `${name}.height`, 0));
        return;
      }

      const currentWindow = 'top' === this.getElementProp(name, 'window') ? top_window : window;
      const $element      = currentWindow.jQuery(settings.selector);
      const hasElement    = $element.length > 0 && $element.is(':visible');

      if (hasElement) {
        this.setElementProp(name, 'exist', hasElement);

        this.setElementHeight(name);
      }
    });
  }

  /**
   * Set known element prop value.
   *
   * @since 4.6.0
   *
   * @param {string} id Need to be unique.
   * @param {string} name
   * @param {string} value
   */
  setElementProp = (id, name, value) => {
    const currentValue = this.getElementProp(id, name);

    // Skip updating prop if the value is the same
    if (currentValue === value) {
      return;
    }

    set(elements, `${id}.${name}`, value);
  }

  /**
   * Get known element prop.
   *
   * @since 4.6.0
   *
   * @param {string} id
   * @param {string} name
   * @param {mixed} defaultValue
   *
   * @returns {mixed}
   */
  getElementProp = (id, name, defaultValue) => get(elements, `${id}.${name}`, defaultValue)

  /**
   * Set element height.
   *
   * @since 4.6.0
   *
   * @param {string} name
   */
  setElementHeight = name => {
    const selector      = this.getElementProp(name, 'selector');
    const currentWindow = 'top' === this.getElementProp(name, 'window', 'app') ? top_window : window;
    const $selector     = currentWindow.jQuery(selector);

    let height = 0;

    forEach($selector, item => {
      const getHeight = this.getElementProp(name, 'getHeight', false);

      if (isFunction(getHeight)) {
        height += getHeight();
      } else {
        height += currentWindow.jQuery(item).outerHeight();
      }
    });

    this.setElementProp(name, 'height', parseInt(height));
  }

  /**
   * Generate offset modules for offset surrounding option.
   *
   * @since 4.6.0
   */
  generateOffsetModules = () => {
    // Get module's width, height, and offsets. These are needed to calculate offset module's
    // adjacent column adjustment. stickyElement will update this later on its initialization
    // This needs to be on earlier and different loop than the one below for generating offset
    // modules because in builder the modules need to be sorted from top to down first
    forEach(this.modules, (module, id) => {
      const $module       = $(this.getProp(id, 'selector'));
      const moduleWidth   = parseInt($module.outerWidth());
      const moduleHeight  = parseInt($module.outerHeight());
      const moduleOffsets = getOffsets($module, moduleWidth, moduleHeight);

      // Only update dimension props if module isn't on sticky state
      if (! this.isSticky(id)) {
        this.setProp(id, 'width', moduleWidth);
        this.setProp(id, 'height', moduleHeight);
        this.setProp(id, 'offsets', moduleOffsets);
      }

      // Set limits
      const position       = this.getProp(id, 'position', 'none');
      const isStickyBottom = includes(['bottom', 'top_bottom'], position);
      const isStickyTop    = includes(['top', 'top_bottom'], position);

      if (isStickyBottom) {
        const topLimit         = this.getProp(id, 'topLimit');
        const topLimitSettings = getLimit($module, topLimit);

        this.setProp(id, 'topLimitSettings', topLimitSettings);
      }

      if (isStickyTop) {
        const bottomLimit         = this.getProp(id, 'bottomLimit');
        const bottomLimitSettings = getLimit($module, bottomLimit);

        this.setProp(id, 'bottomLimitSettings', bottomLimitSettings);
      }
    });

    // Sort modules in builder to ensure top to bottom module order for generating offset modules
    if (isBuilder) {
      this.sortModules();
    }

    const { modules }             = this;
    const modulesSize             = size(modules);
    const topPositionModules      = this.getModulesByPosition('top', 'on');
    const topPositionModulesId    = this.getModulesId(topPositionModules);
    const bottomPositionModules   = this.getModulesByPosition('bottom', 'on');
    const bottomPositionModulesId = this.getModulesId(bottomPositionModules);

    // Capture top/bottom offsetModules updates for later loop
    const offsetModulesUpdates = [];

    forEach(modules, (module, id) => {
      if (isOrHasValue(module.offsetSurrounding, 'on')) {
        // Top position sticky: get all module id that uses top / top_bottom position +
        // has its offset surrounding turn on, that are rendered BEFORE THIS sticky element
        if (includes(['top', 'top_bottom'], this.getProp(id, 'position'))) {
          const topOffsetModuleIndex = topPositionModulesId.indexOf(id);
          const topOffsetModule      = slice(topPositionModulesId, 0, topOffsetModuleIndex);

          // Saves all top offset modules for reference. This still needs to be processed to
          // filter adjacent column later
          this.setProp(id, 'topOffsetModulesAll', topOffsetModule);

          // Mark for adjacent column filtering
          offsetModulesUpdates.push({
            prop: 'topOffsetModules',
            id,
          });
        }

        // Bottom position sticky: get all module id that uses bottom / top_bottom position +
        // has its offset surrounding turn on, that are rendered AFTER THIS sticky element
        if (includes(['bottom', 'top_bottom'], this.getProp(id, 'position'))) {
          const bottomOffsetModuleIndex = bottomPositionModulesId.indexOf(id);
          const bottomOffsetModules     = slice(bottomPositionModulesId, (bottomOffsetModuleIndex + 1), modulesSize);

          // Saves all bottom offset modules for reference. This still needs to be processed to
          // filter adjacent column later
          this.setProp(id, 'bottomOffsetModulesAll', bottomOffsetModules);

          // Mark for adjacent column filtering
          offsetModulesUpdates.push({
            prop: 'bottomOffsetModules',
            id,
          });
        }
      }
    });

    // Top / bottom offset modules adjacent column filtering
    if (offsetModulesUpdates.length > 0) {
      // Default offsets. Make sure all sides element is available
      const defaultOffsets = {
        top: 0,
        right: 0,
        bottom: 0,
        left: 0,
      };

      // Proper limit settings based on current offset modules position
      const offsetLimitPropMaps = {
        topOffsetModules: 'bottomLimitSettings',
        bottomOffsetModules: 'topLimitSettings',
      };

      forEach(offsetModulesUpdates, update => {
        // module's id
        const moduleId = update.id;

        // Need to be defined inside offsetModulesUpdates loop so each surrounding loop starts new
        // Will be updated on every loop so next loop has reference of what is prev modules has
        const prevSurroundingOffsets = {
          ...defaultOffsets,
        };

        // Loop over module's top/bottom offset module ids
        const offsetModules = filter(this.getProp(moduleId, `${update.prop}All`), id => {
          // Modules that are defined at top/bottomOffsetModules prop which is positioned after
          // current module is referred as surrounding (modules) offset
          const surroundingOffsets = {
            ...defaultOffsets,
            ...this.getProp(id, 'offsets', {}),
          };

          // Current module's offset
          const moduleOffsets = {
            ...defaultOffsets,
            ...this.getProp(moduleId, 'offsets'),
          };

          // Module limit's offset
          const moduleLimitOffsets      = this.getProp(moduleId, `${offsetLimitPropMaps[update.prop]}.offsets`);
          const surroundingLimitOffsets = this.getProp(id, `${offsetLimitPropMaps[update.prop]}.offsets`);

          // If current and surrounding modules both have limit offsets, their top and bottom needs
          // to be put in consideration in case they will never offset each other
          if (moduleLimitOffsets && surroundingLimitOffsets) {
            if (surroundingLimitOffsets.top < moduleLimitOffsets.top || surroundingLimitOffsets.bottom > moduleLimitOffsets.bottom) {
              return false;
            }
          }

          // If module has no limits, offset from surrounding sticky elements most likely not a
          // valid offset surrounding. There is a case where surrounding can be valid offset, which
          // is when current module on sticky state between surrounding limit top and bottom.
          // However this rarely happens and requires conditional offset based on current window
          // scroll top which might be over-engineer. Thus this is kept this way until further
          // confirmation with design team
          // @todo probably add conditional offset surrounding; confirm to design team
          if (! moduleLimitOffsets && surroundingLimitOffsets) {
            return false;
          }

          // Top Offset modules (sticky position top): modules rendered before current module
          // Bottom Offset module (sticky position bottom): modules rendered after current module
          // caveat: offset modules that are not vertically aligned with current module should not
          // be considered as offset modules and affecting current module's auto-added offset.
          // Hence this filter. Initially, all offset module should affect module's auto offset
          let shouldPass = true;

          // Surrounding module is beyond current module's right side
          // ***********
          // * current *
          // ***********
          //               ***************
          //               * surrounding *
          //               ***************
          const isSurroundingBeyondCurrentRight = surroundingOffsets.left >= moduleOffsets.right;

          // Surrounding module is beyond current module's left side
          //                   ***********
          //                   * current *
          //                   ***********
          // ***************
          // * surrounding *
          // ***************
          const isSurroundingBeyondCurrentLeft = surroundingOffsets.right < moduleOffsets.left;

          // Surrounding module overlaps with current module's right side
          // ***********                  ************************
          // * current *                  *       current        *
          // ***********            OR    ************************
          //    ***************               ***************
          //    * surrounding *               * surrounding *
          //    ***************               ***************
          const isSurroundingOverlapsCurrent = surroundingOffsets.left > moduleOffsets.left && surroundingOffsets.right > moduleOffsets.left;

          // Previous surrounding module overlaps with current module's left side.
          //       ************************
          //       *       current        *
          //       ************************
          // ********************   ******************************
          // * prev surrounding *   * surrounding (on this loop) *
          // ********************   ******************************
          const isPrevSurroundingOverlapsWithCurrent = moduleOffsets.left <= prevSurroundingOffsets.right && surroundingOffsets.top < prevSurroundingOffsets.bottom;

          // Ignore surrounding height if previous surrounding height has affected current module's offset
          // See isPrevSurroundingOverlapsWithCurrent's figure above
          const isPrevSurroundingHasAffectCurrent = isSurroundingOverlapsCurrent && isPrevSurroundingOverlapsWithCurrent;

          // Ignore the surrounding's height given the following scenarios
          if (isSurroundingBeyondCurrentRight || isSurroundingBeyondCurrentLeft || isPrevSurroundingHasAffectCurrent) {
            shouldPass = false;
          }

          // Save current surrounding offsets for next surrounding offsets comparison
          assign(prevSurroundingOffsets, surroundingOffsets);

          // true: surrounding's height is considered for current module's auto offset
          // false: surrounding's height is ignored
          return shouldPass;
        });

        // Set ${top/bottom}OffsetModules prop which will be synced to stickyElement
        this.setProp(moduleId, `${update.prop}Align`, offsetModules);
      });
    }

    // Perform secondary offset module calculation. The above works by getting the first surrounding
    // sticky on the next row that affects current sticky. This works well when the row is filled
    // like a grid, but fail if there is row in between which is not vertically overlap. Thus,
    // get the closest surrounding offset sticky from last calculation, then fetch it. The idea is
    // the last surrounding sticky might have offset which is not vertically align / overlap to
    // current sticky element
    forEach(this.modules, (module, moduleId) => {
      if (module.topOffsetModulesAlign) {
        const lastTopOffsetModule = last(module.topOffsetModulesAlign);
        const pervTopOffsetModule = this.getProp(lastTopOffsetModule, 'topOffsetModules', this.getProp(lastTopOffsetModule, 'topOffsetModulesAlign', []));

        this.setProp(moduleId, 'topOffsetModules', compact([
          ...pervTopOffsetModule,
          ...[lastTopOffsetModule],
        ]));
      }

      if (module.bottomOffsetModulesAlign) {
        const firstBottomOffsetModule = head(module.bottomOffsetModulesAlign);
        const pervBottomOffsetModule  = this.getProp(firstBottomOffsetModule, 'bottomOffsetModules', this.getProp(firstBottomOffsetModule, 'bottomOffsetModulesAlign', []));

        this.setProp(moduleId, 'bottomOffsetModules', compact([
          ...[firstBottomOffsetModule],
          ...pervBottomOffsetModule,
        ]));
      }
    });
  }

  /**
   * Check if module with given id is on sticky state.
   *
   * @since 4.6.0
   *
   * @param {string} id
   *
   * @returns {bool}
   */
  isSticky = id => get(this.modules, [id, 'isSticky'], false)

  /**
   * Add listener callback for settings change event.
   *
   * @since 4.6.0
   * @param callback
   * @param {Function}
   */
  addSettingsChangeListener = callback => {
    maybeIncreaseEmitterMaxListeners(this, SETTINGS_CHANGE);
    this.on(SETTINGS_CHANGE, callback);
    return this;
  }

  /**
   * Remove listener callback for settings change event.
   *
   * @since 4.6.0
   * @param callback
   * @param {Function}
   */
  removeSettingsChangeListener = callback => {
    this.removeListener(SETTINGS_CHANGE, callback);
    maybeDecreaseEmitterMaxListeners(this, SETTINGS_CHANGE);
    return this;
  }
}

const stickyStoreInstance = new ETScriptStickyStore;

// Register store instance as component to be exposed via global object
registerFrontendComponent('stores', 'sticky', stickyStoreInstance);

// Export store instance
// IMPORTANT: For uniformity, import this as ETScriptStickyStore
export default stickyStoreInstance;
