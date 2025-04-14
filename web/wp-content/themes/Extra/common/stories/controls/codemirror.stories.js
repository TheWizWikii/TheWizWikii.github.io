// External dependencies.
import React from 'react';

// Internal dependencies.
import ETBuilderControlCodeMirror from '../../controls/codemirror/codemirror.jsx';
import '../../node_modules/codemirror/lib/codemirror.css';
import '../../node_modules/codemirror/addon/hint/show-hint.css';
import '../../node_modules/codemirror/addon/search/matchesonscrollbar.css';
import '../../node_modules/codemirror/addon/dialog/dialog.css';
import '../../node_modules/codemirror/addon/display/fullscreen.css';
import '../../node_modules/codemirror-colorpicker/addon/colorpicker/colorpicker.css';
import '../../node_modules/codemirror-colorpicker/dist/codemirror-colorpicker.css';
import 'codemirror-colorpicker';


export default {
  title: 'Controls/Codemirror',
  component: ETBuilderControlCodeMirror,
  render: (args) => {
    return (
      <div style={{ width:'80vw' }}>
        <ETBuilderControlCodeMirror
          {...args}
        />
      </div>
    );
  },
  argTypes: {
    _onChange: { action: 'changed', table: { disable: true } },
    search: { table: { disable: true } },
    value: { table: { disable: true } },
    mode: {
      options: ['html', 'css'],
      control: { type: 'select' },
    },
  },
};

export const Default = {
  args: {
    className: 'code-snippet',
    inline: true,
    lint: true,
    search: '',
    value: '',
    name: 'defaultCodeMirror',
    mode: 'css',
  },
};

export const HTMLMode = {
  args: {
    className: 'code-snippet',
    inline: true,
    lint: true,
    search: '',
    value: '',
    name: 'htmlCodeMirror',
    mode: 'html',
  },
};
