// Internal dependencies.
import Checkbox from '@common-ui/controls/input/checkbox';


export default {
  title: 'Controls/Input/Checkbox',
  component: Checkbox,
  argTypes: {
    onChange: {
      action: 'changed',
      table: {
        disable: true,
      },
    },
  },
  parameters: {
    backgrounds: {
      default: 'dark',
    },
  },
};

export const Default = {
  args: {
    checked: false,
    className: 'storybook-checkbox-unckecked',
  },
};

export const Checked = {
  args: {
    checked: true,
    className: 'storybook-checkbox-checked',
  },
};

export const Disabled = {
  args: {
    checked: false,
    disabled: true,
    className: 'storybook-checkbox-unchecked-disabled',
  },
};

export const Danger = {
  args: {
    checked: true,
    positive: false,
    className: 'storybook-checkbox-checked-danger',
  },
};

export const Children = {
  args: {
    checked: false,
    children: 'Label',
    className: 'storybook-checkbox-unchecked-children',
  },
};
