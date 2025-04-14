// External dependencies.
import React from 'react';
import { useArgs } from '@storybook/preview-api';

// Internal dependencies.
import Toggle from '../../controls/toggle/toggle';


export default {
  title: 'Controls/Toggle',
  component: Toggle,
  render: (args) => {
    const [{ value }, updateArgs] = useArgs();

    const handleChange = (name, newValue) => {
      updateArgs({ value: newValue });
    };

    return (
      <Toggle
        {...args}
        onChange={handleChange}
      />
    );
  },
  argTypes:{
    onChange:{
      table: {
        disable: true,
      },
    },
    options:{
      table: {
        disable: true,
      },
    },
  }  
};

export const Default = {
  args: {
    value: 'off',
    options: { on: 'on', off: 'off', type: 'default' },
  },
};

export const ToggledOn = {
  args: {
    ...Default.args,
    value: 'on',
  },
};
