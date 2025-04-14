import React from 'react';
import ETCoreRange from '../../controls/range/range';


export default {
  title: 'Controls/ETCoreRange',
  component: ETCoreRange,
  render: (args) => {
    const { range_min: min, range_max: max, range_step: step } = args;
    return (
      <ETCoreRange
        {...args}
        value={min}
        _onChange={(e) => {
          console.log(e);
        }}
        range_settings={{
          min,
          max,
          step,
        }}
      />
    );
  },
  argTypes: {
    range_settings: {
      control: false,
    },
    range_min: {
      control: {
        type: 'number',
      },
    },
    range_max: {
      control: {
        type: 'number',
      },
    },
    range_step: {
      control: {
        type: 'number',
      },
    },
  },
};


export const Default = {
  args: {
    name: 'default-range',
    range_min: 0,
    range_max: 100,
    range_step: 1,
  },
};

export const WithCustomUnit = {
  args: {
    ...Default.args,
    name: 'custom-unit-range',
    default_unit: '%',
  },
};

export const WithCustomRange = {
  args: {
    ...Default.args,
    name: 'custom-range',
  },
};

export const WithPrecision = {
  args: {
    ...Default.args,
    name: 'precision-range',
    precision: 2,
  },
};
