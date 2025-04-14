// External dependencies.
import { get } from 'lodash';

// Internal dependencies.
import config from './config';


const i18n = (context, key, ...args) => {
  const value = get(context, key, '');

  if ('production' !== process.env.NODE_ENV && '' === value) {
    console.error('Failed to find i18n string:', key);
  }

  if (args.length > 0) {
    const sprintf = get(window, 'wp.i18n.sprintf');
    return sprintf(value, ...args);
  }

  return value;
};

export default (path, key, ...args) => i18n(config.i18n, [path, key], ...args);
