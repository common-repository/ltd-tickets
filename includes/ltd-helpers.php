<?php
/**
 * LTD Helper Functions.
 *
 * Handles the actions used to display elements on template pages.
 *
 * @since      1.0.0
 * @package    Ltd_Tickets
 * @subpackage Ltd_Tickets/includes
 * @author     Ben Campbell <ben.campbell@londontheatredirect.com>
 */


if (! function_exists( 'ltd_format_price' )) :
    function ltd_format_price($price) {
        if (is_numeric($price) && $price != "0") {
            $formatted_number = number_format($price, 2, '.', ',');
            if ($formatted_number === 0.00) return $price;
            return "<span class='ukds-currency-symbol' ukds-currency-symbol='GBP'>&pound;</span><span class='ukds-currency-value' ukds-currency-value='$formatted_number'>$formatted_number</span>";
        } else {
            return $price;
        }
    }
endif;


if (! function_exists( 'money_format' )) :

    function money_format($format, $number)
    {
        $regex  = '/%((?:[\^!\-]|\+|\(|\=.)*)([0-9]+)?'.
                  '(?:#([0-9]+))?(?:\.([0-9]+))?([in%])/';
        if (setlocale(LC_MONETARY, 0) == 'C') {
            setlocale(LC_MONETARY, '');
        }
        $locale = localeconv();
        preg_match_all($regex, $format, $matches, PREG_SET_ORDER);
        foreach ($matches as $fmatch) {
            $value = floatval($number);
            $flags = array(
                'fillchar'  => preg_match('/\=(.)/', $fmatch[1], $match) ?
                               $match[1] : ' ',
                'nogroup'   => preg_match('/\^/', $fmatch[1]) > 0,
                'usesignal' => preg_match('/\+|\(/', $fmatch[1], $match) ?
                               $match[0] : '+',
                'nosimbol'  => preg_match('/\!/', $fmatch[1]) > 0,
                'isleft'    => preg_match('/\-/', $fmatch[1]) > 0
            );
            $width      = trim($fmatch[2]) ? (int)$fmatch[2] : 0;
            $left       = trim($fmatch[3]) ? (int)$fmatch[3] : 0;
            $right      = trim($fmatch[4]) ? (int)$fmatch[4] : $locale['int_frac_digits'];
            $conversion = $fmatch[5];

            $positive = true;
            if ($value < 0) {
                $positive = false;
                $value  *= -1;
            }
            $letter = $positive ? 'p' : 'n';

            $prefix = $suffix = $cprefix = $csuffix = $signal = '';

            $signal = $positive ? $locale['positive_sign'] : $locale['negative_sign'];
            switch (true) {
                case $locale["{$letter}_sign_posn"] == 1 && $flags['usesignal'] == '+':
                    $prefix = $signal;
                    break;
                case $locale["{$letter}_sign_posn"] == 2 && $flags['usesignal'] == '+':
                    $suffix = $signal;
                    break;
                case $locale["{$letter}_sign_posn"] == 3 && $flags['usesignal'] == '+':
                    $cprefix = $signal;
                    break;
                case $locale["{$letter}_sign_posn"] == 4 && $flags['usesignal'] == '+':
                    $csuffix = $signal;
                    break;
                case $flags['usesignal'] == '(':
                case $locale["{$letter}_sign_posn"] == 0:
                    $prefix = '(';
                    $suffix = ')';
                    break;
            }
            if (!$flags['nosimbol']) {
                $currency = $cprefix .
                            ($conversion == 'i' ? $locale['int_curr_symbol'] : $locale['currency_symbol']) .
                            $csuffix;
            } else {
                $currency = '';
            }
            $space  = $locale["{$letter}_sep_by_space"] ? ' ' : '';

            $value = number_format($value, $right, $locale['mon_decimal_point'],
                     $flags['nogroup'] ? '' : $locale['mon_thousands_sep']);
            $value = @explode($locale['mon_decimal_point'], $value);

            $n = strlen($prefix) + strlen($currency) + strlen($value[0]);
            if ($left > 0 && $left > $n) {
                $value[0] = str_repeat($flags['fillchar'], $left - $n) . $value[0];
            }
            $value = implode($locale['mon_decimal_point'], $value);
            if ($locale["{$letter}_cs_precedes"]) {
                $value = $prefix . $currency . $space . $value . $suffix;
            } else {
                $value = $prefix . $value . $space . $currency . $suffix;
            }
            if ($width > 0) {
                $value = str_pad($value, $width, $flags['fillchar'], $flags['isleft'] ?
                         STR_PAD_RIGHT : STR_PAD_LEFT);
            }

            $format = str_replace($fmatch[0], $value, $format);
        }
        return $format;
    }

