// External dependencies.
import React from 'react';
import { useArgs } from '@storybook/preview-api';
import { action } from '@storybook/addon-actions';

// Internal dependencies.
import ETBuilderControlSelectAdvanced from '../../controls/select-advanced/select-advanced';

// Define the default metadata for the story
export default {
  title: 'Controls/Select Advanced',
  component: ETBuilderControlSelectAdvanced,
  render: args => {
    const [, updateArgs] = useArgs();

    const handleOnChange = (attrName, selectedValue) => {
      action('changed')(attrName, selectedValue);
      updateArgs({ value: selectedValue });
    }

    return (
      <div style={{ width: '300px' }}>
        <ETBuilderControlSelectAdvanced
          {...args}
          _onChange={handleOnChange}
        />
      </div>
    );
  },
  argTypes: {
    options: {
      table: {
        disable: true,
      }
    },
    onOpen: {
      table: {
        disable: true,
      }
    },
    onClose: {
      table: {
        disable: true,
      }
    },
    labelFilter: {
      table: {
        disable: true,
      }
    },
    onChange: {
      action: 'changed',
      table: {
        disable: true,
      },
    },
    beforeList: {
      table: {
        disable: true,
      }
    },
    afterList: {
      table: {
        disable: true,
      }
    },
    getSvgContent: {
      table: {
        disable: true,
      }
    },
    value: { control: 'text' },
  },
};

export const Default = {
  args: {
    label: 'Select Advanced',
    options: ['Apple', 'Banana', 'Orange'],
    name: 'default',
    value: '',
  },
};

export const OptionsObject = {
  args: {
    ...Default.args,
    label: 'Object Options',
    options:  {
      Apple: 'Apple',
      Banana: 'Banana',
      Orange: 'Orange',
    },
  },
};

export const OptionsWithSubOptions = {
  args: {
    ...Default.args,
    label: 'Sub Options',
    options:  {
      Vegetable: {
        Tomato: 'Tomato',
        Carrot: 'Carrot',
      },
      Fruits: {
        Apple: 'Apple',
        Banana: 'Banana',
      },
    },
  },
};

export const FirstNotSelected = {
  args: {
    ...Default.args,
    selectFirst: false,
    emptyLabel: 'Please select an option...',
    value: undefined
  }
}


export const Searchable = {
  args: {
    ...Default.args,
    searchable: true,
  }
}

export const ActiveOnLoad = {
  args: {
    ...Default.args,
    activeOnLoad: true,
  },
  parameters: {
    layout: 'fullscreen',
  },
}

export const BeforeAndAfterList = {
  args: {
    ...Default.args,
    beforeList: () => 'Before Fruits List',
    afterList: () => 'After Fruits List',
  }
}

export const AdditionalContentFirst = {
  args: {
    ...Default.args,
    additionalContentFirst: true,
    getSvgContent: () => {
      return (
        <svg height="18.516px" id="Capa_1" version="1.1" viewBox="0 0 80 88.516" width="30px" xml:space="preserve" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink"><g><path d="M63.254,36.822C51.852,12.287,45.926,2.97,27.295,3.355c-6.635,0.137-5.041-4.805-10.1-2.93   c-5.055,1.876-0.717,4.62-5.889,8.863C-3.207,21.2-1.869,32.221,4.861,58.487c2.838,11.062-6.836,11.605-3.008,22.33   c2.793,7.819,23.393,11.093,45.127,3.028c21.734-8.063,35.453-24.07,32.66-31.889C75.811,41.231,68.059,47.152,63.254,36.822z    M44.621,77.238c-19.41,7.202-35.363,2.965-36.037,1.083C7.422,75.073,14.85,64.24,37.041,56.005   c22.193-8.234,34.576-5.181,35.871-1.553C73.678,56.594,64.033,70.036,44.621,77.238z M38.383,59.764   c-10.148,3.766-17.201,8.073-21.764,11.951c3.211,2.918,9.23,3.63,15.23,1.404c7.637-2.833,12.326-9.337,10.471-14.526   c-0.021-0.063-0.055-0.119-0.078-0.181C40.99,58.826,39.705,59.274,38.383,59.764z"/></g><g/><g/><g/><g/><g/><g/><g/><g/><g/><g/><g/><g/><g/><g/><g/></svg>
      );
    }
  }
}

