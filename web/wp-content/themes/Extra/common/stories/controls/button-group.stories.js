// External dependencies.
import React from 'react';

// Internal dependencies.
import Button from '@common-ui/controls/button/button';
import ETCommonButtonGroup from '@common-ui/controls/button-group/button-group';


const Buttons = (
  <>
    <Button className='et-common-button et-common-button--primary'>Button 1</Button>
    <Button className='et-common-button et-common-button--danger'>Button 2</Button>
    <Button className='et-common-button et-common-button--success'>Button 3</Button>
    <Button className='et-common-button et-common-button--tertiary'>Button 4</Button>
  </>
);

export default {
  title: 'Controls/Button Group',
  component: ETCommonButtonGroup,
  argTypes: {
    onClick: {
      action: 'clicked',
      table: {
        disable: true,
      },
    },
  },
  args: {
    children: Buttons,
    style: {
      padding: '10px',
    },
  },
};

export const Horizontal = {};

export const Vertical = {
  args: {
    vertical: true,
  },
};

export const Block = {
  args: {
    block: true,
  },
};

export const Danger = {
  args: {
    danger: true,
  },
};

export const Elevate = {
  args: {
    elevate: true,
  },
};

export const Info = {
  args: {
    info: true,
  },
};

export const Inverse = {
  args: {
    inverse: true,
  },
};

export const Primary = {
  args: {
    primary: true,
  },
};

export const Success = {
  args: {
    success: true,
  },
};

export const Warning = {
  args: {
    warning: true,
  },
};

export const WithCustomStyle = {
  args: {
    style: { backgroundColor: 'lightblue' },
  },
};

export const WithOnClick = {
  args: {
    onClick: () => alert('Button group clicked!'),
  },
};
