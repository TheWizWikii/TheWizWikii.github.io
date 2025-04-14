import Button from '../../controls/button/button';


export default {
  title: 'Controls/Button',
  component: Button,
  argTypes: { onClick: { action: 'clicked' } },
};

export const Primary = {
  args: {
    children: 'Primary Button',
    ripple: true,
    className: 'et-common-button--primary',
    tip: '',
  },
};

export const Secondary = {
  args: {
    ...Primary.args,
    children: 'Secondary Button',
    className: 'et-common-button--secondary',
  },
};

export const Tertiary = {
  args: {
    ...Primary.args,
    children: 'Tertiary Button',
    className: 'et-common-button--tertiary',
  },
};

// Compact Button
export const Compact = {
  args: {
    ...Primary.args,
    children: 'Compact Button',
    className: 'et-common-button--primary et-common-button--compact',
  },
};

// Meta Button

export const Meta = {
  args: {
    ...Primary.args,
    children: 'Meta Button',
    className: 'et-common-button--meta',
  },
};

// Button with Tip
export const WithTip = {
  args: {
    ...Primary.args,
    children: 'Button With Tip',
    tip: 'Tooltip Text',
  },
};

// Button without Ripple
export const NoRipple = {
  args: {
    ...Primary.args,
    children: 'No Ripple',
    ripple: false,
  },
};
