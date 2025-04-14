// External dependencies.
import React, { useState } from 'react';

// Internal dependencies.
import CommonCategories from '@common-ui/controls/categories/categories';


export default {
  title: 'Controls/Categories',
  component: CommonCategories,
  argTypes: { onCategoriesChange: { action: 'changed' } },
};

const Template = args => {
  const [selectedCategories, setSelectedCategories] = useState(args.selectedCategories);

  const onCategoriesChange = (value, updateType) => {
    args.onCategoriesChange(value, updateType);

    if (updateType === 'add') {
      setSelectedCategories([...selectedCategories, value]);
    } else {
      setSelectedCategories(selectedCategories.filter(category => category !== value));
    }
  };

  return <CommonCategories {...args} selectedCategories={selectedCategories} onCategoriesChange={onCategoriesChange} />;
};

export const Default = args => <Template {...args} />;
Default.args = {
  selectedCategories: [],
  allCategories: {1: 'Category 1', 2: 'Category 2', 3: 'Category 3'},
  disabled: false,
  markedCategories: [],
  categoryMark: '',
};

export const WithSelectedCategories = args => <Template {...args} />;
WithSelectedCategories.args = {
  ...Default.args,
  selectedCategories: [1, 3],
};

export const WithMarkedCategories = args => <Template {...args} />;
WithMarkedCategories.args = {
  ...Default.args,
  markedCategories: ['Category 1'],
  categoryMark: '*',
};

export const Disabled = args => <Template {...args} />;
Disabled.args = {
  ...Default.args,
  disabled: true,
};
