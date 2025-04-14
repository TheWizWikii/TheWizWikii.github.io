(()=>{var e={57990:(e,t,r)=>{"use strict";Object.defineProperty(t,"__esModule",{value:!0}),t.default=void 0;var o=c(r(85890)),n=c(r(99196)),a=c(r(98487)),l=r(65736),s=r(23695),i=r(37188),u=c(r(46362)),d=c(r(78386));function c(e){return e&&e.__esModule?e:{default:e}}const f=a.default.div`
	display: flex;
	align-items: flex-start;
	font-size: 13px;
	line-height: 1.5;
	border: 1px solid rgba(0, 0, 0, 0.2);
	padding: 16px;
	color: ${e=>e.alertColor};
	background: ${e=>e.alertBackground};
	margin-bottom: 20px;
`,p=a.default.div`
	flex-grow: 1;

	a {
		color: ${i.colors.$color_alert_link_text};
	}

	p {
		margin-top: 0;
	}
`,h=(0,a.default)(d.default)`
	margin-top: 0.1rem;
	${(0,s.getDirectionalStyle)("margin-right: 8px","margin-left: 8px")};
`,b=(0,a.default)(u.default)`
	${(0,s.getDirectionalStyle)("margin: -8px -12px -8px 8px","margin: -8px 8px -12px -8px")};
	font-size: 24px;
	line-height: 1.4;
	color: ${e=>e.alertDismissColor};
	flex-shrink: 0;
	min-width: 36px;
	height: 36px;

	// Override the base button style: get rid of the button styling.
	padding: 0;

	&, &:hover, &:active {
		/* Inherits box-sizing: border-box so this doesn't change the rendered size. */
		border: 2px solid transparent;
		background: transparent;
		box-shadow: none;
		color: ${e=>e.alertDismissColor};
	}

	/* Inherits focus style from the Button component. */
	&:focus {
		background: transparent;
		color: ${e=>e.alertDismissColor};
		border-color: ${i.colors.$color_yoast_focus};
		box-shadow: 0px 0px 0px 3px ${i.colors.$color_yoast_focus_outer};
	}
`;class g extends n.default.Component{getTypeDisplayOptions(e){switch(e){case"error":return{color:i.colors.$color_alert_error_text,background:i.colors.$color_alert_error_background,icon:"alert-error"};case"info":return{color:i.colors.$color_alert_info_text,background:i.colors.$color_alert_info_background,icon:"alert-info"};case"success":return{color:i.colors.$color_alert_success_text,background:i.colors.$color_alert_success_background,icon:"alert-success"};case"warning":return{color:i.colors.$color_alert_warning_text,background:i.colors.$color_alert_warning_background,icon:"alert-warning"}}}render(){if(!0===this.props.isAlertDismissed)return null;const e=this.getTypeDisplayOptions(this.props.type),t=this.props.dismissAriaLabel||(0,l.__)("Dismiss this alert","wordpress-seo");
/* translators: Hidden accessibility text. */return n.default.createElement(f,{alertColor:e.color,alertBackground:e.background,className:this.props.className},n.default.createElement(h,{icon:e.icon,color:e.color}),n.default.createElement(p,null,this.props.children),"function"==typeof this.props.onDismissed?n.default.createElement(b,{alertDismissColor:e.color,onClick:this.props.onDismissed,"aria-label":t},"×"):null)}}g.propTypes={children:o.default.any.isRequired,type:o.default.oneOf(["error","info","success","warning"]).isRequired,onDismissed:o.default.func,isAlertDismissed:o.default.bool,dismissAriaLabel:o.default.string,className:o.default.string},g.defaultProps={onDismissed:null,isAlertDismissed:!1,dismissAriaLabel:"",className:""},t.default=g},47529:(e,t,r)=>{"use strict";Object.defineProperty(t,"__esModule",{value:!0}),t.default=void 0;var o=s(r(99196)),n=s(r(85890)),a=s(r(98487)),l=r(23695);function s(e){return e&&e.__esModule?e:{default:e}}const i=a.default.div`
	box-sizing: border-box;

	p {
		margin: 0;
		font-size: 14px;
	}
`,u=a.default.h3`
	margin: 8px 0;
	font-size: 1em;
`,d=a.default.ul`
	margin: 0;
	list-style: none;
	padding: 0;
`,c=(0,l.makeOutboundLink)(a.default.a`
	display: inline-block;
	margin-bottom: 4px;
	font-size: 14px;
`),f=a.default.li`
	margin: 8px 0;
`,p=a.default.div`
	a {
		margin: 8px 0 0;
	}
`,h=e=>o.default.createElement(f,{className:e.className},o.default.createElement(c,{className:`${e.className}-link`,href:e.link},e.title),o.default.createElement("p",{className:`${e.className}-description`},e.description));h.propTypes={className:n.default.string.isRequired,title:n.default.string.isRequired,link:n.default.string.isRequired,description:n.default.string.isRequired};const b=e=>o.default.createElement(i,{className:e.className},o.default.createElement(u,{className:`${e.className}__header`},e.title?e.title:e.feed.title),o.default.createElement(d,{className:`${e.className}__posts`,role:"list"},e.feed.items.map((t=>o.default.createElement(h,{className:`${e.className}__post`,key:t.link,title:t.title,link:t.link,description:t.description})))),e.footerLinkText&&o.default.createElement(p,{className:`${e.className}__footer`},o.default.createElement(c,{className:`${e.className}__footer-link`,href:e.feedLink?e.feedLink:e.feed.link},e.footerLinkText)));b.propTypes={className:n.default.string,feed:n.default.object.isRequired,title:n.default.string,footerLinkText:n.default.string,feedLink:n.default.string},b.defaultProps={className:"articlelist-feed"},t.default=b},79743:(e,t,r)=>{"use strict";Object.defineProperty(t,"__esModule",{value:!0}),t.default=t.FullHeightCard=void 0;var o=function(e,t){if(e&&e.__esModule)return e;if(null===e||"object"!=typeof e&&"function"!=typeof e)return{default:e};var r=d(t);if(r&&r.has(e))return r.get(e);var o={__proto__:null},n=Object.defineProperty&&Object.getOwnPropertyDescriptor;for(var a in e)if("default"!==a&&Object.prototype.hasOwnProperty.call(e,a)){var l=n?Object.getOwnPropertyDescriptor(e,a):null;l&&(l.get||l.set)?Object.defineProperty(o,a,l):o[a]=e[a]}return o.default=e,r&&r.set(e,o),o}(r(99196)),n=u(r(85890)),a=u(r(98487)),l=r(37188),s=r(23695),i=u(r(97230));function u(e){return e&&e.__esModule?e:{default:e}}function d(e){if("function"!=typeof WeakMap)return null;var t=new WeakMap,r=new WeakMap;return(d=function(e){return e?r:t})(e)}const c=a.default.div`
	position: relative;
	display: flex;
	flex-direction: column;
	background-color: ${l.colors.$color_white};
	width: 100%;
	box-shadow: 0 2px 4px 0 rgba(0,0,0,0.2);
`,f=a.default.img`
	width: 100%;
	vertical-align: bottom;
`,p=a.default.div`
	padding: 12px 16px;
	display: flex;
	flex-direction: column;
	flex-grow: 1;
`,h=a.default.a`
	text-decoration: none;
	color: ${l.colors.$color_pink_dark};
	/* IE11 bug header image height see https://github.com/philipwalton/flexbugs#flexbug-5 */
	overflow: hidden;

	&:hover,
	&:focus,
	&:active {
		text-decoration: underline;
		color: ${l.colors.$color_pink_dark};
	}

	&:focus,
	&:active {
		box-shadow: none;
	}
`,b=a.default.h2`
	margin: 16px 16px 0 16px;
	font-weight: 400;
	font-size: 1.5em;
	line-height: 1.2;
	color: currentColor;
`,g=(0,s.makeOutboundLink)(h);class m extends o.default.Component{getHeader(){return this.props.header?this.props.header.link?o.default.createElement(g,{href:this.props.header.link},o.default.createElement(f,{src:this.props.header.image,alt:""}),o.default.createElement(b,null,this.props.header.title)):o.default.createElement(o.Fragment,null,o.default.createElement(f,{src:this.props.header.image,alt:""}),";",o.default.createElement(b,null,this.props.header.title)):null}getBanner(){return this.props.banner?o.default.createElement(i.default,this.props.banner,this.props.banner.text):null}render(){return o.default.createElement(c,{className:this.props.className,id:this.props.id},this.getHeader(),this.getBanner(),o.default.createElement(p,null,this.props.children))}}t.default=m,t.FullHeightCard=(0,a.default)(m)`
	height: 100%;
`,m.propTypes={className:n.default.string,id:n.default.string,header:n.default.shape({title:n.default.string,image:n.default.string.isRequired,link:n.default.string}),banner:n.default.shape({text:n.default.string.isRequired,textColor:n.default.string,backgroundColor:n.default.string}),children:n.default.any},m.defaultProps={className:"",id:"",header:null,banner:null,children:null}},97230:(e,t,r)=>{"use strict";Object.defineProperty(t,"__esModule",{value:!0}),t.default=c;var o=function(e,t){if(e&&e.__esModule)return e;if(null===e||"object"!=typeof e&&"function"!=typeof e)return{default:e};var r=i(t);if(r&&r.has(e))return r.get(e);var o={__proto__:null},n=Object.defineProperty&&Object.getOwnPropertyDescriptor;for(var a in e)if("default"!==a&&Object.prototype.hasOwnProperty.call(e,a)){var l=n?Object.getOwnPropertyDescriptor(e,a):null;l&&(l.get||l.set)?Object.defineProperty(o,a,l):o[a]=e[a]}return o.default=e,r&&r.set(e,o),o}(r(99196)),n=s(r(85890)),a=s(r(98487)),l=r(37188);function s(e){return e&&e.__esModule?e:{default:e}}function i(e){if("function"!=typeof WeakMap)return null;var t=new WeakMap,r=new WeakMap;return(i=function(e){return e?r:t})(e)}const u=a.default.span`
	position: absolute;
	
	top: 8px;
	left: -8px;
	
	font-weight: 500;
	color: ${e=>e.textColor};
	line-height: 16px;
	
	background-color: ${e=>e.backgroundColor};
	padding: 8px 16px;
	box-shadow: 0 2px 4px 0 rgba(0, 0, 0, 0.2);
`,d=a.default.span`
	position: absolute;
	
	top: 40px;
	left: -8px;
	
	/* This code makes the triangle. */
	border-top: 8px solid ${l.colors.$color_purple_dark};
	border-left: 8px solid transparent;
`;function c(e){return o.default.createElement(o.Fragment,null,o.default.createElement(u,{backgroundColor:e.backgroundColor,textColor:e.textColor},e.children),o.default.createElement(d,null))}c.propTypes={backgroundColor:n.default.string,textColor:n.default.string,children:n.default.any},c.defaultProps={backgroundColor:l.colors.$color_pink_dark,textColor:l.colors.$color_white,children:null}},57272:(e,t,r)=>{"use strict";Object.defineProperty(t,"__esModule",{value:!0}),t.Collapsible=void 0,t.CollapsibleStateless=v,t.default=t.StyledIconsButton=t.StyledContainerTopLevel=t.StyledContainer=void 0,t.wrapInHeading=m;var o=r(23695),n=r(37188),a=r(92819),l=c(r(85890)),s=c(r(99196)),i=c(r(98487)),u=c(r(71875)),d=r(91752);function c(e){return e&&e.__esModule?e:{default:e}}function f(){return f=Object.assign?Object.assign.bind():function(e){for(var t=1;t<arguments.length;t++){var r=arguments[t];for(var o in r)Object.prototype.hasOwnProperty.call(r,o)&&(e[o]=r[o])}return e},f.apply(this,arguments)}const p=i.default.div`
	padding: 0 16px;
	margin-bottom: 16px;
`,h=t.StyledContainer=i.default.div`
	background-color: ${n.colors.$color_white};
`,b=t.StyledContainerTopLevel=(0,i.default)(h)`
	border-top: var(--yoast-border-default);
`,g=t.StyledIconsButton=(0,i.default)(u.default)`
	width: 100%;
	background-color: ${n.colors.$color_white};
	padding: 16px;
	justify-content: flex-start;
	border-color: transparent;
	border: none;
	border-radius: 0;
	box-shadow: none;
	font-weight: normal;

	:focus {
		outline: 1px solid ${n.colors.$color_blue};
		outline-offset: -1px;
	}

	:active {
		box-shadow: none;
		background-color: ${n.colors.$color_white};
	}

	svg {
		${e=>e.hasSubTitle?"align-self: flex-start;":""}
		&:first-child {
			${(0,o.getDirectionalStyle)("margin-right: 8px","margin-left: 8px")};
		}
		&:last-child {
			${(0,o.getDirectionalStyle)("margin-left: 8px","margin-right: 8px")};
		}
	}
`;function m(e,t){const r=`h${t.level}`,o=(0,i.default)(r)`
		margin: 0 !important;
		padding: 0 !important;
		font-size: ${t.fontSize} !important;
		font-weight: ${t.fontWeight} !important;
		color: ${t.color} !important;

		${d.StyledTitle} {
			font-weight: ${t.fontWeight};
			color: ${t.color};
		}
	`;return function(t){return s.default.createElement(o,null,s.default.createElement(e,t))}}const y=m(g,{level:2,fontSize:"1rem",fontWeight:"normal"});function v(e){const{children:t,className:r,hasPadding:o,hasSeparator:n,Heading:a,id:l,isOpen:i,onToggle:u,prefixIcon:c,prefixIconCollapsed:f,suffixIcon:g,suffixIconCollapsed:m,subTitle:y,title:v,titleScreenReaderText:_}=e;let x=t;i&&o&&(x=s.default.createElement(p,{className:"collapsible_content"},t));const C=n?b:h;return s.default.createElement(C,{className:r},s.default.createElement(a,{id:l,"aria-expanded":i,onClick:u,prefixIcon:i?c:f,suffixIcon:i?g:m,hasSubTitle:!!y},s.default.createElement(d.SectionTitle,{title:v,titleScreenReaderText:_,subTitle:y})),x)}v.propTypes={children:l.default.oneOfType([l.default.arrayOf(l.default.node),l.default.node]),className:l.default.string,Heading:l.default.func,isOpen:l.default.bool.isRequired,hasSeparator:l.default.bool,hasPadding:l.default.bool,onToggle:l.default.func.isRequired,prefixIcon:l.default.shape({icon:l.default.string,color:l.default.string,size:l.default.string}),prefixIconCollapsed:l.default.shape({icon:l.default.string,color:l.default.string,size:l.default.string}),subTitle:l.default.string,suffixIcon:l.default.shape({icon:l.default.string,color:l.default.string,size:l.default.string}),suffixIconCollapsed:l.default.shape({icon:l.default.string,color:l.default.string,size:l.default.string}),title:l.default.string.isRequired,titleScreenReaderText:l.default.string,id:l.default.string},v.defaultProps={Heading:y,id:null,children:null,className:null,subTitle:null,titleScreenReaderText:null,hasSeparator:!1,hasPadding:!1,prefixIcon:null,prefixIconCollapsed:null,suffixIcon:null,suffixIconCollapsed:null};class _ extends s.default.Component{constructor(e){super(e),this.state={isOpen:e.initialIsOpen,headingProps:e.headingProps,Heading:m(g,e.headingProps)},this.toggleCollapse=this.toggleCollapse.bind(this)}static getDerivedStateFromProps(e,t){return e.headingProps.level!==t.headingProps.level||e.headingProps.fontSize!==t.headingProps.fontSize||e.headingProps.fontWeight!==t.headingProps.fontWeight||e.headingProps.color!==t.headingProps.color?{...t,headingProps:e.headingProps,Heading:m(g,e.headingProps)}:null}toggleCollapse(){const{isOpen:e}=this.state,{onToggle:t}=this.props;t&&!1===t(e)||this.setState({isOpen:!e})}render(){const{isOpen:e}=this.state,{children:t}=this.props,r=(0,a.omit)(this.props,["children","onToggle"]);return s.default.createElement(v,f({Heading:this.state.Heading,isOpen:e,onToggle:this.toggleCollapse},r),e&&t)}}t.Collapsible=_,_.propTypes={children:l.default.oneOfType([l.default.arrayOf(l.default.node),l.default.node]),className:l.default.string,initialIsOpen:l.default.bool,hasSeparator:l.default.bool,hasPadding:l.default.bool,prefixIcon:l.default.shape({icon:l.default.string,color:l.default.string,size:l.default.string}),prefixIconCollapsed:l.default.shape({icon:l.default.string,color:l.default.string,size:l.default.string}),suffixIcon:l.default.shape({icon:l.default.string,color:l.default.string,size:l.default.string}),suffixIconCollapsed:l.default.shape({icon:l.default.string,color:l.default.string,size:l.default.string}),title:l.default.string.isRequired,titleScreenReaderText:l.default.string,subTitle:l.default.string,headingProps:l.default.shape({level:l.default.number,fontSize:l.default.string,fontWeight:l.default.string,color:l.default.string}),onToggle:l.default.func},_.defaultProps={hasSeparator:!1,hasPadding:!1,initialIsOpen:!1,subTitle:null,titleScreenReaderText:null,children:null,className:null,prefixIcon:null,prefixIconCollapsed:null,suffixIcon:{icon:"chevron-up",color:n.colors.$black,size:"24px"},suffixIconCollapsed:{icon:"chevron-down",color:n.colors.$black,size:"24px"},headingProps:{level:2,fontSize:"1rem",fontWeight:"normal",color:n.colors.$color_headings},onToggle:null},t.default=_},69424:(e,t,r)=>{"use strict";Object.defineProperty(t,"__esModule",{value:!0}),t.default=void 0;var o=function(e,t){if(e&&e.__esModule)return e;if(null===e||"object"!=typeof e&&"function"!=typeof e)return{default:e};var r=u(t);if(r&&r.has(e))return r.get(e);var o={__proto__:null},n=Object.defineProperty&&Object.getOwnPropertyDescriptor;for(var a in e)if("default"!==a&&Object.prototype.hasOwnProperty.call(e,a)){var l=n?Object.getOwnPropertyDescriptor(e,a):null;l&&(l.get||l.set)?Object.defineProperty(o,a,l):o[a]=e[a]}return o.default=e,r&&r.set(e,o),o}(r(99196)),n=i(r(85890)),a=i(r(98487)),l=r(37188),s=r(23695);function i(e){return e&&e.__esModule?e:{default:e}}function u(e){if("function"!=typeof WeakMap)return null;var t=new WeakMap,r=new WeakMap;return(u=function(e){return e?r:t})(e)}const d=a.default.a`
	color: ${l.colors.$color_black};
	white-space: nowrap;
	display: block;
	border-radius: 4px;
	background-color: ${l.colors.$color_grey_cta};
	padding: 12px 16px;
	box-shadow: inset 0 -4px 0 rgba(0, 0, 0, 0.2);
	border: none;
	text-decoration: none;
	font-weight: bold;
	font-size: inherit;
	margin-bottom: 8px;

	&:hover,
	&:focus,
	&:active {
		color: ${l.colors.$color_black};
		background-color: ${l.colors.$color_grey_hover};
	}

	&:active {
		background-color: ${l.colors.$color_grey_hover};
		transform: translateY( 1px );
		box-shadow: none;
		filter: none;
	}
`,c=a.default.a`
	cursor: pointer;
	color: ${l.colors.$color_black};
	white-space: nowrap;
	display: block;
	border-radius: 4px;
	background-color: ${l.colors.$color_button_upsell};
	padding: 12px 16px;
	box-shadow: inset 0 -4px 0 rgba(0, 0, 0, 0.2);
	border: none;
	text-decoration: none;
	font-weight: bold;
	font-size: inherit;
	margin-top: 0;
	margin-bottom: 8px;

	&:hover,
	&:focus,
	&:active {
		color: ${l.colors.$color_black};
		background: ${l.colors.$color_button_upsell_hover};
	}

	&:active {
		background-color: ${l.colors.$color_button_hover_upsell};
		transform: translateY( 1px );
		box-shadow: none;
		filter: none;
	}
`,f=a.default.a`
	font-weight: bold;
`,p=(0,s.makeOutboundLink)(f),h=a.default.div`
	text-align: center;
`,b=a.default.div`
	ul {
		list-style-type: none;
		margin: 0;
		padding: 0;
	}

	li {
		position: relative;
		${(0,s.getDirectionalStyle)("margin-left","margin-right")}: 16px;

		&:before {
			content: "✓";
			color: ${l.colors.$color_green};
			position: absolute;
			font-weight: bold;
			display: inline-block;
			${(0,s.getDirectionalStyle)("left","right")}: -16px;
		}
	}
`,g=a.default.div`
	margin-bottom: 12px;
	border-bottom: 1px ${l.colors.$color_grey} solid;
	flex-grow: 1;
`;class m extends o.default.Component{getActionBlock(e,t){const r=(0,s.makeOutboundLink)(e);return"true"===t?o.default.createElement(h,null,o.default.createElement(r,{href:this.props.courseUrl},this.props.ctaButtonData.ctaButtonCopy)):o.default.createElement(h,null,o.default.createElement(r,{href:this.props.ctaButtonData.ctaButtonUrl},this.props.ctaButtonData.ctaButtonCopy),o.default.createElement(p,{href:this.props.courseUrl},this.props.readMoreLinkText))}render(){const e="regular"===this.props.ctaButtonData.ctaButtonType?d:c;return o.default.createElement(o.Fragment,null,o.default.createElement(g,null,o.default.createElement(b,{dangerouslySetInnerHTML:{__html:this.props.description}})),this.getActionBlock(e,this.props.isBundle))}}t.default=m,m.propTypes={description:n.default.string,courseUrl:n.default.string,ctaButtonData:n.default.object,readMoreLinkText:n.default.string,isBundle:n.default.string},m.defaultProps={description:"",courseUrl:"",ctaButtonData:{},readMoreLinkText:"",isBundle:""}},27938:(e,t,r)=>{"use strict";Object.defineProperty(t,"__esModule",{value:!0}),t.default=void 0;var o=u(r(99196)),n=u(r(85890)),a=u(r(98487)),l=r(65736),s=r(25158),i=r(37188);function u(e){return e&&e.__esModule?e:{default:e}}const d=a.default.p`
	text-align: center;
	margin: 0 0 16px;
	padding: 16px 16px 8px 16px;
	border-bottom: 4px solid ${i.colors.$color_bad};
	background: ${i.colors.$color_white};
`;class c extends o.default.Component{constructor(e){super(e),this.state={hasError:!1}}componentDidCatch(){this.setState({hasError:!0})}render(){if(this.state.hasError){const e=(0,l.__)("Something went wrong. Please reload the page.","wordpress-seo");return(0,s.speak)(e,"assertive"),o.default.createElement(d,null,e)}return this.props.children}}t.default=c,c.propTypes={children:n.default.any},c.defaultProps={children:null}},9802:(e,t)=>{"use strict";Object.defineProperty(t,"__esModule",{value:!0}),t.getId=t.default=void 0;const r=()=>Math.random().toString(36).substring(2,6);t.getId=e=>e||r(),t.default=r},33014:(e,t,r)=>{"use strict";Object.defineProperty(t,"__esModule",{value:!0}),t.default=void 0;var o=a(r(99196)),n=a(r(85890));function a(e){return e&&e.__esModule?e:{default:e}}const l=e=>{const t=`h${e.level}`;return o.default.createElement(t,{className:e.className},e.children)};l.propTypes={level:n.default.number,className:n.default.string,children:n.default.any},l.defaultProps={level:1},t.default=l},30812:(e,t,r)=>{"use strict";Object.defineProperty(t,"__esModule",{value:!0}),t.helpTextPropType=t.default=void 0;var o=function(e,t){if(e&&e.__esModule)return e;if(null===e||"object"!=typeof e&&"function"!=typeof e)return{default:e};var r=i(t);if(r&&r.has(e))return r.get(e);var o={__proto__:null},n=Object.defineProperty&&Object.getOwnPropertyDescriptor;for(var a in e)if("default"!==a&&Object.prototype.hasOwnProperty.call(e,a)){var l=n?Object.getOwnPropertyDescriptor(e,a):null;l&&(l.get||l.set)?Object.defineProperty(o,a,l):o[a]=e[a]}return o.default=e,r&&r.set(e,o),o}(r(99196)),n=s(r(85890)),a=s(r(98487)),l=r(37188);function s(e){return e&&e.__esModule?e:{default:e}}function i(e){if("function"!=typeof WeakMap)return null;var t=new WeakMap,r=new WeakMap;return(i=function(e){return e?r:t})(e)}const u=a.default.p`
	color: ${e=>e.textColor};
	font-size: ${e=>e.textFontSize};
	margin-top: 0;
`;class d extends o.PureComponent{render(){const{children:e,textColor:t,textFontSize:r}=this.props;return o.default.createElement(u,{textColor:t,textFontSize:r},e)}}t.default=d;const c=t.helpTextPropType={children:n.default.oneOfType([n.default.string,n.default.array]),textColor:n.default.string,textFontSize:n.default.string};d.propTypes={...c,children:c.children.isRequired},d.defaultProps={textColor:l.colors.$color_help_text}},77844:(e,t,r)=>{"use strict";Object.defineProperty(t,"__esModule",{value:!0}),t.default=s;var o=a(r(99196)),n=a(r(85890));function a(e){return e&&e.__esModule?e:{default:e}}function l(){return l=Object.assign?Object.assign.bind():function(e){for(var t=1;t<arguments.length;t++){var r=arguments[t];for(var o in r)Object.prototype.hasOwnProperty.call(r,o)&&(e[o]=r[o])}return e},l.apply(this,arguments)}function s(e){return o.default.createElement("iframe",l({title:e.title},e))}s.propTypes={title:n.default.string.isRequired}},7992:(e,t,r)=>{"use strict";Object.defineProperty(t,"__esModule",{value:!0}),t.default=void 0;var o=s(r(99196)),n=s(r(85890)),a=s(r(98487)),l=s(r(16653));function s(e){return e&&e.__esModule?e:{default:e}}function i(){return i=Object.assign?Object.assign.bind():function(e){for(var t=1;t<arguments.length;t++){var r=arguments[t];for(var o in r)Object.prototype.hasOwnProperty.call(r,o)&&(e[o]=r[o])}return e},i.apply(this,arguments)}const u=e=>{const t=(0,a.default)(e.icon)`
		width: ${e.width};
		height: ${e.height};
		${e.color?`fill: ${e.color};`:""}
		flex: 0 0 auto;
	`,r=(0,l.default)(e,["icon","width","height","color"]);return o.default.createElement(t,i({role:"img","aria-hidden":"true",focusable:"false"},r))};u.propTypes={icon:n.default.func.isRequired,width:n.default.string,height:n.default.string,color:n.default.string},u.defaultProps={width:"16px",height:"16px"},t.default=u},21529:(e,t,r)=>{"use strict";Object.defineProperty(t,"__esModule",{value:!0}),t.default=void 0;var o=i(r(99196)),n=i(r(98487)),a=i(r(85890)),l=r(37188),s=i(r(78386));function i(e){return e&&e.__esModule?e:{default:e}}const u=n.default.button`
	align-items: center;
	justify-content: center;
	box-sizing: border-box;
	min-width: 32px;
	display: inline-flex;
	border: 1px solid ${l.colors.$color_button_border};
	background-color: ${e=>e.pressed?e.pressedBackground:e.unpressedBackground};
	box-shadow: ${e=>e.pressed?`inset 0 2px 0 ${(0,l.rgba)(e.pressedBoxShadowColor,.7)}`:`0 1px 0 ${(0,l.rgba)(e.unpressedBoxShadowColor,.7)}`};
	border-radius: 3px;
	cursor: pointer;
	padding: 0;
	height: ${e=>e.pressed?"23px":"24px"};

	&:hover {
		border-color: ${e=>e.hoverBorderColor};
	}
	&:disabled {
		background-color: ${e=>e.unpressedBackground};
		box-shadow: none;
		border: none;
		cursor: default;
	}
`,d=function(e){const t="disabled"===e.marksButtonStatus;let r;return r=t?e.disabledIconColor:e.pressed?e.pressedIconColor:e.unpressedIconColor,o.default.createElement(u,{disabled:t,type:"button",onClick:e.onClick,pressed:e.pressed,unpressedBoxShadowColor:e.unpressedBoxShadowColor,pressedBoxShadowColor:e.pressedBoxShadowColor,pressedBackground:e.pressedBackground,unpressedBackground:e.unpressedBackground,id:e.id,"aria-label":e.ariaLabel,"aria-pressed":e.pressed,unpressedIconColor:t?e.disabledIconColor:e.unpressedIconColor,pressedIconColor:e.pressedIconColor,hoverBorderColor:e.hoverBorderColor,className:e.className},o.default.createElement(s.default,{icon:e.icon,color:r,size:"18px"}))};d.propTypes={id:a.default.string.isRequired,ariaLabel:a.default.string.isRequired,onClick:a.default.func.isRequired,unpressedBoxShadowColor:a.default.string,pressedBoxShadowColor:a.default.string,pressedBackground:a.default.string,unpressedBackground:a.default.string,pressedIconColor:a.default.string,unpressedIconColor:a.default.string,icon:a.default.string.isRequired,pressed:a.default.bool.isRequired,hoverBorderColor:a.default.string,marksButtonStatus:a.default.string,disabledIconColor:a.default.string,className:a.default.string},d.defaultProps={unpressedBoxShadowColor:l.colors.$color_button_border,pressedBoxShadowColor:l.colors.$color_purple,pressedBackground:l.colors.$color_pink_dark,unpressedBackground:l.colors.$color_button,pressedIconColor:l.colors.$color_white,unpressedIconColor:l.colors.$color_button_text,hoverBorderColor:l.colors.$color_white,marksButtonStatus:"enabled",disabledIconColor:l.colors.$color_grey},t.default=d},22027:(e,t,r)=>{"use strict";Object.defineProperty(t,"__esModule",{value:!0}),t.default=void 0;var o=i(r(99196)),n=i(r(98487)),a=i(r(85890)),l=r(37188),s=i(r(78386));function i(e){return e&&e.__esModule?e:{default:e}}const u=n.default.button`
	box-sizing: border-box;
	min-width: 32px;
	display: inline-block;
	border: 1px solid ${l.colors.$color_button_border};
	background-color: ${e=>e.background};
	box-shadow: ${e=>e.boxShadowColor};
	border-radius: 3px;
	cursor: pointer;
	padding: 0;
	height: "24px";
	&:hover {
		border-color: ${e=>e.hoverBorderColor};
	}
`,d=function(e){return o.default.createElement(u,{type:"button",onClick:e.onClick,boxShadowColor:e.boxShadowColor,background:e.background,id:e.id,"aria-label":e.ariaLabel,iconColor:e.iconColor,hoverBorderColor:e.hoverBorderColor,className:e.className},o.default.createElement(s.default,{icon:e.icon,color:e.iconColor,size:"18px"}))};d.propTypes={id:a.default.string.isRequired,ariaLabel:a.default.string.isRequired,onClick:a.default.func.isRequired,boxShadowColor:a.default.string,background:a.default.string,iconColor:a.default.string,icon:a.default.string.isRequired,hoverBorderColor:a.default.string,className:a.default.string},d.defaultProps={boxShadowColor:l.colors.$color_button_border,background:l.colors.$color_button,iconColor:l.colors.$color_button_text,hoverBorderColor:l.colors.$color_white},t.default=d},1453:(e,t,r)=>{"use strict";Object.defineProperty(t,"__esModule",{value:!0}),t.default=t.SimulatedLabel=void 0;var o=l(r(99196)),n=l(r(85890)),a=l(r(98487));function l(e){return e&&e.__esModule?e:{default:e}}function s(){return s=Object.assign?Object.assign.bind():function(e){for(var t=1;t<arguments.length;t++){var r=arguments[t];for(var o in r)Object.prototype.hasOwnProperty.call(r,o)&&(e[o]=r[o])}return e},s.apply(this,arguments)}t.SimulatedLabel=a.default.div`
	cursor: pointer;
	font-size: 14px;
	font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Oxygen-Sans, Ubuntu, Cantarell, "Helvetica Neue", sans-serif;
	margin: 4px 0;
	color: #303030;
	font-weight: 500;
`;const i=e=>o.default.createElement("label",s({htmlFor:e.for,className:e.className},e.optionalAttributes),e.children);i.propTypes={for:n.default.string.isRequired,optionalAttributes:n.default.shape({"aria-label":n.default.string,onClick:n.default.func,className:n.default.string}),children:n.default.any.isRequired,className:n.default.string},i.defaultProps={className:"",optionalAttributes:{}},t.default=i},44722:(e,t,r)=>{"use strict";Object.defineProperty(t,"__esModule",{value:!0}),t.languageNoticePropType=t.default=void 0;var o=function(e,t){if(e&&e.__esModule)return e;if(null===e||"object"!=typeof e&&"function"!=typeof e)return{default:e};var r=d(t);if(r&&r.has(e))return r.get(e);var o={__proto__:null},n=Object.defineProperty&&Object.getOwnPropertyDescriptor;for(var a in e)if("default"!==a&&Object.prototype.hasOwnProperty.call(e,a)){var l=n?Object.getOwnPropertyDescriptor(e,a):null;l&&(l.get||l.set)?Object.defineProperty(o,a,l):o[a]=e[a]}return o.default=e,r&&r.set(e,o),o}(r(99196)),n=u(r(85890)),a=u(r(98487)),l=r(65736),s=u(r(96746)),i=r(23695);function u(e){return e&&e.__esModule?e:{default:e}}function d(e){if("function"!=typeof WeakMap)return null;var t=new WeakMap,r=new WeakMap;return(d=function(e){return e?r:t})(e)}const c=a.default.p`
	margin: 1em 0;
`,f=(0,i.makeOutboundLink)(a.default.a`
	margin-left: 4px;
`);class p extends o.PureComponent{render(){const{changeLanguageLink:e,canChangeLanguage:t,language:r,showLanguageNotice:n}=this.props;if(!n)return null;
/* Translators: %s expands to the actual language. */let a=(0,l.__)("Your site language is set to %s. ","wordpress-seo");return t||(
/* Translators: %s expands to the actual language. */
a=(0,l.__)("Your site language is set to %s. If this is not correct, contact your site administrator.","wordpress-seo")),a=(0,l.sprintf)(a,`{{strong}}${r}{{/strong}}`),a=(0,s.default)({mixedString:a,components:{strong:o.default.createElement("strong",null)}}),o.default.createElement(c,null,a,t&&o.default.createElement(f,{href:e},(0,l.__)("Change language","wordpress-seo")))}}t.default=p;const h=t.languageNoticePropType={changeLanguageLink:n.default.string.isRequired,canChangeLanguage:n.default.bool,language:n.default.string.isRequired,showLanguageNotice:n.default.bool};p.propTypes=h,p.defaultProps={canChangeLanguage:!1,showLanguageNotice:!1}},50933:(e,t,r)=>{"use strict";Object.defineProperty(t,"__esModule",{value:!0}),t.default=void 0;var o=s(r(99196)),n=s(r(85890)),a=function(e,t){if(e&&e.__esModule)return e;if(null===e||"object"!=typeof e&&"function"!=typeof e)return{default:e};var r=l(t);if(r&&r.has(e))return r.get(e);var o={__proto__:null},n=Object.defineProperty&&Object.getOwnPropertyDescriptor;for(var a in e)if("default"!==a&&Object.prototype.hasOwnProperty.call(e,a)){var s=n?Object.getOwnPropertyDescriptor(e,a):null;s&&(s.get||s.set)?Object.defineProperty(o,a,s):o[a]=e[a]}return o.default=e,r&&r.set(e,o),o}(r(98487));function l(e){if("function"!=typeof WeakMap)return null;var t=new WeakMap,r=new WeakMap;return(l=function(e){return e?r:t})(e)}function s(e){return e&&e.__esModule?e:{default:e}}const i=({className:e})=>(""!==e&&(e+=" "),e+="yoast-loader",o.default.createElement("svg",{version:"1.1",id:"Y__x2B__bg",x:"0px",y:"0px",viewBox:"0 0 500 500",className:e},o.default.createElement("g",null,o.default.createElement("g",null,o.default.createElement("linearGradient",{id:"SVGID_1_",gradientUnits:"userSpaceOnUse",x1:"250",y1:"428.6121",x2:"250",y2:"77.122"},o.default.createElement("stop",{offset:"0",style:{stopColor:"#570732"}}),o.default.createElement("stop",{offset:"2.377558e-02",style:{stopColor:"#5D0936"}}),o.default.createElement("stop",{offset:"0.1559",style:{stopColor:"#771549"}}),o.default.createElement("stop",{offset:"0.3019",style:{stopColor:"#8B1D58"}}),o.default.createElement("stop",{offset:"0.4669",style:{stopColor:"#992362"}}),o.default.createElement("stop",{offset:"0.6671",style:{stopColor:"#A12768"}}),o.default.createElement("stop",{offset:"1",style:{stopColor:"#A4286A"}})),o.default.createElement("path",{fill:"url(#SVGID_1_)",d:"M454.7,428.6H118.4c-40.2,0-73.2-32.9-73.2-73.2V150.3c0-40.2,32.9-73.2,73.2-73.2h263.1 c40.2,0,73.2,32.9,73.2,73.2V428.6z"})),o.default.createElement("g",null,o.default.createElement("g",null,o.default.createElement("g",null,o.default.createElement("g",null,o.default.createElement("path",{fill:"#A4286A",d:"M357.1,102.4l-43.8,9.4L239.9,277l-47.2-147.8h-70.2l78.6,201.9c6.7,17.2,6.7,36.3,0,53.5 c-6.7,17.2,45.1-84.1,24.7-75.7c0,0,34.9,97.6,36.4,94.5c7-14.3,13.7-30.3,20.2-48.5L387.4,72 C387.4,72,358.4,102.4,357.1,102.4z"}))))),o.default.createElement("g",null,o.default.createElement("linearGradient",{id:"SVGID_2_",gradientUnits:"userSpaceOnUse",x1:"266.5665",y1:"-6.9686",x2:"266.5665",y2:"378.4586"},o.default.createElement("stop",{offset:"0",style:{stopColor:"#77B227"}}),o.default.createElement("stop",{offset:"0.4669",style:{stopColor:"#75B027"}}),o.default.createElement("stop",{offset:"0.635",style:{stopColor:"#6EAB27"}}),o.default.createElement("stop",{offset:"0.7549",style:{stopColor:"#63A027"}}),o.default.createElement("stop",{offset:"0.8518",style:{stopColor:"#529228"}}),o.default.createElement("stop",{offset:"0.9339",style:{stopColor:"#3C7F28"}}),o.default.createElement("stop",{offset:"1",style:{stopColor:"#246B29"}})),o.default.createElement("path",{fill:"url(#SVGID_2_)",d:"M337,6.1l-98.6,273.8l-47.2-147.8H121L199.6,334c6.7,17.2,6.7,36.3,0,53.5 c-8.8,22.5-23.4,41.8-59,46.6v59.9c69.4,0,106.9-42.6,140.3-136.1L412.1,6.1H337z"}),o.default.createElement("path",{fill:"#FFFFFF",d:"M140.6,500h-6.1v-71.4l5.3-0.7c34.8-4.7,46.9-24.2,54.1-42.7c6.2-15.8,6.2-33.2,0-49l-81.9-210.3h83.7 l43.1,134.9L332.7,0h88.3L286.7,359.9c-17.9,50-36.4,83.4-58.1,105.3C205,488.9,177,500,140.6,500z M146.7,439.2v48.3 c29.9-1.2,53.3-11.1,73.1-31.1c20.4-20.5,38-52.6,55.3-100.9L403.2,12.3h-61.9L238.1,299l-51.3-160.8H130l75.3,193.5 c7.3,18.7,7.3,39.2,0,57.9C197.7,409.3,184.1,432.4,146.7,439.2z"})))));i.propTypes={className:n.default.string},i.defaultProps={className:""};const u=a.keyframes`
	0%   { transform: scale( 0.70 ); opacity: 0.4; }
	80%  { opacity: 1 }
	100%  { transform: scale( 0.95 ); opacity: 1 }
`;t.default=(0,a.default)(i)`
	animation: ${u} 1.15s infinite;
	animation-direction: alternate;
	animation-timing-function: cubic-bezier(0.96, 0.02, 0.63, 0.86);
`},73028:(e,t,r)=>{"use strict";Object.defineProperty(t,"__esModule",{value:!0}),t.default=void 0;var o,n=(o=r(99196))&&o.__esModule?o:{default:o};function a(){return a=Object.assign?Object.assign.bind():function(e){for(var t=1;t<arguments.length;t++){var r=arguments[t];for(var o in r)Object.prototype.hasOwnProperty.call(r,o)&&(e[o]=r[o])}return e},a.apply(this,arguments)}t.default=e=>n.default.createElement("svg",a({},e,{xmlns:"http://www.w3.org/2000/svg",viewBox:"0 0 520 240"}),n.default.createElement("linearGradient",{id:"a",gradientUnits:"userSpaceOnUse",x1:"476.05",y1:"194.48",x2:"476.05",y2:"36.513"},n.default.createElement("stop",{offset:"0",style:{stopColor:"#570732"}}),n.default.createElement("stop",{offset:".038",style:{stopColor:"#610b39"}}),n.default.createElement("stop",{offset:".155",style:{stopColor:"#79164b"}}),n.default.createElement("stop",{offset:".287",style:{stopColor:"#8c1e59"}}),n.default.createElement("stop",{offset:".44",style:{stopColor:"#9a2463"}}),n.default.createElement("stop",{offset:".633",style:{stopColor:"#a22768"}}),n.default.createElement("stop",{offset:"1",style:{stopColor:"#a4286a"}})),n.default.createElement("path",{fill:"url(#a)",d:"M488.7 146.1v-56h20V65.9h-20V36.5h-30.9v29.3h-15.7v24.3h15.7v52.8c0 30 20.9 47.8 43 51.5l9.2-24.8c-12.9-1.6-21.2-11.2-21.3-23.5z"}),n.default.createElement("linearGradient",{id:"b",gradientUnits:"userSpaceOnUse",x1:"287.149",y1:"172.553",x2:"287.149",y2:"61.835"},n.default.createElement("stop",{offset:"0",style:{stopColor:"#570732"}}),n.default.createElement("stop",{offset:".038",style:{stopColor:"#610b39"}}),n.default.createElement("stop",{offset:".155",style:{stopColor:"#79164b"}}),n.default.createElement("stop",{offset:".287",style:{stopColor:"#8c1e59"}}),n.default.createElement("stop",{offset:".44",style:{stopColor:"#9a2463"}}),n.default.createElement("stop",{offset:".633",style:{stopColor:"#a22768"}}),n.default.createElement("stop",{offset:"1",style:{stopColor:"#a4286a"}})),n.default.createElement("path",{fill:"url(#b)",d:"M332.8 137.3V95.2c0-1.5-.1-3-.2-4.4-2.7-34-51-33.9-88.3-20.9L255 91.7c24.3-11.6 38.9-8.6 44-2.9l.4.4v.1c2.6 3.5 2 9 2 13.4-31.8 0-65.7 4.2-65.7 39.1 0 26.5 33.2 43.6 68 18.3l5.2 12.4h29.8c-2.8-14.5-5.9-27-5.9-35.2zm-31.2-.3c-24.5 27.4-46.9 1.6-23.9-9.6 6.8-2.3 15.9-2.4 23.9-2.4v12z"}),n.default.createElement("linearGradient",{id:"c",gradientUnits:"userSpaceOnUse",x1:"390.54",y1:"172.989",x2:"390.54",y2:"61.266"},n.default.createElement("stop",{offset:"0",style:{stopColor:"#570732"}}),n.default.createElement("stop",{offset:".038",style:{stopColor:"#610b39"}}),n.default.createElement("stop",{offset:".155",style:{stopColor:"#79164b"}}),n.default.createElement("stop",{offset:".287",style:{stopColor:"#8c1e59"}}),n.default.createElement("stop",{offset:".44",style:{stopColor:"#9a2463"}}),n.default.createElement("stop",{offset:".633",style:{stopColor:"#a22768"}}),n.default.createElement("stop",{offset:"1",style:{stopColor:"#a4286a"}})),n.default.createElement("path",{fill:"url(#c)",d:"M380.3 92.9c0-10.4 16.6-15.2 42.8-3.3l9.1-22C397 57 348.9 56 348.6 92.8c-.1 17.7 11.2 27.2 27.5 33.2 11.3 4.2 27.6 6.4 27.6 15.4-.1 11.8-25.3 13.6-48.4-2.3l-9.3 23.8c31.4 15.6 89.7 16.1 89.4-23.1-.4-38.5-55.1-31.9-55.1-46.9z"}),n.default.createElement("linearGradient",{id:"d",gradientUnits:"userSpaceOnUse",x1:"76.149",y1:"3.197",x2:"76.149",y2:"178.39"},n.default.createElement("stop",{offset:"0",style:{stopColor:"#77b227"}}),n.default.createElement("stop",{offset:".467",style:{stopColor:"#75b027"}}),n.default.createElement("stop",{offset:".635",style:{stopColor:"#6eab27"}}),n.default.createElement("stop",{offset:".755",style:{stopColor:"#63a027"}}),n.default.createElement("stop",{offset:".852",style:{stopColor:"#529228"}}),n.default.createElement("stop",{offset:".934",style:{stopColor:"#3c7f28"}}),n.default.createElement("stop",{offset:"1",style:{stopColor:"#246b29"}})),n.default.createElement("path",{fill:"url(#d)",d:"M108.2 9.2L63.4 133.6 41.9 66.4H10l35.7 91.8c3 7.8 3 16.5 0 24.3-4 10.2-10.6 19-26.8 21.2v27.2c31.5 0 48.6-19.4 63.8-61.9L142.3 9.2h-34.1z"}),n.default.createElement("linearGradient",{id:"e",gradientUnits:"userSpaceOnUse",x1:"175.228",y1:"172.923",x2:"175.228",y2:"62.17"},n.default.createElement("stop",{offset:"0",style:{stopColor:"#570732"}}),n.default.createElement("stop",{offset:".038",style:{stopColor:"#610b39"}}),n.default.createElement("stop",{offset:".155",style:{stopColor:"#79164b"}}),n.default.createElement("stop",{offset:".287",style:{stopColor:"#8c1e59"}}),n.default.createElement("stop",{offset:".44",style:{stopColor:"#9a2463"}}),n.default.createElement("stop",{offset:".633",style:{stopColor:"#a22768"}}),n.default.createElement("stop",{offset:"1",style:{stopColor:"#a4286a"}})),n.default.createElement("path",{fill:"url(#e)",d:"M175.2 62.2c-38.6 0-54 27.3-54 56.2 0 30 15.1 54.6 54 54.6 38.7 0 54.1-27.6 54-54.6-.1-33-16.8-56.2-54-56.2zm0 87.1c-15.7 0-23.4-11.7-23.4-30.9s8.3-32.9 23.4-32.9c15 0 23.2 13.7 23.2 32.9s-7.5 30.9-23.2 30.9z"}))},79610:(e,t,r)=>{"use strict";Object.defineProperty(t,"__esModule",{value:!0}),t.default=void 0;var o=u(r(99196)),n=u(r(85890)),a=u(r(83253)),l=u(r(98487)),s=r(37188),i=u(r(78386));function u(e){return e&&e.__esModule?e:{default:e}}const d=l.default.h1`
	float: left;
	margin: -4px 0 2rem;
	font-size: 1rem;
`,c=l.default.button`
	float: right;
	width: 44px;
	height: 44px;
	background: transparent;
	border: 0;
	margin: -16px -16px 0 0;
	padding: 0;
	cursor: pointer;
`;class f extends o.default.Component{constructor(e){super(e)}render(){return o.default.createElement(a.default,{isOpen:this.props.isOpen,onRequestClose:this.props.onClose,role:"dialog",contentLabel:this.props.modalAriaLabel,overlayClassName:`yoast-modal__overlay ${this.props.className}`,className:`yoast-modal__content ${this.props.className}`,appElement:this.props.appElement,bodyOpenClassName:"yoast-modal_is-open"},o.default.createElement("div",null,this.props.heading&&o.default.createElement(d,{className:"yoast-modal__title"},this.props.heading),this.props.closeIconButton&&o.default.createElement(c,{type:"button",onClick:this.props.onClose,className:`yoast-modal__button-close-icon ${this.props.closeIconButtonClassName}`,"aria-label":this.props.closeIconButton},o.default.createElement(i.default,{icon:"times",color:s.colors.$color_grey_text}))),o.default.createElement("div",{className:"yoast-modal__inside"},this.props.children),this.props.closeButton&&o.default.createElement("div",{className:"yoast-modal__actions"},o.default.createElement("button",{type:"button",onClick:this.props.onClose,className:`yoast-modal__button-close ${this.props.closeButtonClassName}`},this.props.closeButton)))}}f.propTypes={children:n.default.any,className:n.default.string,isOpen:n.default.bool,onClose:n.default.func.isRequired,modalAriaLabel:n.default.string.isRequired,appElement:n.default.object.isRequired,heading:n.default.string,closeIconButton:n.default.string,closeIconButtonClassName:n.default.string,closeButton:n.default.string,closeButtonClassName:n.default.string},f.defaultProps={children:null,className:"",heading:"",closeIconButton:"",closeIconButtonClassName:"",closeButton:"",closeButtonClassName:"",isOpen:!1};const p=(0,l.default)(f)`
	&.yoast-modal__overlay {
		position: fixed;
		top: 0;
		left: 0;
		right: 0;
		bottom: 0;
		background-color: rgba(0, 0, 0, 0.6);
		transition: background 100ms ease-out;
		z-index: 999999;
	}

	&.yoast-modal__content {
		position: absolute;
		top: 50%;
		left: 50%;
		right: auto;
		bottom: auto;
		width: auto;
		max-width: 90%;
		max-height: 90%;
		border: 0;
		border-radius: 0;
		margin-right: -50%;
		padding: 24px;
		transform: translate(-50%, -50%);
		background-color: #fff;
		outline: none;

		@media screen and ( max-width: 500px ) {
			overflow-y: auto;
		}

		@media screen and ( max-height: 640px ) {
			overflow-y: auto;
		}
	}

	.yoast-modal__inside {
		clear: both;
	}

	.yoast-modal__actions {
		text-align: right;
	}

	.yoast-modal__actions button {
		margin: 24px 0 0 8px;
	}
`;t.default=p},64737:(e,t,r)=>{"use strict";Object.defineProperty(t,"__esModule",{value:!0}),t.default=void 0;var o=i(r(99196)),n=i(r(85890)),a=i(r(98487)),l=r(37188),s=i(r(78386));function i(e){return e&&e.__esModule?e:{default:e}}const u=a.default.div`
	padding: 8px;
`,d=a.default.ol`
	padding: 0;
	margin: 0;

	list-style: none;
	counter-reset: multi-step-progress-counter;

	li {
		counter-increment: multi-step-progress-counter;
	}
`,c=a.default.li`
	display: flex;
	align-items: baseline;

	margin: 8px 0;

	:first-child {
		margin-top: 0;
	}

	:last-child {
		margin-bottom: 0;
	}

	span {
		margin: 0 8px;
	}

	svg {
		position: relative;
		top: 2px;
	}

	::before {
		content: counter( multi-step-progress-counter );
		font-size: 12px;
		background: ${l.colors.$color_pink_dark};
		border-radius: 50%;
		min-width: 16px;
		height: 16px;
		padding: 4px;
		color: ${l.colors.$color_white};
		text-align: center;
	}
`,f=(0,a.default)(c)`
	span {
		color: ${l.colors.$palette_grey_text_light};
	}

	::before {
		background-color: ${l.colors.$palette_grey_medium_dark};
	}
`,p=(0,a.default)(c)`
	::before {
		background-color: ${l.colors.$palette_grey_medium_dark};
	}
`;class h extends o.default.Component{render(){return o.default.createElement(u,{role:"status","aria-live":"polite","aria-relevant":"additions text","aria-atomic":!0},o.default.createElement(d,null,this.props.steps.map((e=>{switch(e.status){case"running":return this.renderRunningState(e);case"failed":return this.renderFailedState(e);case"finished":return this.renderFinishedState(e);default:return this.renderPendingState(e)}}))))}renderPendingState(e){return o.default.createElement(f,{key:e.id},o.default.createElement("span",null,e.text))}renderRunningState(e){return o.default.createElement(p,{key:e.id},o.default.createElement("span",null,e.text),o.default.createElement(s.default,{icon:"loading-spinner"}))}renderFinishedState(e){return o.default.createElement(c,{key:e.id},o.default.createElement("span",null,e.text),o.default.createElement(s.default,{icon:"check",color:l.colors.$color_green_medium_light}))}renderFailedState(e){return o.default.createElement(c,{key:e.id},o.default.createElement("span",null,e.text),o.default.createElement(s.default,{icon:"times",color:l.colors.$color_red}))}}h.defaultProps={steps:[]},h.propTypes={steps:n.default.arrayOf(n.default.shape({status:n.default.oneOf(["pending","running","finished","failed"]).isRequired,text:n.default.string.isRequired,id:n.default.string.isRequired}))},t.default=h},18506:(e,t,r)=>{"use strict";Object.defineProperty(t,"__esModule",{value:!0}),t.default=void 0;var o=d(r(99196)),n=d(r(85890)),a=d(r(98487)),l=r(65736),s=r(37188),i=d(r(8272)),u=d(r(78386));function d(e){return e&&e.__esModule?e:{default:e}}const c=a.default.div`
	display: flex;
	align-items: center;
	padding: 24px;

	h1, h2, h3, h4, h5, h6 {
		font-size: 1.4em;
		line-height: 1;
		margin: 0 0 4px 0;

		@media screen and ( max-width: ${s.breakpoints.mobile} ) {
			${e=>e.isDismissable?"margin-right: 30px;":""}
		}
	}

	p:last-child {
		margin: 0;
	}

	@media screen and ( max-width: ${s.breakpoints.mobile} ) {
		display: block;
		position: relative;
		padding: 16px;
	}
`,f=a.default.img`
	flex: 0 0 ${e=>e.imageWidth?e.imageWidth:"auto"};
	height: ${e=>e.imageHeight?e.imageHeight:"auto"};
	margin-right: 24px;

	@media screen and ( max-width: ${s.breakpoints.mobile} ) {
		display: none;
	}
`,p=a.default.div`
	flex: 1 1 auto;
`,h=a.default.button`
	flex: 0 0 40px;
	height: 40px;
	border: 0;
	margin: 0 0 0 10px;
	padding: 0;
	background: transparent;
	cursor: pointer;

	@media screen and ( max-width: ${s.breakpoints.mobile} ) {
		width: 40px;
		position: absolute;
		top: 5px;
		right: 5px;
		margin: 0;
	}
`,b=(0,a.default)(u.default)`
	vertical-align: middle;
`;function g(e){const t=`${e.headingLevel}`;return o.default.createElement(i.default,null,o.default.createElement(c,{isDismissable:e.isDismissable},e.imageSrc&&o.default.createElement(f,{src:e.imageSrc,imageWidth:e.imageWidth,imageHeight:e.imageHeight,alt:""}),o.default.createElement(p,null,o.default.createElement(t,null,e.title),o.default.createElement("p",{className:"prova",dangerouslySetInnerHTML:{__html:e.html}})),e.isDismissable&&o.default.createElement(h,{onClick:e.onClick,type:"button","aria-label":(0,l.__)("Dismiss this notice","wordpress-seo")},o.default.createElement(b,{icon:"times",color:s.colors.$color_grey_text,size:"24px"}))))}g.propTypes={imageSrc:n.default.string,imageWidth:n.default.string,imageHeight:n.default.string,title:n.default.string,html:n.default.string,isDismissable:n.default.bool,onClick:n.default.func,headingLevel:n.default.string},g.defaultProps={isDismissable:!1,headingLevel:"h3"},t.default=g},8272:(e,t,r)=>{"use strict";Object.defineProperty(t,"__esModule",{value:!0}),t.default=void 0;var o=l(r(85890)),n=l(r(98487)),a=r(37188);function l(e){return e&&e.__esModule?e:{default:e}}const s=n.default.div`
	box-shadow: 0 1px 2px rgba(0, 0, 0, 0.2);
	background-color: ${e=>e.backgroundColor};
	min-height: ${e=>e.minHeight};
`;s.propTypes={backgroundColor:o.default.string,minHeight:o.default.string},s.defaultProps={backgroundColor:a.colors.$color_white,minHeight:"0"},t.default=s},57186:(e,t,r)=>{"use strict";Object.defineProperty(t,"__esModule",{value:!0}),t.default=void 0;var o=l(r(85890)),n=l(r(98487)),a=r(37188);function l(e){return e&&e.__esModule?e:{default:e}}const s=n.default.progress`
	box-sizing: border-box;
	width: 100%;
	height: 8px;
	display: block;
	margin-top: 8px;
	appearance: none;
	background-color: ${e=>e.backgroundColor};
	border: 1px solid ${e=>e.borderColor};

	::-webkit-progress-bar {
	   	background-color: ${e=>e.backgroundColor};
	}

	::-webkit-progress-value {
		background-color: ${e=>e.progressColor};
		transition: width 250ms;
	}

	::-moz-progress-bar {
		background-color: ${e=>e.progressColor};
	}
	
	::-ms-fill {
		background-color: ${e=>e.progressColor};
		border: 0;
	}
`;s.defaultProps={max:1,value:0,progressColor:a.colors.$color_good,backgroundColor:a.colors.$color_background_light,borderColor:a.colors.$color_input_border,"aria-hidden":"true"},s.propTypes={max:o.default.number,value:o.default.number,progressColor:o.default.string,backgroundColor:o.default.string,borderColor:o.default.string,"aria-hidden":o.default.string},t.default=s},5180:(e,t,r)=>{"use strict";Object.defineProperty(t,"__esModule",{value:!0}),t.default=void 0;var o=r(23695),n=s(r(99196)),a=s(r(85890)),l=s(r(98487));function s(e){return e&&e.__esModule?e:{default:e}}const{stripTagsFromHtmlString:i}=o.strings,u=["a","b","strong","em","i","span","p","ul","ol","li","div"],d=l.default.li`
	display: table-row;
	font-size: 14px;
`,c=l.default.span`
	display: table-cell;
	padding: 2px;
`,f=(0,l.default)(c)`
	position: relative;
	top: 1px;
	display: inline-block;
	height: 8px;
	width: 8px;
	border-radius: 50%;
	background-color: ${e=>e.scoreColor};
`;f.propTypes={scoreColor:a.default.string.isRequired};const p=(0,l.default)(c)`
	padding-left: 8px;
	width: 100%;
`,h=(0,l.default)(c)`
	font-weight: 600;
	text-align: right;
	padding-left: 16px;
`,b=e=>n.default.createElement(d,{className:`${e.className}`},n.default.createElement(f,{className:`${e.className}-bullet`,scoreColor:e.scoreColor}),n.default.createElement(p,{className:`${e.className}-text`,dangerouslySetInnerHTML:{__html:i(e.html,u)}}),e.value&&n.default.createElement(h,{className:`${e.className}-score`},e.value));b.propTypes={className:a.default.string.isRequired,scoreColor:a.default.string.isRequired,html:a.default.string.isRequired,value:a.default.number};const g=l.default.ul`
	display: table;
	box-sizing: border-box;
	list-style: none;
	max-width: 100%;
	min-width: 200px;
	margin: 8px 0;
	padding: 0 8px;
`,m=e=>n.default.createElement(g,{className:e.className,role:"list"},e.items.map(((t,r)=>n.default.createElement(b,{className:`${e.className}__item`,key:r,scoreColor:t.color,html:t.html,value:t.value}))));m.propTypes={className:a.default.string,items:a.default.arrayOf(a.default.shape({color:a.default.string.isRequired,html:a.default.string.isRequired,value:a.default.number}))},m.defaultProps={className:"score-assessments"},t.default=m},37553:(e,t,r)=>{"use strict";Object.defineProperty(t,"__esModule",{value:!0}),t.default=void 0;var o=l(r(99196)),n=l(r(85890)),a=l(r(33014));function l(e){return e&&e.__esModule?e:{default:e}}const s=e=>o.default.createElement("section",{className:e.className},e.headingText&&o.default.createElement(a.default,{level:e.headingLevel,className:e.headingClassName},e.headingText),e.children);s.propTypes={className:n.default.string,headingText:n.default.string,headingLevel:n.default.number,headingClassName:n.default.string,children:n.default.any},s.defaultProps={headingLevel:1},t.default=s},91752:(e,t,r)=>{"use strict";Object.defineProperty(t,"__esModule",{value:!0}),t.StyledTitleContainer=t.StyledTitle=t.StyledSubTitle=t.SectionTitle=void 0;var o=i(r(99196)),n=i(r(85890)),a=i(r(98487)),l=r(37188),s=i(r(42479));function i(e){return e&&e.__esModule?e:{default:e}}const u=t.StyledTitleContainer=a.default.span`
	flex-grow: 1;
	overflow-x: hidden;
	line-height: normal; // Avoid vertical scrollbar in IE 11 when rendered in the WP sidebar.
`,d=t.StyledTitle=a.default.span`
	display: block;
	line-height: 1.5; 
	text-overflow: ellipsis;
	overflow: hidden;
	color: ${l.colors.$color_headings};
`,c=t.StyledSubTitle=a.default.span`
	display: block;
	white-space: nowrap;
	text-overflow: ellipsis;
	overflow: hidden;
	font-size: 0.8125rem;
	margin-top: 2px;
`,f=e=>o.default.createElement(u,null,o.default.createElement(d,null,e.title,e.titleScreenReaderText&&o.default.createElement(s.default,null," "+e.titleScreenReaderText)),e.subTitle&&o.default.createElement(c,null,e.subTitle));t.SectionTitle=f,f.propTypes={title:n.default.string.isRequired,titleScreenReaderText:n.default.string,subTitle:n.default.string}},49526:(e,t,r)=>{"use strict";Object.defineProperty(t,"__esModule",{value:!0}),t.default=void 0;var o=l(r(99196)),n=l(r(85890)),a=l(r(98487));function l(e){return e&&e.__esModule?e:{default:e}}const s=a.default.div`
	margin: 8px 0;
	height: ${e=>e.barHeight};
	overflow: hidden;
`,i=a.default.span`
	display: inline-block;
	vertical-align: top;
	width: ${e=>`${e.progressWidth}%`};
	background-color: ${e=>e.progressColor};
	height: 100%;
`;i.propTypes={progressWidth:n.default.number.isRequired,progressColor:n.default.string.isRequired};const u=e=>{let t=0;for(let r=0;r<e.items.length;r++)e.items[r].value=Math.max(e.items[r].value,0),t+=e.items[r].value;return t<=0?null:o.default.createElement(s,{className:e.className,barHeight:e.barHeight},e.items.map(((r,n)=>o.default.createElement(i,{className:`${e.className}__part`,key:n,progressColor:r.color,progressWidth:r.value/t*100}))))};u.propTypes={className:n.default.string,items:n.default.arrayOf(n.default.shape({value:n.default.number.isRequired,color:n.default.string.isRequired})),barHeight:n.default.string},u.defaultProps={className:"stacked-progress-bar",barHeight:"24px"},t.default=u},78538:(e,t,r)=>{"use strict";Object.defineProperty(t,"__esModule",{value:!0}),t.default=t.StyledSectionBase=t.StyledIcon=t.StyledHeading=void 0;var o=c(r(99196)),n=c(r(85890)),a=c(r(98487)),l=r(37188),s=r(23695),i=c(r(37553)),u=c(r(33014)),d=c(r(78386));function c(e){return e&&e.__esModule?e:{default:e}}const f=t.StyledHeading=(0,a.default)(u.default)`
	margin-left: ${(0,s.getDirectionalStyle)("0","20px")};
	padding: ${(0,s.getDirectionalStyle)("0","20px")};
`,p=t.StyledIcon=(0,a.default)(d.default)``,h=t.StyledSectionBase=(0,a.default)(i.default)`
	box-shadow: ${e=>e.hasPaperStyle?`0 1px 2px ${(0,l.rgba)(l.colors.$color_black,.2)}`:"none"};
	background-color: ${e=>e.hasPaperStyle?l.colors.$color_white:"transparent"};
	padding-right: ${e=>e.hasPaperStyle?"20px":"0"};
	padding-left: ${e=>e.hasPaperStyle?"20px":"0"};
	padding-bottom: ${e=>e.headingText?"0":"10px"};
	padding-top: ${e=>e.headingText?"0":"10px"};

	*, & {
		box-sizing: border-box;

		&:before, &:after {
			box-sizing: border-box;
		}
	}

	& ${f} {
		display: flex;
		align-items: center;
		padding: 8px 0 0;
		font-size: 1rem;
		line-height: 1.5;
		margin: 0 0 16px;
		font-family: "Open Sans", sans-serif;
		font-weight: 300;
		color: ${e=>e.headingColor?e.headingColor:`${l.colors.$color_grey_dark}`};
	}

	& ${p} {
		flex: 0 0 auto;
		${(0,s.getDirectionalStyle)("margin-right","margin-left")}: 8px;
	}
`,b=e=>o.default.createElement(h,{className:e.className,headingColor:e.headingColor,hasPaperStyle:e.hasPaperStyle},e.headingText&&o.default.createElement(f,{level:e.headingLevel,className:e.headingClassName},e.headingIcon&&o.default.createElement(p,{icon:e.headingIcon,color:e.headingIconColor,size:e.headingIconSize}),e.headingText),e.children);b.propTypes={className:n.default.string,headingLevel:n.default.number,headingClassName:n.default.string,headingColor:n.default.string,headingIcon:n.default.string,headingIconColor:n.default.string,headingIconSize:n.default.string,headingText:n.default.string,hasPaperStyle:n.default.bool,children:n.default.any},b.defaultProps={className:"yoast-section",headingLevel:2,hasPaperStyle:!0},t.default=b},78386:(e,t,r)=>{"use strict";Object.defineProperty(t,"__esModule",{value:!0}),t.icons=t.default=void 0;var o=l(r(99196)),n=l(r(98487)),a=r(23695);function l(e){return e&&e.__esModule?e:{default:e}}const s=n.default.svg`
	width: ${e=>e.size};
	height: ${e=>e.size};
	flex: none;

	animation: loadingSpinnerRotator 1.4s linear infinite;

	& .path {
		stroke: ${e=>e.fill};
		stroke-dasharray: 187;
		stroke-dashoffset: 0;
		transform-origin: center;
		animation: loadingSpinnerDash 1.4s ease-in-out infinite;
	}

	@keyframes loadingSpinnerRotator {
		0% { transform: rotate( 0deg ); }
		100% { transform: rotate( 270deg ); }
	}

	@keyframes loadingSpinnerDash {
		0% { stroke-dashoffset: 187; }
		50% {
			stroke-dashoffset: 47;
			transform:rotate( 135deg );
		}
		100% {
			stroke-dashoffset: 187;
			transform: rotate( 450deg );
		}
	}
`,i="0 0 1792 1792",u=t.icons={"chevron-down":{viewbox:"0 0 24 24",width:"24px",path:[o.default.createElement("g",{key:"1"},o.default.createElement("path",{fill:"none",d:"M0,0h24v24H0V0z"})),o.default.createElement("g",{key:"2"},o.default.createElement("path",{d:"M7.41,8.59L12,13.17l4.59-4.58L18,10l-6,6l-6-6L7.41,8.59z"}))]},"chevron-up":{viewbox:"0 0 24 24",width:"24px",path:[o.default.createElement("g",{key:"1"},o.default.createElement("path",{fill:"none",d:"M0,0h24v24H0V0z"})),o.default.createElement("g",{key:"2"},o.default.createElement("path",{d:"M12,8l-6,6l1.41,1.41L12,10.83l4.59,4.58L18,14L12,8z"}))]},clipboard:{viewbox:i,path:"M768 1664h896v-640h-416q-40 0-68-28t-28-68v-416h-384v1152zm256-1440v-64q0-13-9.5-22.5t-22.5-9.5h-704q-13 0-22.5 9.5t-9.5 22.5v64q0 13 9.5 22.5t22.5 9.5h704q13 0 22.5-9.5t9.5-22.5zm256 672h299l-299-299v299zm512 128v672q0 40-28 68t-68 28h-960q-40 0-68-28t-28-68v-160h-544q-40 0-68-28t-28-68v-1344q0-40 28-68t68-28h1088q40 0 68 28t28 68v328q21 13 36 28l408 408q28 28 48 76t20 88z"},check:{viewbox:i,path:"M249.2,431.2c-23,0-45.6,9.4-61.8,25.6L25.6,618.6C9.4,634.8,0,657.4,0,680.4c0,23,9.4,45.6,25.6,61.8 l593.1,593.1c16.2,16.2,38.8,25.6,61.8,25.6c23,0,45.6-9.4,61.8-25.6L1766.4,311c16.2-16.2,25.6-38.8,25.6-61.8 s-9.4-45.6-25.6-61.8L1604.5,25.6C1588.3,9.4,1565.8,0,1542.8,0c-23,0-45.6,9.4-61.8,25.6L680.4,827L311,456.3 C294.8,440.5,272.3,431.2,249.2,431.2z"},"angle-down":{viewbox:i,path:"M1395 736q0 13-10 23l-466 466q-10 10-23 10t-23-10l-466-466q-10-10-10-23t10-23l50-50q10-10 23-10t23 10l393 393 393-393q10-10 23-10t23 10l50 50q10 10 10 23z"},"angle-left":{viewbox:i,path:"M1203 544q0 13-10 23l-393 393 393 393q10 10 10 23t-10 23l-50 50q-10 10-23 10t-23-10l-466-466q-10-10-10-23t10-23l466-466q10-10 23-10t23 10l50 50q10 10 10 23z"},"angle-right":{viewbox:i,path:"M1171 960q0 13-10 23l-466 466q-10 10-23 10t-23-10l-50-50q-10-10-10-23t10-23l393-393-393-393q-10-10-10-23t10-23l50-50q10-10 23-10t23 10l466 466q10 10 10 23z"},"angle-up":{viewbox:i,path:"M1395 1184q0 13-10 23l-50 50q-10 10-23 10t-23-10l-393-393-393 393q-10 10-23 10t-23-10l-50-50q-10-10-10-23t10-23l466-466q10-10 23-10t23 10l466 466q10 10 10 23z"},"arrow-down":{viewbox:i,path:"M896 1791L120.91 448.5L1671.09 448.5z"},"arrow-left":{viewbox:i,path:"M1343.5 1671.09L1 896L1343.5 120.91z"},"arrow-right":{viewbox:i,path:"M1791 896L448.5 1671.09L448.5 120.91z"},"arrow-up":{viewbox:i,path:"M1671.09 1343.5L120.91 1343.5L896 1z"},"caret-right":{viewbox:"0 0 192 512",path:"M 0 384.662 V 127.338 c 0 -17.818 21.543 -26.741 34.142 -14.142 l 128.662 128.662 c 7.81 7.81 7.81 20.474 0 28.284 L 34.142 398.804 C 21.543 411.404 0 402.48 0 384.662 Z"},circle:{viewbox:i,path:"M1664 896q0 209-103 385.5t-279.5 279.5-385.5 103-385.5-103-279.5-279.5-103-385.5 103-385.5 279.5-279.5 385.5-103 385.5 103 279.5 279.5 103 385.5z"},desktop:{viewbox:i,path:"M1728 992v-832q0-13-9.5-22.5t-22.5-9.5h-1600q-13 0-22.5 9.5t-9.5 22.5v832q0 13 9.5 22.5t22.5 9.5h1600q13 0 22.5-9.5t9.5-22.5zm128-832v1088q0 66-47 113t-113 47h-544q0 37 16 77.5t32 71 16 43.5q0 26-19 45t-45 19h-512q-26 0-45-19t-19-45q0-14 16-44t32-70 16-78h-544q-66 0-113-47t-47-113v-1088q0-66 47-113t113-47h1600q66 0 113 47t47 113z"},edit:{viewbox:i,path:"M491 1536l91-91-235-235-91 91v107h128v128h107zm523-928q0-22-22-22-10 0-17 7l-542 542q-7 7-7 17 0 22 22 22 10 0 17-7l542-542q7-7 7-17zm-54-192l416 416-832 832h-416v-416zm683 96q0 53-37 90l-166 166-416-416 166-165q36-38 90-38 53 0 91 38l235 234q37 39 37 91z"},eye:{viewbox:i,path:"M1664 960q-152-236-381-353 61 104 61 225 0 185-131.5 316.5t-316.5 131.5-316.5-131.5-131.5-316.5q0-121 61-225-229 117-381 353 133 205 333.5 326.5t434.5 121.5 434.5-121.5 333.5-326.5zm-720-384q0-20-14-34t-34-14q-125 0-214.5 89.5t-89.5 214.5q0 20 14 34t34 14 34-14 14-34q0-86 61-147t147-61q20 0 34-14t14-34zm848 384q0 34-20 69-140 230-376.5 368.5t-499.5 138.5-499.5-139-376.5-368q-20-35-20-69t20-69q140-229 376.5-368t499.5-139 499.5 139 376.5 368q20 35 20 69z"},"exclamation-triangle":{viewbox:i,path:"M1024 1375v-190q0-14-9.5-23.5T992 1152H800q-13 0-22.5 9.5T768 1185v190q0 14 9.5 23.5t22.5 9.5h192q13 0 22.5-9.5t9.5-23.5zm-2-374l18-459q0-12-10-19-13-11-24-11H786q-11 0-24 11-10 7-10 21l17 457q0 10 10 16.5t24 6.5h185q14 0 23.5-6.5t10.5-16.5zm-14-934l768 1408q35 63-2 126-17 29-46.5 46t-63.5 17H128q-34 0-63.5-17T18 1601q-37-63-2-126L784 67q17-31 47-49t65-18 65 18 47 49z"},"file-text":{viewbox:i,path:"M1596 380q28 28 48 76t20 88v1152q0 40-28 68t-68 28h-1344q-40 0-68-28t-28-68v-1600q0-40 28-68t68-28h896q40 0 88 20t76 48zm-444-244v376h376q-10-29-22-41l-313-313q-12-12-41-22zm384 1528v-1024h-416q-40 0-68-28t-28-68v-416h-768v1536h1280zm-1024-864q0-14 9-23t23-9h704q14 0 23 9t9 23v64q0 14-9 23t-23 9h-704q-14 0-23-9t-9-23v-64zm736 224q14 0 23 9t9 23v64q0 14-9 23t-23 9h-704q-14 0-23-9t-9-23v-64q0-14 9-23t23-9h704zm0 256q14 0 23 9t9 23v64q0 14-9 23t-23 9h-704q-14 0-23-9t-9-23v-64q0-14 9-23t23-9h704z"},gear:{viewbox:i,path:"M1800 800h-218q-26 -107 -81 -193l154 -154l-210 -210l-154 154q-88 -55 -191 -79v-218h-300v218q-103 24 -191 79l-154 -154l-212 212l154 154q-55 88 -79 191h-218v297h217q23 101 80 194l-154 154l210 210l154 -154q85 54 193 81v218h300v-218q103 -24 191 -79 l154 154l212 -212l-154 -154q57 -93 80 -194h217v-297zM950 650q124 0 212 88t88 212t-88 212t-212 88t-212 -88t-88 -212t88 -212t212 -88z"},key:{viewbox:i,path:"M832 512q0-80-56-136t-136-56-136 56-56 136q0 42 19 83-41-19-83-19-80 0-136 56t-56 136 56 136 136 56 136-56 56-136q0-42-19-83 41 19 83 19 80 0 136-56t56-136zm851 704q0 17-49 66t-66 49q-9 0-28.5-16t-36.5-33-38.5-40-24.5-26l-96 96 220 220q28 28 28 68 0 42-39 81t-81 39q-40 0-68-28l-671-671q-176 131-365 131-163 0-265.5-102.5t-102.5-265.5q0-160 95-313t248-248 313-95q163 0 265.5 102.5t102.5 265.5q0 189-131 365l355 355 96-96q-3-3-26-24.5t-40-38.5-33-36.5-16-28.5q0-17 49-66t66-49q13 0 23 10 6 6 46 44.5t82 79.5 86.5 86 73 78 28.5 41z"},list:{viewbox:i,path:"M384 1408q0 80-56 136t-136 56-136-56-56-136 56-136 136-56 136 56 56 136zm0-512q0 80-56 136t-136 56-136-56-56-136 56-136 136-56 136 56 56 136zm1408 416v192q0 13-9.5 22.5t-22.5 9.5h-1216q-13 0-22.5-9.5t-9.5-22.5v-192q0-13 9.5-22.5t22.5-9.5h1216q13 0 22.5 9.5t9.5 22.5zm-1408-928q0 80-56 136t-136 56-136-56-56-136 56-136 136-56 136 56 56 136zm1408 416v192q0 13-9.5 22.5t-22.5 9.5h-1216q-13 0-22.5-9.5t-9.5-22.5v-192q0-13 9.5-22.5t22.5-9.5h1216q13 0 22.5 9.5t9.5 22.5zm0-512v192q0 13-9.5 22.5t-22.5 9.5h-1216q-13 0-22.5-9.5t-9.5-22.5v-192q0-13 9.5-22.5t22.5-9.5h1216q13 0 22.5 9.5t9.5 22.5z"},"loading-spinner":{viewbox:"0 0 66 66",CustomComponent:s,path:[o.default.createElement("circle",{key:"5",className:"path",fill:"none",strokeWidth:"6",strokeLinecap:"round",cx:"33",cy:"33",r:"30"})]},mobile:{viewbox:i,path:"M976 1408q0-33-23.5-56.5t-56.5-23.5-56.5 23.5-23.5 56.5 23.5 56.5 56.5 23.5 56.5-23.5 23.5-56.5zm208-160v-704q0-13-9.5-22.5t-22.5-9.5h-512q-13 0-22.5 9.5t-9.5 22.5v704q0 13 9.5 22.5t22.5 9.5h512q13 0 22.5-9.5t9.5-22.5zm-192-848q0-16-16-16h-160q-16 0-16 16t16 16h160q16 0 16-16zm288-16v1024q0 52-38 90t-90 38h-512q-52 0-90-38t-38-90v-1024q0-52 38-90t90-38h512q52 0 90 38t38 90z"},"pencil-square":{viewbox:i,path:"M888 1184l116-116-152-152-116 116v56h96v96h56zm440-720q-16-16-33 1l-350 350q-17 17-1 33t33-1l350-350q17-17 1-33zm80 594v190q0 119-84.5 203.5t-203.5 84.5h-832q-119 0-203.5-84.5t-84.5-203.5v-832q0-119 84.5-203.5t203.5-84.5h832q63 0 117 25 15 7 18 23 3 17-9 29l-49 49q-14 14-32 8-23-6-45-6h-832q-66 0-113 47t-47 113v832q0 66 47 113t113 47h832q66 0 113-47t47-113v-126q0-13 9-22l64-64q15-15 35-7t20 29zm-96-738l288 288-672 672h-288v-288zm444 132l-92 92-288-288 92-92q28-28 68-28t68 28l152 152q28 28 28 68t-28 68z"},plus:{viewbox:i,path:"M1600 736v192q0 40-28 68t-68 28h-416v416q0 40-28 68t-68 28h-192q-40 0-68-28t-28-68v-416h-416q-40 0-68-28t-28-68v-192q0-40 28-68t68-28h416v-416q0-40 28-68t68-28h192q40 0 68 28t28 68v416h416q40 0 68 28t28 68z"},"plus-circle":{viewbox:i,path:"M1344 960v-128q0-26-19-45t-45-19h-256v-256q0-26-19-45t-45-19h-128q-26 0-45 19t-19 45v256h-256q-26 0-45 19t-19 45v128q0 26 19 45t45 19h256v256q0 26 19 45t45 19h128q26 0 45-19t19-45v-256h256q26 0 45-19t19-45zm320-64q0 209-103 385.5t-279.5 279.5-385.5 103-385.5-103-279.5-279.5-103-385.5 103-385.5 279.5-279.5 385.5-103 385.5 103 279.5 279.5 103 385.5z"},"question-circle":{viewbox:i,path:"M1024 1376v-192q0-14-9-23t-23-9h-192q-14 0-23 9t-9 23v192q0 14 9 23t23 9h192q14 0 23-9t9-23zm256-672q0-88-55.5-163t-138.5-116-170-41q-243 0-371 213-15 24 8 42l132 100q7 6 19 6 16 0 25-12 53-68 86-92 34-24 86-24 48 0 85.5 26t37.5 59q0 38-20 61t-68 45q-63 28-115.5 86.5t-52.5 125.5v36q0 14 9 23t23 9h192q14 0 23-9t9-23q0-19 21.5-49.5t54.5-49.5q32-18 49-28.5t46-35 44.5-48 28-60.5 12.5-81zm384 192q0 209-103 385.5t-279.5 279.5-385.5 103-385.5-103-279.5-279.5-103-385.5 103-385.5 279.5-279.5 385.5-103 385.5 103 279.5 279.5 103 385.5z"},search:{viewbox:i,path:"M1216 832q0-185-131.5-316.5t-316.5-131.5-316.5 131.5-131.5 316.5 131.5 316.5 316.5 131.5 316.5-131.5 131.5-316.5zm512 832q0 52-38 90t-90 38q-54 0-90-38l-343-342q-179 124-399 124-143 0-273.5-55.5t-225-150-150-225-55.5-273.5 55.5-273.5 150-225 225-150 273.5-55.5 273.5 55.5 225 150 150 225 55.5 273.5q0 220-124 399l343 343q37 37 37 90z"},"seo-score-bad":{viewbox:"0 0 496 512",path:"M248 8C111 8 0 119 0 256s111 248 248 248s248-111 248-248S385 8 248 8z M328 176c17.7 0 32 14.3 32 32 s-14.3 32-32 32s-32-14.3-32-32S310.3 176 328 176z M168 176c17.7 0 32 14.3 32 32s-14.3 32-32 32s-32-14.3-32-32S150.3 176 168 176 z M338.2 394.2C315.8 367.4 282.9 352 248 352s-67.8 15.4-90.2 42.2c-13.5 16.3-38.1-4.2-24.6-20.5C161.7 339.6 203.6 320 248 320 s86.3 19.6 114.7 53.8C376.3 390 351.7 410.5 338.2 394.2L338.2 394.2z"},"seo-score-good":{viewbox:"0 0 496 512",path:"M248 8C111 8 0 119 0 256s111 248 248 248s248-111 248-248S385 8 248 8z M328 176c17.7 0 32 14.3 32 32 s-14.3 32-32 32s-32-14.3-32-32S310.3 176 328 176z M168 176c17.7 0 32 14.3 32 32s-14.3 32-32 32s-32-14.3-32-32S150.3 176 168 176 z M362.8 346.2C334.3 380.4 292.5 400 248 400s-86.3-19.6-114.8-53.8c-13.6-16.3 11-36.7 24.6-20.5c22.4 26.9 55.2 42.2 90.2 42.2 s67.8-15.4 90.2-42.2C351.6 309.5 376.3 329.9 362.8 346.2L362.8 346.2z"},"seo-score-none":{viewbox:"0 0 496 512",path:"M248 8C111 8 0 119 0 256s111 248 248 248s248-111 248-248S385 8 248 8z"},"seo-score-ok":{viewbox:"0 0 496 512",path:"M248 8c137 0 248 111 248 248S385 504 248 504S0 393 0 256S111 8 248 8z M360 208c0-17.7-14.3-32-32-32 s-32 14.3-32 32s14.3 32 32 32S360 225.7 360 208z M344 368c21.2 0 21.2-32 0-32H152c-21.2 0-21.2 32 0 32H344z M200 208 c0-17.7-14.3-32-32-32s-32 14.3-32 32s14.3 32 32 32S200 225.7 200 208z"},times:{viewbox:i,path:"M1490 1322q0 40-28 68l-136 136q-28 28-68 28t-68-28l-294-294-294 294q-28 28-68 28t-68-28l-136-136q-28-28-28-68t28-68l294-294-294-294q-28-28-28-68t28-68l136-136q28-28 68-28t68 28l294 294 294-294q28-28 68-28t68 28l136 136q28 28 28 68t-28 68l-294 294 294 294q28 28 28 68z"},"times-circle":{viewbox:"0 0 20 20",path:"M10 2c4.42 0 8 3.58 8 8s-3.58 8-8 8-8-3.58-8-8 3.58-8 8-8zm5 11l-3-3 3-3-2-2-3 3-3-3-2 2 3 3-3 3 2 2 3-3 3 3z"},"alert-info":{viewbox:"0 0 512 512",path:"M256 8C119.043 8 8 119.083 8 256c0 136.997 111.043 248 248 248s248-111.003 248-248C504 119.083 392.957 8 256 8zm0 110c23.196 0 42 18.804 42 42s-18.804 42-42 42-42-18.804-42-42 18.804-42 42-42zm56 254c0 6.627-5.373 12-12 12h-88c-6.627 0-12-5.373-12-12v-24c0-6.627 5.373-12 12-12h12v-64h-12c-6.627 0-12-5.373-12-12v-24c0-6.627 5.373-12 12-12h64c6.627 0 12 5.373 12 12v100h12c6.627 0 12 5.373 12 12v24z"},"alert-error":{viewbox:"0 0 512 512",path:"M256 8C119 8 8 119 8 256s111 248 248 248 248-111 248-248S393 8 256 8zm121.6 313.1c4.7 4.7 4.7 12.3 0 17L338 377.6c-4.7 4.7-12.3 4.7-17 0L256 312l-65.1 65.6c-4.7 4.7-12.3 4.7-17 0L134.4 338c-4.7-4.7-4.7-12.3 0-17l65.6-65-65.6-65.1c-4.7-4.7-4.7-12.3 0-17l39.6-39.6c4.7-4.7 12.3-4.7 17 0l65 65.7 65.1-65.6c4.7-4.7 12.3-4.7 17 0l39.6 39.6c4.7 4.7 4.7 12.3 0 17L312 256l65.6 65.1z"},"alert-success":{viewbox:"0 0 512 512",path:"M504 256c0 136.967-111.033 248-248 248S8 392.967 8 256 119.033 8 256 8s248 111.033 248 248zM227.314 387.314l184-184c6.248-6.248 6.248-16.379 0-22.627l-22.627-22.627c-6.248-6.249-16.379-6.249-22.628 0L216 308.118l-70.059-70.059c-6.248-6.248-16.379-6.248-22.628 0l-22.627 22.627c-6.248 6.248-6.248 16.379 0 22.627l104 104c6.249 6.249 16.379 6.249 22.628.001z"},"alert-warning":{viewbox:"0 0 576 512",path:"M569.517 440.013C587.975 472.007 564.806 512 527.94 512H48.054c-36.937 0-59.999-40.055-41.577-71.987L246.423 23.985c18.467-32.009 64.72-31.951 83.154 0l239.94 416.028zM288 354c-25.405 0-46 20.595-46 46s20.595 46 46 46 46-20.595 46-46-20.595-46-46-46zm-43.673-165.346l7.418 136c.347 6.364 5.609 11.346 11.982 11.346h48.546c6.373 0 11.635-4.982 11.982-11.346l7.418-136c.375-6.874-5.098-12.654-11.982-12.654h-63.383c-6.884 0-12.356 5.78-11.981 12.654z"},"chart-square-bar":{viewbox:"0 0 24 24",path:[o.default.createElement("path",{key:"1",fill:"#ffffff",stroke:"currentColor",strokeLinecap:"round",strokeLinejoin:"round",strokeWidth:"2",d:"M16 8v8m-4-5v5m-4-2v2m-2 4h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"})]}};t.default=(0,a.createSvgIconComponent)(u)},97485:(e,t,r)=>{"use strict";Object.defineProperty(t,"__esModule",{value:!0}),t.default=void 0;var o=d(r(99196)),n=d(r(85890)),a=d(r(98487)),l=r(3199),s=r(23613),i=r(82572),u=r(23695);function d(e){return e&&e.__esModule?e:{default:e}}function c(){return c=Object.assign?Object.assign.bind():function(e){for(var t=1;t<arguments.length;t++){var r=arguments[t];for(var o in r)Object.prototype.hasOwnProperty.call(r,o)&&(e[o]=r[o])}return e},c.apply(this,arguments)}const f=a.default.span`
	margin-bottom: 0.5em;
`,p=(0,a.default)(i.InputLabel)`
	display: inline-block;
	margin-bottom: 0;
	${(0,u.getDirectionalStyle)("margin-right: 4px","margin-left: 4px")};
`,h=e=>{const{label:t,helpLink:r,...n}=e;return o.default.createElement(l.InputContainer,null,o.default.createElement(f,null,o.default.createElement(p,{htmlFor:n.id},t),r),o.default.createElement(s.InputField,c({},n,{autoComplete:"off"})))};h.propTypes={type:n.default.string,id:n.default.string.isRequired,label:n.default.string,helpLink:n.default.node},h.defaultProps={type:"text",label:"",helpLink:null},t.default=h},51316:(e,t,r)=>{"use strict";Object.defineProperty(t,"__esModule",{value:!0}),t.default=void 0;var o=i(r(99196)),n=i(r(98487)),a=i(r(85890)),l=r(340),s=r(37188);function i(e){return e&&e.__esModule?e:{default:e}}const u=n.default.div`
	font-size: 1em;

	.react-tabs__tab-list {
		display: flex;
		flex-wrap: wrap;
		justify-content: center;
		list-style: none;
		padding: 0;
		margin: 0;
		border-bottom: 4px solid ${s.colors.$color_grey_light};
	}

	.react-tabs__tab {
		flex: 0 1 ${e=>e.tabsBaseWidth};
		text-align: center;
		margin: 0 16px;
		padding: 16px 0;
		cursor: pointer;
		font-family: "Open Sans", sans-serif;
		font-size: ${e=>e.tabsFontSize};
		line-height: 1.33333333;
		font-weight: ${e=>e.tabsFontWeight};
		color: ${e=>e.tabsTextColor};
		text-transform: ${e=>e.tabsTextTransform};

		&.react-tabs__tab--selected {
			box-shadow: 0 4px 0 0 ${s.colors.$color_pink_dark};
		}
	}

	.react-tabs__tab-panel {
		display: none;
		padding: 24px 40px;

		@media screen and ( max-width: ${s.breakpoints.mobile} ) {
			padding: 16px 16px;
		}

		:focus {
			outline: none;
		}

		&.react-tabs__tab-panel--selected {
			display: block;
		}
	}
`;u.propTypes={tabsTextColor:a.default.string,tabsTextTransform:a.default.string,tabsFontSize:a.default.string,tabsFontWeight:a.default.string,tabsBaseWidth:a.default.string};class d extends o.default.Component{getTabs(){return this.props.items.map((e=>o.default.createElement(l.Tab,{key:e.id},e.label)))}getTabPanels(){return this.props.items.map((e=>o.default.createElement(l.TabPanel,{key:e.id,tabIndex:"0"},e.content)))}render(){return o.default.createElement(u,{tabsTextColor:this.props.tabsTextColor,tabsTextTransform:this.props.tabsTextTransform,tabsFontSize:this.props.tabsFontSize,tabsFontWeight:this.props.tabsFontWeight,tabsBaseWidth:this.props.tabsBaseWidth},o.default.createElement(l.Tabs,{onSelect:this.props.onTabSelect},o.default.createElement(l.TabList,null,this.getTabs()),this.getTabPanels()))}componentDidMount(){this.props.onTabsMounted()}}d.propTypes={items:a.default.arrayOf(a.default.shape({id:a.default.string.isRequired,label:a.default.string.isRequired,content:a.default.object.isRequired})),tabsTextColor:a.default.string,tabsTextTransform:a.default.string,tabsFontSize:a.default.string,tabsFontWeight:a.default.string,tabsBaseWidth:a.default.string,onTabSelect:a.default.func,onTabsMounted:a.default.func},d.defaultProps={items:[],tabsTextColor:s.colors.$color_grey_dark,tabsTextTransform:"none",tabsFontSize:"1.5em",tabsFontWeight:"200",tabsBaseWidth:"200px",onTabSelect:()=>{},onTabsMounted:()=>{}},t.default=d},22386:(e,t,r)=>{"use strict";Object.defineProperty(t,"__esModule",{value:!0}),t.default=void 0;var o=a(r(99196)),n=a(r(85890));function a(e){return e&&e.__esModule?e:{default:e}}function l(){return l=Object.assign?Object.assign.bind():function(e){for(var t=1;t<arguments.length;t++){var r=arguments[t];for(var o in r)Object.prototype.hasOwnProperty.call(r,o)&&(e[o]=r[o])}return e},l.apply(this,arguments)}class s extends o.default.Component{constructor(e){super(e),this.setReference=this.setReference.bind(this)}render(){return o.default.createElement("textarea",l({ref:this.setReference,name:this.props.name,value:this.props.value,onChange:this.props.onChange},this.props.optionalAttributes))}setReference(e){this.ref=e}componentDidUpdate(){this.props.hasFocus&&this.ref.focus()}}s.propTypes={name:n.default.string,value:n.default.string,onChange:n.default.func,optionalAttributes:n.default.object,hasFocus:n.default.bool},s.defaultProps={name:"textarea",value:"",hasFocus:!1,onChange:null,optionalAttributes:{}},t.default=s},18547:(e,t,r)=>{"use strict";Object.defineProperty(t,"__esModule",{value:!0}),t.default=void 0;var o=i(r(99196)),n=i(r(85890)),a=i(r(1453)),l=i(r(66763)),s=i(r(22386));function i(e){return e&&e.__esModule?e:{default:e}}class u extends o.default.Component{constructor(e){super(e),this.optionalAttributes=this.parseOptionalAttributes()}render(){return this.optionalAttributes=this.parseOptionalAttributes(),this.props.class&&(this.optionalAttributes.container.className=this.props.class),o.default.createElement("div",this.optionalAttributes.container,o.default.createElement(a.default,{for:this.props.name,optionalAttributes:this.optionalAttributes.label},this.props.label),this.getTextField())}getTextField(){return!0===this.props.multiline?o.default.createElement("div",null,o.default.createElement(s.default,{name:this.props.name,id:this.props.name,onChange:this.props.onChange,optionalAttributes:this.optionalAttributes.field,hasFocus:this.props.hasFocus,value:this.props.value}),this.props.explanation&&o.default.createElement("p",null,this.props.explanation)):o.default.createElement("div",null,o.default.createElement(l.default,{name:this.props.name,id:this.props.name,type:"text",onChange:this.props.onChange,value:this.props.value,hasFocus:this.props.hasFocus,autoComplete:this.props.autoComplete,optionalAttributes:this.optionalAttributes.field}),this.props.explanation&&o.default.createElement("p",null,this.props.explanation))}parseOptionalAttributes(){const e={},t={},r={id:this.props.name};return Object.keys(this.props).forEach(function(o){o.startsWith("label-")&&(t[o.split("-").pop()]=this.props[o]),o.startsWith("field-")&&(r[o.split("-").pop()]=this.props[o]),o.startsWith("container-")&&(e[o.split("-").pop()]=this.props[o])}.bind(this)),{label:t,field:r,container:e}}}u.propTypes={label:n.default.string.isRequired,name:n.default.string.isRequired,onChange:n.default.func.isRequired,value:n.default.string,optionalAttributes:n.default.object,multiline:n.default.bool,hasFocus:n.default.bool,class:n.default.string,explanation:n.default.string,autoComplete:n.default.string},u.defaultProps={optionalAttributes:{},multiline:!1,hasFocus:!1,value:null,class:null,explanation:!1,autoComplete:null},t.default=u},47816:(e,t,r)=>{"use strict";Object.defineProperty(t,"__esModule",{value:!0}),t.default=void 0;var o=u(r(85890)),n=u(r(99196)),a=u(r(98487)),l=r(65736),s=r(23695),i=r(37188);function u(e){return e&&e.__esModule?e:{default:e}}const d=a.default.div`
	display: flex;
	width: 100%;
	justify-content: space-between;
	align-items: center;
	position: relative;
`,c=a.default.span`
	${(0,s.getDirectionalStyle)("margin-right","margin-left")}: 16px;
	flex: 1;
	cursor: pointer;
`,f=a.default.div`
	background-color: ${e=>e.isEnabled?"#a5d6a7":i.colors.$color_button_border};
	border-radius: 7px;
	height: 14px;
	width: 30px;
	cursor: pointer;
	margin: 0;
	outline: 0;
	&:focus > span {
		box-shadow: inset 0 0 0 1px ${i.colors.$color_white}, 0 0 0 1px #5b9dd9, 0 0 2px 1px rgba(30, 140, 190, .8);
	}
`,p=a.default.span`
	background-color: ${e=>e.isEnabled?i.colors.$color_green_medium_light:i.colors.$color_grey_medium_dark};
	${e=>e.isEnabled?(0,s.getDirectionalStyle)("margin-left: 12px;","margin-right: 12px;"):(0,s.getDirectionalStyle)("margin-left: -2px;","margin-right: -2px;")};
	box-shadow: 0 2px 2px 2px rgba(0, 0, 0, 0.1);
	border-radius: 100%;
	height: 20px;
	width: 20px;
	position: absolute;
	margin-top: -3px;
`,h=a.default.span`
	font-size: 14px;
	line-height: 20px;
	${(0,s.getDirectionalStyle)("margin-left","margin-right")}: 8px;
	font-style: italic;
`;class b extends n.default.Component{constructor(e){super(e),this.onClick=this.props.onToggleDisabled,this.onKeyUp=this.props.onToggleDisabled,this.setToggleState=this.setToggleState.bind(this),this.handleOnKeyDown=this.handleOnKeyDown.bind(this),!0!==e.disable&&(this.onClick=this.setToggleState.bind(this),this.onKeyUp=this.setToggleState.bind(this))}setToggleState(e){"keyup"===e.type&&32!==e.keyCode||this.props.onSetToggleState(!this.props.isEnabled)}handleOnKeyDown(e){32===e.keyCode&&e.preventDefault()}render(){return n.default.createElement(d,null,this.props.labelText&&n.default.createElement(c,{id:this.props.id,onClick:this.onClick},this.props.labelText),n.default.createElement(f,{isEnabled:this.props.isEnabled,onKeyDown:this.handleOnKeyDown,onClick:this.onClick,onKeyUp:this.onKeyUp,tabIndex:"0",role:"checkbox","aria-labelledby":this.props.id,"aria-checked":this.props.isEnabled,"aria-disabled":this.props.disable},n.default.createElement(p,{isEnabled:this.props.isEnabled})),this.props.showToggleStateLabel&&n.default.createElement(h,{"aria-hidden":"true"},this.props.isEnabled?(0,l.__)("On","wordpress-seo"):(0,l.__)("Off","wordpress-seo")))}}b.propTypes={isEnabled:o.default.bool,onSetToggleState:o.default.func,disable:o.default.bool,onToggleDisabled:o.default.func,id:o.default.string.isRequired,labelText:o.default.string,showToggleStateLabel:o.default.bool},b.defaultProps={isEnabled:!1,onSetToggleState:()=>{},labelText:"",disable:!1,onToggleDisabled:()=>{},showToggleStateLabel:!0},t.default=b},1382:(e,t,r)=>{"use strict";Object.defineProperty(t,"__esModule",{value:!0}),t.default=void 0;var o=d(r(66366)),n=d(r(85890)),a=d(r(99196)),l=d(r(98487)),s=r(37188),i=r(23695),u=d(r(78386));function d(e){return e&&e.__esModule?e:{default:e}}const c=l.default.div`
	display: flex;
	padding: 16px;
	background: ${s.colors.$color_alert_warning_background};
	color: ${s.colors.$color_alert_warning_text};
`,f=(0,l.default)(u.default)`
	margin-top: 2px;
`,p=l.default.div`
	margin: ${(0,i.getDirectionalStyle)("0 0 0 8px","0 8px 0 0")};
`;class h extends a.default.Component{render(){const{message:e}=this.props;return(0,o.default)(e)?null:a.default.createElement(c,null,a.default.createElement(f,{icon:"exclamation-triangle",size:"16px"}),a.default.createElement(p,null,e))}}h.propTypes={message:n.default.array},h.defaultProps={message:[]},t.default=h},64385:(e,t,r)=>{"use strict";Object.defineProperty(t,"__esModule",{value:!0}),t.default=void 0;var o=a(r(99196)),n=a(r(85890));function a(e){return e&&e.__esModule?e:{default:e}}const l=e=>{console.warn("The WordList component has been deprecated and will be removed in a future release.");const{title:t,classNamePrefix:r,words:n,header:a,footer:l}=e,s=o.default.createElement("ol",{className:r+"__list"},n.map((e=>o.default.createElement("li",{key:e,className:r+"__item"},e))));return o.default.createElement("div",{className:r},o.default.createElement("p",null,o.default.createElement("strong",null,t)),a,s,l)};l.propTypes={words:n.default.array.isRequired,title:n.default.string.isRequired,header:n.default.string,footer:n.default.string,classNamePrefix:n.default.string},l.defaultProps={classNamePrefix:"",header:"",footer:""},t.default=l},1993:(e,t,r)=>{"use strict";Object.defineProperty(t,"__esModule",{value:!0}),t.default=void 0;var o=i(r(99196)),n=i(r(85890)),a=i(r(96746)),l=r(65736),s=i(r(31367));function i(e){return e&&e.__esModule?e:{default:e}}const u=({words:e,researchArticleLink:t})=>{const r=o.default.createElement("p",{className:"yoast-field-group__title"},(0,l.__)("Prominent words","wordpress-seo")),n=o.default.createElement("p",null,0===e.length?(0,l.__)("Once you add a bit more copy, we'll give you a list of words that occur the most in the content. These give an indication of what your content focuses on.","wordpress-seo"):(0,l.__)("The following words occur the most in the content. These give an indication of what your content focuses on. If the words differ a lot from your topic, you might want to rewrite your content accordingly. ","wordpress-seo")),i=o.default.createElement("p",null,(e=>{const t=(0,l.sprintf)((0,l.__)("Read our %1$sultimate guide to keyword research%2$s to learn more about keyword research and keyword strategy.","wordpress-seo"),"{{a}}","{{/a}}");return(0,a.default)({mixedString:t,components:{a:o.default.createElement("a",{href:e,target:"_blank",rel:"noreferrer"})}})})(t));return o.default.createElement(s.default,{words:e,header:r,introduction:n,footer:i})};u.propTypes={words:n.default.arrayOf(n.default.object).isRequired,researchArticleLink:n.default.string.isRequired},t.default=u},31367:(e,t,r)=>{"use strict";Object.defineProperty(t,"__esModule",{value:!0}),t.default=void 0;var o=l(r(99196)),n=l(r(85890)),a=l(r(74643));function l(e){return e&&e.__esModule?e:{default:e}}class s extends o.default.Component{constructor(e){super(e),this.state={words:[]}}static getDerivedStateFromProps(e){const t=[...e.words];t.sort(((e,t)=>t.getOccurrences()-e.getOccurrences()));const r=t.map((e=>e.getOccurrences())),o=Math.max(...r);return{words:t.map((e=>{const t=e.getOccurrences();return{name:e.getWord(),number:t,width:t/o*100}}))}}render(){return o.default.createElement("div",null,this.props.header,this.props.introduction,o.default.createElement(a.default,{items:this.state.words}),this.props.footer)}}s.propTypes={words:n.default.array.isRequired,header:n.default.element,introduction:n.default.element,footer:n.default.element},s.defaultProps={header:null,introduction:null,footer:null},t.default=s},14085:(e,t,r)=>{"use strict";Object.defineProperty(t,"__esModule",{value:!0}),t.default=void 0;var o,n=(o=r(99196))&&o.__esModule?o:{default:o};function a(){return a=Object.assign?Object.assign.bind():function(e){for(var t=1;t<arguments.length;t++){var r=arguments[t];for(var o in r)Object.prototype.hasOwnProperty.call(r,o)&&(e[o]=r[o])}return e},a.apply(this,arguments)}t.default=e=>n.default.createElement("svg",a({},e,{xmlns:"http://www.w3.org/2000/svg",viewBox:"0 0 500 500"}),n.default.createElement("path",{d:"M80,0H420a80,80,0,0,1,80,80V500a0,0,0,0,1,0,0H80A80,80,0,0,1,0,420V80A80,80,0,0,1,80,0Z",fill:"#a4286a"}),n.default.createElement("path",{d:"M437.61,2,155.89,500H500V80A80,80,0,0,0,437.61,2Z",fill:"#6c2548"}),n.default.createElement("path",{d:"M74.4,337.3v34.9c21.6-.9,38.5-8,52.8-22.5s27.4-38,39.9-72.9l92.6-248H214.9L140.3, 236l-37-116.2h-41l54.4,139.8a57.54,57.54,0,0,1,0,41.8C111.2,315.6,101.3,332.3,74.4,337.3Z",fill:"#fff"}),n.default.createElement("circle",{cx:"368.33",cy:"124.68",r:"97.34",transform:"translate(19.72 296.97) rotate(-45)",fill:"#9fda4f"}),n.default.createElement("path",{d:"M416.2,39.93,320.46,209.44A97.34,97.34,0,1,0,416.2,39.93Z",fill:"#77b227"}),n.default.createElement("path",{d:"M294.78,254.75h0l-.15-.08-.13-.07h0a63.6,63.6,0,0,0-62.56,110.76h0l.07,0,.06,0h0a63.6,63.6,0,0,0,62.71-110.67Z",fill:"#fec228"}),n.default.createElement("path",{d:"M294.5,254.59,231.94,365.35A63.6,63.6,0,1,0,294.5,254.59Z",fill:"#f49a00"}),n.default.createElement("path",{d:"M222.31,450.07A38.16,38.16,0,0,0,203,416.83h0l0,0h0a38.18,38.18,0,1,0,19.41,33.27Z",fill:"#ff4e47"}),n.default.createElement("path",{d:"M202.9,416.8l-37.54,66.48A38.17,38.17,0,0,0,202.9,416.8Z",fill:"#ed261f"}))},72768:(e,t,r)=>{"use strict";Object.defineProperty(t,"__esModule",{value:!0}),t.default=u;var o=s(r(99196)),n=s(r(85890)),a=s(r(98487)),l=s(r(77844));function s(e){return e&&e.__esModule?e:{default:e}}const i=a.default.div`
	position: relative;
	padding-bottom: 56.25%; /* 16:9 */
	height: 0;
	overflow: hidden;

	iframe {
		position: absolute;
		top: 0;
		left: 0;
		width: 100%;
		height: 100%;
	}
`;function u(e){return o.default.createElement(i,null,o.default.createElement(l.default,e))}u.propTypes={width:n.default.number,height:n.default.number,src:n.default.string.isRequired,title:n.default.string.isRequired,frameBorder:n.default.number,allowFullScreen:n.default.bool},u.defaultProps={width:560,height:315,frameBorder:0,allowFullScreen:!0}},76916:(e,t,r)=>{"use strict";Object.defineProperty(t,"__esModule",{value:!0}),t.default=void 0;var o=l(r(99196)),n=l(r(85890)),a=l(r(43905));function l(e){return e&&e.__esModule?e:{default:e}}class s extends o.default.Component{constructor(){super(),this.focus=this.focus.bind(this),this.blur=this.blur.bind(this),this.state={focused:!1}}focus(){this.setState({focused:!0})}blur(){this.setState({focused:!1})}getStyles(){return!0===this.state.focused?a.default.ScreenReaderText.focused:a.default.ScreenReaderText.default}render(){return o.default.createElement("a",{href:"#"+this.props.anchor,className:"screen-reader-shortcut",style:this.getStyles(),onFocus:this.focus,onBlur:this.blur},this.props.children)}}s.propTypes={anchor:n.default.string.isRequired,children:n.default.string.isRequired},t.default=s},42479:(e,t,r)=>{"use strict";Object.defineProperty(t,"__esModule",{value:!0}),t.default=void 0;var o=l(r(99196)),n=l(r(85890)),a=l(r(43905));function l(e){return e&&e.__esModule?e:{default:e}}const s=e=>o.default.createElement("span",{className:"screen-reader-text",style:a.default.ScreenReaderText.default},e.children);s.propTypes={children:n.default.string.isRequired},t.default=s},43905:(e,t)=>{"use strict";Object.defineProperty(t,"__esModule",{value:!0}),t.default=void 0,t.default={ScreenReaderText:{default:{clip:"rect(1px, 1px, 1px, 1px)",position:"absolute",height:"1px",width:"1px",overflow:"hidden"},focused:{clip:"auto",display:"block",left:"5px",top:"5px",height:"auto",width:"auto",zIndex:"100000",position:"absolute",backgroundColor:"#eeeeee ",padding:"10px"}}}},75377:(e,t,r)=>{"use strict";r(89364),r(26242),r(91370),r(68798),r(16798)},41834:(e,t,r)=>{"use strict";Object.defineProperty(t,"__esModule",{value:!0}),t.default=void 0;var o,n=(o=r(99196))&&o.__esModule?o:{default:o},a=r(65736);t.default=()=>n.default.createElement("span",{className:"yoast-badge yoast-beta-badge"},(0,a.__)("Beta","wordpress-seo"))},40815:(e,t,r)=>{"use strict";Object.defineProperty(t,"__esModule",{value:!0}),Object.defineProperty(t,"BetaBadge",{enumerable:!0,get:function(){return n.default}}),r(15446),r(54760);var o,n=(o=r(41834))&&o.__esModule?o:{default:o}},22490:(e,t,r)=>{"use strict";Object.defineProperty(t,"__esModule",{value:!0}),t.ButtonStyledLink=t.Button=void 0;var o=a(r(99196)),n=a(r(85890));function a(e){return e&&e.__esModule?e:{default:e}}function l(){return l=Object.assign?Object.assign.bind():function(e){for(var t=1;t<arguments.length;t++){var r=arguments[t];for(var o in r)Object.prototype.hasOwnProperty.call(r,o)&&(e[o]=r[o])}return e},l.apply(this,arguments)}r(40367);const s="yoast-button yoast-button--",i={buy:{iconAfter:"yoast-button--buy__caret"},edit:{iconBefore:"yoast-button--edit"},upsell:{iconAfter:"yoast-button--buy__caret"}},u={primary:s+"primary",secondary:s+"secondary",buy:s+"buy",hide:"yoast-hide",remove:"yoast-remove",upsell:s+"buy",purple:s+"primary",grey:s+"secondary",yellow:s+"buy",edit:s+"primary"},d=(e,t)=>{let r=u[e];return t&&(r+=" yoast-button--small"),r},c=e=>i[e]||null,f=e=>{const{children:t,className:r,variant:n,small:a,type:s,buttonRef:i,...u}=e,f=c(n),p=f&&f.iconBefore,h=f&&f.iconAfter;return o.default.createElement("button",l({ref:i,className:r||d(n,a),type:s},u),!!p&&o.default.createElement("span",{className:p}),t,!!h&&o.default.createElement("span",{className:h}))};t.Button=f,f.propTypes={onClick:n.default.func,type:n.default.string,className:n.default.string,buttonRef:n.default.object,small:n.default.bool,variant:n.default.oneOf(Object.keys(u)),children:n.default.oneOfType([n.default.node,n.default.arrayOf(n.default.node)])},f.defaultProps={className:"",type:"button",variant:"primary",small:!1,children:null,onClick:null,buttonRef:null};const p=e=>{const{children:t,className:r,variant:n,small:a,buttonRef:s,...i}=e,u=c(n),f=u&&u.iconBefore,p=u&&u.iconAfter;return o.default.createElement("a",l({className:r||d(n,a),ref:s},i),!!f&&o.default.createElement("span",{className:f}),t,!!p&&o.default.createElement("span",{className:p}))};t.ButtonStyledLink=p,p.propTypes={href:n.default.string.isRequired,variant:n.default.oneOf(Object.keys(u)),small:n.default.bool,className:n.default.string,buttonRef:n.default.object,children:n.default.oneOfType([n.default.node,n.default.arrayOf(n.default.node)])},p.defaultProps={className:"",variant:"primary",small:!1,children:null,buttonRef:null}},13537:(e,t,r)=>{"use strict";Object.defineProperty(t,"__esModule",{value:!0}),t.CloseButton=void 0;var o=l(r(99196)),n=l(r(85890));r(40367);var a=r(65736);function l(e){return e&&e.__esModule?e:{default:e}}function s(){return s=Object.assign?Object.assign.bind():function(e){for(var t=1;t<arguments.length;t++){var r=arguments[t];for(var o in r)Object.prototype.hasOwnProperty.call(r,o)&&(e[o]=r[o])}return e},s.apply(this,arguments)}const i=o.default.createElement("svg",{xmlns:"http://www.w3.org/2000/svg",viewBox:"0 0 352 512",role:"img","aria-hidden":"true",focusable:"false"},o.default.createElement("path",{d:"M242.72 256l100.07-100.07c12.28-12.28 12.28-32.19 0-44.48l-22.24-22.24c-12.28-12.28-32.19-12.28-44.48 0L176 189.28 75.93 89.21c-12.28-12.28-32.19-12.28-44.48 0L9.21 111.45c-12.28 12.28-12.28 32.19 0 44.48L109.28 256 9.21 356.07c-12.28 12.28-12.28 32.19 0 44.48l22.24 22.24c12.28 12.28 32.2 12.28 44.48 0L176 322.72l100.07 100.07c12.28 12.28 32.2 12.28 44.48 0l22.24-22.24c12.28-12.28 12.28-32.19 0-44.48L242.72 256z"})),u=e=>o.default.createElement("button",s({className:"yoast-close","aria-label":(0,a.__)("Close","wordpress-seo"),type:"button"},e),i);t.CloseButton=u,u.propTypes={onClick:n.default.func.isRequired}},76149:(e,t,r)=>{"use strict";Object.defineProperty(t,"__esModule",{value:!0}),Object.defineProperty(t,"ButtonStyledLink",{enumerable:!0,get:function(){return o.ButtonStyledLink}}),Object.defineProperty(t,"CloseButton",{enumerable:!0,get:function(){return n.CloseButton}}),Object.defineProperty(t,"NewButton",{enumerable:!0,get:function(){return o.Button}}),r(40367);var o=r(22490),n=r(13537)},46362:(e,t,r)=>{"use strict";Object.defineProperty(t,"__esModule",{value:!0}),t.BaseButton=void 0,t.addActiveStyle=h,t.addBaseStyle=c,t.addButtonStyles=void 0,t.addFocusStyle=f,t.addHoverStyle=p,t.default=void 0;var o=i(r(98487)),n=i(r(57349)),a=i(r(85890)),l=r(37188),s=r(23695);function i(e){return e&&e.__esModule?e:{default:e}}const u={minHeight:32,verticalPadding:4,borderWidth:1},d=u.minHeight-2*u.verticalPadding-2*u.borderWidth;function c(e){return(0,o.default)(e)`
		display: inline-flex;
		align-items: center;
		justify-content: center;
		vertical-align: middle;
		border-width: ${`${u.borderWidth}px`};
		border-style: solid;
		margin: 0;
		padding: ${`${u.verticalPadding}px`} 10px;
		border-radius: 3px;
		cursor: pointer;
		box-sizing: border-box;
		font-size: inherit;
		font-family: inherit;
		font-weight: inherit;
		text-align: ${(0,s.getDirectionalStyle)("left","right")};
		overflow: visible;
		min-height: ${`${u.minHeight}px`};
		transition: var(--yoast-transition-default);

		svg {
			// Safari 10
			align-self: center;
		}

		// Only needed for IE 10+. Don't add spaces within brackets for this to work.
		@media all and (-ms-high-contrast: none), (-ms-high-contrast: active) {
			::after {
				display: inline-block;
				content: "";
				min-height: ${`${d}px`};
			}
		}
	`}function f(e){return(0,o.default)(e)`
		&::-moz-focus-inner {
			border-width: 0;
		}

		&:focus {
			outline: none;
			border-color: ${e=>e.focusBorderColor};
			color: ${e=>e.focusColor};
			background-color: ${e=>e.focusBackgroundColor};
			box-shadow: 0 0 3px ${e=>(0,l.rgba)(e.focusBoxShadowColor,.8)}
		}
	`}function p(e){return(0,o.default)(e)`
		&:hover {
			color: ${e=>e.hoverColor};
			background-color: ${e=>e.hoverBackgroundColor};
			border-color: var(--yoast-color-border--default);
		}
	`}function h(e){return(0,o.default)(e)`
		&:active {
			color: ${e=>e.activeColor};
			background-color: ${e=>e.activeBackgroundColor};
			border-color: ${e=>e.hoverBorderColor};
			box-shadow: inset 0 2px 5px -3px ${e=>(0,l.rgba)(e.activeBorderColor,.5)}
		}
	`}const b=t.addButtonStyles=(0,n.default)([h,f,p,c]),g=t.BaseButton=b(o.default.button`
		color: ${e=>e.textColor};
		border-color: ${e=>e.borderColor};
		background: ${e=>e.backgroundColor};
		box-shadow: 0 1px 0 ${e=>(0,l.rgba)(e.boxShadowColor,1)};
	`);g.propTypes={type:a.default.string,backgroundColor:a.default.string,textColor:a.default.string,borderColor:a.default.string,boxShadowColor:a.default.string,hoverColor:a.default.string,hoverBackgroundColor:a.default.string,activeColor:a.default.string,activeBackgroundColor:a.default.string,activeBorderColor:a.default.string,focusColor:a.default.string,focusBackgroundColor:a.default.string,focusBorderColor:a.default.string,focusBoxShadowColor:a.default.string},g.defaultProps={type:"button",backgroundColor:l.colors.$color_button,textColor:l.colors.$color_button_text,borderColor:l.colors.$color_button_border,boxShadowColor:l.colors.$color_button_border,hoverColor:l.colors.$color_button_text_hover,hoverBackgroundColor:l.colors.$color_button_hover,activeColor:l.colors.$color_button_text_hover,activeBackgroundColor:l.colors.$color_button,activeBorderColor:l.colors.$color_button_border_active,focusColor:l.colors.$color_button_text_hover,focusBackgroundColor:l.colors.$color_white,focusBorderColor:l.colors.$color_blue,focusBoxShadowColor:l.colors.$color_blue_dark},t.default=g},4532:(e,t,r)=>{"use strict";Object.defineProperty(t,"__esModule",{value:!0}),t.default=void 0;var o=d(r(99196)),n=d(r(85890)),a=d(r(98487)),l=d(r(16653)),s=r(23695),i=d(r(46362)),u=r(95235);function d(e){return e&&e.__esModule?e:{default:e}}const c=e=>{const{children:t,icon:r,iconColor:n}=e;let d=u.SvgIcon;t&&(d=function(e){return(0,a.default)(e)`
		margin: ${(0,s.getDirectionalStyle)("0 8px 0 0","0 0 0 8px")};
		flex-shrink: 0;
	`}(d));const c=(0,l.default)(e,"icon");return o.default.createElement(i.default,c,o.default.createElement(d,{icon:r,color:n}),t)};c.propTypes={icon:n.default.string.isRequired,iconColor:n.default.string,children:n.default.oneOfType([n.default.arrayOf(n.default.node),n.default.node,n.default.string])},c.defaultProps={iconColor:"#000"},t.default=c},3234:(e,t,r)=>{"use strict";Object.defineProperty(t,"__esModule",{value:!0}),t.default=void 0;var o=c(r(99196)),n=c(r(98487)),a=c(r(85890)),l=c(r(57349)),s=c(r(16653)),i=r(37188),u=c(r(78386)),d=r(46362);function c(e){return e&&e.__esModule?e:{default:e}}const f=(0,l.default)([d.addActiveStyle,d.addFocusStyle,d.addHoverStyle])(n.default.button`
		display: inline-flex;
		flex-direction: column;
		justify-content: center;
		align-items: center;
		cursor: pointer;
		box-sizing: border-box;
		border: 1px solid transparent;
		margin: 0;
		padding: 8px;
		overflow: visible;
		font-family: inherit;
		font-weight: inherit;
		color: ${e=>e.textColor};
		background: ${e=>e.backgroundColor};
		font-size: ${e=>e.textFontSize};

		svg {
			margin: 0 0 8px;
			flex-shrink: 0;
			fill: currentColor;
			// Safari 10
			align-self: center;
		}

		&:active {
			box-shadow: none;
		}
	`),p=e=>{const{children:t,icon:r,textColor:n}=e,a=(0,s.default)(e,"icon");return o.default.createElement(f,a,o.default.createElement(u.default,{icon:r,color:n}),t)};p.propTypes={type:a.default.string,icon:a.default.string.isRequired,textColor:a.default.string,textFontSize:a.default.string,backgroundColor:a.default.string,borderColor:a.default.string,hoverColor:a.default.string,hoverBackgroundColor:a.default.string,hoverBorderColor:a.default.string,activeColor:a.default.string,activeBackgroundColor:a.default.string,activeBorderColor:a.default.string,focusColor:a.default.string,focusBackgroundColor:a.default.string,focusBorderColor:a.default.string,focusBoxShadowColor:a.default.string,children:a.default.oneOfType([a.default.arrayOf(a.default.node),a.default.node,a.default.string]).isRequired},p.defaultProps={type:"button",textColor:i.colors.$color_blue,textFontSize:"inherit",backgroundColor:"transparent",borderColor:"transparent",hoverColor:i.colors.$color_white,hoverBackgroundColor:i.colors.$color_blue,hoverBorderColor:i.colors.$color_button_border_hover,activeColor:i.colors.$color_white,activeBackgroundColor:i.colors.$color_blue,activeBorderColor:i.colors.$color_button_border_active,focusColor:i.colors.$color_white,focusBackgroundColor:i.colors.$color_blue,focusBorderColor:i.colors.$color_blue,focusBoxShadowColor:i.colors.$color_blue_dark},t.default=p},71875:(e,t,r)=>{"use strict";Object.defineProperty(t,"__esModule",{value:!0}),t.default=void 0;var o=s(r(99196)),n=s(r(85890)),a=s(r(46362)),l=r(95235);function s(e){return e&&e.__esModule?e:{default:e}}function i(){return i=Object.assign?Object.assign.bind():function(e){for(var t=1;t<arguments.length;t++){var r=arguments[t];for(var o in r)Object.prototype.hasOwnProperty.call(r,o)&&(e[o]=r[o])}return e},i.apply(this,arguments)}const u=e=>{const{children:t,className:r,prefixIcon:n,suffixIcon:s,...u}=e;return o.default.createElement(a.default,i({className:r},u),n&&n.icon&&o.default.createElement(l.SvgIcon,{icon:n.icon,color:n.color,size:n.size}),t,s&&s.icon&&o.default.createElement(l.SvgIcon,{icon:s.icon,color:s.color,size:s.size}))};u.propTypes={className:n.default.string,prefixIcon:n.default.shape({icon:n.default.string,color:n.default.string,size:n.default.string}),suffixIcon:n.default.shape({icon:n.default.string,color:n.default.string,size:n.default.string}),children:n.default.oneOfType([n.default.arrayOf(n.default.node),n.default.node,n.default.string])},t.default=u},16785:(e,t,r)=>{"use strict";Object.defineProperty(t,"__esModule",{value:!0}),t.LinkButton=void 0;var o=s(r(98487)),n=s(r(85890)),a=r(37188),l=r(46362);function s(e){return e&&e.__esModule?e:{default:e}}const i=t.LinkButton=(0,l.addButtonStyles)(o.default.a`
		text-decoration: none;
		color: ${e=>e.textColor};
		border-color: ${e=>e.borderColor};
		background: ${e=>e.backgroundColor};
		box-shadow: 0 1px 0 ${e=>(0,a.rgba)(e.boxShadowColor,1)};
	`);i.propTypes={backgroundColor:n.default.string,textColor:n.default.string,borderColor:n.default.string,boxShadowColor:n.default.string,hoverColor:n.default.string,hoverBackgroundColor:n.default.string,hoverBorderColor:n.default.string,activeColor:n.default.string,activeBackgroundColor:n.default.string,activeBorderColor:n.default.string,focusColor:n.default.string,focusBackgroundColor:n.default.string,focusBorderColor:n.default.string,focusBoxShadowColor:n.default.string},i.defaultProps={backgroundColor:a.colors.$color_button,textColor:a.colors.$color_button_text,borderColor:a.colors.$color_button_border,boxShadowColor:a.colors.$color_button_border,hoverColor:a.colors.$color_button_text_hover,hoverBackgroundColor:a.colors.$color_button_hover,hoverBorderColor:a.colors.$color_button_border_hover,activeColor:a.colors.$color_button_text_hover,activeBackgroundColor:a.colors.$color_button,activeBorderColor:a.colors.$color_button_border_hover,focusColor:a.colors.$color_button_text_hover,focusBackgroundColor:a.colors.$color_white,focusBorderColor:a.colors.$color_blue,focusBoxShadowColor:a.colors.$color_blue_dark}},60813:(e,t,r)=>{"use strict";Object.defineProperty(t,"__esModule",{value:!0}),t.UpsellButtonBase=t.UpsellButton=void 0,t.addButtonStyles=p;var o=u(r(99196)),n=u(r(85890)),a=u(r(98487)),l=r(37188),s=u(r(78386)),i=r(83908);function u(e){return e&&e.__esModule?e:{default:e}}const d={minHeight:48,verticalPadding:8,borderWidth:0},c=d.minHeight-2*d.verticalPadding-2*d.borderWidth,f=(0,a.default)(s.default)`
		margin: 2px 4px 0 4px;
		flex-shrink: 0;
`;function p(e){return(0,a.default)(e)`
		display: inline-flex;
		align-items: center;
		justify-content: center;
		vertical-align: middle;
		min-height: ${`${d.minHeight}px`};
		margin: 0;
		overflow: auto;
		min-width: 152px;
		padding: 0 16px;
		padding: ${`${d.verticalPadding}px`} 8px ${`${d.verticalPadding}px`} 16px;
		border: 0;
		border-radius: 4px;
		box-sizing: border-box;
		font: 400 16px/24px "Open Sans", sans-serif;
		box-shadow: inset 0 -4px 0 rgba(0, 0, 0, 0.2);
		filter: drop-shadow(0 2px 4px rgba(0, 0, 0, 0.2));
		transition: box-shadow 150ms ease-out;

		&:hover,
		&:focus,
		&:active {
			background: ${l.colors.$color_button_upsell_hover};
		}

		&:active {
			transform: translateY( 1px );
			box-shadow: none;
			filter: none;
		}

		// Only needed for IE 10+. Don't add spaces within brackets for this to work.
		@media all and (-ms-high-contrast: none), (-ms-high-contrast: active) {
			::after {
				display: inline-block;
				content: "";
				min-height: ${`${c}px`};
			}
		}
	`}const h=t.UpsellButtonBase=p((0,a.default)(i.YoastButtonBase)`
		color: ${e=>e.textColor};
		background: ${e=>e.backgroundColor};
		overflow: visible;
		cursor: pointer;

		&::-moz-focus-inner {
			border-width: 0;
		}

		// Only needed for Safari 10 and only for buttons.
		span {
			display: inherit;
			align-items: inherit;
			justify-content: inherit;
			width: 100%;
		}
	`);h.propTypes={backgroundColor:n.default.string,hoverColor:n.default.string,textColor:n.default.string},h.defaultProps={backgroundColor:l.colors.$color_button_upsell,hoverColor:l.colors.$color_button_hover_upsell,textColor:l.colors.$color_black};const b=e=>{const{children:t}=e;return o.default.createElement(h,e,t,o.default.createElement(f,{icon:"caret-right",color:l.colors.$color_black,size:"16px"}))};t.UpsellButton=b,b.propTypes={backgroundColor:n.default.string,hoverColor:n.default.string,textColor:n.default.string,children:n.default.oneOfType([n.default.arrayOf(n.default.node),n.default.node])}},71732:(e,t,r)=>{"use strict";Object.defineProperty(t,"__esModule",{value:!0}),t.UpsellLinkButton=void 0;var o,n=(o=r(98487))&&o.__esModule?o:{default:o},a=r(37188);t.UpsellLinkButton=n.default.a`
	align-items: center;
	justify-content: center;
	vertical-align: middle;
	color: ${a.colors.$color_black};
	white-space: nowrap;
	display: inline-flex;
	border-radius: 4px;
	background-color: ${a.colors.$color_button_upsell};
	padding: 4px 8px 8px;
	box-shadow: inset 0 -4px 0 rgba(0, 0, 0, 0.2);
	border: none;
	text-decoration: none;
	font-size: inherit;

	&:hover,
	&:focus,
	&:active {
		color: ${a.colors.$color_black};
		background: ${a.colors.$color_button_upsell_hover};
	}

	&:active {
		background-color: ${a.colors.$color_button_hover_upsell};
		transform: translateY( 1px );
		box-shadow: none;
		filter: none;
	}
`},83908:(e,t,r)=>{"use strict";Object.defineProperty(t,"__esModule",{value:!0}),t.YoastButtonBase=t.YoastButton=void 0,t.addButtonStyles=d;var o=s(r(99196)),n=s(r(85890)),a=s(r(98487)),l=r(37188);function s(e){return e&&e.__esModule?e:{default:e}}const i={minHeight:48,verticalPadding:0,borderWidth:0},u=i.minHeight-2*i.verticalPadding-2*i.borderWidth;function d(e){return(0,a.default)(e)`
		display: inline-flex;
		align-items: center;
		justify-content: center;
		vertical-align: middle;
		min-height: ${`${i.minHeight}px`};
		margin: 0;
		padding: 0 16px;
		padding: ${`${i.verticalPadding}px`} 16px;
		border: 0;
		border-radius: 4px;
		box-sizing: border-box;
		font: 400 14px/24px "Open Sans", sans-serif;
		text-transform: uppercase;
		box-shadow: 0 2px 8px 0 ${(0,l.rgba)(l.colors.$color_black,.3)};
		transition: box-shadow 150ms ease-out;

		&:hover,
		&:focus,
		&:active {
			box-shadow:
				0 4px 10px 0 ${(0,l.rgba)(l.colors.$color_black,.2)},
				inset 0 0 0 100px ${(0,l.rgba)(l.colors.$color_black,.1)};
			color: ${e=>e.textColor};
		}

		&:active {
			transform: translateY( 1px );
			box-shadow: none;
		}

		// Only needed for IE 10+. Don't add spaces within brackets for this to work.
		@media all and (-ms-high-contrast: none), (-ms-high-contrast: active) {
			::after {
				display: inline-block;
				content: "";
				min-height: ${`${u}px`};
			}
		}
	`}const c=({className:e,onClick:t,type:r,children:n,isExpanded:a})=>o.default.createElement("button",{className:e,onClick:t,type:r,"aria-expanded":a},o.default.createElement("span",null,n));t.YoastButtonBase=c,c.propTypes={className:n.default.string,onClick:n.default.func,type:n.default.string,isExpanded:n.default.bool,children:n.default.oneOfType([n.default.arrayOf(n.default.node),n.default.node,n.default.string])},c.defaultProps={type:"button"};const f=t.YoastButton=d((0,a.default)(c)`
		color: ${e=>e.textColor};
		background: ${e=>e.backgroundColor};
		min-width: 152px;
		${e=>e.withTextShadow?`text-shadow: 0 0 2px ${l.colors.$color_black}`:""};
		overflow: visible;
		cursor: pointer;

		&::-moz-focus-inner {
			border-width: 0;
		}

		// Only needed for Safari 10 and only for buttons.
		span {
			display: inherit;
			align-items: inherit;
			justify-content: inherit;
			width: 100%;
		}
	`);f.propTypes={backgroundColor:n.default.string,textColor:n.default.string,withTextShadow:n.default.bool},f.defaultProps={backgroundColor:l.colors.$color_green_medium_light,textColor:l.colors.$color_white,withTextShadow:!0}},78892:(e,t,r)=>{"use strict";Object.defineProperty(t,"__esModule",{value:!0}),t.YoastLinkButton=void 0;var o=s(r(85890)),n=s(r(98487)),a=r(37188),l=r(83908);function s(e){return e&&e.__esModule?e:{default:e}}const i=t.YoastLinkButton=(0,l.addButtonStyles)(n.default.a`
		text-decoration: none;
		color: ${e=>e.textColor};
		background: ${e=>e.backgroundColor};
		min-width: 152px;
		${e=>e.withTextShadow?`text-shadow: 0 0 2px ${a.colors.$color_black}`:""};
	`);i.propTypes={backgroundColor:o.default.string,textColor:o.default.string,withTextShadow:o.default.bool},i.defaultProps={backgroundColor:a.colors.$color_green_medium_light,textColor:a.colors.$color_white,withTextShadow:!0}},24764:(e,t,r)=>{"use strict";Object.defineProperty(t,"__esModule",{value:!0}),t.default=i;var o,n=(o=r(85890))&&o.__esModule?o:{default:o},a=function(e,t){if(e&&e.__esModule)return e;if(null===e||"object"!=typeof e&&"function"!=typeof e)return{default:e};var r=s(t);if(r&&r.has(e))return r.get(e);var o={__proto__:null},n=Object.defineProperty&&Object.getOwnPropertyDescriptor;for(var a in e)if("default"!==a&&Object.prototype.hasOwnProperty.call(e,a)){var l=n?Object.getOwnPropertyDescriptor(e,a):null;l&&(l.get||l.set)?Object.defineProperty(o,a,l):o[a]=e[a]}return o.default=e,r&&r.set(e,o),o}(r(99196)),l=r(36925);function s(e){if("function"!=typeof WeakMap)return null;var t=new WeakMap,r=new WeakMap;return(s=function(e){return e?r:t})(e)}function i(e){const t=(0,a.useCallback)((t=>{e.onChange(t.target.value)}),[e.onChange]);return a.default.createElement(l.FieldGroup,{wrapperClassName:"yoast-field-group yoast-field-group__checkbox"},a.default.createElement("input",{type:"checkbox",id:e.id,checked:e.checked,onChange:t}),a.default.createElement("label",{htmlFor:e.id},e.label))}r(79520),i.propTypes={id:n.default.string.isRequired,label:n.default.oneOfType([n.default.string,n.default.arrayOf(n.default.node),n.default.node]).isRequired,checked:n.default.bool,onChange:n.default.func.isRequired},i.defaultProps={checked:!1}},16236:(e,t,r)=>{"use strict";Object.defineProperty(t,"__esModule",{value:!0}),Object.defineProperty(t,"Checkbox",{enumerable:!0,get:function(){return n.default}}),r(79520);var o,n=(o=r(24764))&&o.__esModule?o:{default:o}},74643:(e,t,r)=>{"use strict";Object.defineProperty(t,"__esModule",{value:!0}),t.default=void 0;var o=l(r(99196)),n=l(r(85890)),a=r(65736);function l(e){return e&&e.__esModule?e:{default:e}}r(8436);const s={width:n.default.number.isRequired,name:n.default.string.isRequired,number:n.default.number.isRequired},i=e=>{
/* translators: Hidden accessibility text; %d expands to number of occurrences. */
const t=(0,a.sprintf)((0,a.__)("%d occurrences","wordpress-seo"),e.number);return o.default.createElement("li",{key:e.name+"_dataItem",style:{"--yoast-width":`${e.width}%`}},e.name,o.default.createElement("span",null,e.number),o.default.createElement("span",{className:"screen-reader-text"},t))};i.propTypes=s;const u=e=>o.default.createElement("ul",{className:"yoast-data-model","aria-label":(0,a.__)("Prominent words","wordpress-seo")},e.items.map(i));u.propTypes={items:n.default.arrayOf(n.default.shape(s))},u.defaultProps={items:[]},t.default=u},35957:(e,t,r)=>{"use strict";Object.defineProperty(t,"__esModule",{value:!0}),Object.defineProperty(t,"DataModel",{enumerable:!0,get:function(){return n.default}}),r(8436);var o,n=(o=r(74643))&&o.__esModule?o:{default:o}},84084:(e,t,r)=>{"use strict";Object.defineProperty(t,"__esModule",{value:!0}),t.default=t.FieldGroupProps=t.FieldGroupDefaultProps=void 0;var o=u(r(99196)),n=u(r(85890));r(86264);var a=function(e,t){if(e&&e.__esModule)return e;if(null===e||"object"!=typeof e&&"function"!=typeof e)return{default:e};var r=i(t);if(r&&r.has(e))return r.get(e);var o={__proto__:null},n=Object.defineProperty&&Object.getOwnPropertyDescriptor;for(var a in e)if("default"!==a&&Object.prototype.hasOwnProperty.call(e,a)){var l=n?Object.getOwnPropertyDescriptor(e,a):null;l&&(l.get||l.set)?Object.defineProperty(o,a,l):o[a]=e[a]}return o.default=e,r&&r.set(e,o),o}(r(14764)),l=u(r(70610)),s=u(r(46708));function i(e){if("function"!=typeof WeakMap)return null;var t=new WeakMap,r=new WeakMap;return(i=function(e){return e?r:t})(e)}function u(e){return e&&e.__esModule?e:{default:e}}const d=({htmlFor:e,label:t,linkTo:r,linkText:n,description:i,children:u,wrapperClassName:d,titleClassName:c,hasNewBadge:f,hasPremiumBadge:p})=>{const h=e?o.default.createElement("label",{htmlFor:e},t):o.default.createElement("b",null,t);return o.default.createElement("div",{className:d},""!==t&&o.default.createElement("div",{className:c},h,p&&o.default.createElement(s.default,{inLabel:!0}),f&&o.default.createElement(l.default,{inLabel:!0}),""!==r&&o.default.createElement(a.default,{linkTo:r,linkText:n})),""!==i&&o.default.createElement("p",{className:"field-group-description"},i),u)},c=t.FieldGroupProps={label:n.default.string,description:n.default.string,children:n.default.oneOfType([n.default.node,n.default.arrayOf(n.default.node)]),wrapperClassName:n.default.string,titleClassName:n.default.string,htmlFor:n.default.string,...a.helpIconProps},f=t.FieldGroupDefaultProps={label:"",description:"",children:[],wrapperClassName:"yoast-field-group",titleClassName:"yoast-field-group__title",htmlFor:"",...a.helpIconDefaultProps};d.propTypes=c,d.defaultProps=f,t.default=d},36925:(e,t,r)=>{"use strict";Object.defineProperty(t,"__esModule",{value:!0}),Object.defineProperty(t,"FieldGroup",{enumerable:!0,get:function(){return n.default}}),r(86264);var o,n=(o=r(84084))&&o.__esModule?o:{default:o}},14764:(e,t,r)=>{"use strict";Object.defineProperty(t,"__esModule",{value:!0}),t.helpIconProps=t.helpIconDefaultProps=t.default=void 0;var o=l(r(99196)),n=l(r(85890)),a=r(65736);function l(e){return e&&e.__esModule?e:{default:e}}r(14640);const s=t.helpIconProps={linkTo:n.default.string,linkText:n.default.string},i=t.helpIconDefaultProps={linkTo:"",linkText:""},u=({linkTo:e,linkText:t})=>o.default.createElement("a",{className:"yoast-help",target:"_blank",href:e,rel:"noopener noreferrer"},o.default.createElement("span",{className:"yoast-help__icon"},o.default.createElement("svg",{xmlns:"http://www.w3.org/2000/svg",viewBox:"0 0 12 12",role:"img","aria-hidden":"true",focusable:"false"},o.default.createElement("path",{d:"M12 6A6 6 0 110 6a6 6 0 0112 0zM6.2 2C4.8 2 4 2.5 3.3 3.5l.1.4.8.7.4-.1c.5-.5.8-.9 1.4-.9.5 0 1.1.4 1.1.8s-.3.6-.7.9C5.8 5.6 5 6 5 7c0 .2.2.4.3.4h1.4L7 7c0-.8 2-.8 2-2.6C9 3 7.5 2 6.2 2zM6 8a1.1 1.1 0 100 2.2A1.1 1.1 0 006 8z"}))),o.default.createElement("span",{className:"screen-reader-text"},t),o.default.createElement("span",{className:"screen-reader-text"},/* translators: Hidden accessibility text. */
(0,a.__)("(Opens in a new browser tab)","wordpress-seo")));u.propTypes=s,u.defaultProps=i,t.default=u},86631:(e,t,r)=>{"use strict";Object.defineProperty(t,"__esModule",{value:!0}),Object.defineProperty(t,"HelpIcon",{enumerable:!0,get:function(){return o.default}}),Object.defineProperty(t,"helpIconDefaultProps",{enumerable:!0,get:function(){return o.helpIconDefaultProps}}),Object.defineProperty(t,"helpIconProps",{enumerable:!0,get:function(){return o.helpIconProps}}),r(14640);var o=function(e,t){if(e&&e.__esModule)return e;if(null===e||"object"!=typeof e&&"function"!=typeof e)return{default:e};var r=n(t);if(r&&r.has(e))return r.get(e);var o={__proto__:null},a=Object.defineProperty&&Object.getOwnPropertyDescriptor;for(var l in e)if("default"!==l&&Object.prototype.hasOwnProperty.call(e,l)){var s=a?Object.getOwnPropertyDescriptor(e,l):null;s&&(s.get||s.set)?Object.defineProperty(o,l,s):o[l]=e[l]}return o.default=e,r&&r.set(e,o),o}(r(14764));function n(e){if("function"!=typeof WeakMap)return null;var t=new WeakMap,r=new WeakMap;return(n=function(e){return e?r:t})(e)}},42345:(e,t,r)=>{"use strict";Object.defineProperty(t,"__esModule",{value:!0}),t.default=void 0;var o=u(r(99196)),n=r(65736),a=u(r(67967)),l=u(r(85890)),s=u(r(84084)),i=u(r(57990));function u(e){return e&&e.__esModule?e:{default:e}}function d(e){const t=!1===e.usingFallback&&""!==e.imageUrl,r=e.imageUrl||e.defaultImageUrl||"",l=e.warnings.length>0&&t;let u=l?"yoast-image-select__preview yoast-image-select__preview-has-warnings":"yoast-image-select__preview";""===r&&(u="yoast-image-select__preview yoast-image-select__preview--no-preview");const d={imageSelected:t,onClick:e.onClick,onRemoveImageClick:e.onRemoveImageClick,selectImageButtonId:e.selectImageButtonId,replaceImageButtonId:e.replaceImageButtonId,removeImageButtonId:e.removeImageButtonId,isDisabled:e.isDisabled},c=()=>o.default.createElement("span",{className:"screen-reader-text"},t?(0,n.__)("Replace image","wordpress-seo"):(0,n.__)("Select image","wordpress-seo"));return o.default.createElement("div",{className:"yoast-image-select",onMouseEnter:e.onMouseEnter,onMouseLeave:e.onMouseLeave},o.default.createElement(s.default,{label:e.label,hasNewBadge:e.hasNewBadge,hasPremiumBadge:e.hasPremiumBadge},e.hasPreview&&o.default.createElement("button",{className:u,onClick:e.onClick,type:"button",disabled:e.isDisabled},""!==r&&o.default.createElement("img",{src:r,alt:e.imageAltText,className:"yoast-image-select__preview--image"}),o.default.createElement(c,null)),l&&o.default.createElement("div",{role:"alert"},e.warnings.map(((e,t)=>o.default.createElement(i.default,{key:`warning${t}`,type:"warning"},e)))),o.default.createElement(a.default,d)))}t.default=d,d.propTypes={defaultImageUrl:l.default.string,imageUrl:l.default.string,imageAltText:l.default.string,hasPreview:l.default.bool.isRequired,label:l.default.string.isRequired,onClick:l.default.func,onMouseEnter:l.default.func,onMouseLeave:l.default.func,onRemoveImageClick:l.default.func,selectImageButtonId:l.default.string,replaceImageButtonId:l.default.string,removeImageButtonId:l.default.string,warnings:l.default.arrayOf(l.default.string),hasNewBadge:l.default.bool,isDisabled:l.default.bool,usingFallback:l.default.bool,hasPremiumBadge:l.default.bool},d.defaultProps={defaultImageUrl:"",imageUrl:"",imageAltText:"",onClick:()=>{},onMouseEnter:()=>{},onMouseLeave:()=>{},onRemoveImageClick:()=>{},selectImageButtonId:"",replaceImageButtonId:"",removeImageButtonId:"",warnings:[],hasNewBadge:!1,isDisabled:!1,usingFallback:!1,hasPremiumBadge:!1}},67967:(e,t,r)=>{"use strict";Object.defineProperty(t,"__esModule",{value:!0}),t.default=void 0;var o,n=function(e,t){if(e&&e.__esModule)return e;if(null===e||"object"!=typeof e&&"function"!=typeof e)return{default:e};var r=i(t);if(r&&r.has(e))return r.get(e);var o={__proto__:null},n=Object.defineProperty&&Object.getOwnPropertyDescriptor;for(var a in e)if("default"!==a&&Object.prototype.hasOwnProperty.call(e,a)){var l=n?Object.getOwnPropertyDescriptor(e,a):null;l&&(l.get||l.set)?Object.defineProperty(o,a,l):o[a]=e[a]}return o.default=e,r&&r.set(e,o),o}(r(99196)),a=r(76149),l=r(65736),s=(o=r(85890))&&o.__esModule?o:{default:o};function i(e){if("function"!=typeof WeakMap)return null;var t=new WeakMap,r=new WeakMap;return(i=function(e){return e?r:t})(e)}const u=e=>{const{imageSelected:t,onClick:r,onRemoveImageClick:o,selectImageButtonId:s,replaceImageButtonId:i,removeImageButtonId:u,isDisabled:d}=e,c=(0,n.useCallback)((e=>{e.target.previousElementSibling.focus(),o()}),[o]);return n.default.createElement("div",{className:"yoast-image-select-buttons"},n.default.createElement(a.NewButton,{variant:"secondary",id:t?i:s,onClick:r,disabled:d},t?(0,l.__)("Replace image","wordpress-seo"):(0,l.__)("Select image","wordpress-seo")),t&&n.default.createElement(a.NewButton,{variant:"remove",id:u,onClick:c,disabled:d},(0,l.__)("Remove image","wordpress-seo")))};t.default=u,u.propTypes={imageSelected:s.default.bool,onClick:s.default.func,onRemoveImageClick:s.default.func,selectImageButtonId:s.default.string,replaceImageButtonId:s.default.string,removeImageButtonId:s.default.string,isDisabled:s.default.bool},u.defaultProps={imageSelected:!1,onClick:()=>{},onRemoveImageClick:()=>{},selectImageButtonId:"",replaceImageButtonId:"",removeImageButtonId:"",isDisabled:!1}},38951:(e,t,r)=>{"use strict";Object.defineProperty(t,"__esModule",{value:!0}),Object.defineProperty(t,"ImageSelect",{enumerable:!0,get:function(){return o.default}}),Object.defineProperty(t,"ImageSelectButtons",{enumerable:!0,get:function(){return n.default}}),r(55022);var o=a(r(42345)),n=a(r(67967));function a(e){return e&&e.__esModule?e:{default:e}}},95235:(e,t,r)=>{"use strict";Object.defineProperty(t,"__esModule",{value:!0});var o={StyledSection:!0,StyledHeading:!0,StyledSectionBase:!0,LinkButton:!0,Button:!0,BaseButton:!0,addHoverStyle:!0,addActiveStyle:!0,addFocusStyle:!0,addBaseStyle:!0,addButtonStyles:!0,Collapsible:!0,CollapsibleStateless:!0,StyledIconsButton:!0,StyledContainer:!0,StyledContainerTopLevel:!0,wrapInHeading:!0,Alert:!0,ArticleList:!0,Card:!0,FullHeightCard:!0,CardBanner:!0,CourseDetails:!0,IconLabeledButton:!0,IconButton:!0,IconsButton:!0,ErrorBoundary:!0,Heading:!0,HelpText:!0,Icon:!0,IconButtonToggle:!0,IconCTAEditButton:!0,IFrame:!0,Input:!0,WordOccurrenceInsights:!0,KeywordSuggestions:!0,Label:!0,SimulatedLabel:!0,LanguageNotice:!0,languageNoticePropType:!0,Loader:!0,MultiStepProgress:!0,Notification:!0,Paper:!0,ProgressBar:!0,Section:!0,SectionTitle:!0,ScoreAssessments:!0,StackedProgressBar:!0,SvgIcon:!0,icons:!0,SynonymsInput:!0,Textarea:!0,Textfield:!0,Toggle:!0,UpsellButton:!0,UpsellLinkButton:!0,YoastButton:!0,InputField:!0,YoastLinkButton:!0,Logo:!0,Modal:!0,YoastSeoIcon:!0,Tabs:!0,Warning:!0,YouTubeVideo:!0,WordList:!0,WordOccurrences:!0,VariableEditorInputContainer:!0,ListTable:!0,ZebrafiedListTable:!0,Row:!0,ScreenReaderText:!0,ScreenReaderShortcut:!0};Object.defineProperty(t,"Alert",{enumerable:!0,get:function(){return w.default}}),Object.defineProperty(t,"ArticleList",{enumerable:!0,get:function(){return P.default}}),Object.defineProperty(t,"BaseButton",{enumerable:!0,get:function(){return C.BaseButton}}),Object.defineProperty(t,"Button",{enumerable:!0,get:function(){return C.default}}),Object.defineProperty(t,"Card",{enumerable:!0,get:function(){return k.default}}),Object.defineProperty(t,"CardBanner",{enumerable:!0,get:function(){return E.default}}),Object.defineProperty(t,"Collapsible",{enumerable:!0,get:function(){return O.default}}),Object.defineProperty(t,"CollapsibleStateless",{enumerable:!0,get:function(){return O.CollapsibleStateless}}),Object.defineProperty(t,"CourseDetails",{enumerable:!0,get:function(){return j.default}}),Object.defineProperty(t,"ErrorBoundary",{enumerable:!0,get:function(){return $.default}}),Object.defineProperty(t,"FullHeightCard",{enumerable:!0,get:function(){return k.FullHeightCard}}),Object.defineProperty(t,"Heading",{enumerable:!0,get:function(){return q.default}}),Object.defineProperty(t,"HelpText",{enumerable:!0,get:function(){return N.default}}),Object.defineProperty(t,"IFrame",{enumerable:!0,get:function(){return L.default}}),Object.defineProperty(t,"Icon",{enumerable:!0,get:function(){return B.default}}),Object.defineProperty(t,"IconButton",{enumerable:!0,get:function(){return M.default}}),Object.defineProperty(t,"IconButtonToggle",{enumerable:!0,get:function(){return I.default}}),Object.defineProperty(t,"IconCTAEditButton",{enumerable:!0,get:function(){return R.default}}),Object.defineProperty(t,"IconLabeledButton",{enumerable:!0,get:function(){return S.default}}),Object.defineProperty(t,"IconsButton",{enumerable:!0,get:function(){return T.default}}),Object.defineProperty(t,"Input",{enumerable:!0,get:function(){return z.default}}),Object.defineProperty(t,"InputField",{enumerable:!0,get:function(){return le.InputField}}),Object.defineProperty(t,"KeywordSuggestions",{enumerable:!0,get:function(){return D.default}}),Object.defineProperty(t,"Label",{enumerable:!0,get:function(){return F.default}}),Object.defineProperty(t,"LanguageNotice",{enumerable:!0,get:function(){return A.default}}),Object.defineProperty(t,"LinkButton",{enumerable:!0,get:function(){return a.LinkButton}}),Object.defineProperty(t,"ListTable",{enumerable:!0,get:function(){return me.ListTable}}),Object.defineProperty(t,"Loader",{enumerable:!0,get:function(){return W.default}}),Object.defineProperty(t,"Logo",{enumerable:!0,get:function(){return ie.default}}),Object.defineProperty(t,"Modal",{enumerable:!0,get:function(){return ue.default}}),Object.defineProperty(t,"MultiStepProgress",{enumerable:!0,get:function(){return H.default}}),Object.defineProperty(t,"Notification",{enumerable:!0,get:function(){return U.default}}),Object.defineProperty(t,"Paper",{enumerable:!0,get:function(){return V.default}}),Object.defineProperty(t,"ProgressBar",{enumerable:!0,get:function(){return G.default}}),Object.defineProperty(t,"Row",{enumerable:!0,get:function(){return ye.Row}}),Object.defineProperty(t,"ScoreAssessments",{enumerable:!0,get:function(){return Z.default}}),Object.defineProperty(t,"ScreenReaderShortcut",{enumerable:!0,get:function(){return _e.default}}),Object.defineProperty(t,"ScreenReaderText",{enumerable:!0,get:function(){return ve.default}}),Object.defineProperty(t,"Section",{enumerable:!0,get:function(){return Y.default}}),Object.defineProperty(t,"SectionTitle",{enumerable:!0,get:function(){return K.SectionTitle}}),Object.defineProperty(t,"SimulatedLabel",{enumerable:!0,get:function(){return F.SimulatedLabel}}),Object.defineProperty(t,"StackedProgressBar",{enumerable:!0,get:function(){return X.default}}),Object.defineProperty(t,"StyledContainer",{enumerable:!0,get:function(){return O.StyledContainer}}),Object.defineProperty(t,"StyledContainerTopLevel",{enumerable:!0,get:function(){return O.StyledContainerTopLevel}}),Object.defineProperty(t,"StyledHeading",{enumerable:!0,get:function(){return n.StyledHeading}}),Object.defineProperty(t,"StyledIconsButton",{enumerable:!0,get:function(){return O.StyledIconsButton}}),Object.defineProperty(t,"StyledSection",{enumerable:!0,get:function(){return n.default}}),Object.defineProperty(t,"StyledSectionBase",{enumerable:!0,get:function(){return n.StyledSectionBase}}),Object.defineProperty(t,"SvgIcon",{enumerable:!0,get:function(){return J.default}}),Object.defineProperty(t,"SynonymsInput",{enumerable:!0,get:function(){return Q.default}}),Object.defineProperty(t,"Tabs",{enumerable:!0,get:function(){return ce.default}}),Object.defineProperty(t,"Textarea",{enumerable:!0,get:function(){return ee.default}}),Object.defineProperty(t,"Textfield",{enumerable:!0,get:function(){return te.default}}),Object.defineProperty(t,"Toggle",{enumerable:!0,get:function(){return re.default}}),Object.defineProperty(t,"UpsellButton",{enumerable:!0,get:function(){return oe.UpsellButton}}),Object.defineProperty(t,"UpsellLinkButton",{enumerable:!0,get:function(){return ne.UpsellLinkButton}}),Object.defineProperty(t,"VariableEditorInputContainer",{enumerable:!0,get:function(){return ge.VariableEditorInputContainer}}),Object.defineProperty(t,"Warning",{enumerable:!0,get:function(){return fe.default}}),Object.defineProperty(t,"WordList",{enumerable:!0,get:function(){return he.default}}),Object.defineProperty(t,"WordOccurrenceInsights",{enumerable:!0,get:function(){return D.default}}),Object.defineProperty(t,"WordOccurrences",{enumerable:!0,get:function(){return be.default}}),Object.defineProperty(t,"YoastButton",{enumerable:!0,get:function(){return ae.YoastButton}}),Object.defineProperty(t,"YoastLinkButton",{enumerable:!0,get:function(){return se.YoastLinkButton}}),Object.defineProperty(t,"YoastSeoIcon",{enumerable:!0,get:function(){return de.default}}),Object.defineProperty(t,"YouTubeVideo",{enumerable:!0,get:function(){return pe.default}}),Object.defineProperty(t,"ZebrafiedListTable",{enumerable:!0,get:function(){return me.ZebrafiedListTable}}),Object.defineProperty(t,"addActiveStyle",{enumerable:!0,get:function(){return C.addActiveStyle}}),Object.defineProperty(t,"addBaseStyle",{enumerable:!0,get:function(){return C.addBaseStyle}}),Object.defineProperty(t,"addButtonStyles",{enumerable:!0,get:function(){return C.addButtonStyles}}),Object.defineProperty(t,"addFocusStyle",{enumerable:!0,get:function(){return C.addFocusStyle}}),Object.defineProperty(t,"addHoverStyle",{enumerable:!0,get:function(){return C.addHoverStyle}}),Object.defineProperty(t,"icons",{enumerable:!0,get:function(){return J.icons}}),Object.defineProperty(t,"languageNoticePropType",{enumerable:!0,get:function(){return A.languageNoticePropType}}),Object.defineProperty(t,"wrapInHeading",{enumerable:!0,get:function(){return O.wrapInHeading}}),r(75377),r(37704);var n=Oe(r(78538)),a=r(16785),l=r(76149);Object.keys(l).forEach((function(e){"default"!==e&&"__esModule"!==e&&(Object.prototype.hasOwnProperty.call(o,e)||e in t&&t[e]===l[e]||Object.defineProperty(t,e,{enumerable:!0,get:function(){return l[e]}}))}));var s=r(16236);Object.keys(s).forEach((function(e){"default"!==e&&"__esModule"!==e&&(Object.prototype.hasOwnProperty.call(o,e)||e in t&&t[e]===s[e]||Object.defineProperty(t,e,{enumerable:!0,get:function(){return s[e]}}))}));var i=r(35957);Object.keys(i).forEach((function(e){"default"!==e&&"__esModule"!==e&&(Object.prototype.hasOwnProperty.call(o,e)||e in t&&t[e]===i[e]||Object.defineProperty(t,e,{enumerable:!0,get:function(){return i[e]}}))}));var u=r(36925);Object.keys(u).forEach((function(e){"default"!==e&&"__esModule"!==e&&(Object.prototype.hasOwnProperty.call(o,e)||e in t&&t[e]===u[e]||Object.defineProperty(t,e,{enumerable:!0,get:function(){return u[e]}}))}));var d=r(38951);Object.keys(d).forEach((function(e){"default"!==e&&"__esModule"!==e&&(Object.prototype.hasOwnProperty.call(o,e)||e in t&&t[e]===d[e]||Object.defineProperty(t,e,{enumerable:!0,get:function(){return d[e]}}))}));var c=r(24420);Object.keys(c).forEach((function(e){"default"!==e&&"__esModule"!==e&&(Object.prototype.hasOwnProperty.call(o,e)||e in t&&t[e]===c[e]||Object.defineProperty(t,e,{enumerable:!0,get:function(){return c[e]}}))}));var f=r(54297);Object.keys(f).forEach((function(e){"default"!==e&&"__esModule"!==e&&(Object.prototype.hasOwnProperty.call(o,e)||e in t&&t[e]===f[e]||Object.defineProperty(t,e,{enumerable:!0,get:function(){return f[e]}}))}));var p=r(46465);Object.keys(p).forEach((function(e){"default"!==e&&"__esModule"!==e&&(Object.prototype.hasOwnProperty.call(o,e)||e in t&&t[e]===p[e]||Object.defineProperty(t,e,{enumerable:!0,get:function(){return p[e]}}))}));var h=r(55138);Object.keys(h).forEach((function(e){"default"!==e&&"__esModule"!==e&&(Object.prototype.hasOwnProperty.call(o,e)||e in t&&t[e]===h[e]||Object.defineProperty(t,e,{enumerable:!0,get:function(){return h[e]}}))}));var b=r(5185);Object.keys(b).forEach((function(e){"default"!==e&&"__esModule"!==e&&(Object.prototype.hasOwnProperty.call(o,e)||e in t&&t[e]===b[e]||Object.defineProperty(t,e,{enumerable:!0,get:function(){return b[e]}}))}));var g=r(86631);Object.keys(g).forEach((function(e){"default"!==e&&"__esModule"!==e&&(Object.prototype.hasOwnProperty.call(o,e)||e in t&&t[e]===g[e]||Object.defineProperty(t,e,{enumerable:!0,get:function(){return g[e]}}))}));var m=r(43781);Object.keys(m).forEach((function(e){"default"!==e&&"__esModule"!==e&&(Object.prototype.hasOwnProperty.call(o,e)||e in t&&t[e]===m[e]||Object.defineProperty(t,e,{enumerable:!0,get:function(){return m[e]}}))}));var y=r(8850);Object.keys(y).forEach((function(e){"default"!==e&&"__esModule"!==e&&(Object.prototype.hasOwnProperty.call(o,e)||e in t&&t[e]===y[e]||Object.defineProperty(t,e,{enumerable:!0,get:function(){return y[e]}}))}));var v=r(51495);Object.keys(v).forEach((function(e){"default"!==e&&"__esModule"!==e&&(Object.prototype.hasOwnProperty.call(o,e)||e in t&&t[e]===v[e]||Object.defineProperty(t,e,{enumerable:!0,get:function(){return v[e]}}))}));var _=r(40815);Object.keys(_).forEach((function(e){"default"!==e&&"__esModule"!==e&&(Object.prototype.hasOwnProperty.call(o,e)||e in t&&t[e]===_[e]||Object.defineProperty(t,e,{enumerable:!0,get:function(){return _[e]}}))}));var x=r(31233);Object.keys(x).forEach((function(e){"default"!==e&&"__esModule"!==e&&(Object.prototype.hasOwnProperty.call(o,e)||e in t&&t[e]===x[e]||Object.defineProperty(t,e,{enumerable:!0,get:function(){return x[e]}}))}));var C=Oe(r(46362)),O=Oe(r(57272)),w=xe(r(57990)),P=xe(r(47529)),k=Oe(r(79743)),E=xe(r(97230)),j=xe(r(69424)),S=xe(r(3234)),M=xe(r(4532)),T=xe(r(71875)),$=xe(r(27938)),q=xe(r(33014)),N=xe(r(30812)),B=xe(r(7992)),I=xe(r(21529)),R=xe(r(22027)),L=xe(r(77844)),z=xe(r(66763)),D=xe(r(1993)),F=Oe(r(1453)),A=Oe(r(44722)),W=xe(r(50933)),H=xe(r(64737)),U=xe(r(18506)),V=xe(r(8272)),G=xe(r(57186)),Y=xe(r(37553)),K=r(91752),Z=xe(r(5180)),X=xe(r(49526)),J=Oe(r(78386)),Q=xe(r(97485)),ee=xe(r(22386)),te=xe(r(18547)),re=xe(r(47816)),oe=r(60813),ne=r(71732),ae=r(83908),le=r(23613),se=r(78892),ie=xe(r(73028)),ue=xe(r(79610)),de=xe(r(14085)),ce=xe(r(51316)),fe=xe(r(1382)),pe=xe(r(72768)),he=xe(r(64385)),be=xe(r(31367)),ge=r(3199),me=r(98872),ye=r(87923),ve=xe(r(42479)),_e=xe(r(76916));function xe(e){return e&&e.__esModule?e:{default:e}}function Ce(e){if("function"!=typeof WeakMap)return null;var t=new WeakMap,r=new WeakMap;return(Ce=function(e){return e?r:t})(e)}function Oe(e,t){if(!t&&e&&e.__esModule)return e;if(null===e||"object"!=typeof e&&"function"!=typeof e)return{default:e};var r=Ce(t);if(r&&r.has(e))return r.get(e);var o={__proto__:null},n=Object.defineProperty&&Object.getOwnPropertyDescriptor;for(var a in e)if("default"!==a&&Object.prototype.hasOwnProperty.call(e,a)){var l=n?Object.getOwnPropertyDescriptor(e,a):null;l&&(l.get||l.set)?Object.defineProperty(o,a,l):o[a]=e[a]}return o.default=e,r&&r.set(e,o),o}},66763:(e,t,r)=>{"use strict";Object.defineProperty(t,"__esModule",{value:!0}),t.default=void 0;var o=a(r(99196)),n=a(r(85890));function a(e){return e&&e.__esModule?e:{default:e}}function l(){return l=Object.assign?Object.assign.bind():function(e){for(var t=1;t<arguments.length;t++){var r=arguments[t];for(var o in r)Object.prototype.hasOwnProperty.call(r,o)&&(e[o]=r[o])}return e},l.apply(this,arguments)}class s extends o.default.Component{constructor(e){super(e),this.setReference=this.setReference.bind(this)}componentDidUpdate(){this.props.hasFocus&&this.ref.focus()}setReference(e){this.ref=e}render(){return o.default.createElement("input",l({ref:this.setReference,type:this.props.type,name:this.props.name,defaultValue:this.props.value,onChange:this.props.onChange,autoComplete:this.props.autoComplete,className:this.props.className},this.props.optionalAttributes))}}s.propTypes={name:n.default.string,type:n.default.oneOf(["button","checkbox","number","password","progress","radio","submit","text"]),value:n.default.any,onChange:n.default.func,optionalAttributes:n.default.object,hasFocus:n.default.bool,autoComplete:n.default.string,className:n.default.string},s.defaultProps={name:"input",type:"text",value:"",hasFocus:!1,className:"",onChange:null,optionalAttributes:{},autoComplete:null},t.default=s},3199:(e,t,r)=>{"use strict";Object.defineProperty(t,"__esModule",{value:!0}),t.VariableEditorInputContainer=t.InputContainer=void 0;var o,n=(o=r(98487))&&o.__esModule?o:{default:o};t.VariableEditorInputContainer=n.default.div.attrs({})`
	flex: 0 1 100%;
	border: 1px solid ${e=>e.isActive?"#5b9dd9":"#ddd"};
	padding: 4px 5px;
	box-sizing: border-box;
	box-shadow: ${e=>e.isActive?"0 0 2px rgba(30,140,190,.8);":"inset 0 1px 2px rgba(0,0,0,.07)"};
	background-color: #fff;
	color: #32373c;
	outline: 0;
	transition: 50ms border-color ease-in-out;
	position: relative;
	font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Oxygen-Sans, Ubuntu, Cantarell, "Helvetica Neue", sans-serif;
	font-size: 14px;
	cursor: text;
`,t.InputContainer=n.default.div`
	display: flex;
	flex-direction: column;
	margin: 1em 0;
`},23613:(e,t,r)=>{"use strict";Object.defineProperty(t,"__esModule",{value:!0}),t.InputField=void 0;var o,n=(o=r(98487))&&o.__esModule?o:{default:o},a=r(37188);t.InputField=n.default.input`
	&&& {
		padding: 0 8px;
		min-height: 34px;
		font-size: 1em;
		box-shadow: inset 0 1px 2px ${(0,a.rgba)(a.colors.$color_black,.07)};
		border: 1px solid ${a.colors.$color_input_border};
		border-radius: 0;

		&:focus {
			border-color: #5b9dd9;
			box-shadow: 0 0 2px ${(0,a.rgba)(a.colors.$color_snippet_focus,.8)};
		}
	}
`},82572:(e,t,r)=>{"use strict";Object.defineProperty(t,"__esModule",{value:!0}),t.InputLabel=void 0;var o,n=(o=r(98487))&&o.__esModule?o:{default:o};t.InputLabel=n.default.label`
	font-size: 1em;
	font-weight: bold;
	margin-bottom: 0.5em;
	display: block;
`},91315:(e,t,r)=>{"use strict";Object.defineProperty(t,"__esModule",{value:!0}),t.default=void 0;var o=u(r(99196)),n=u(r(85890)),a=r(92819),l=r(65736);r(60526);var s=function(e,t){if(e&&e.__esModule)return e;if(null===e||"object"!=typeof e&&"function"!=typeof e)return{default:e};var r=i(t);if(r&&r.has(e))return r.get(e);var o={__proto__:null},n=Object.defineProperty&&Object.getOwnPropertyDescriptor;for(var a in e)if("default"!==a&&Object.prototype.hasOwnProperty.call(e,a)){var l=n?Object.getOwnPropertyDescriptor(e,a):null;l&&(l.get||l.set)?Object.defineProperty(o,a,l):o[a]=e[a]}return o.default=e,r&&r.set(e,o),o}(r(84084));function i(e){if("function"!=typeof WeakMap)return null;var t=new WeakMap,r=new WeakMap;return(i=function(e){return e?r:t})(e)}function u(e){return e&&e.__esModule?e:{default:e}}function d(e){return{hours:Math.floor(e/3600),minutes:Math.floor(e%3600/60),seconds:e%3600%60}}class c extends o.default.Component{constructor(e){super(e),this.state={...d(e.duration)},this.onHoursChange=this.onHoursChange.bind(this),this.onMinutesChange=this.onMinutesChange.bind(this),this.onSecondsChange=this.onSecondsChange.bind(this)}formatValue(e,t=Number.MIN_VALUE,r=Number.MAX_VALUE){const o=parseInt(e.target.value,10)||0;return(0,a.clamp)(o,t,r)}onHoursChange(e){this.props.onChange(3600*this.formatValue(e,0)+60*this.state.minutes+this.state.seconds)}onMinutesChange(e){this.props.onChange(3600*this.state.hours+60*this.formatValue(e,0,59)+this.state.seconds)}onSecondsChange(e){this.props.onChange(3600*this.state.hours+60*this.state.minutes+this.formatValue(e,0,59))}static getDerivedStateFromProps(e,t){const r=d(e.duration);return(0,a.isEqual)(r,t)?null:{...r}}render(){const e=this.props,t=e.id;return o.default.createElement(s.default,e,o.default.createElement("div",{className:"duration-inputs__wrapper"},o.default.createElement("div",{className:"duration-inputs__input-wrapper"},o.default.createElement("label",{htmlFor:t+"-hours"},(0,l.__)("hours","wordpress-seo")),o.default.createElement("input",{id:t+"-hours",name:"hours",value:this.state.hours,type:"number",className:"yoast-field-group__inputfield duration-inputs__input","aria-describedby":e.hoursAriaDescribedBy,readOnly:e.readOnly,min:0,onChange:this.onHoursChange})),o.default.createElement("div",{className:"duration-inputs__input-wrapper"},o.default.createElement("label",{htmlFor:t+"-minutes"},(0,l.__)("minutes","wordpress-seo")),o.default.createElement("input",{id:t+"-minutes",name:"minutes",value:this.state.minutes,type:"number",className:"yoast-field-group__inputfield duration-inputs__input","aria-describedby":e.minutesAriaDescribedBy,readOnly:e.readOnly,min:0,max:59,onChange:this.onMinutesChange})),o.default.createElement("div",{className:"duration-inputs__input-wrapper"},o.default.createElement("label",{htmlFor:t+"-seconds"},(0,l.__)("seconds","wordpress-seo")),o.default.createElement("input",{id:t+"-seconds",name:"seconds",value:this.state.seconds,type:"number",className:"yoast-field-group__inputfield duration-inputs__input","aria-describedby":e.secondsAriaDescribedBy,readOnly:e.readOnly,min:0,max:59,onChange:this.onSecondsChange}))))}}c.propTypes={duration:n.default.number.isRequired,hoursAriaDescribedBy:n.default.string,minutesAriaDescribedBy:n.default.string,secondsAriaDescribedBy:n.default.string,id:n.default.string.isRequired,...s.FieldGroupProps},c.defaultProps={hoursAriaDescribedBy:"",minutesAriaDescribedBy:"",secondsAriaDescribedBy:"",...s.FieldGroupDefaultProps},t.default=c},70399:(e,t,r)=>{"use strict";Object.defineProperty(t,"__esModule",{value:!0}),t.inputTypes=t.default=void 0;var o=s(r(99196)),n=s(r(85890)),a=function(e,t){if(e&&e.__esModule)return e;if(null===e||"object"!=typeof e&&"function"!=typeof e)return{default:e};var r=l(t);if(r&&r.has(e))return r.get(e);var o={__proto__:null},n=Object.defineProperty&&Object.getOwnPropertyDescriptor;for(var a in e)if("default"!==a&&Object.prototype.hasOwnProperty.call(e,a)){var s=n?Object.getOwnPropertyDescriptor(e,a):null;s&&(s.get||s.set)?Object.defineProperty(o,a,s):o[a]=e[a]}return o.default=e,r&&r.set(e,o),o}(r(84084));function l(e){if("function"!=typeof WeakMap)return null;var t=new WeakMap,r=new WeakMap;return(l=function(e){return e?r:t})(e)}function s(e){return e&&e.__esModule?e:{default:e}}r(60526);const i=t.inputTypes=["text","color","date","datetime-local","email","hidden","month","number","password","search","tel","time","url","week","range"],u=e=>{const t={...e};return e.id&&(t.htmlFor=e.id),o.default.createElement(a.default,t,o.default.createElement("input",{id:e.id,name:e.name,value:e.value,type:e.type,className:"yoast-field-group__inputfield","aria-describedby":e.ariaDescribedBy,placeholder:e.placeholder,readOnly:e.readOnly,min:e.min,max:e.max,step:e.step,onChange:(r=e.onChange,e=>{r(e.target.value)})}));var r};u.propTypes={id:n.default.string,name:n.default.string,value:n.default.string,type:n.default.oneOf(i),ariaDescribedBy:n.default.string,placeholder:n.default.string,readOnly:n.default.bool,min:n.default.number,max:n.default.number,step:n.default.number,onChange:n.default.func,...a.FieldGroupProps},u.defaultProps={id:"",name:"",value:"",ariaDescribedBy:"",readOnly:!1,type:"text",placeholder:void 0,min:void 0,max:void 0,step:void 0,onChange:void 0,...a.FieldGroupDefaultProps},t.default=u},24420:(e,t,r)=>{"use strict";Object.defineProperty(t,"__esModule",{value:!0}),Object.defineProperty(t,"DurationInput",{enumerable:!0,get:function(){return n.default}}),Object.defineProperty(t,"TextInput",{enumerable:!0,get:function(){return o.default}}),r(60526);var o=a(r(70399)),n=a(r(91315));function a(e){return e&&e.__esModule?e:{default:e}}},77509:(e,t,r)=>{"use strict";Object.defineProperty(t,"__esModule",{value:!0}),t.default=void 0;var o=l(r(99196)),n=l(r(85890)),a=r(36925);function l(e){return e&&e.__esModule?e:{default:e}}class s extends o.default.Component{getInsightsCardContent(){return o.default.createElement("div",{className:"yoast-insights-card__content"},o.default.createElement("p",{className:"yoast-insights-card__score"},o.default.createElement("span",{className:"yoast-insights-card__amount"},this.props.amount),this.props.unit),this.props.description&&o.default.createElement("div",{className:"yoast-insights-card__description"},this.props.description))}render(){return o.default.createElement(a.FieldGroup,{label:this.props.title,linkTo:this.props.linkTo,linkText:this.props.linkText,wrapperClassName:"yoast-insights-card"},this.getInsightsCardContent())}}t.default=s,s.propTypes={title:n.default.string,amount:n.default.oneOfType([n.default.number,n.default.oneOf(["?"])]).isRequired,description:n.default.element,unit:n.default.string,linkTo:n.default.string,linkText:n.default.string},s.defaultProps={title:"",description:null,unit:"",linkTo:"",linkText:""}},54297:(e,t,r)=>{"use strict";Object.defineProperty(t,"__esModule",{value:!0}),Object.defineProperty(t,"InsightsCard",{enumerable:!0,get:function(){return n.default}}),r(70418);var o,n=(o=r(77509))&&o.__esModule?o:{default:o}},70610:(e,t,r)=>{"use strict";Object.defineProperty(t,"__esModule",{value:!0}),t.default=void 0;var o=l(r(99196)),n=l(r(85890)),a=r(65736);function l(e){return e&&e.__esModule?e:{default:e}}const s=({inLabel:e})=>o.default.createElement("span",{className:e?"yoast-badge yoast-badge__in-label yoast-new-badge":"yoast-badge yoast-new-badge"},(0,a.__)("New","wordpress-seo"));s.propTypes={inLabel:n.default.bool},s.defaultProps={inLabel:!1},t.default=s},8850:(e,t,r)=>{"use strict";Object.defineProperty(t,"__esModule",{value:!0}),Object.defineProperty(t,"NewBadge",{enumerable:!0,get:function(){return n.default}}),r(15446),r(15210);var o,n=(o=r(70610))&&o.__esModule?o:{default:o}},46708:(e,t,r)=>{"use strict";Object.defineProperty(t,"__esModule",{value:!0}),t.default=void 0;var o=a(r(99196)),n=a(r(85890));function a(e){return e&&e.__esModule?e:{default:e}}const l=({inLabel:e})=>o.default.createElement("span",{className:e?"yoast-badge yoast-badge__in-label yoast-premium-badge":"yoast-badge yoast-premium-badge"},"Premium");l.propTypes={inLabel:n.default.bool},l.defaultProps={inLabel:!1},t.default=l},51495:(e,t,r)=>{"use strict";Object.defineProperty(t,"__esModule",{value:!0}),Object.defineProperty(t,"PremiumBadge",{enumerable:!0,get:function(){return n.default}}),r(15446),r(29001);var o,n=(o=r(46708))&&o.__esModule?o:{default:o}},17230:(e,t,r)=>{"use strict";Object.defineProperty(t,"__esModule",{value:!0}),t.default=void 0;var o,n=d(r(99196)),a=(o=r(85890))&&o.__esModule?o:{default:o},l=r(92819),s=d(r(84084)),i=r(9802);function u(e){if("function"!=typeof WeakMap)return null;var t=new WeakMap,r=new WeakMap;return(u=function(e){return e?r:t})(e)}function d(e,t){if(!t&&e&&e.__esModule)return e;if(null===e||"object"!=typeof e&&"function"!=typeof e)return{default:e};var r=u(t);if(r&&r.has(e))return r.get(e);var o={__proto__:null},n=Object.defineProperty&&Object.getOwnPropertyDescriptor;for(var a in e)if("default"!==a&&Object.prototype.hasOwnProperty.call(e,a)){var l=n?Object.getOwnPropertyDescriptor(e,a):null;l&&(l.get||l.set)?Object.defineProperty(o,a,l):o[a]=e[a]}return o.default=e,r&&r.set(e,o),o}function c(){return c=Object.assign?Object.assign.bind():function(e){for(var t=1;t<arguments.length;t++){var r=arguments[t];for(var o in r)Object.prototype.hasOwnProperty.call(r,o)&&(e[o]=r[o])}return e},c.apply(this,arguments)}r(17672);const f={options:a.default.array.isRequired,onChange:a.default.func.isRequired,groupName:a.default.string.isRequired,id:a.default.string.isRequired,selected:a.default.oneOfType([a.default.string,a.default.number])},p={selected:null},h=({value:e,label:t,checked:r,onChange:o,groupName:a,id:l})=>n.default.createElement(n.Fragment,null,n.default.createElement("input",{type:"radio",name:a,id:l,value:e,onChange:o,checked:r}),n.default.createElement("label",{htmlFor:l},t));h.propTypes={value:a.default.oneOfType([a.default.string,a.default.number]).isRequired,label:a.default.string.isRequired,checked:a.default.bool,groupName:a.default.string.isRequired,onChange:a.default.func,id:a.default.string.isRequired},h.defaultProps={checked:!1,onChange:l.noop};const b=({options:e,onChange:t,groupName:r,id:o,selected:a})=>n.default.createElement("div",{className:"yoast-field-group__radiobutton"},e.map((e=>n.default.createElement(h,c({key:e.value,groupName:r,checked:a===e.value,onChange:t,id:`${o}_${e.value}`},e)))));b.propTypes=f,b.defaultProps=p;const g=({options:e,onChange:t,groupName:r,id:o,selected:a})=>n.default.createElement("div",{onChange:t},e.map((e=>n.default.createElement("div",{className:"yoast-field-group__radiobutton yoast-field-group__radiobutton--vertical",key:e.value},n.default.createElement(h,c({groupName:r,checked:a===e.value,id:`${o}_${e.value}`},e))))));g.propTypes=f,g.defaultProps=p;const m=e=>{const{id:t,options:r,groupName:o,onChange:a,vertical:l,selected:u,...d}=e,c={options:r,groupName:o,selected:u,onChange:e=>a(e.target.value),id:(0,i.getId)(t)};return n.default.createElement(s.default,d,l?n.default.createElement(g,c):n.default.createElement(b,c))};m.propTypes={id:a.default.string,groupName:a.default.string.isRequired,options:a.default.arrayOf(a.default.shape({value:a.default.oneOfType([a.default.string,a.default.number]).isRequired,label:a.default.string.isRequired})).isRequired,selected:a.default.oneOfType([a.default.string,a.default.number]),onChange:a.default.func,vertical:a.default.bool,...s.FieldGroupProps},m.defaultProps={id:"",vertical:!1,selected:null,onChange:()=>{},...s.FieldGroupDefaultProps},t.default=m},46465:(e,t,r)=>{"use strict";Object.defineProperty(t,"__esModule",{value:!0}),Object.defineProperty(t,"RadioButtonGroup",{enumerable:!0,get:function(){return n.default}}),r(17672);var o,n=(o=r(17230))&&o.__esModule?o:{default:o}},69970:(e,t,r)=>{"use strict";Object.defineProperty(t,"__esModule",{value:!0}),t.YoastReactSelect=t.SingleSelect=t.Select=t.MultiSelect=void 0;var o=r(92819),n=d(r(99196)),a=i(r(85890)),l=d(r(84084)),s=i(r(92651));function i(e){return e&&e.__esModule?e:{default:e}}function u(e){if("function"!=typeof WeakMap)return null;var t=new WeakMap,r=new WeakMap;return(u=function(e){return e?r:t})(e)}function d(e,t){if(!t&&e&&e.__esModule)return e;if(null===e||"object"!=typeof e&&"function"!=typeof e)return{default:e};var r=u(t);if(r&&r.has(e))return r.get(e);var o={__proto__:null},n=Object.defineProperty&&Object.getOwnPropertyDescriptor;for(var a in e)if("default"!==a&&Object.prototype.hasOwnProperty.call(e,a)){var l=n?Object.getOwnPropertyDescriptor(e,a):null;l&&(l.get||l.set)?Object.defineProperty(o,a,l):o[a]=e[a]}return o.default=e,r&&r.set(e,o),o}function c(){return c=Object.assign?Object.assign.bind():function(e){for(var t=1;t<arguments.length;t++){var r=arguments[t];for(var o in r)Object.prototype.hasOwnProperty.call(r,o)&&(e[o]=r[o])}return e},c.apply(this,arguments)}r(67496);const f=a.default.shape({name:a.default.string,value:a.default.string}),p={id:a.default.string.isRequired,name:a.default.string,options:a.default.arrayOf(f).isRequired,selected:a.default.oneOfType([a.default.arrayOf(a.default.string),a.default.string]),onChange:a.default.func,disabled:a.default.bool,...l.FieldGroupProps},h={name:"",selected:[],onChange:()=>{},disabled:!1,...l.FieldGroupDefaultProps},b=({name:e,value:t})=>n.default.createElement("option",{key:t,value:t},e);b.propTypes={name:a.default.string.isRequired,value:a.default.string.isRequired};const g=e=>{const{id:t,isMulti:r,isSearchable:o,inputId:a,selected:i,options:u,name:d,onChange:f,...p}=e,h=Array.isArray(i)?i:[i],b=(e=>e.map((e=>({value:e.value,label:e.name}))))(u),g=b.filter((e=>h.includes(e.value)));return n.default.createElement(l.default,c({},p,{htmlFor:a}),n.default.createElement(s.default,{isMulti:r,id:t,name:d,inputId:a,value:g,options:b,hideSelectedOptions:!1,onChange:f,className:"yoast-select-container",classNamePrefix:"yoast-select",isClearable:!1,isSearchable:o,placeholder:""}))};t.YoastReactSelect=g,g.propTypes=p,g.defaultProps=h;const m=e=>{const{onChange:t}=e,r=(0,n.useCallback)((e=>t(e.value)));return n.default.createElement(g,c({},e,{isMulti:!1,isSearchable:!0,onChange:r}))};t.SingleSelect=m,m.propTypes=p,m.defaultProps=h;const y=e=>{const{onChange:t}=e,r=(0,n.useCallback)((e=>{e||(e=[]),t(e.map((e=>e.value)))}));return n.default.createElement(g,c({},e,{isMulti:!0,isSearchable:!1,onChange:r}))};t.MultiSelect=y,y.propTypes=p,y.defaultProps=h;class v extends n.default.Component{constructor(e){super(e),this.onBlurHandler=this.onBlurHandler.bind(this),this.onInputHandler=this.onInputHandler.bind(this),this.state={selected:this.props.selected}}onBlurHandler(e){this.props.onChange(e.target.value)}onInputHandler(e){this.setState({selected:e.target.value}),this.props.onOptionFocus&&this.props.onOptionFocus(e.target.name,e.target.value)}componentDidUpdate(e){e.selected!==this.props.selected&&this.setState({selected:this.props.selected})}render(){const{id:e,options:t,name:r,disabled:a,...s}=this.props;return n.default.createElement(l.default,c({},s,{htmlFor:e}),n.default.createElement("select",{id:e,name:r,value:this.state.selected,onBlur:this.onBlurHandler,onInput:this.onInputHandler,onChange:o.noop,disabled:a},t.map(b)))}}t.Select=v,v.propTypes={...p,onOptionFocus:a.default.func},v.defaultProps={...h,onOptionFocus:null}},55138:(e,t,r)=>{"use strict";Object.defineProperty(t,"__esModule",{value:!0}),Object.defineProperty(t,"MultiSelect",{enumerable:!0,get:function(){return o.MultiSelect}}),Object.defineProperty(t,"Select",{enumerable:!0,get:function(){return o.Select}}),Object.defineProperty(t,"SingleSelect",{enumerable:!0,get:function(){return o.SingleSelect}}),r(67496);var o=r(69970)},37677:(e,t,r)=>{"use strict";Object.defineProperty(t,"__esModule",{value:!0}),t.default=void 0;var o=a(r(99196)),n=a(r(85890));function a(e){return e&&e.__esModule?e:{default:e}}function l(e){let t=e.rating;t<0&&(t=0),t>5&&(t=5);const r=20*t;return o.default.createElement("div",{"aria-hidden":"true",className:"yoast-star-rating"},o.default.createElement("span",{className:"yoast-star-rating__placeholder",role:"img"},o.default.createElement("span",{className:"yoast-star-rating__fill",style:{width:r+"%"}})))}t.default=l,l.propTypes={rating:n.default.number.isRequired}},5185:(e,t,r)=>{"use strict";Object.defineProperty(t,"__esModule",{value:!0}),Object.defineProperty(t,"StarRating",{enumerable:!0,get:function(){return n.default}}),r(10205);var o,n=(o=r(37677))&&o.__esModule?o:{default:o}},98872:(e,t,r)=>{"use strict";Object.defineProperty(t,"__esModule",{value:!0}),t.ZebrafiedListTable=t.ListTable=void 0,t.makeFullWidth=function(e){return(0,a.default)(e)`
		@media screen and ( max-width: 800px ) {
			min-width: 100%;
			margin-top: 1em;
			padding-right: 0;
			padding-left: 0;
		}
	`};var o=s(r(85890)),n=s(r(99196)),a=s(r(98487)),l=r(37188);function s(e){return e&&e.__esModule?e:{default:e}}function i(){return i=Object.assign?Object.assign.bind():function(e){for(var t=1;t<arguments.length;t++){var r=arguments[t];for(var o in r)Object.prototype.hasOwnProperty.call(r,o)&&(e[o]=r[o])}return e},i.apply(this,arguments)}const u=a.default.ul`
	margin: 0;
 	padding: 0;
 	list-style: none;
 	position: relative;
 	width: 100%;

 	li:first-child {
		& > span::before {
			left: auto;
		}
	}
`;u.propTypes={children:o.default.any};class d extends n.default.Component{constructor(e){super(e)}getChildren(){return 1===this.props.children?[this.props.children]:this.props.children}render(){const e=this.getChildren();return n.default.createElement(u,{role:"list"},e)}}t.ListTable=d,t.ZebrafiedListTable=class extends d{constructor(e){super(e),this.zebraProps=Object.assign({},e)}zebrafyChildren(){let e=this.props.children;this.props.children.map||(e=[e]),this.zebraProps.children=e.map(((e,t)=>n.default.cloneElement(e,{background:t%2==1?l.colors.$color_white:l.colors.$color_background_light,key:t})))}render(){return this.zebrafyChildren(),n.default.createElement(u,i({role:"list"},this.zebraProps))}},d.propTypes={children:o.default.oneOfType([o.default.arrayOf(o.default.node),o.default.node])},d.defaultProps={children:[]}},87923:(e,t,r)=>{"use strict";Object.defineProperty(t,"__esModule",{value:!0}),t.RowResponsiveWrap=t.Row=void 0;var o=l(r(85890)),n=l(r(98487)),a=r(37188);function l(e){return e&&e.__esModule?e:{default:e}}const s=t.Row=n.default.li`
	background: ${e=>e.background};
	display: flex;
	min-height: ${e=>e.rowHeight};
	align-items: center;
	justify-content: space-between;
`;s.propTypes={background:o.default.string,hasHeaderLabels:o.default.bool,rowHeight:o.default.string},s.defaultProps={background:a.colors.$color_white,hasHeaderLabels:!0},t.RowResponsiveWrap=(0,n.default)(s)`
	@media screen and ( max-width: 800px ) {
		flex-wrap: wrap;
		align-items: flex-start;

		&:first-child {
			margin-top: ${e=>e.hasHeaderLabels?"24px":"0"};
		}

		// Use the column headers (if any) as labels.
		& > span::before {
			position: static;
			display: inline-block;
			padding-right: 0.5em;
			font-size: inherit;
		}
		& > span {
			padding-left: 0;
		}
	}
`},43781:(e,t,r)=>{"use strict";r(20784)},37704:(e,t,r)=>{"use strict";r(82221)},31233:(e,t,r)=>{"use strict";r(40265)},94184:(e,t)=>{var r;!function(){"use strict";var o={}.hasOwnProperty;function n(){for(var e=[],t=0;t<arguments.length;t++){var r=arguments[t];if(r){var a=typeof r;if("string"===a||"number"===a)e.push(r);else if(Array.isArray(r)&&r.length){var l=n.apply(null,r);l&&e.push(l)}else if("object"===a)for(var s in r)o.call(r,s)&&r[s]&&e.push(s)}}return e.join(" ")}e.exports?(n.default=n,e.exports=n):void 0===(r=function(){return n}.apply(t,[]))||(e.exports=r)}()},58875:(e,t,r)=>{var o;!function(){"use strict";var n=!("undefined"==typeof window||!window.document||!window.document.createElement),a={canUseDOM:n,canUseWorkers:"undefined"!=typeof Worker,canUseEventListeners:n&&!(!window.addEventListener&&!window.attachEvent),canUseViewport:n&&!!window.screen};void 0===(o=function(){return a}.call(t,r,t,e))||(e.exports=o)}()},96746:(e,t,r)=>{"use strict";Object.defineProperty(t,"__esModule",{value:!0});var o="function"==typeof Symbol&&"symbol"==typeof Symbol.iterator?function(e){return typeof e}:function(e){return e&&"function"==typeof Symbol&&e.constructor===Symbol&&e!==Symbol.prototype?"symbol":typeof e},n=s(r(99196)),a=s(r(49156)),l=s(r(76743));function s(e){return e&&e.__esModule?e:{default:e}}var i=void 0;function u(e,t){var r,l,s,d,c,f,p,h,b=[],g={};for(f=0;f<e.length;f++)if("string"!==(c=e[f]).type){if(!t.hasOwnProperty(c.value)||void 0===t[c.value])throw new Error("Invalid interpolation, missing component node: `"+c.value+"`");if("object"!==o(t[c.value]))throw new Error("Invalid interpolation, component node must be a ReactElement or null: `"+c.value+"`","\n> "+i);if("componentClose"===c.type)throw new Error("Missing opening component token: `"+c.value+"`");if("componentOpen"===c.type){r=t[c.value],s=f;break}b.push(t[c.value])}else b.push(c.value);return r&&(d=function(e,t){var r,o,n=t[e],a=0;for(o=e+1;o<t.length;o++)if((r=t[o]).value===n.value){if("componentOpen"===r.type){a++;continue}if("componentClose"===r.type){if(0===a)return o;a--}}throw new Error("Missing closing component token `"+n.value+"`")}(s,e),p=u(e.slice(s+1,d),t),l=n.default.cloneElement(r,{},p),b.push(l),d<e.length-1&&(h=u(e.slice(d+1),t),b=b.concat(h))),1===b.length?b[0]:(b.forEach((function(e,t){e&&(g["interpolation-child-"+t]=e)})),(0,a.default)(g))}t.default=function(e){var t=e.mixedString,r=e.components,n=e.throwErrors;if(i=t,!r)return t;if("object"!==(void 0===r?"undefined":o(r))){if(n)throw new Error("Interpolation Error: unable to process `"+t+"` because components is not an object");return t}var a=(0,l.default)(t);try{return u(a,r)}catch(e){if(n)throw new Error("Interpolation Error: unable to process `"+t+"` because of error `"+e.message+"`");return t}}},76743:e=>{"use strict";function t(e){return e.match(/^\{\{\//)?{type:"componentClose",value:e.replace(/\W/g,"")}:e.match(/\/\}\}$/)?{type:"componentSelfClosing",value:e.replace(/\W/g,"")}:e.match(/^\{\{/)?{type:"componentOpen",value:e.replace(/\W/g,"")}:{type:"string",value:e}}e.exports=function(e){return e.split(/(\{\{\/?\s*\w+\s*\/?\}\})/g).map(t)}},15446:(e,t,r)=>{"use strict";r.r(t)},89364:(e,t,r)=>{"use strict";r.r(t)},26242:(e,t,r)=>{"use strict";r.r(t)},91370:(e,t,r)=>{"use strict";r.r(t)},68798:(e,t,r)=>{"use strict";r.r(t)},16798:(e,t,r)=>{"use strict";r.r(t)},54760:(e,t,r)=>{"use strict";r.r(t)},40367:(e,t,r)=>{"use strict";r.r(t)},79520:(e,t,r)=>{"use strict";r.r(t)},8436:(e,t,r)=>{"use strict";r.r(t)},86264:(e,t,r)=>{"use strict";r.r(t)},14640:(e,t,r)=>{"use strict";r.r(t)},55022:(e,t,r)=>{"use strict";r.r(t)},60526:(e,t,r)=>{"use strict";r.r(t)},70418:(e,t,r)=>{"use strict";r.r(t)},15210:(e,t,r)=>{"use strict";r.r(t)},29001:(e,t,r)=>{"use strict";r.r(t)},17672:(e,t,r)=>{"use strict";r.r(t)},67496:(e,t,r)=>{"use strict";r.r(t)},10205:(e,t,r)=>{"use strict";r.r(t)},20784:(e,t,r)=>{"use strict";r.r(t)},82221:(e,t,r)=>{"use strict";r.r(t)},40265:(e,t,r)=>{"use strict";r.r(t)},49156:(e,t,r)=>{"use strict";var o=r(99196),n="function"==typeof Symbol&&Symbol.for&&Symbol.for("react.element")||60103,a=r(47942),l=r(29179),s=r(70397),i=".",u=":",d="function"==typeof Symbol&&Symbol.iterator,c="@@iterator";function f(e,t){return e&&"object"==typeof e&&null!=e.key?(r=e.key,o={"=":"=0",":":"=2"},"$"+(""+r).replace(/[=:]/g,(function(e){return o[e]}))):t.toString(36);var r,o}function p(e,t,r,o){var a,s=typeof e;if("undefined"!==s&&"boolean"!==s||(e=null),null===e||"string"===s||"number"===s||"object"===s&&e.$$typeof===n)return r(o,e,""===t?i+f(e,0):t),1;var h=0,b=""===t?i:t+u;if(Array.isArray(e))for(var g=0;g<e.length;g++)h+=p(a=e[g],b+f(a,g),r,o);else{var m=function(e){var t=e&&(d&&e[d]||e[c]);if("function"==typeof t)return t}(e);if(m)for(var y,v=m.call(e),_=0;!(y=v.next()).done;)h+=p(a=y.value,b+f(a,_++),r,o);else if("object"===s){var x=""+e;l(!1,"Objects are not valid as a React child (found: %s).%s","[object Object]"===x?"object with keys {"+Object.keys(e).join(", ")+"}":x,"")}}return h}var h=/\/+/g;function b(e){return(""+e).replace(h,"$&/")}var g,m,y=v,v=function(e){var t=this;if(t.instancePool.length){var r=t.instancePool.pop();return t.call(r,e),r}return new t(e)};function _(e,t,r,o){this.result=e,this.keyPrefix=t,this.func=r,this.context=o,this.count=0}function x(e,t,r){var n,l,s=e.result,i=e.keyPrefix,u=e.func,d=e.context,c=u.call(d,t,e.count++);Array.isArray(c)?C(c,s,r,a.thatReturnsArgument):null!=c&&(o.isValidElement(c)&&(n=c,l=i+(!c.key||t&&t.key===c.key?"":b(c.key)+"/")+r,c=o.cloneElement(n,{key:l},void 0!==n.props?n.props.children:void 0)),s.push(c))}function C(e,t,r,o,n){var a="";null!=r&&(a=b(r)+"/");var l=_.getPooled(t,a,o,n);!function(e,t,r){null==e||p(e,"",t,r)}(e,x,l),_.release(l)}_.prototype.destructor=function(){this.result=null,this.keyPrefix=null,this.func=null,this.context=null,this.count=0},g=function(e,t,r,o){var n=this;if(n.instancePool.length){var a=n.instancePool.pop();return n.call(a,e,t,r,o),a}return new n(e,t,r,o)},(m=_).instancePool=[],m.getPooled=g||y,m.poolSize||(m.poolSize=10),m.release=function(e){var t=this;l(e instanceof t,"Trying to release an instance into a pool of a different type."),e.destructor(),t.instancePool.length<t.poolSize&&t.instancePool.push(e)},e.exports=function(e){if("object"!=typeof e||!e||Array.isArray(e))return s(!1,"React.addons.createFragment only accepts a single object. Got: %s",e),e;if(o.isValidElement(e))return s(!1,"React.addons.createFragment does not accept a ReactElement without a wrapper object."),e;l(1!==e.nodeType,"React.addons.createFragment(...): Encountered an invalid child; DOM elements are not valid children of React components.");var t=[];for(var r in e)C(e[r],t,r,a.thatReturnsArgument);return t}},47942:e=>{"use strict";function t(e){return function(){return e}}var r=function(){};r.thatReturns=t,r.thatReturnsFalse=t(!1),r.thatReturnsTrue=t(!0),r.thatReturnsNull=t(null),r.thatReturnsThis=function(){return this},r.thatReturnsArgument=function(e){return e},e.exports=r},29179:e=>{"use strict";e.exports=function(e,t,r,o,n,a,l,s){if(!e){var i;if(void 0===t)i=new Error("Minified exception occurred; use the non-minified dev environment for the full error message and additional helpful warnings.");else{var u=[r,o,n,a,l,s],d=0;(i=new Error(t.replace(/%s/g,(function(){return u[d++]})))).name="Invariant Violation"}throw i.framesToPop=1,i}}},70397:(e,t,r)=>{"use strict";var o=r(47942);e.exports=o},46871:(e,t,r)=>{"use strict";function o(){var e=this.constructor.getDerivedStateFromProps(this.props,this.state);null!=e&&this.setState(e)}function n(e){this.setState(function(t){var r=this.constructor.getDerivedStateFromProps(e,t);return null!=r?r:null}.bind(this))}function a(e,t){try{var r=this.props,o=this.state;this.props=e,this.state=t,this.__reactInternalSnapshotFlag=!0,this.__reactInternalSnapshot=this.getSnapshotBeforeUpdate(r,o)}finally{this.props=r,this.state=o}}function l(e){var t=e.prototype;if(!t||!t.isReactComponent)throw new Error("Can only polyfill class components");if("function"!=typeof e.getDerivedStateFromProps&&"function"!=typeof t.getSnapshotBeforeUpdate)return e;var r=null,l=null,s=null;if("function"==typeof t.componentWillMount?r="componentWillMount":"function"==typeof t.UNSAFE_componentWillMount&&(r="UNSAFE_componentWillMount"),"function"==typeof t.componentWillReceiveProps?l="componentWillReceiveProps":"function"==typeof t.UNSAFE_componentWillReceiveProps&&(l="UNSAFE_componentWillReceiveProps"),"function"==typeof t.componentWillUpdate?s="componentWillUpdate":"function"==typeof t.UNSAFE_componentWillUpdate&&(s="UNSAFE_componentWillUpdate"),null!==r||null!==l||null!==s){var i=e.displayName||e.name,u="function"==typeof e.getDerivedStateFromProps?"getDerivedStateFromProps()":"getSnapshotBeforeUpdate()";throw Error("Unsafe legacy lifecycles will not be called for components using new component APIs.\n\n"+i+" uses "+u+" but also contains the following legacy lifecycles:"+(null!==r?"\n  "+r:"")+(null!==l?"\n  "+l:"")+(null!==s?"\n  "+s:"")+"\n\nThe above lifecycles should be removed. Learn more about this warning here:\nhttps://fb.me/react-async-component-lifecycle-hooks")}if("function"==typeof e.getDerivedStateFromProps&&(t.componentWillMount=o,t.componentWillReceiveProps=n),"function"==typeof t.getSnapshotBeforeUpdate){if("function"!=typeof t.componentDidUpdate)throw new Error("Cannot polyfill getSnapshotBeforeUpdate() for components that do not define componentDidUpdate() on the prototype");t.componentWillUpdate=a;var d=t.componentDidUpdate;t.componentDidUpdate=function(e,t,r){var o=this.__reactInternalSnapshotFlag?this.__reactInternalSnapshot:r;d.call(this,e,t,o)}}return e}r.r(t),r.d(t,{polyfill:()=>l}),o.__suppressDeprecationWarning=!0,n.__suppressDeprecationWarning=!0,a.__suppressDeprecationWarning=!0},29983:(e,t,r)=>{"use strict";Object.defineProperty(t,"__esModule",{value:!0}),t.bodyOpenClassName=t.portalClassName=void 0;var o=Object.assign||function(e){for(var t=1;t<arguments.length;t++){var r=arguments[t];for(var o in r)Object.prototype.hasOwnProperty.call(r,o)&&(e[o]=r[o])}return e},n=function(){function e(e,t){for(var r=0;r<t.length;r++){var o=t[r];o.enumerable=o.enumerable||!1,o.configurable=!0,"value"in o&&(o.writable=!0),Object.defineProperty(e,o.key,o)}}return function(t,r,o){return r&&e(t.prototype,r),o&&e(t,o),t}}(),a=r(99196),l=h(a),s=h(r(91850)),i=h(r(85890)),u=h(r(28747)),d=function(e){if(e&&e.__esModule)return e;var t={};if(null!=e)for(var r in e)Object.prototype.hasOwnProperty.call(e,r)&&(t[r]=e[r]);return t.default=e,t}(r(57149)),c=r(51112),f=h(c),p=r(46871);function h(e){return e&&e.__esModule?e:{default:e}}function b(e,t){if(!e)throw new ReferenceError("this hasn't been initialised - super() hasn't been called");return!t||"object"!=typeof t&&"function"!=typeof t?e:t}var g=t.portalClassName="ReactModalPortal",m=t.bodyOpenClassName="ReactModal__Body--open",y=c.canUseDOM&&void 0!==s.default.createPortal,v=function(){return y?s.default.createPortal:s.default.unstable_renderSubtreeIntoContainer};function _(e){return e()}var x=function(e){function t(){var e,r,n;!function(e,t){if(!(e instanceof t))throw new TypeError("Cannot call a class as a function")}(this,t);for(var a=arguments.length,i=Array(a),d=0;d<a;d++)i[d]=arguments[d];return r=n=b(this,(e=t.__proto__||Object.getPrototypeOf(t)).call.apply(e,[this].concat(i))),n.removePortal=function(){!y&&s.default.unmountComponentAtNode(n.node);var e=_(n.props.parentSelector);e&&e.contains(n.node)?e.removeChild(n.node):console.warn('React-Modal: "parentSelector" prop did not returned any DOM element. Make sure that the parent element is unmounted to avoid any memory leaks.')},n.portalRef=function(e){n.portal=e},n.renderPortal=function(e){var r=v()(n,l.default.createElement(u.default,o({defaultStyles:t.defaultStyles},e)),n.node);n.portalRef(r)},b(n,r)}return function(e,t){if("function"!=typeof t&&null!==t)throw new TypeError("Super expression must either be null or a function, not "+typeof t);e.prototype=Object.create(t&&t.prototype,{constructor:{value:e,enumerable:!1,writable:!0,configurable:!0}}),t&&(Object.setPrototypeOf?Object.setPrototypeOf(e,t):e.__proto__=t)}(t,e),n(t,[{key:"componentDidMount",value:function(){c.canUseDOM&&(y||(this.node=document.createElement("div")),this.node.className=this.props.portalClassName,_(this.props.parentSelector).appendChild(this.node),!y&&this.renderPortal(this.props))}},{key:"getSnapshotBeforeUpdate",value:function(e){return{prevParent:_(e.parentSelector),nextParent:_(this.props.parentSelector)}}},{key:"componentDidUpdate",value:function(e,t,r){if(c.canUseDOM){var o=this.props,n=o.isOpen,a=o.portalClassName;e.portalClassName!==a&&(this.node.className=a);var l=r.prevParent,s=r.nextParent;s!==l&&(l.removeChild(this.node),s.appendChild(this.node)),(e.isOpen||n)&&!y&&this.renderPortal(this.props)}}},{key:"componentWillUnmount",value:function(){if(c.canUseDOM&&this.node&&this.portal){var e=this.portal.state,t=Date.now(),r=e.isOpen&&this.props.closeTimeoutMS&&(e.closesAt||t+this.props.closeTimeoutMS);r?(e.beforeClose||this.portal.closeWithTimeout(),setTimeout(this.removePortal,r-t)):this.removePortal()}}},{key:"render",value:function(){return c.canUseDOM&&y?(!this.node&&y&&(this.node=document.createElement("div")),v()(l.default.createElement(u.default,o({ref:this.portalRef,defaultStyles:t.defaultStyles},this.props)),this.node)):null}}],[{key:"setAppElement",value:function(e){d.setElement(e)}}]),t}(a.Component);x.propTypes={isOpen:i.default.bool.isRequired,style:i.default.shape({content:i.default.object,overlay:i.default.object}),portalClassName:i.default.string,bodyOpenClassName:i.default.string,htmlOpenClassName:i.default.string,className:i.default.oneOfType([i.default.string,i.default.shape({base:i.default.string.isRequired,afterOpen:i.default.string.isRequired,beforeClose:i.default.string.isRequired})]),overlayClassName:i.default.oneOfType([i.default.string,i.default.shape({base:i.default.string.isRequired,afterOpen:i.default.string.isRequired,beforeClose:i.default.string.isRequired})]),appElement:i.default.instanceOf(f.default),onAfterOpen:i.default.func,onRequestClose:i.default.func,closeTimeoutMS:i.default.number,ariaHideApp:i.default.bool,shouldFocusAfterRender:i.default.bool,shouldCloseOnOverlayClick:i.default.bool,shouldReturnFocusAfterClose:i.default.bool,preventScroll:i.default.bool,parentSelector:i.default.func,aria:i.default.object,data:i.default.object,role:i.default.string,contentLabel:i.default.string,shouldCloseOnEsc:i.default.bool,overlayRef:i.default.func,contentRef:i.default.func,id:i.default.string,overlayElement:i.default.func,contentElement:i.default.func},x.defaultProps={isOpen:!1,portalClassName:g,bodyOpenClassName:m,role:"dialog",ariaHideApp:!0,closeTimeoutMS:0,shouldFocusAfterRender:!0,shouldCloseOnEsc:!0,shouldCloseOnOverlayClick:!0,shouldReturnFocusAfterClose:!0,preventScroll:!1,parentSelector:function(){return document.body},overlayElement:function(e,t){return l.default.createElement("div",e,t)},contentElement:function(e,t){return l.default.createElement("div",e,t)}},x.defaultStyles={overlay:{position:"fixed",top:0,left:0,right:0,bottom:0,backgroundColor:"rgba(255, 255, 255, 0.75)"},content:{position:"absolute",top:"40px",left:"40px",right:"40px",bottom:"40px",border:"1px solid #ccc",background:"#fff",overflow:"auto",WebkitOverflowScrolling:"touch",borderRadius:"4px",outline:"none",padding:"20px"}},(0,p.polyfill)(x),t.default=x},28747:(e,t,r)=>{"use strict";Object.defineProperty(t,"__esModule",{value:!0});var o=Object.assign||function(e){for(var t=1;t<arguments.length;t++){var r=arguments[t];for(var o in r)Object.prototype.hasOwnProperty.call(r,o)&&(e[o]=r[o])}return e},n="function"==typeof Symbol&&"symbol"==typeof Symbol.iterator?function(e){return typeof e}:function(e){return e&&"function"==typeof Symbol&&e.constructor===Symbol&&e!==Symbol.prototype?"symbol":typeof e},a=function(){function e(e,t){for(var r=0;r<t.length;r++){var o=t[r];o.enumerable=o.enumerable||!1,o.configurable=!0,"value"in o&&(o.writable=!0),Object.defineProperty(e,o.key,o)}}return function(t,r,o){return r&&e(t.prototype,r),o&&e(t,o),t}}(),l=r(99196),s=b(r(85890)),i=h(r(99685)),u=b(r(88338)),d=h(r(57149)),c=h(r(32409)),f=b(r(51112)),p=b(r(89623));function h(e){if(e&&e.__esModule)return e;var t={};if(null!=e)for(var r in e)Object.prototype.hasOwnProperty.call(e,r)&&(t[r]=e[r]);return t.default=e,t}function b(e){return e&&e.__esModule?e:{default:e}}r(35063);var g={overlay:"ReactModal__Overlay",content:"ReactModal__Content"},m=0,y=function(e){function t(e){!function(e,t){if(!(e instanceof t))throw new TypeError("Cannot call a class as a function")}(this,t);var r=function(e,t){if(!e)throw new ReferenceError("this hasn't been initialised - super() hasn't been called");return!t||"object"!=typeof t&&"function"!=typeof t?e:t}(this,(t.__proto__||Object.getPrototypeOf(t)).call(this,e));return r.setOverlayRef=function(e){r.overlay=e,r.props.overlayRef&&r.props.overlayRef(e)},r.setContentRef=function(e){r.content=e,r.props.contentRef&&r.props.contentRef(e)},r.afterClose=function(){var e=r.props,t=e.appElement,o=e.ariaHideApp,n=e.htmlOpenClassName,a=e.bodyOpenClassName;a&&c.remove(document.body,a),n&&c.remove(document.getElementsByTagName("html")[0],n),o&&m>0&&0==(m-=1)&&d.show(t),r.props.shouldFocusAfterRender&&(r.props.shouldReturnFocusAfterClose?(i.returnFocus(r.props.preventScroll),i.teardownScopedFocus()):i.popWithoutFocus()),r.props.onAfterClose&&r.props.onAfterClose(),p.default.deregister(r)},r.open=function(){r.beforeOpen(),r.state.afterOpen&&r.state.beforeClose?(clearTimeout(r.closeTimer),r.setState({beforeClose:!1})):(r.props.shouldFocusAfterRender&&(i.setupScopedFocus(r.node),i.markForFocusLater()),r.setState({isOpen:!0},(function(){r.setState({afterOpen:!0}),r.props.isOpen&&r.props.onAfterOpen&&r.props.onAfterOpen({overlayEl:r.overlay,contentEl:r.content})})))},r.close=function(){r.props.closeTimeoutMS>0?r.closeWithTimeout():r.closeWithoutTimeout()},r.focusContent=function(){return r.content&&!r.contentHasFocus()&&r.content.focus({preventScroll:!0})},r.closeWithTimeout=function(){var e=Date.now()+r.props.closeTimeoutMS;r.setState({beforeClose:!0,closesAt:e},(function(){r.closeTimer=setTimeout(r.closeWithoutTimeout,r.state.closesAt-Date.now())}))},r.closeWithoutTimeout=function(){r.setState({beforeClose:!1,isOpen:!1,afterOpen:!1,closesAt:null},r.afterClose)},r.handleKeyDown=function(e){9===e.keyCode&&(0,u.default)(r.content,e),r.props.shouldCloseOnEsc&&27===e.keyCode&&(e.stopPropagation(),r.requestClose(e))},r.handleOverlayOnClick=function(e){null===r.shouldClose&&(r.shouldClose=!0),r.shouldClose&&r.props.shouldCloseOnOverlayClick&&(r.ownerHandlesClose()?r.requestClose(e):r.focusContent()),r.shouldClose=null},r.handleContentOnMouseUp=function(){r.shouldClose=!1},r.handleOverlayOnMouseDown=function(e){r.props.shouldCloseOnOverlayClick||e.target!=r.overlay||e.preventDefault()},r.handleContentOnClick=function(){r.shouldClose=!1},r.handleContentOnMouseDown=function(){r.shouldClose=!1},r.requestClose=function(e){return r.ownerHandlesClose()&&r.props.onRequestClose(e)},r.ownerHandlesClose=function(){return r.props.onRequestClose},r.shouldBeClosed=function(){return!r.state.isOpen&&!r.state.beforeClose},r.contentHasFocus=function(){return document.activeElement===r.content||r.content.contains(document.activeElement)},r.buildClassName=function(e,t){var o="object"===(void 0===t?"undefined":n(t))?t:{base:g[e],afterOpen:g[e]+"--after-open",beforeClose:g[e]+"--before-close"},a=o.base;return r.state.afterOpen&&(a=a+" "+o.afterOpen),r.state.beforeClose&&(a=a+" "+o.beforeClose),"string"==typeof t&&t?a+" "+t:a},r.attributesFromObject=function(e,t){return Object.keys(t).reduce((function(r,o){return r[e+"-"+o]=t[o],r}),{})},r.state={afterOpen:!1,beforeClose:!1},r.shouldClose=null,r.moveFromContentToOverlay=null,r}return function(e,t){if("function"!=typeof t&&null!==t)throw new TypeError("Super expression must either be null or a function, not "+typeof t);e.prototype=Object.create(t&&t.prototype,{constructor:{value:e,enumerable:!1,writable:!0,configurable:!0}}),t&&(Object.setPrototypeOf?Object.setPrototypeOf(e,t):e.__proto__=t)}(t,e),a(t,[{key:"componentDidMount",value:function(){this.props.isOpen&&this.open()}},{key:"componentDidUpdate",value:function(e,t){this.props.isOpen&&!e.isOpen?this.open():!this.props.isOpen&&e.isOpen&&this.close(),this.props.shouldFocusAfterRender&&this.state.isOpen&&!t.isOpen&&this.focusContent()}},{key:"componentWillUnmount",value:function(){this.state.isOpen&&this.afterClose(),clearTimeout(this.closeTimer)}},{key:"beforeOpen",value:function(){var e=this.props,t=e.appElement,r=e.ariaHideApp,o=e.htmlOpenClassName,n=e.bodyOpenClassName;n&&c.add(document.body,n),o&&c.add(document.getElementsByTagName("html")[0],o),r&&(m+=1,d.hide(t)),p.default.register(this)}},{key:"render",value:function(){var e=this.props,t=e.id,r=e.className,n=e.overlayClassName,a=e.defaultStyles,l=e.children,s=r?{}:a.content,i=n?{}:a.overlay;if(this.shouldBeClosed())return null;var u={ref:this.setOverlayRef,className:this.buildClassName("overlay",n),style:o({},i,this.props.style.overlay),onClick:this.handleOverlayOnClick,onMouseDown:this.handleOverlayOnMouseDown},d=o({id:t,ref:this.setContentRef,style:o({},s,this.props.style.content),className:this.buildClassName("content",r),tabIndex:"-1",onKeyDown:this.handleKeyDown,onMouseDown:this.handleContentOnMouseDown,onMouseUp:this.handleContentOnMouseUp,onClick:this.handleContentOnClick,role:this.props.role,"aria-label":this.props.contentLabel},this.attributesFromObject("aria",o({modal:!0},this.props.aria)),this.attributesFromObject("data",this.props.data||{}),{"data-testid":this.props.testId}),c=this.props.contentElement(d,l);return this.props.overlayElement(u,c)}}]),t}(l.Component);y.defaultProps={style:{overlay:{},content:{}},defaultStyles:{}},y.propTypes={isOpen:s.default.bool.isRequired,defaultStyles:s.default.shape({content:s.default.object,overlay:s.default.object}),style:s.default.shape({content:s.default.object,overlay:s.default.object}),className:s.default.oneOfType([s.default.string,s.default.object]),overlayClassName:s.default.oneOfType([s.default.string,s.default.object]),bodyOpenClassName:s.default.string,htmlOpenClassName:s.default.string,ariaHideApp:s.default.bool,appElement:s.default.instanceOf(f.default),onAfterOpen:s.default.func,onAfterClose:s.default.func,onRequestClose:s.default.func,closeTimeoutMS:s.default.number,shouldFocusAfterRender:s.default.bool,shouldCloseOnOverlayClick:s.default.bool,shouldReturnFocusAfterClose:s.default.bool,preventScroll:s.default.bool,role:s.default.string,contentLabel:s.default.string,aria:s.default.object,data:s.default.object,children:s.default.node,shouldCloseOnEsc:s.default.bool,overlayRef:s.default.func,contentRef:s.default.func,id:s.default.string,overlayElement:s.default.func,contentElement:s.default.func,testId:s.default.string},t.default=y,e.exports=t.default},57149:(e,t,r)=>{"use strict";Object.defineProperty(t,"__esModule",{value:!0}),t.assertNodeList=s,t.setElement=function(e){var t=e;if("string"==typeof t&&a.canUseDOM){var r=document.querySelectorAll(t);s(r,t),t="length"in r?r[0]:r}return l=t||l},t.validateElement=i,t.hide=function(e){i(e)&&(e||l).setAttribute("aria-hidden","true")},t.show=function(e){i(e)&&(e||l).removeAttribute("aria-hidden")},t.documentNotReadyOrSSRTesting=function(){l=null},t.resetForTesting=function(){l=null};var o,n=(o=r(42473))&&o.__esModule?o:{default:o},a=r(51112),l=null;function s(e,t){if(!e||!e.length)throw new Error("react-modal: No elements were found for selector "+t+".")}function i(e){return!(!e&&!l&&((0,n.default)(!1,["react-modal: App element is not defined.","Please use `Modal.setAppElement(el)` or set `appElement={el}`.","This is needed so screen readers don't see main content","when modal is opened. It is not recommended, but you can opt-out","by setting `ariaHideApp={false}`."].join(" ")),1))}},35063:(e,t,r)=>{"use strict";var o,n=(o=r(89623))&&o.__esModule?o:{default:o},a=void 0,l=void 0,s=[];function i(){0!==s.length&&s[s.length-1].focusContent()}n.default.subscribe((function(e,t){a&&l||((a=document.createElement("div")).setAttribute("data-react-modal-body-trap",""),a.style.position="absolute",a.style.opacity="0",a.setAttribute("tabindex","0"),a.addEventListener("focus",i),(l=a.cloneNode()).addEventListener("focus",i)),(s=t).length>0?(document.body.firstChild!==a&&document.body.insertBefore(a,document.body.firstChild),document.body.lastChild!==l&&document.body.appendChild(l)):(a.parentElement&&a.parentElement.removeChild(a),l.parentElement&&l.parentElement.removeChild(l))}))},32409:(e,t)=>{"use strict";Object.defineProperty(t,"__esModule",{value:!0}),t.dumpClassLists=function(){};var r={},o={};t.add=function(e,t){return n=e.classList,a="html"==e.nodeName.toLowerCase()?r:o,void t.split(" ").forEach((function(e){!function(e,t){e[t]||(e[t]=0),e[t]+=1}(a,e),n.add(e)}));var n,a},t.remove=function(e,t){return n=e.classList,a="html"==e.nodeName.toLowerCase()?r:o,void t.split(" ").forEach((function(e){!function(e,t){e[t]&&(e[t]-=1)}(a,e),0===a[e]&&n.remove(e)}));var n,a}},99685:(e,t,r)=>{"use strict";Object.defineProperty(t,"__esModule",{value:!0}),t.handleBlur=i,t.handleFocus=u,t.markForFocusLater=function(){a.push(document.activeElement)},t.returnFocus=function(){var e=arguments.length>0&&void 0!==arguments[0]&&arguments[0],t=null;try{return void(0!==a.length&&(t=a.pop()).focus({preventScroll:e}))}catch(e){console.warn(["You tried to return focus to",t,"but it is not in the DOM anymore"].join(" "))}},t.popWithoutFocus=function(){a.length>0&&a.pop()},t.setupScopedFocus=function(e){l=e,window.addEventListener?(window.addEventListener("blur",i,!1),document.addEventListener("focus",u,!0)):(window.attachEvent("onBlur",i),document.attachEvent("onFocus",u))},t.teardownScopedFocus=function(){l=null,window.addEventListener?(window.removeEventListener("blur",i),document.removeEventListener("focus",u)):(window.detachEvent("onBlur",i),document.detachEvent("onFocus",u))};var o,n=(o=r(37845))&&o.__esModule?o:{default:o},a=[],l=null,s=!1;function i(){s=!0}function u(){if(s){if(s=!1,!l)return;setTimeout((function(){l.contains(document.activeElement)||((0,n.default)(l)[0]||l).focus()}),0)}}},89623:(e,t)=>{"use strict";Object.defineProperty(t,"__esModule",{value:!0});var r=new function e(){var t=this;!function(e,t){if(!(e instanceof t))throw new TypeError("Cannot call a class as a function")}(this,e),this.register=function(e){-1===t.openInstances.indexOf(e)&&(t.openInstances.push(e),t.emit("register"))},this.deregister=function(e){var r=t.openInstances.indexOf(e);-1!==r&&(t.openInstances.splice(r,1),t.emit("deregister"))},this.subscribe=function(e){t.subscribers.push(e)},this.emit=function(e){t.subscribers.forEach((function(r){return r(e,t.openInstances.slice())}))},this.openInstances=[],this.subscribers=[]};t.default=r,e.exports=t.default},51112:(e,t,r)=>{"use strict";Object.defineProperty(t,"__esModule",{value:!0}),t.canUseDOM=void 0;var o,n=((o=r(58875))&&o.__esModule?o:{default:o}).default,a=n.canUseDOM?window.HTMLElement:{};t.canUseDOM=n.canUseDOM,t.default=a},88338:(e,t,r)=>{"use strict";Object.defineProperty(t,"__esModule",{value:!0}),t.default=function(e,t){var r=(0,n.default)(e);if(r.length){var o=void 0,a=t.shiftKey,l=r[0],s=r[r.length-1];if(e===document.activeElement){if(!a)return;o=s}if(s!==document.activeElement||a||(o=l),l===document.activeElement&&a&&(o=s),o)return t.preventDefault(),void o.focus();var i=/(\bChrome\b|\bSafari\b)\//.exec(navigator.userAgent);if(null!=i&&"Chrome"!=i[1]&&null==/\biPod\b|\biPad\b/g.exec(navigator.userAgent)){var u=r.indexOf(document.activeElement);if(u>-1&&(u+=a?-1:1),void 0===(o=r[u]))return t.preventDefault(),void(o=a?s:l).focus();t.preventDefault(),o.focus()}}else t.preventDefault()};var o,n=(o=r(37845))&&o.__esModule?o:{default:o};e.exports=t.default},37845:(e,t)=>{"use strict";Object.defineProperty(t,"__esModule",{value:!0}),t.default=function(e){return[].slice.call(e.querySelectorAll("*"),0).filter(n)};var r=/input|select|textarea|button|object/;function o(e){var t=e.offsetWidth<=0&&e.offsetHeight<=0;if(t&&!e.innerHTML)return!0;var r=window.getComputedStyle(e);return t?"visible"!==r.getPropertyValue("overflow")||e.scrollWidth<=0&&e.scrollHeight<=0:"none"==r.getPropertyValue("display")}function n(e){var t=e.getAttribute("tabindex");null===t&&(t=void 0);var n=isNaN(t);return(n||t>=0)&&function(e,t){var n=e.nodeName.toLowerCase();return(r.test(n)&&!e.disabled||"a"===n&&e.href||t)&&function(e){for(var t=e;t&&t!==document.body;){if(o(t))return!1;t=t.parentNode}return!0}(e)}(e,!n)}e.exports=t.default},83253:(e,t,r)=>{"use strict";Object.defineProperty(t,"__esModule",{value:!0});var o,n=(o=r(29983))&&o.__esModule?o:{default:o};t.default=n.default,e.exports=t.default},340:(e,t,r)=>{"use strict";r.r(t),r.d(t,{Tab:()=>j,TabList:()=>P,TabPanel:()=>T,Tabs:()=>O,resetIdCounter:()=>g}),r(85890);var o=r(99196),n=r.n(o);function a(e){return e.type&&"Tab"===e.type.tabsRole}function l(e){return e.type&&"TabPanel"===e.type.tabsRole}function s(e){return e.type&&"TabList"===e.type.tabsRole}function i(e,t,r){return t in e?Object.defineProperty(e,t,{value:r,enumerable:!0,configurable:!0,writable:!0}):e[t]=r,e}function u(e,t){return o.Children.map(e,(function(e){return null===e?null:function(e){return a(e)||s(e)||l(e)}(e)?t(e):e.props&&e.props.children&&"object"==typeof e.props.children?(0,o.cloneElement)(e,function(e){for(var t=1;t<arguments.length;t++){var r=null!=arguments[t]?arguments[t]:{},o=Object.keys(r);"function"==typeof Object.getOwnPropertySymbols&&(o=o.concat(Object.getOwnPropertySymbols(r).filter((function(e){return Object.getOwnPropertyDescriptor(r,e).enumerable})))),o.forEach((function(t){i(e,t,r[t])}))}return e}({},e.props,{children:u(e.props.children,t)})):e}))}function d(e,t){return o.Children.forEach(e,(function(e){null!==e&&(a(e)||l(e)?t(e):e.props&&e.props.children&&"object"==typeof e.props.children&&(s(e)&&t(e),d(e.props.children,t)))}))}var c,f=r(94184),p=r.n(f),h=0;function b(){return"react-tabs-"+h++}function g(){h=0}function m(e){var t=0;return d(e,(function(e){a(e)&&t++})),t}function y(){return y=Object.assign||function(e){for(var t=1;t<arguments.length;t++){var r=arguments[t];for(var o in r)Object.prototype.hasOwnProperty.call(r,o)&&(e[o]=r[o])}return e},y.apply(this,arguments)}function v(e){return e&&"getAttribute"in e}function _(e){return v(e)&&"tab"===e.getAttribute("role")}function x(e){return v(e)&&"true"===e.getAttribute("aria-disabled")}try{c=!("undefined"==typeof window||!window.document||!window.document.activeElement)}catch(e){c=!1}var C=function(e){var t,r;function i(){for(var t,r=arguments.length,o=new Array(r),n=0;n<r;n++)o[n]=arguments[n];return(t=e.call.apply(e,[this].concat(o))||this).tabNodes=[],t.handleKeyDown=function(e){if(t.isTabFromContainer(e.target)){var r=t.props.selectedIndex,o=!1,n=!1;32!==e.keyCode&&13!==e.keyCode||(o=!0,n=!1,t.handleClick(e)),37===e.keyCode||38===e.keyCode?(r=t.getPrevTab(r),o=!0,n=!0):39===e.keyCode||40===e.keyCode?(r=t.getNextTab(r),o=!0,n=!0):35===e.keyCode?(r=t.getLastTab(),o=!0,n=!0):36===e.keyCode&&(r=t.getFirstTab(),o=!0,n=!0),o&&e.preventDefault(),n&&t.setSelected(r,e)}},t.handleClick=function(e){var r=e.target;do{if(t.isTabFromContainer(r)){if(x(r))return;var o=[].slice.call(r.parentNode.children).filter(_).indexOf(r);return void t.setSelected(o,e)}}while(null!=(r=r.parentNode))},t}r=e,(t=i).prototype=Object.create(r.prototype),t.prototype.constructor=t,t.__proto__=r;var f=i.prototype;return f.setSelected=function(e,t){if(!(e<0||e>=this.getTabsCount())){var r=this.props;(0,r.onSelect)(e,r.selectedIndex,t)}},f.getNextTab=function(e){for(var t=this.getTabsCount(),r=e+1;r<t;r++)if(!x(this.getTab(r)))return r;for(var o=0;o<e;o++)if(!x(this.getTab(o)))return o;return e},f.getPrevTab=function(e){for(var t=e;t--;)if(!x(this.getTab(t)))return t;for(t=this.getTabsCount();t-- >e;)if(!x(this.getTab(t)))return t;return e},f.getFirstTab=function(){for(var e=this.getTabsCount(),t=0;t<e;t++)if(!x(this.getTab(t)))return t;return null},f.getLastTab=function(){for(var e=this.getTabsCount();e--;)if(!x(this.getTab(e)))return e;return null},f.getTabsCount=function(){return m(this.props.children)},f.getPanelsCount=function(){return e=this.props.children,t=0,d(e,(function(e){l(e)&&t++})),t;var e,t},f.getTab=function(e){return this.tabNodes["tabs-"+e]},f.getChildren=function(){var e=this,t=0,r=this.props,i=r.children,d=r.disabledTabClassName,f=r.focus,p=r.forceRenderTabPanel,h=r.selectedIndex,g=r.selectedTabClassName,m=r.selectedTabPanelClassName;this.tabIds=this.tabIds||[],this.panelIds=this.panelIds||[];for(var y=this.tabIds.length-this.getTabsCount();y++<0;)this.tabIds.push(b()),this.panelIds.push(b());return u(i,(function(r){var i=r;if(s(r)){var b=0,y=!1;c&&(y=n().Children.toArray(r.props.children).filter(a).some((function(t,r){return document.activeElement===e.getTab(r)}))),i=(0,o.cloneElement)(r,{children:u(r.props.children,(function(t){var r="tabs-"+b,n=h===b,a={tabRef:function(t){e.tabNodes[r]=t},id:e.tabIds[b],panelId:e.panelIds[b],selected:n,focus:n&&(f||y)};return g&&(a.selectedClassName=g),d&&(a.disabledClassName=d),b++,(0,o.cloneElement)(t,a)}))})}else if(l(r)){var v={id:e.panelIds[t],tabId:e.tabIds[t],selected:h===t};p&&(v.forceRender=p),m&&(v.selectedClassName=m),t++,i=(0,o.cloneElement)(r,v)}return i}))},f.isTabFromContainer=function(e){if(!_(e))return!1;var t=e.parentElement;do{if(t===this.node)return!0;if(t.getAttribute("data-tabs"))break;t=t.parentElement}while(t);return!1},f.render=function(){var e=this,t=this.props,r=(t.children,t.className),o=(t.disabledTabClassName,t.domRef),a=(t.focus,t.forceRenderTabPanel,t.onSelect,t.selectedIndex,t.selectedTabClassName,t.selectedTabPanelClassName,function(e,t){if(null==e)return{};var r,o,n={},a=Object.keys(e);for(o=0;o<a.length;o++)r=a[o],t.indexOf(r)>=0||(n[r]=e[r]);return n}(t,["children","className","disabledTabClassName","domRef","focus","forceRenderTabPanel","onSelect","selectedIndex","selectedTabClassName","selectedTabPanelClassName"]));return n().createElement("div",y({},a,{className:p()(r),onClick:this.handleClick,onKeyDown:this.handleKeyDown,ref:function(t){e.node=t,o&&o(t)},"data-tabs":!0}),this.getChildren())},i}(o.Component);C.defaultProps={className:"react-tabs",focus:!1},C.propTypes={};var O=function(e){var t,r;function o(t){var r;return(r=e.call(this,t)||this).handleSelected=function(e,t,n){var a=r.props.onSelect;if("function"!=typeof a||!1!==a(e,t,n)){var l={focus:"keydown"===n.type};o.inUncontrolledMode(r.props)&&(l.selectedIndex=e),r.setState(l)}},r.state=o.copyPropsToState(r.props,{},t.defaultFocus),r}r=e,(t=o).prototype=Object.create(r.prototype),t.prototype.constructor=t,t.__proto__=r;var a=o.prototype;return a.componentWillReceiveProps=function(e){this.setState((function(t){return o.copyPropsToState(e,t)}))},o.inUncontrolledMode=function(e){return null===e.selectedIndex},o.copyPropsToState=function(e,t,r){void 0===r&&(r=!1);var n={focus:r};if(o.inUncontrolledMode(e)){var a,l=m(e.children)-1;a=null!=t.selectedIndex?Math.min(t.selectedIndex,l):e.defaultIndex||0,n.selectedIndex=a}return n},a.render=function(){var e=this.props,t=e.children,r=(e.defaultIndex,e.defaultFocus,function(e,t){if(null==e)return{};var r,o,n={},a=Object.keys(e);for(o=0;o<a.length;o++)r=a[o],t.indexOf(r)>=0||(n[r]=e[r]);return n}(e,["children","defaultIndex","defaultFocus"])),o=this.state,a=o.focus,l=o.selectedIndex;return r.focus=a,r.onSelect=this.handleSelected,null!=l&&(r.selectedIndex=l),n().createElement(C,r,t)},o}(o.Component);function w(){return w=Object.assign||function(e){for(var t=1;t<arguments.length;t++){var r=arguments[t];for(var o in r)Object.prototype.hasOwnProperty.call(r,o)&&(e[o]=r[o])}return e},w.apply(this,arguments)}O.defaultProps={defaultFocus:!1,forceRenderTabPanel:!1,selectedIndex:null,defaultIndex:null},O.propTypes={},O.tabsRole="Tabs";var P=function(e){var t,r;function o(){return e.apply(this,arguments)||this}return r=e,(t=o).prototype=Object.create(r.prototype),t.prototype.constructor=t,t.__proto__=r,o.prototype.render=function(){var e=this.props,t=e.children,r=e.className,o=function(e,t){if(null==e)return{};var r,o,n={},a=Object.keys(e);for(o=0;o<a.length;o++)r=a[o],t.indexOf(r)>=0||(n[r]=e[r]);return n}(e,["children","className"]);return n().createElement("ul",w({},o,{className:p()(r),role:"tablist"}),t)},o}(o.Component);function k(){return k=Object.assign||function(e){for(var t=1;t<arguments.length;t++){var r=arguments[t];for(var o in r)Object.prototype.hasOwnProperty.call(r,o)&&(e[o]=r[o])}return e},k.apply(this,arguments)}P.defaultProps={className:"react-tabs__tab-list"},P.propTypes={},P.tabsRole="TabList";var E="react-tabs__tab",j=function(e){var t,r;function o(){return e.apply(this,arguments)||this}r=e,(t=o).prototype=Object.create(r.prototype),t.prototype.constructor=t,t.__proto__=r;var a=o.prototype;return a.componentDidMount=function(){this.checkFocus()},a.componentDidUpdate=function(){this.checkFocus()},a.checkFocus=function(){var e=this.props,t=e.selected,r=e.focus;t&&r&&this.node.focus()},a.render=function(){var e,t=this,r=this.props,o=r.children,a=r.className,l=r.disabled,s=r.disabledClassName,i=(r.focus,r.id),u=r.panelId,d=r.selected,c=r.selectedClassName,f=r.tabIndex,h=r.tabRef,b=function(e,t){if(null==e)return{};var r,o,n={},a=Object.keys(e);for(o=0;o<a.length;o++)r=a[o],t.indexOf(r)>=0||(n[r]=e[r]);return n}(r,["children","className","disabled","disabledClassName","focus","id","panelId","selected","selectedClassName","tabIndex","tabRef"]);return n().createElement("li",k({},b,{className:p()(a,(e={},e[c]=d,e[s]=l,e)),ref:function(e){t.node=e,h&&h(e)},role:"tab",id:i,"aria-selected":d?"true":"false","aria-disabled":l?"true":"false","aria-controls":u,tabIndex:f||(d?"0":null)}),o)},o}(o.Component);function S(){return S=Object.assign||function(e){for(var t=1;t<arguments.length;t++){var r=arguments[t];for(var o in r)Object.prototype.hasOwnProperty.call(r,o)&&(e[o]=r[o])}return e},S.apply(this,arguments)}j.defaultProps={className:E,disabledClassName:E+"--disabled",focus:!1,id:null,panelId:null,selected:!1,selectedClassName:E+"--selected"},j.propTypes={},j.tabsRole="Tab";var M="react-tabs__tab-panel",T=function(e){var t,r;function o(){return e.apply(this,arguments)||this}return r=e,(t=o).prototype=Object.create(r.prototype),t.prototype.constructor=t,t.__proto__=r,o.prototype.render=function(){var e,t=this.props,r=t.children,o=t.className,a=t.forceRender,l=t.id,s=t.selected,i=t.selectedClassName,u=t.tabId,d=function(e,t){if(null==e)return{};var r,o,n={},a=Object.keys(e);for(o=0;o<a.length;o++)r=a[o],t.indexOf(r)>=0||(n[r]=e[r]);return n}(t,["children","className","forceRender","id","selected","selectedClassName","tabId"]);return n().createElement("div",S({},d,{className:p()(o,(e={},e[i]=s,e)),role:"tabpanel",id:l,"aria-labelledby":u}),a||s?r:null)},o}(o.Component);T.defaultProps={className:M,forceRender:!1,selectedClassName:M+"--selected"},T.propTypes={},T.tabsRole="TabPanel"},42473:e=>{"use strict";e.exports=function(){}},99196:e=>{"use strict";e.exports=window.React},91850:e=>{"use strict";e.exports=window.ReactDOM},92819:e=>{"use strict";e.exports=window.lodash},57349:e=>{"use strict";e.exports=window.lodash.flow},66366:e=>{"use strict";e.exports=window.lodash.isEmpty},16653:e=>{"use strict";e.exports=window.lodash.omit},25158:e=>{"use strict";e.exports=window.wp.a11y},65736:e=>{"use strict";e.exports=window.wp.i18n},23695:e=>{"use strict";e.exports=window.yoast.helpers},85890:e=>{"use strict";e.exports=window.yoast.propTypes},92651:e=>{"use strict";e.exports=window.yoast.reactSelect},37188:e=>{"use strict";e.exports=window.yoast.styleGuide},98487:e=>{"use strict";e.exports=window.yoast.styledComponents}},t={};function r(o){var n=t[o];if(void 0!==n)return n.exports;var a=t[o]={exports:{}};return e[o](a,a.exports,r),a.exports}r.n=e=>{var t=e&&e.__esModule?()=>e.default:()=>e;return r.d(t,{a:t}),t},r.d=(e,t)=>{for(var o in t)r.o(t,o)&&!r.o(e,o)&&Object.defineProperty(e,o,{enumerable:!0,get:t[o]})},r.o=(e,t)=>Object.prototype.hasOwnProperty.call(e,t),r.r=e=>{"undefined"!=typeof Symbol&&Symbol.toStringTag&&Object.defineProperty(e,Symbol.toStringTag,{value:"Module"}),Object.defineProperty(e,"__esModule",{value:!0})};var o=r(95235);(window.yoast=window.yoast||{}).componentsNew=o})();