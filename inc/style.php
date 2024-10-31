<?php
namespace WRP\Inc;

require_once WRP_DIR_PATH . 'inc/getCSS.php';

// Generate Styles
class WRPStyleGenerator {
	public static $styles = [];
	public static function addStyle( $selector, $styles ){
		if( array_key_exists( $selector, self::$styles ) ){
			self::$styles[$selector] = wp_parse_args( self::$styles[$selector], $styles );
		}else { self::$styles[$selector] = $styles; }
	}
	public static function renderStyle(){
		$output = '';
		foreach( self::$styles as $selector => $style ){
			$new = '';
			foreach( $style as $property => $value ){
				if( $value == '' ){ $new .= $property; }else { $new .= " $property: $value;"; }
			}
			$output .= "$selector { $new }";
		}
		return $output;
	}
}

class Style{
	static function generatedStyle( $attributes ) {
		extract( $attributes );

		// Generate Styles
		$wrpStyles = new WRPStyleGenerator();

		$mainSl = "#wrpRecentProducts-$cId";
		$productSl = "$mainSl .wrpProduct";

		$wrpStyles::addStyle( "$mainSl .wrpRecentProducts", [
			'grid-gap' => "$rowGap $columnGap"
		] );
		$wrpStyles::addStyle( "$productSl", [
			'text-align' => $textAlign,
			GetCSS::getBackgroundCSS( $productBG ) => '',
			GetCSS::getBorderCSS( $productBorder ) => '',
			'box-shadow' => GetCSS::getShadowCSS( $productShadow )
		] );
		$wrpStyles::addStyle( "$productSl .wrpProductImg", [
			'border-top-left-radius' => $productBorder['radius'] ?? '0',
			'border-top-right-radius' => $productBorder['radius'] ?? '0'
		] );
		$wrpStyles::addStyle( "$productSl .productTitle", [
			'color' => $titleColor
		] );
		$wrpStyles::addStyle( "$productSl .productRating .star-rating span", [
			'color' => $ratingColor
		] );
		$wrpStyles::addStyle( "$productSl .productPrice", [
			'color' => $priceColor
		] );
		$wrpStyles::addStyle( "$productSl .productAddToCartArea", [
			'justify-content' => $textAlign
		] );
		$wrpStyles::addStyle( "$productSl .productAddToCartArea .button", [
			GetCSS::getColorsCSS( $addToCartColors ) => ''
		] );
		$wrpStyles::addStyle( "$productSl .productOnSale", [
			GetCSS::getColorsCSS( $onSaleColors ) => ''
		] );

		ob_start();
			echo GetCSS::getTypoCSS( '', $titleTypo )['googleFontLink'];
			echo GetCSS::getTypoCSS( '', $priceTypo )['googleFontLink'];
			echo GetCSS::getTypoCSS( '', $addToCartTypo )['googleFontLink'];
			echo GetCSS::getTypoCSS( "$productSl .productTitle", $titleTypo )['styles'];
			echo GetCSS::getTypoCSS( "$productSl .productPrice", $priceTypo )['styles'];
			echo GetCSS::getTypoCSS( "$productSl .productAddToCartArea .button", $addToCartTypo )['styles'];

			echo wp_kses( $wrpStyles::renderStyle(), [] );

			$wrpStyles::$styles = []; // Empty styles
		return ob_get_clean();
	}
}