endif;


if (! function_exists( 'ltd_get_url_var' )) {

    function ltd_get_url_var($name)
    {
        $strURL = $_SERVER['REQUEST_URI'];
        $arrVals = explode("/",$strURL);
        $found = 0;
        foreach ($arrVals as $index => $value)
        {
            if($value == $name) $found = $index;
        }
        $place = $found + 1;
        return $arrVals[$place];
    }

}

if (! function_exists( 'ltd_build_awin_deeplink' )) {

    function ltd_build_awin_deeplink( $awinid, $url, $clickref = "" ) {

        return "http://www.awin1.com/cread.php?awinmid=610&awinaffid=" . $awinid . "&clickref=" . $clickref . "&p=" . urlencode($url);

    }

}

if (! function_exists( 'ltd_sanitise_currency' )) {
    function ltd_sanitise_currency($input) {
        $currency = sanitize_text_field($input);
        if (preg_match("/^-?[0-9]+(?:\.[0-9]{1,2})?$/", $currency)) {
            return $currency;
         }
         return false;
     }
}


if (! function_exists( 'ltd_sanitise_meta_text' )) {
    function ltd_sanitise_meta_text($input) {
        $pattern = "/<([^>\s]+)[^>]*>(?:\s*(?:<br \/>|&nbsp;|&thinsp;|&ensp;|&emsp;|&#8201;|&#8194;|&#8195;)\s*|\s*)*<\/\1>/";
        $strip = preg_replace($pattern, '', $input);
        $allowedtags = array(
            'p'         => array(),
            'span'      => array(),
            'br'        => array(),
            'em'        => array(),
            'i'         => array(),
            'b'         => array(),
            'strong'    => array(),
        );
        return wp_kses($strip, $allowedtags);
    }
}

if (! function_exists( 'ltd_sanitise_date_field' )) {
    function ltd_sanitise_date_field($input) {
        if (preg_match("/^[0-9]{4}-(0[1-9]|1[0-2])-(0[1-9]|[1-2][0-9]|3[0-1])$/", $input)) {
            return $input;
        } else {
            try {
                $dateString = strtotime($input);
                return date("Y-m-d", $dateString);
            } catch(Exception $e) {
                $log = new InlineLog();
                $log->Log(
                   array(
                       'type'      =>'ERROR',
                       'message'   => sprintf(
                                       'Invalid Date Format entered - ' . esc_attr($input),
                                       $e->getCode(), $e->getMessage()),
                       'stack'     =>  var_export($e->getTrace(), true)
                   )
               );
                return false;
            }
        }

    }
}


if ( ! function_exists( 'is_product_taxonomy' ) ) {

	function is_product_taxonomy() {
		return is_tax( get_object_taxonomies( 'ukds-products' ) );
	}
}

if (! function_exists( 'ukds_get_page_id' )) {

    function ukds_get_page_id( $page ) {
        $options = get_option('ltd-tickets');
        $page = apply_filters( 'ukds_get_' . $page . '_page_id', $options[ $page . '_page'] );
        return $page ? absint( $page ) : -1;
    }

}

if (! function_exists( 'ltd_get_api_host' )) {
    function ltd_get_api_host($api_key) {
        $api = new LTD_Tickets_Integration(LTD_PLUGIN_NAME, LTD_PLUGIN_VERSION);
        $products = $api->fetch_products($api_key);
        $sub = "";

        if (!empty($products)) {
            if (isset( $products[0]['EventDetailUrl'] )) {
                $bookUrl = parse_url($products[0]['EventDetailUrl']);
                $sub = "https://" . $bookUrl['host'];
            }
        }
        return $sub;

    }
}

