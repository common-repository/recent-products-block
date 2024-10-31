<?php
namespace WRP\Inc;

class GetCSS{
	static function getBackgroundCSS($bg, $isSolid = true, $isGradient = true, $isImage = true) {
		extract( $bg );
		$type = $type ?? 'solid';
		$color = $color ?? '#000000b3';
		$gradient = $gradient ?? 'linear-gradient(135deg, #4527a4, #8344c5)';
		$image = $image ?? [];
		$position = $position ?? 'center center';
		$attachment = $attachment ?? 'initial';
		$repeat = $repeat ?? 'no-repeat';
		$size = $size ?? 'cover';
		$overlayColor = $overlayColor ?? '#000000b3';

		$gradientCSS = $isGradient ? "background: $gradient;" : '';

		$imgUrl = $image['url'] ?? '';
		$imageCSS = $isImage ? "background: url($imgUrl); background-color: $overlayColor; background-position: $position; background-size: $size; background-repeat: $repeat; background-attachment: $attachment; background-blend-mode: overlay;" : '';

		$solidCSS = $isSolid ? "background: $color;" : '';
	
		$styles = 'gradient' === $type ? $gradientCSS : ( 'image' === $type ? $imageCSS : $solidCSS );
	
		return $styles;
	}

	static function getBorderCSS( $border ) {
		extract( $border );
		$width = $width ?? '0px';
		$style = $style ?? 'solid';
		$color = $color ?? '#0000';
		$side = $side ?? 'all';
		$radius = $radius ?? '0px';
	
		$borderSideCheck = function( $s ) use ( $side ) {
			$bSide = strtolower( $side );
			return false !== strpos( $bSide, 'all' ) || false !== strpos( $bSide, $s );
		};
	
		$noWidth = $width === '0px' || !$width;
		$borderCSS = "$width $style $color";

		$styles = '';
		foreach ( ['top', 'right', 'bottom', 'left'] as $s ) {
			if ( !$noWidth && $borderSideCheck( $s ) ) { $styles .= "border-$s: $borderCSS;"; }
		}
		if ( $radius ) { $styles .= "border-radius: $radius;"; }
	
		return $styles;
	}

	static function getColorsCSS( $colors ) {
		extract( $colors );
		$color = $color ?? '#333';
		$bgType = $bgType ?? 'solid';
		$bg = $bg ?? '#0000';
		$gradient = $gradient ?? 'linear-gradient(135deg, #4527a4, #8344c5)';

		$background = $bgType === 'gradient' ? $gradient : $bg;
	
		$styles = '';
		$styles .= $color ? "color: $color;" : '';
		$styles .= ( $gradient || $bg ) ? "background: $background;" : '';
	
		return $styles;
	}

	static function getShadowCSS( $shadow ) {
		extract( $shadow );
		$type = $type ?? 'box';
		$hOffset = $hOffset ?? '0px';
		$vOffset = $vOffset ?? '0px';
		$blur = $blur ?? '0px';
		$spreed = $spreed ?? '0px';
		$color = $color ?? '#7090b0';
		$isInset = $isInset ?? false;

		$inset = $isInset ? 'inset' : '';
		$offsetBlur = "$hOffset $vOffset $blur";

		$styles = 'text' === $type ? "$offsetBlur $color" : "$offsetBlur $spreed $color $inset";
	
		return $styles ?: 'none';
	}

	static function generateCss( $value, $cssProperty ) {
		return !$value ? '' : "$cssProperty: $value;";
	}

	static function getTypoCSS( $selector, $typo, $isFamily = true ) {
		extract( $typo );
		$fontFamily = $fontFamily ?? 'Default';
		$fontCategory = $fontCategory ?? 'sans-serif';
		$fontVariant = $fontVariant ?? 400;
		$fontWeight = $fontWeight ?? 400;
		$isUploadFont = $isUploadFont ?? true;
		$fontSize = $fontSize ?? [ 'desktop' => 15, 'tablet' => 15, 'mobile' => 15 ];
		$fontStyle = $fontStyle ?? 'normal';
		$textTransform = $textTransform ?? 'none';
		$textDecoration = $textDecoration ?? 'auto';
		$lineHeight = $lineHeight ?? '135%';
		$letterSpace = $letterSpace ?? '0px';

		$isEmptyFamily = !$isFamily || !$fontFamily || 'Default' === $fontFamily;
		$desktopFontSize = $fontSize['desktop'] ?? $fontSize;
		$tabletFontSize = $fontSize['tablet'] ?? $desktopFontSize;
		$mobileFontSize = $fontSize['mobile'] ?? $tabletFontSize;

		$styles = ( $isEmptyFamily ? '' : "font-family: '$fontFamily', $fontCategory;" )
			. self::generateCss( $fontWeight, 'font-weight' )
			. 'font-size: '. $desktopFontSize .'px;'
			. self::generateCss( $fontStyle, 'font-style' )
			. self::generateCss( $textTransform, 'text-transform' )
			. self::generateCss( $textDecoration, 'text-decoration' )
			. self::generateCss( $lineHeight, 'line-height' )
			. self::generateCss( $letterSpace, 'letter-spacing' );

		// Google font link
		$linkQuery = ( !$fontVariant || 400 === $fontVariant ) ? '' : ( '400i' === $fontVariant ? ':ital@1' : ( false !== strpos( $fontVariant, '00i' ) ? ': ital, wght@1, '. str_replace( '00i', '00', $fontVariant ) .' ' : ": wght@$fontVariant " ) );

		$link = $isEmptyFamily ? '' : 'https://fonts.googleapis.com/css2?family='. str_replace( ' ', '+', $fontFamily ) ."$linkQuery&display=swap";

		return [
			'googleFontLink' => !$isUploadFont || $isEmptyFamily ? '' : "@import url( $link );",
			'styles' => preg_replace( '/\s+/', ' ', trim( "
				$selector{ $styles }
				@media (max-width: 768px) {
					$selector{ font-size: $tabletFontSize" . "px; }
				}
				@media (max-width: 576px) {
					$selector{ font-size: $mobileFontSize" . "px; }
				}
			" ) )
		];
	}
}