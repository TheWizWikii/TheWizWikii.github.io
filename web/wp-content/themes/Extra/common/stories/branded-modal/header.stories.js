// External dependencies.
import React from 'react';

// Internal dependencies.
import CommonIcon from '@common-ui/common-icon/common-icon';
import Button from '@common-ui/controls/button/button';
import Header from '@common-ui/branded-modal/header';


export default {
  title: 'Branded Modal/Header',
  component: Header,
  argTypes: {
    onClose: {
      action: 'closed',
      table: {
        disable: true,
      },
    },
    additionalButton: {
      action: 'additionalButtonClicked',
      table: {
        disable: true,
      },
    },
  },
  args: {
    style: {
      width: '450px',
    },
  },
};

export const Default = {
  args: {
    title: 'Default Header',
    showCloseButton: true,
  },
};

export const WithoutCloseButton = {
  args: {
    title: 'Header without close button',
    showCloseButton: false,
  },
};

export const WithAdditionalButton = {
  args: {
    title: 'Header with additional button',
    showCloseButton: true,
    additionalButton: () => (
      <Button
        className='et-common-library__portability-button'
        onClick={() => {}}
      >
        <CommonIcon size='14' icon='portability' color='#fff' />
      </Button>
    ),
  },
};

export const WithCustomClassName = {
  args: {
    title: 'Header with custom class name',
    showCloseButton: true,
    className: 'storybook-header-custom-class',
  },
};
