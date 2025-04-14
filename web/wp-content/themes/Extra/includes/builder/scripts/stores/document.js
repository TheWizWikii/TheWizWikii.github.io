// External dependencies
import { EventEmitter } from 'events';
import debounce from 'lodash/debounce';
import get from 'lodash/get';

// Internal dependencies
import {
  maybeDecreaseEmitterMaxListeners,
  maybeIncreaseEmitterMaxListeners,
  registerFrontendComponent,
} from '../utils/utils';


const HEIGHT_CHANGE    = 'height_change';
const WIDTH_CHANGE     = 'width_change';
const DIMENSION_CHANGE = 'dimension_change';

// States
const states = {
  height: 0,
  width: 0,
};

/**
 * Document store; track document height (at the moment) and its changes. Builder elements
 * should listen and get this store's value instead of directly getting it from document.
 * ETScriptDocumentStore is not exported; intentionally export its instance so there'll only be one
 * ETScriptDocumentStore instance.
 *
 * @since 4.6.0
 */
class ETScriptDocumentStore extends EventEmitter {
  /**
   * ETScriptDocumentStore constructor.
   *
   * @since 4.6.0
   */
  constructor() {
    super();

    this.setHeight(get(document, 'documentElement.offsetHeight'));
    this.setWidth(get(document, 'documentElement.offsetWidth'));
  }

  /**
   * Record document height.
   *
   * @since 4.6.0
   *
   * @param {number} height
   *
   * @returns {Window}
   */
  setHeight = height => {
    if (height === states.height) {
      return this;
    }

    states.height = height;

    this.emit(HEIGHT_CHANGE);
    this.emit(DIMENSION_CHANGE);

    return this;
  };

  /**
   * Record document width.
   *
   * @since 4.6.0
   *
   * @param {number} width
   *
   * @returns {Window}
   */
  setWidth = width => {
    if (width === states.width) {
      return this;
    }

    states.width = width;

    this.emit(WIDTH_CHANGE);
    this.emit(DIMENSION_CHANGE);

    return this;
  };

  /**
   * Get recorded document height.
   *
   * @since 4.6.0
   *
   * @returns {number}
   */
  get height() {
    return states.height;
  }

  /**
   * Get recorded document width.
   *
   * @since 4.6.0
   *
   * @returns {number}
   */
  get width() {
    return states.width;
  }

  /**
   * Add document dimension change event listener.
   *
   * @since 4.6.0
   *
   * @param {Function} callback
   *
   * @returns {Window}
   */
  addDimensionChangeListener = callback => {
    maybeIncreaseEmitterMaxListeners(this, DIMENSION_CHANGE);
    this.on(DIMENSION_CHANGE, callback);
    return this;
  };

  /**
   * Remove document dimension change event listener.
   *
   * @since 4.6.0
   *
   * @param {Function} callback
   *
   * @returns {Window}
   */
  removeDimensionChangeListener = callback => {
    this.removeListener(DIMENSION_CHANGE, callback);
    maybeDecreaseEmitterMaxListeners(this, DIMENSION_CHANGE);
    return this;
  };

  /**
   * Add document height change event listener.
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
   * Remove document height change event listener.
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
   * Add document width change event listener.
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
   * Remove document width change event listener.
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
}

// Create document store instance
const documentStoreInstance = new ETScriptDocumentStore();

/**
 * Event's function callback to update document store's props
 *
 * @since 4.6.2
 */
function updateDocumentStoreProps() {
  const documentHeight = get(document, 'documentElement.offsetHeight');
  const documentWidth  = get(document, 'documentElement.offsetWidth');

  // Store automatically ignore if given height value is equal to the current one; so this is fine
  documentStoreInstance.setHeight(documentHeight).setWidth(documentWidth);
}

// Listen to document's DOM change, debounce its callback, and update store's props
const documentObserver = new MutationObserver(debounce(updateDocumentStoreProps, 50));

// Observe document change
// @todo probably plug this on only when necessary
// @todo also enable to plug this off
documentObserver.observe(document, {
  attributes: true,
  childList: true,
  subtree: true,
});

// Update document store properties when Divi's fixed header transition is completed
window.addEventListener('ETDiviFixedHeaderTransitionEnd', updateDocumentStoreProps);

// Register store instance as component to be exposed via global object
registerFrontendComponent('stores', 'document', documentStoreInstance);

// Export store instance.
// IMPORTANT: For uniformity, import this as ETScriptDocumentStore
export default documentStoreInstance;
