// External Dependencies
import findKey from 'lodash/findKey';
import forEach from 'lodash/forEach';
import includes from 'lodash/includes';
import isNull from 'lodash/isNull';
import isString from 'lodash/isString';
import isUndefined from 'lodash/isUndefined';
import { v4 as uuidv4 } from 'uuid';


const regexps = {
  hex: /^#[a-f0-9]{3}([a-f0-9]{3})?$/i,
  rgb: /^rgba?\(\s*\d{1,3}\s*,\s*\d{1,3}\s*,\s*\d{1,3}\s*(,\s*[\d\.]+)?\s*\)$/,
  hsl: /^hsla?\(\s*\d{1,3}\s*,\s*\d{1,3}%\s*,\s*\d{1,3}%\s*(,\s*[\d\.]+)?\s*\)$/,
};

const conversionMaths = {
  common: [
    { // 1
      h: 15,
      s: 20,
      l: 20,
    },
    { // Base
      h: 0,
      s: 0,
      l: 0,
    },
    { // 3
      h: - 15,
      s: 0,
      l: 0,
    },
    { // 4
      h: - 15,
      s: 0,
      l: - 30,
    },
    { // 5
      h: 165,
      s: 0,
      l: - 20,
    },
    { // 6
      h: 165,
      s: 0,
      l: 0,
    },
    { // 7
      h: 180,
      s: 0,
      l: 0,
    },
    { // 8
      h: 195,
      s: - 20,
      l: 20,
    },
  ],
  black: [
    { // 1
      h: 0,
      s: 0,
      l: 100,
    },
    { // Base
      h: 0,
      s: 0,
      l: 0,
    },
    { // 3
      h: 0,
      s: 0,
      l: 14,
    },
    { // 4
      h: 0,
      s: 0,
      l: 28,
    },
    { // 5
      h: 0,
      s: 0,
      l: 42,
    },
    { // 6
      h: 0,
      s: 0,
      l: 56,
    },
    { // 7
      h: 0,
      s: 0,
      l: 70,
    },
    { // 8
      h: 0,
      s: 0,
      l: 84,
    },
  ],
  white: [
    { // 1
      h: 0,
      s: 0,
      l: - 100,
    },
    { // Base
      h: 0,
      s: 0,
      l: 0,
    },
    { // 3
      h: 0,
      s: 0,
      l: - 16,
    },
    { // 4
      h: 0,
      s: 0,
      l: - 30,
    },
    { // 5
      h: 0,
      s: 0,
      l: - 44,
    },
    { // 6
      h: 0,
      s: 0,
      l: - 58,
    },
    { // 7
      h: 0,
      s: 0,
      l: - 72,
    },
    { // 8
      h: 0,
      s: 0,
      l: - 86,
    },
  ],
};

/**
 * Class for color conversion between RGB, HEX, and HSL color models
 * Also contains helper function for detecting color model type etc.
 */
class ETBuilderUtilsColor {
  static transparent = 'rgba(255,255,255,0)';

  static validUnits = [
    '%', // | Percent
    'px', // | Pixels
    'em', // | Font Size (em)
    'rem', // | Root-level Font Size (rem)
    'ex', // | X-Height (ex)
    'ch', // | Zero-width (ch)
    'pc', // | Picas (pc)
    'pt', // | Points (pt)
    'cm', // | Centimeters (cm)
    'mm', // | Millimeters (mm)
    'in', // | Inches (in)
    'vh', // | Viewport Height (vh)
    'vw', // | Viewport Width (vw)
    'vmin', // | Viewport Minimum (vmin)
    'vmax', // | Viewport Maximum (vmax)
  ];

  static isHex(colorString) {
    return regexps.hex.test(colorString);
  }

  static isRgb(colorString) {
    return regexps.rgb.test(colorString);
  }

  static isHsl(colorString) {
    return regexps.hsl.test(colorString);
  }

  /**
   * Retrieves color type from CSS color string. Supports HEX, RGB and HSL color types.
   *
   * @param colorString
   * @returns The matched color type in string, else `undefined`.
   */
  static getColorType(colorString) {
    return findKey(regexps, r => r.test(colorString));
  }

  static isColorValid(colorString) {
    return ! isUndefined(this.getColorType(colorString));
  }

  static normalize(colorValue) {
    const colorString = isString(colorValue) ? colorValue.toLowerCase().replace(/ /g, '') : '';

    if (colorString && this.isColorValid(colorString)) {
      return colorString;
    }

    return '';
  }

