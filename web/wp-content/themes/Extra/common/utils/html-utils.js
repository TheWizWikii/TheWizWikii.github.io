import { isString } from 'lodash';
// import ETBuilderStore from '../stores/et-builder-store';

/**
 * Get the HTML of the current page.
 *
 * @returns {string} Page's HTML string.
 */
export function getPageHTML() {
  return document.getElementById('et-boc').innerHTML || '';
}

/**
 * Strip Style tags in a HTML string.
 *
 * @param {string} htmlString HTML string.
 * @returns {string} HTML string.
 */
export function stripStyleTag(htmlString) {
  // Create a new DOMParser instance
  const parser = new DOMParser();

  // Parse the HTML string into a DOM document
  const doc = parser.parseFromString(htmlString, 'text/html');

  // Remove all nested <style> nodes
  const styleNodes = doc.getElementsByTagName('style');
  for (let i = styleNodes.length - 1; i >= 0; i--) {
    const styleNode = styleNodes[i];
    styleNode.parentNode.removeChild(styleNode);
  }

  // Get the modified HTML string without nested <style> nodes
  return doc.documentElement.innerHTML;
}

/**
 * Strip attributes from heading tags.
 *
 * @param {string} htmlString HTML string.
 * @returns {string} HTML string.
 */
export function stripHeadingAttributes(htmlString) {
  // Create a new DOMParser instance
  const parser = new DOMParser();

  // Parse the HTML string into a DOM document
  const doc = parser.parseFromString(htmlString, 'text/html');

  // Strip HTML tags, except for heading tags, and remove attributes within heading tags
  const elements = doc.body.childNodes;
  for (let i = elements.length - 1; i >= 0; i--) {
    const element = elements[i];

    if (element.nodeType === Node.ELEMENT_NODE) {
      if (element.tagName !== 'H1' && element.tagName !== 'H2' && element.tagName !== 'H3' && element.tagName !== 'H4' && element.tagName !== 'H5' && element.tagName !== 'H6') {
        while (element.firstChild) {
          element.parentNode.insertBefore(element.firstChild, element);
        }
        element.parentNode.removeChild(element);
      } else {
        // Remove attributes within heading tags
        const { attributes } = element;
        for (let j = attributes.length - 1; j >= 0; j--) {
          element.removeAttribute(attributes[j].name);
        }
      }
    }
  }

  // Get the modified HTML string without HTML tags, except for heading tags
  return doc.body.innerHTML;
}

/**
 * Strip HTML Tags except heading tags.
 *
 * @param {string} htmlString HTML string.
 * @returns {string} HTML string.
 */
export function stripHTML(htmlString) {
  // String style tags along w/ the content.
  let strippedHTML = stripStyleTag(htmlString);

  // Remove all HTML tags except heading tags.
  strippedHTML = strippedHTML.replace(/<(?!\/?(h[1-6]))[^<>]*>/gi, '');

  // Remove all attributes in heading tags.
  strippedHTML = stripHeadingAttributes(strippedHTML);

  // Replace any encoded HTML entities.
  strippedHTML = strippedHTML.replace(/&([a-z\d]+|#[xX][a-f\d]+);/ig, '');

  // Replace any new line characters.
  strippedHTML = strippedHTML.replace(/(\r\n|\n|\r|\t)/gm, '');

  return strippedHTML;
}

/**
 * Gets HTML content of the given class name.
 * @param {string} className Class name.
 * @returns {string}
 */
export function getHTMLByClassName(className) {
  // Create a new DOMParser instance
  const parser = new DOMParser();

  const htmlString = getPageHTML();

  // Parse the HTML string into a DOM document
  const doc = parser.parseFromString(htmlString, 'text/html');

  // Get HTML content by class name using getElementsByClassName()
  const elementsByClass = doc.getElementsByClassName(className);
  for (let j = 0; j < elementsByClass.length; j++) {
    const htmlContentByClass = elementsByClass[j].innerHTML;

    if (htmlContentByClass) {
      return htmlContentByClass;
    }
  }

  return '';
}

/**
 * Gets section HTML.
 *
 * @param {string} componentAddress Component Address.
 * @returns {string} Section HTML.
 */
// export function getSectionHTML(componentAddress) {
//   const sections  = ETBuilderStore.getSections();
//   const addresses = componentAddress.split('.');

//   if (! addresses.length) {
//     return '';
//   }

//   const sectionShortcodeObj = sections[addresses[0]];
//   const sectionClass        = `${sectionShortcodeObj.type}_${sectionShortcodeObj.address}`;

//   return getHTMLByClassName(sectionClass);
// }

// export function getModuleHTML(componentAddress) {
//   const component   = ETBuilderStore.getShortcodeObjAtAddress(componentAddress);
//   const moduleClass = `${component.type}_${component._order}`;

//   return getHTMLByClassName(moduleClass);
// }

export function stripDefaultValues(value) {
  if ('string' !== typeof value) {
    return value;
  }

  Object.entries(ETBuilderBackend.defaults).forEach(([moduleType, moduleFields]) => {
    Object.entries(moduleFields).forEach(([field, content]) => {
      if (isString(content) && value.includes(content)) {
        value = value.replace(content, '');
      }
    });
  });
  return value;
}

export function getMaxCharacterLimit(textbox) {
  const style = getComputedStyle(textbox);
  const width = parseInt(style.width);
  const font = style.font;
  const characterWidth = getCharacterWidth(font);
  const characterLimit = Math.floor(width / characterWidth);
  return characterLimit + 20;
}

function getCharacterWidth(font) {
  const canvas = document.createElement("canvas");
  const context = canvas.getContext("2d");
  context.font = font;
  const metrics = context.measureText("A");
  return metrics.width;
}
