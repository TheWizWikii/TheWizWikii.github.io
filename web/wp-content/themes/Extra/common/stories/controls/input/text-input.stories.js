import TextInput from '../../../controls/input/text-input';


export default {
  title: 'Controls/Input/TextInput',
  component: TextInput,
  argTypes: {
    onChange: {
      action: 'changed',
      table: {
        disable: true,
      },
    },
    type: {
      options: ['text', 'password', 'email', 'number', 'date', 'month', 'url', 'week', 'time', 'search', 'datetime-local'],
      control: 'select',
      description: 'Type of the input.',
    },
  },
};


export const Default = {
  args: {
    className: 'et-common-input--text',
    type: 'text',
  },
};

export const EmailInput = {
  args: {
    ...Default.args,
    type: 'email',
    className: 'et-common-input--email',
  },
};

export const PasswordInput = {
  args: {
    ...Default.args,
    type: 'password',
    className: 'et-common-input--password',
  },
};


export const NumberInput = {
  args: {
    ...Default.args,
    type: 'number',
    className: 'et-common-input--number',
  },
};