  /**
   * Extracts number values from rgb or rgba color definition string.
   *
   * @param rgb String color in rgb or rgba format.
   * @param colorString
   * @returns Array of red, blue, green and opacity values, else `undefined` in case of incorrect color string.
   */
  static rgbExtract(colorString) {
    let result;

    if (this.isRgb(colorString)) {
      const rgb = colorString.replace(/^(rgb|rgba)\(/, '').replace(/\)$/, '').replace(/\s/g, '').split(',');

      result = [
        parseInt(rgb[0]),
        parseInt(rgb[1]),
        parseInt(rgb[2]),
      ];

      if (4 === rgb.length) { // opacity included
        result.push(parseFloat(rgb[3]));
      }
    }

    return result;
  }

  /**
   * Converts RGB to HSL.
   * Assumes r, g and b between 0 and 255.
   *
   * @param r Red color value.
   * @param g Green color value.
   * @param b Blue color value.
   * @returns Array of hue, saturation and lightness in integer.
   * Like hue = 195, saturation = 15, lightness = 95.
   */
  static rgbToHsl(r, g, b) {
    const red   = r / 255;
    const green = g / 255;
    const blue  = b / 255;

    const max = Math.max(red, green, blue);
    const min = Math.min(red, green, blue);

    let hue         = 0;// achromatic by default
    let saturation  = 0;// achromatic by default
    const lightness = (max + min) / 2;

    if (max !== min) {
      const diff = max - min;

      if (lightness > 0.5) {
        saturation = diff / (2 - max - min);
      } else {
        saturation = diff / (max + min);
      }

      switch (max) {
        case red:
          hue = (green - blue) / diff + (green < blue ? 6 : 0);
          break;
        case green:
          hue = (blue - red) / diff + 2;
          break;
        case blue:
          hue = (red - green) / diff + 4;
          break;
      }

      hue *= 0.6;
    }

    const h = Math.round(100 * hue);
    const s = Math.round(100 * saturation);
    const l = Math.round(100 * lightness);

    return [h, s, l];
  }

  /**
   * Helper function to calculate color channel value.
   *
   * @param temp_1
   * @param temp_2
   * @param temp_hue
   * @returns Calculated channel value.
   * @private
   */
  static _adjustHslValue(temp_1, temp_2, temp_hue) {
    // normalize hue
    if (temp_hue < 0) {
      temp_hue += 1;
    }
    if (temp_hue > 1) {
      temp_hue -= 1;
    }

    if ((6 * temp_hue) < 1) {
      return temp_2 + (temp_1 - temp_2) * 6 * temp_hue;
    } if ((2 * temp_hue) < 1) {
      return temp_1;
    } if ((3 * temp_hue) < 2) {
      return temp_2 + (temp_1 - temp_2) * (2 / 3 - temp_hue) * 6;
    }

    return temp_2;
  }

  /**
   * Converts HSL to RGB.
   * Assumes h, s and l in integer. Like hue = 195, saturation = 15, lightness = 95.
   *
   * @param h Hue value in degrees.
   * @param s Saturation value in percentage.
   * @param l Lightness value.
   * @returns Array of red, blue and green values between 0 and 255.
   */
  static hslToRgb(h, s, l) {
    const hue        = h / 360;
    const saturation = s / 100;
    const lightness  = l / 100;

    let red   = lightness;// achromatic by default
    let green = lightness;// achromatic by default
    let blue  = lightness;// achromatic by default

    if (saturation !== 0) {
      const temp1 = lightness < 0.5
        ? lightness * (1 + saturation)
        : lightness + saturation - lightness * saturation;
      const temp2 = 2 * lightness - temp1;

      red   = this._adjustHslValue(temp1, temp2, hue + 1 / 3);
      green = this._adjustHslValue(temp1, temp2, hue);
      blue  = this._adjustHslValue(temp1, temp2, hue - 1 / 3);
    }

    return [Math.round(red * 255), Math.round(green * 255), Math.round(blue * 255)];
  }

  /**
   * Converts HEX color representation to RGB.
   *
   * @param {string} hex May contains `#` symbol and be in short 3-digit or long 6-digit format.
   * @returns {Array} Red, blue and green values between 0 and 255, else `undefined` in case of invalid hex value.
   */
  static hexToRgb(hex) {
    let result;

    let value   = hex.replace('#', '');
    const regex = new RegExp(`(.{${value.length / 3}})`, 'g');// split string into 3 parts

    value = value.match(regex);

    if (! isNull(value)) {
      for (let i = 0; i < value.length; i++) {
        // check for short notation of channel color value
        const channel = 1 === value[i].length ? value[i] + value[i] : value[i];
        value[i]      = parseInt(channel, 16);
      }

      result = value;
    }

    return result;
  }

