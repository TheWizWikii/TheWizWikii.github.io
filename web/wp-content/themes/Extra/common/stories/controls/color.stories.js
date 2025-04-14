// External dependencies.
import React from 'react';

// Internal dependencies.
import { useArgs } from '@storybook/preview-api';
import ETBuilderControlColor from '../../controls/color/color';
import { action } from '@storybook/addon-actions';

export default {
  title: 'Controls/ColorPicker',
  component: ETBuilderControlColor,
  render: (args) => {
    const [, updateArgs] = useArgs();

    const handleChange = (name, color) => {
      updateArgs({ value: color });
      action('color changed')(name, color);
    };

    return (
      <div style={{ width: '30vw' }}>
        <ETBuilderControlColor
          {...args}
          _onChange={handleChange}
        />
      </div>
    );
  },
  argTypes: {
    _onChange: {
      table: {
        disable: true,
      },
    },
    value: {
      control: 'color',
    }
  },
};


export const Default = {
  args: {
    animate: true,
    hideHarmoniusColors: false,
    name: 'Default color',
  },
};

export const WithPreview = {
  ...Default.args,
  title: 'With Preview',
  args: {
    ...Default.args,
    hasPreview: true,
    name: 'With Preview',
  },
};

export const ReadonlyColorPicker = {
  ...Default.args,
  title: 'Readonly Color Picker',
  args: {
    ...Default.args,
    readonly: true,
    name: 'Readonly Color Picker',
  },
};

export const WithAlphaChannel = {
  ...Default.args,
  title: 'With Alpha Channel',
  args: {
    ...Default.args,
    isAlpha: true,
    name: 'With Alpha Channel',
  },
};

export const CustomPalette = {
  ...Default.args,
  title: 'Custom Palette',
  args: {
    ...Default.args,
    showPickerPalettes: true,
    hideHarmoniusColors: false,
    name: 'Custom Palette',
    value: '#ff69b4',
  },
};
