// External dependencies.
import React from 'react';

// Internal dependencies.
import CommonIcon from '@common-ui/common-icon/common-icon';
import Button from '@common-ui/controls/button/button';
import Header from '@common-ui/branded-modal/header';
import Modal from '@common-ui/branded-modal/modal';

export default {
  title: 'Branded Modal/Full Modal',
  component: Modal,
};

export const FullModal = (args) => (
  <Modal {...args}>
    <Header
      title='Default Header'
      showCloseButton={true}
      additionalButton={() => (
        <Button
          className='et-common-library__portability-button'
          onClick={() => {}}
        >
          <CommonIcon size='14' icon='portability' color='#fff' />
        </Button>
      )}
    />
    <div style={{ width: '450px', height: '350px', padding: '20px' }}>
      <h1>Modal Content</h1>
      <p>This is the default modal content. It can be replaced with any custom content.</p>
    </div>
  </Modal>
);

FullModal.args = {
  animation: true,
};
