// External dependencies.
import React from 'react';

// Internal dependencies.
import ETBuilderControlSelectMenu from '@common-ui/controls/select-menu/select-menu';


export default {
  title: 'Controls/Select Menu',
  component: ETBuilderControlSelectMenu,
  argTypes: {
    onSelect: {
      action: 'selected',
      table: {
        disable: true,
      },
    },
  },
  args: {
    target: document.body,
  },
};

export const Default = {
  args: {
    options: {
      option1: 'Option 1',
      option2: 'Option 2',
      option3: 'Option 3',
    },
    button: 'Select an option',
  },
};

export const WithSearchField = {
  args: {
    ...Default.args,
    menuStyle: {
      minWidth: '260px',
    },
    showSearchField: true,
  },
};

export const WithSubmenu = {
  args: {
    options: {
      option1: {
        name: 'Option 1',
        items: {
          subOption1: 'Sub Option 1',
          subOption2: 'Sub Option 2',
        },
      },
      option2: 'Option 2',
      option3: 'Option 3',
    },
    button: 'Select an option',
  },
};

export const ScrollableParentMenu = {
  args: {
    options: {
      option1: {
        name: 'Option 1',
        items: {
          subOption1: 'Sub Option 1',
          subOption2: 'Sub Option 2',
        },
      },
      option2: 'Option 2',
      option3: 'Option 3',
    },
    button: 'Select an option',
    scrollableParentMenu: true,
  },
};

const printMenuButton = () => {
  return (
    <div className='et-fb-settings-custom-select-wrapper-outer et-fb-settings-context-select-wrapper-outer'>
      <div
        id='et-fb-context'
        className='et-fb-settings-custom-select-wrapper et-fb-settings-option-select-closed'
      >
        <ul
          className='et-fb-settings-option-select et-fb-settings-option-select-advanced et-fb-main-setting'
          style={{ maxHeight: 'none' }}
        >
          <li
            className='select-option-item et-fb-selected-item select-option-item-creative'
            data-value='creative'
          >
            <span className='select-option-item__name'>Select Menu</span>
            <span className='et-fb-select-marker'>
              <div
                className='et-common-icon et-common-icon--menu-expand'
                style={{
                  fill: 'rgb(190, 201, 213)',
                  width: '28px',
                  minWidth: '28px',
                  height: '28px',
                  margin: '-6px',
                }}
              >
                <svg
                  viewBox='0 0 28 28'
                  preserveAspectRatio='xMidYMid meet'
                  shapeRendering='geometricPrecision'
                >
                  <g fillRule='evenodd'>
                    <path d='M14 20l-3-5h6zM14 8l3 5h-6z' fillRule='evenodd'></path>
                  </g>
                </svg>
              </div>
            </span>
          </li>
        </ul>
      </div>
    </div>
  );
};

export const WithOriginalButton = {
  args: {
    ...Default.args,
    useOriginalButton: true,
    button: printMenuButton(),
  },
};