  /**
   * Converts RGB color to 6-digit HEX format.
   *
   * Assumes color is passed as a separate r, g and b values between 0 and 255.
   *
   * @param {number} r Red color value.
   * @param {number} g Green color value.
   * @param {number} b Blue color value.
   * @returns {string} 6-digit HEX value.
   */
  static rgbToHex(r, g, b) {
    const red   = `0${r.toString(16)}`;
    const green = `0${g.toString(16)}`;
    const blue  = `0${b.toString(16)}`;

    return `#${red.slice(- 2)}${green.slice(- 2)}${blue.slice(- 2)}`;
  }

  /**
   * Generates array of 8 harmonious colors including base color.
   *
   * Assumes base color is passed in HSL values. Like hue = 195, saturation = 15, lightness = 95.
   *
   * @param {number} h Hue value in degrees.
   * @param {number} s Saturation value in percentage.
   * @param {number} l Lightness value.
   * @returns {Array} Colors in HSL array format.
   */
  static generateHarmoniousColors(h, s, l) {
    let maths    = conversionMaths.common;
    const result = [];

    if (0 === l) { // pure black
      maths = conversionMaths.black;
    } else if (100 === l) { // pure white
      maths = conversionMaths.white;
    }

    forEach(maths, formula => {
      // constraints for h, s and l values.
      // h is angle in degrees so use modulo 360
      const hNew = (h + formula.h) % 360;

      // s and l are values between 0 and 100 percent
      const sNew  = Math.min(Math.max(s + formula.s, 0), 100);
      const lNew  = Math.min(Math.max(l + formula.l, 0), 100);
      const color = [hNew, sNew, lNew];
      result.push(color);
    });

    return result;
  }

  /**
   * Converts the gradient stops attribute from string to array of gradient stop objects.
   *
   * @typedef {object} GradientStop
   * @property {string} color The gradient stop color.
   * @property {number} percent The gradient stop position in percent.
   * @property {number} index The gradient stop position in order.
   * @property {string} uuidv4 A unique identifier for the gradient stop.
   *
   * @param {string} gradient The gradient stops string.
   *
   * @returns {GradientStop[]} Converted value as array.
   */
  static parseGradientString(gradient) {
    const stops = gradient.split('|');

    const gradientStops = stops.map((stop, index) => {
      stop = stop.trim().split(' ');

      // Take all but the final value and recombine. (Needed for non-hex colors.)
      const color = stop.slice(0, -1).join(' ');

      // Extract the final array value (position) and split into integer and unit.
      const position      = Number.parseInt(stop.slice(-1), 10);
      const cleanPosition = Number.isNaN(position) ? 0 : position;
      // Exclude the substring captured for `position` and set the rest as unit.
      const unit          = stop.slice(-1)[0].substring(Number.isNaN(position) ? 0 : position.toString().length);
      const cleanUnit = ! includes(this.validUnits, unit) ? '%' : unit;

      return new GradientStop(color, cleanPosition, index, uuidv4(), cleanUnit);
    });

    return gradientStops;
  }

  /**
   * Converts the gradient stops array into string notation.
   *
   * @typedef {object} GradientStop
   * @property {string} color The gradient stop color.
   * @property {number} position The gradient stop position (0-100).
   *
   * @param {GradientStop[]} stops The gradient stops array.
   * @param {boolean} convertGCID Whether to pass a GCID as-is or convert it to its CSS color code.
   *
   * @returns {string} Converted value as array.
   */
  static toGradientString(stops, convertGCID = false) {
    return stops.map(({ color, position, unit }) => {
      const colorValue = convertGCID ? ETBuilderGlobalColorsStore.getColorValue(color) || color : color;
      return `${colorValue} ${position}${unit}`;
    }).join('|');
  }

  /**
   * Calculates color Luma.
   * See http://en.wikipedia.org/wiki/Luma_%28video%29.
   *
   * @param {number} r Red color value.
   * @param {number} g Gree color value.
   * @param {number} b Blue color value.
   *
   * @returns {number} Luma value.
   */
  static luma(r, g, b) {
    return 0.2126 * r + 0.7152 * g + 0.0722 * b;
  }
}

export default ETBuilderUtilsColor;
