// External dependencies.
import $ from 'jquery';

// Internal dependencies.
import config from './config';


export const request = (method, data, options = {}) => {
  const deferred = $.ajax({
    type: method,
    url: config.api,
    dataType: 'json',
    data,
    ...options,
  });

  return Promise.resolve(deferred.promise())
    .then(response => {
      if (false === response.success) {
        return Promise.reject(response.data || {});
      }
      return Promise.resolve(response.data);
    });
};

export const post = (data, options = {}) => request('POST', data, options);
