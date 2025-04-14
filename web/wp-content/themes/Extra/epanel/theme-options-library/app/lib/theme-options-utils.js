import {
  noop,
  trim,
  set,
} from 'lodash';

import config from '@common-ui/lib/config';
import { post } from '@common-ui/lib/request';
import { saveToCloudPure } from '@cloud/app/lib/api';

/* eslint-disable import/prefer-default-export */
const saveThemeOptionsToLocal = (item, content) => {
  const {
    item_name,
    selected_cats,
    new_category_name,
    new_tag_name,
    builtFor,
  } = item;

  const {
    nonces,
    post_types,
  } = config;

  return post({
    action: 'et_library_save_item',
    et_library_save_item_nonce: nonces.et_library_save_item,
    post_type: post_types.et_theme_options,
    item_name,
    selected_cats,
    new_category_name,
    new_tag_name,
    content,
    builtFor,
  });
};

// eslint-disable-next-line arrow-parens
const sanitizeCommaSeparatedTaxNames = (taxName) => {
  const categoryName = 'string' === typeof taxName && taxName ? taxName : '';

  return categoryName.split(',').map(newCategory => trim(newCategory));
};

const saveThemeOptionsToCloud = async (obj, content) => {
  const {
    builtFor,
    item_name,
    new_category_name,
    new_tag_name,
    providedBaseUrl,
    selected_cats,
    selected_tags,
  } = obj;

  const newCategories = sanitizeCommaSeparatedTaxNames(new_category_name);
  const newTags       = sanitizeCommaSeparatedTaxNames(new_tag_name);
  const termsData     = {
    tags: [...selected_tags, ...newTags],
    categories: [...selected_cats, ...newCategories],
  };

  const newCloudItem = {
    title: item_name,
    content,
    status: 'publish',
  };

  if (builtFor) {
    set(newCloudItem, 'meta._built_for', builtFor);
  }

  return saveToCloudPure('theme-options', newCloudItem, termsData, noop, 0, providedBaseUrl);
};
/* eslint-enable */

export {
  saveThemeOptionsToCloud,
  saveThemeOptionsToLocal,
};
