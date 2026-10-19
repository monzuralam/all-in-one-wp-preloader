/**
 * WordPress dependencies
 */
import { __ } from '@wordpress/i18n';
import { BaseControl, ColorPalette } from '@wordpress/components';

const colors = [
	{ name: __( 'White', 'all-in-one-wp-preloader' ), color: '#ffffff' },
	{ name: __( 'Light Gray', 'all-in-one-wp-preloader' ), color: '#f0f0f1' },
	{ name: __( 'Dark', 'all-in-one-wp-preloader' ), color: '#1e1e1e' },
	{ name: __( 'Black', 'all-in-one-wp-preloader' ), color: '#000000' },
	{ name: __( 'Blue', 'all-in-one-wp-preloader' ), color: '#2271b1' },
	{ name: __( 'Purple', 'all-in-one-wp-preloader' ), color: '#6c3fd1' },
	{ name: __( 'Green', 'all-in-one-wp-preloader' ), color: '#00a32a' },
	{ name: __( 'Red', 'all-in-one-wp-preloader' ), color: '#d63638' },
];

export default function ColorControl( { value, onChange } ) {
	return (
		<BaseControl
			__nextHasNoMarginBottom
			id="ai1wp-bg-color"
			label={ __( 'Background Color', 'all-in-one-wp-preloader' ) }
		>
			<ColorPalette
				colors={ colors }
				value={ value || undefined }
				onChange={ ( color ) => onChange( color || '' ) }
				clearable
			/>
		</BaseControl>
	);
}
