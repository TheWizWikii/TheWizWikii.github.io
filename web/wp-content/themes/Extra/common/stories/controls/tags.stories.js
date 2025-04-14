// External dependencies.
import React from 'react';
import { action } from '@storybook/addon-actions';
import { useArgs } from '@storybook/preview-api';

// Internal dependencies.
import CommonTags from '../../controls/tags/tags';


// Initial data for tags
const initialAllTags = {
  1: 'React',
  2: 'JavaScript',
  3: 'CSS',
  4: 'HTML',
  5: 'PHP',
  6: 'WordPress',
};

const initialSelectedTags = [1, 2];

export default {
  title: 'Controls/CommonTags',
  component: CommonTags,
  render : (args) => {
    const [{ selectedTags, allTags }, updateArgs] = useArgs();
    const handleTagsChange = (tag, updateType, name) => {
      if ('add' === updateType) {
        const tagId = tag.id;
        if (tagId) {
          updateArgs({ selectedTags: [...selectedTags, tagId] });
        }
        if (!Object.values(allTags).includes(tag.text)) {
          allTags[tagId] = tag.text;
          updateArgs({ allTags });
        }
      } else if ('remove' === updateType) {
        selectedTags.splice(tag,1);
        updateArgs({ selectedTags: [...selectedTags] });
      }
      action('changed')(tag, updateType, name);
    };

    return (
      <CommonTags
        {...args}
        allTags={allTags}
        onTagsChange={handleTagsChange}
        selectedTags={selectedTags}
      />
    );
  },
  argTypes: {
    allTags: {
      table: {
        disable: true,
      },
    },
    selectedTags: {
      table: {
        disable: true,
      },
    },
    onTagsChange: {
      table: {
        disable: true,
      },
    },
    delimiters: {
      table: {
        disable: true,
      },
    },
  },
};

export const Default = {
  args: {
    autofocus: false,
    selectedTags: initialSelectedTags,
    allTags: initialAllTags,
  },
};

export const WithAutofocus = {
  args: {
    ...Default.args,
    autofocus: true,
  }
};

