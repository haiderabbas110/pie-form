/**
 * Pie Forms Form Block
 *
 * A block for embedding a Pie Forms into a post/page.
 */
'use strict';
/* global pf_form_block_data, wp */

const {
  __
} = wp.i18n;
const {
  createElement
} = wp.element;
const {
  registerBlockType
} = wp.blocks;
const {
  InspectorControls
} = wp.blockEditor;
const {
  serverSideRender: ServerSideRender
} = wp;
const {
  PanelBody,
  SelectControl,
  ToggleControl,
  Placeholder
} = wp.components;
const PieFormIcon = createElement('svg', {
  width: 56,
  height: 56,
  viewBox: '0 0 30 46',
}, createElement('path', {
  fill: 'currentColor',
  d: 'M30,0H6C2.7,0,0,2.7,0,6V30c0,3.3,2.7,6,6,6H30c3.3,0,6-2.7,6-6V6C36,2.7,33.3,0,30,0z M17.2,27.3h-5.1v-3.4h5.1V27.3z M24.1,19.7H12.1v-3.4h11.9V19.7z M26.6,12H12.1V8.6h14.5V12z'
}));
registerBlockType('pie-forms-for-wp/form-selector', {
  title: pf_form_block_data.i18n.title,
  icon: PieFormIcon,
  category: 'widgets',
  keywords: pf_form_block_data.i18n.form_keywords,
  description: pf_form_block_data.i18n.description,
  attributes: {
    formId: {
      type: 'string'
    },
    displayTitle: {
      type: 'boolean'
    },
    displayDescription: {
      type: 'boolean'
    }
  },

  edit(props) {
    const {
      attributes: {
        formId = '',
        displayTitle = false,
        displayDescription = false
      },
      setAttributes
    } = props;
    const formOptions = pf_form_block_data.forms.map(value => ({
      value: value.id,
      label: value.form_title
	}));
    let jsx;
    formOptions.unshift({
      value: '',
      label: pf_form_block_data.i18n.form_select
    });

    function selectForm(value) {
      setAttributes({
        formId: value
      });
    }

    function toggleDisplayTitle(value) {
      setAttributes({
        displayTitle: value
      });
    }

    function toggleDisplayDescription(value) {
      setAttributes({
        displayDescription: value
      });
    }

    jsx = [/*#__PURE__*/React.createElement(InspectorControls, {
      key: "pf-gutenberg-form-selector-inspector-controls"
    }, /*#__PURE__*/React.createElement(PanelBody, {
      title: pf_form_block_data.i18n.form_settings
    }, /*#__PURE__*/React.createElement(SelectControl, {
      label: pf_form_block_data.i18n.form_selected,
      value: formId,
      options: formOptions,
      onChange: selectForm
    }), /*#__PURE__*/React.createElement(ToggleControl, {
      label: pf_form_block_data.i18n.show_title,
      checked: displayTitle,
      onChange: toggleDisplayTitle
    }), /*#__PURE__*/React.createElement(ToggleControl, {
      label: pf_form_block_data.i18n.show_description,
      checked: displayDescription,
      onChange: toggleDisplayDescription
    })))];

    if (formId) {
      jsx.push( /*#__PURE__*/React.createElement(ServerSideRender, {
        key: "pf-gutenberg-form-selector-server-side-renderer",
        block: "pie-forms-for-wp/form-selector",
        attributes: props.attributes
      }));
    } else {
      jsx.push( /*#__PURE__*/React.createElement(Placeholder, {
        key: "pf-gutenberg-form-selector-wrap",
        icon: PieFormIcon,
        instructions: pf_form_block_data.i18n.title,
        className: "pie-form-gutenberg-form-selector-wrap"
      }, /*#__PURE__*/React.createElement(SelectControl, {
        key: "pf-gutenberg-form-selector-select-control",
        value: formId,
        options: formOptions,
        onChange: selectForm
      })));
    }

    return jsx;
  },

  save() {
    return null;
  }

});