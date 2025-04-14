<?php
/**
 * WP Captcha
 * https://getwpcaptcha.com/
 * (c) WebFactory Ltd, 2022 - 2023, www.webfactoryltd.com
 */

class WPCaptcha_Captcha {
  // convert HEX(HTML) color notation to RGB
  static function hex2rgb($color) {
    if ($color[0] == '#') {
      $color = substr($color, 1);
    }

    if (strlen($color) == 6) {
        list($r, $g, $b) = array($color[0].$color[1],
                                 $color[2].$color[3],
                                 $color[4].$color[5]);
    } elseif (strlen($color) == 3) {
        list($r, $g, $b) = array($color[0].$color[0], $color[1].$color[1], $color[2].$color[2]);
    } else {
        return array(255, 255, 255);
    }

    $r = hexdec($r);
    $g = hexdec($g);
    $b = hexdec($b);

    return array($r, $g, $b);
  } // html2rgb


  // output captcha image
  static function generate() {
    $a = rand(0, (int) 10);
    $b = rand(0, (int) 10);
    $color = @$_GET['color'];
    $color = urldecode($color);
    if(isset($_GET['id'])){
        $captcha_cookie_name = 'wpcaptcha_captcha_' . intval($_GET['id']);
    } else{
        $captcha_cookie_name = 'wpcaptcha_captcha';
    }

    if ($a > $b) {
      $out = "$a - $b";
      $captcha_value = $a - $b;

    } else {
      $out = "$a + $b";
      $captcha_value = $a + $b;
    }

    setcookie($captcha_cookie_name, $captcha_value, time() + 60 * 5, '/');

    $font   = 5;
    $width  = ImageFontWidth($font) * strlen($out);
    $height = ImageFontHeight($font);
    $im     = ImageCreate($width, $height);

    $x = imagesx($im) - $width ;
    $y = imagesy($im) - $height;

    $white = imagecolorallocate ($im, 255, 255, 255);
    $gray = imagecolorallocate ($im, 66, 66, 66);
    $black = imagecolorallocate ($im, 0, 0, 0);
    $trans_color = $white; //transparent color

    if ($color) {
      $color = self::hex2rgb($color);
      $new_color = imagecolorallocate ($im, $color[0], $color[1], $color[2]);
      imagefill($im, 1, 1, $new_color);
    } else {
      imagecolortransparent($im, $trans_color);
    }

    imagestring ($im, $font, $x, $y, $out, $black);

    // always add noise
    if (1 == 1) {
      $color_min = 100;
      $color_max = 200;
      $rand1 = imagecolorallocate ($im, rand($color_min,$color_max), rand($color_min,$color_max), rand($color_min,$color_max));
      $rand2 = imagecolorallocate ($im, rand($color_min,$color_max), rand($color_min,$color_max), rand($color_min,$color_max));
      $rand3 = imagecolorallocate ($im, rand($color_min,$color_max), rand($color_min,$color_max), rand($color_min,$color_max));
      $rand4 = imagecolorallocate ($im, rand($color_min,$color_max), rand($color_min,$color_max), rand($color_min,$color_max));
      $rand5 = imagecolorallocate ($im, rand($color_min,$color_max), rand($color_min,$color_max), rand($color_min,$color_max));
                
      $style = array($rand1, $rand2, $rand3, $rand4, $rand5);
      imagesetstyle($im, $style);
      imageline($im, rand(0, $width), 0, rand(0, $width), $height, IMG_COLOR_STYLED);
      imageline($im, rand(0, $width), 0, rand(0, $width), $height, IMG_COLOR_STYLED);
      imageline($im, rand(0, $width), 0, rand(0, $width), $height, IMG_COLOR_STYLED);
      imageline($im, rand(0, $width), 0, rand(0, $width), $height, IMG_COLOR_STYLED);
      imageline($im, rand(0, $width), 0, rand(0, $width), $height, IMG_COLOR_STYLED);
    }

    header('Cache-Control: no-cache, must-revalidate');
    header('Expires: Sat, 26 Jul 1997 05:00:00 GMT');
    header('Content-type: image/gif');
    imagegif($im);
    die();
  } // create
} // WPCaptcha_Captcha


if (isset($_GET['wpcaptcha-generate-image'])) {
  WPCaptcha_Captcha::generate();
}