if (! function_exists( 'ltd_curl_get_contents' )) {
    function ltd_curl_get_contents($url)
    {
		$log = new InlineLog();
        try {
            $curl = curl_init();
            curl_setopt( $curl, CURLOPT_HEADER, 0);
            curl_setopt( $curl, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt( $curl, CURLOPT_URL, $url);
            curl_setopt( $curl, CURLOPT_SSL_VERIFYPEER, false );

            $curl_response = curl_exec($curl);
            if ($curl_response === false) {
                throw new Exception( curl_error( $curl ), curl_errno( $curl ) );
            }
            curl_close($curl);
            return $curl_response;

        }
        catch(Exception $e) {

            $log->Log(
               array(
                   'type'      =>'ERROR',
                   'message'   => sprintf(
                                   'Curl failed with error #%d: %s',
                                   $e->getCode(), $e->getMessage()),
                   'stack'     =>  var_export($e->getTrace(), true)
               )
           );
        }
        return false;
    }
}

if (! function_exists( 'ltd_curl_get_headers' )) {
    function ltd_curl_get_headers($url)
    {
		$log = new InlineLog();
        try {
            $curl = curl_init();
            curl_setopt( $curl, CURLOPT_HEADER, 0);
            curl_setopt( $curl, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt( $curl, CURLOPT_URL, $url);
            curl_setopt( $curl, CURLOPT_SSL_VERIFYPEER, false );

            curl_exec($curl);
			$response_code = curl_getinfo( $curl, CURLINFO_HTTP_CODE );
            if ($response_code === false) {
                throw new Exception( curl_error( $curl ), curl_errno( $curl ) );
            }
            curl_close($curl);
            return $response_code;
        }
        catch(Exception $e) {
            $log->Log(
               array(
                   'type'      =>'ERROR',
                   'message'   => sprintf(
                                   'Curl failed with error #%d: %s',
                                   $e->getCode(), $e->getMessage()),
                   'stack'     =>  var_export($e->getTrace(), true)
               )
           );
        }
        return false;
    }
}


if (! function_exists( 'ltd_generate_featured_image' ) ) {

    function ltd_generate_featured_image( $image_url, $post_id  ){
        $upload_dir = wp_upload_dir();
		$log = new InlineLog();

        $image_data = "";
        if(ltd_curl_get_headers($image_url) != "200"){
            $log->Log(array("type"=>"ERROR","message"=>"Failed to load image: " . $image_url));
            return false;
        } else {
            $image_data = ltd_curl_get_contents($image_url);
            if ($image_data === false) return false;
        }

        $filename = basename($image_url);
        if(wp_mkdir_p($upload_dir['path']))    {
            $file = $upload_dir['path'] . '/' . $filename;
        } else {
            $file = $upload_dir['basedir'] . '/' . $filename;
        }


        $success = file_put_contents($file, $image_data);
		if ($success === false) {
			$log->Log(array("type"=>"ERROR","message"=>"Failed to write image: " . $file));
            return false;
		}


        $wp_filetype = wp_check_filetype($filename, null );
        $attachment = array(
            'post_mime_type' => $wp_filetype['type'],
            'post_title' => sanitize_file_name($filename),
            'post_content' => '',
            'post_status' => 'inherit'
        );
        $attach_id = false;
        try {
            $attach_id = wp_insert_attachment( $attachment, $file, $post_id );
            require_once(ABSPATH . 'wp-admin/includes/image.php');
            $attach_data = wp_generate_attachment_metadata( $attach_id, $file );
            $res1= wp_update_attachment_metadata( $attach_id, $attach_data );
            $res2= set_post_thumbnail( $post_id, $attach_id );
        }
        catch (Exception $e) {
            $attach_id = false;
            $log->Log(array("type"=>"ERROR","message"=>"Error attaching image: " . $file . " to post: " . $post_id));
        }
        return $attach_id;
    }

}


if (! function_exists( 'ltd_image_crop_dimensions' )) {

    function ltd_image_crop_dimensions($default, $orig_w, $orig_h, $new_w, $new_h, $crop){
        if ( !$crop ) return null; // let the wordpress default function handle this

        $aspect_ratio = $orig_w / $orig_h;
        $size_ratio = max($new_w / $orig_w, $new_h / $orig_h);

        $crop_w = round($new_w / $size_ratio);
        $crop_h = round($new_h / $size_ratio);

        $s_x = floor( ($orig_w - $crop_w) / 2 );
        $s_y = floor( ($orig_h - $crop_h) / 2 );

        return array( 0, 0, (int) $s_x, (int) $s_y, (int) $new_w, (int) $new_h, (int) $crop_w, (int) $crop_h );
    }

}

if ( ! function_exists( 'remove_accent' ) ) {
	function remove_accent($str) {
		$a = array('À', 'Á', 'Â', 'Ã', 'Ä', 'Å', 'Æ', 'Ç', 'È', 'É', 'Ê', 'Ë', 'Ì', 'Í', 'Î', 'Ï', 'Ð', 'Ñ', 'Ò', 'Ó', 'Ô', 'Õ', 'Ö', 'Ø', 'Ù', 'Ú', 'Û', 'Ü', 'Ý', 'ß', 'à', 'á', 'â', 'ã', 'ä', 'å', 'æ', 'ç', 'è', 'é', 'ê', 'ë', 'ì', 'í', 'î', 'ï', 'ñ', 'ò', 'ó', 'ô', 'õ', 'ö', 'ø', 'ù', 'ú', 'û', 'ü', 'ý', 'ÿ', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', 'Œ', 'œ', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', 'Š', 'š', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', 'Ÿ', '?', '?', '?', '?', 'Ž', 'ž', '?', 'ƒ', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?');
		$b = array('A', 'A', 'A', 'A', 'A', 'A', 'AE', 'C', 'E', 'E', 'E', 'E', 'I', 'I', 'I', 'I', 'D', 'N', 'O', 'O', 'O', 'O', 'O', 'O', 'U', 'U', 'U', 'U', 'Y', 's', 'a', 'a', 'a', 'a', 'a', 'a', 'ae', 'c', 'e', 'e', 'e', 'e', 'i', 'i', 'i', 'i', 'n', 'o', 'o', 'o', 'o', 'o', 'o', 'u', 'u', 'u', 'u', 'y', 'y', 'A', 'a', 'A', 'a', 'A', 'a', 'C', 'c', 'C', 'c', 'C', 'c', 'C', 'c', 'D', 'd', 'D', 'd', 'E', 'e', 'E', 'e', 'E', 'e', 'E', 'e', 'E', 'e', 'G', 'g', 'G', 'g', 'G', 'g', 'G', 'g', 'H', 'h', 'H', 'h', 'I', 'i', 'I', 'i', 'I', 'i', 'I', 'i', 'I', 'i', 'IJ', 'ij', 'J', 'j', 'K', 'k', 'L', 'l', 'L', 'l', 'L', 'l', 'L', 'l', 'l', 'l', 'N', 'n', 'N', 'n', 'N', 'n', 'n', 'O', 'o', 'O', 'o', 'O', 'o', 'OE', 'oe', 'R', 'r', 'R', 'r', 'R', 'r', 'S', 's', 'S', 's', 'S', 's', 'S', 's', 'T', 't', 'T', 't', 'T', 't', 'U', 'u', 'U', 'u', 'U', 'u', 'U', 'u', 'U', 'u', 'U', 'u', 'W', 'w', 'Y', 'y', 'Y', 'Z', 'z', 'Z', 'z', 'Z', 'z', 's', 'f', 'O', 'o', 'U', 'u', 'A', 'a', 'I', 'i', 'O', 'o', 'U', 'u', 'U', 'u', 'U', 'u', 'U', 'u', 'U', 'u', 'A', 'a', 'AE', 'ae', 'O', 'o');
		return str_replace($a, $b, $str);
	}
}

if ( ! function_exists( 'post_slug' )) {
	function post_slug($str) {
		return strtolower(preg_replace(array('/[^a-zA-Z0-9 -]/', '/[ -]+/', '/^-|-$/'),
		array('', '-', ''), remove_accent($str)));
	}
}

if ( ! function_exists( 'HTMLToRGB' ) ) {
	function HTMLToRGB($htmlCode) {
		if($htmlCode[0] == '#')
            $htmlCode = substr($htmlCode, 1);

		if (strlen($htmlCode) == 3)
		{
            $htmlCode = $htmlCode[0] . $htmlCode[0] . $htmlCode[1] . $htmlCode[1] . $htmlCode[2] . $htmlCode[2];
		}

		$r = hexdec($htmlCode[0] . $htmlCode[1]);
		$g = hexdec($htmlCode[2] . $htmlCode[3]);
		$b = hexdec($htmlCode[4] . $htmlCode[5]);

		return $b + ($g << 0x8) + ($r << 0x10);
	}
}

if ( ! function_exists( 'RGBToHSL' ) ) {
	function RGBToHSL($RGB) {
		$r = 0xFF & ($RGB >> 0x10);
		$g = 0xFF & ($RGB >> 0x8);
		$b = 0xFF & $RGB;

		$r = ((float)$r) / 255.0;
		$g = ((float)$g) / 255.0;
		$b = ((float)$b) / 255.0;

		$maxC = max($r, $g, $b);
		$minC = min($r, $g, $b);

		$l = ($maxC + $minC) / 2.0;

		if($maxC == $minC)
		{
            $s = 0;
            $h = 0;
		}
		else
		{
            if($l < .5)
            {
                $s = ($maxC - $minC) / ($maxC + $minC);
            }
            else
            {
                $s = ($maxC - $minC) / (2.0 - $maxC - $minC);
            }
            if($r == $maxC)
                $h = ($g - $b) / ($maxC - $minC);
		  if($g == $maxC)
			$h = 2.0 + ($b - $r) / ($maxC - $minC);
		  if($b == $maxC)
			$h = 4.0 + ($r - $g) / ($maxC - $minC);

		  $h = $h / 6.0;
		}

		$h = (int)round(255.0 * $h);
		$s = (int)round(255.0 * $s);
		$l = (int)round(255.0 * $l);
		return (object) Array('hue' => $h, 'saturation' => $s, 'lightness' => $l);
	}
}