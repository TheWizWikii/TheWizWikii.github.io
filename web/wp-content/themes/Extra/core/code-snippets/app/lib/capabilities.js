// External dependencies.
import {
  every,
  get,
  isArray,
  isEmpty,
} from 'lodash';

// Internal dependencies.
import config from './config';


const isAllowedActionPure = (capabilities, action, restrictByDefault = false) => {
  if (isEmpty(action)) {
    return true;
  }

  const defaultValue = restrictByDefault ? 'off' : 'on';

  if (isArray(action)) {
    return every(action, action => 'on' === get(capabilities, action, defaultValue));
  }

  return 'on' === get(capabilities, action, defaultValue);
};

export const isAllowedAction = (...args) => isAllowedActionPure(config.capabilities, ...args);
