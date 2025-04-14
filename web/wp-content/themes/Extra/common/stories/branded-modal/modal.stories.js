// External dependencies.
import React from 'react';

// Internal dependencies.
import Modal from '@common-ui/branded-modal/modal';


export default {
  title: 'Branded Modal/Modal',
  component: Modal,
  argTypes: {
    animation: {
      control: 'boolean',
    },
  },
  args: {
    children: (
      <div style={{ width: '450px', height: '350px', padding: '20px' }}>
        <h1>Modal Content</h1>
        <p>This is the default modal content. It can be replaced with any custom content.</p>
      </div>
    ),
  },
};

export const NoAnimation = {
  args: {
    animation: false,
  },
};

export const WithAnimation = {
  args: {
    animation: true,
  },
};
