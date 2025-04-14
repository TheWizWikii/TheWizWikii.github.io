import Textarea from '../../../controls/input/textarea';


export default {
  title: 'Controls/Input/Textarea',
  component: Textarea, // The actual component
  argTypes: {
    onChange: {
      action: 'changed',
      table: {
        disable: true,
      },
    },
  },
};

export const WithPlaceholder = {
  args: {
    placeholder: 'Enter text here...',
    className: 'textarea',
  },
};
