/**
 * WordPress dependencies
 */
import { useRef } from '@wordpress/element';
import { __ } from '@wordpress/i18n';
import { BaseControl, Button } from '@wordpress/components';

const { defaultImage } = window.ai1wpAdmin;

export default function ImageControl( { value, onChange } ) {
	const frame = useRef();

	const open = () => {
		if ( ! frame.current ) {
			frame.current = window.wp.media( {
				title: __( 'Select Image', 'all-in-one-wp-preloader' ),
				button: { text: __( 'Use Image', 'all-in-one-wp-preloader' ) },
				library: { type: 'image' },
				multiple: false,
			} );
		}
		frame.current.off( 'select' );
		frame.current.on( 'select', () => {
			const attachment = frame.current
				.state()
				.get( 'selection' )
				.first()
				.toJSON();
			onChange( attachment.url );
		} );
		frame.current.open();
	};

	return (
		<BaseControl
			__nextHasNoMarginBottom
			id="ai1wp-image"
			label={ __( 'Preloader Image', 'all-in-one-wp-preloader' ) }
			help={ __(
				'If no image select then load default image.',
				'all-in-one-wp-preloader'
			) }
		>
			<div className="ai1wp-image">
				<button
					type="button"
					className="ai1wp-image__preview"
					onClick={ open }
					aria-label={ __(
						'Select Image',
						'all-in-one-wp-preloader'
					) }
				>
					<img src={ value || defaultImage } alt="" />
					{ ! value && (
						<span className="ai1wp-image__badge">
							{ __( 'Default', 'all-in-one-wp-preloader' ) }
						</span>
					) }
				</button>
				<div className="ai1wp-image__actions">
					<Button
						variant="secondary"
						onClick={ open }
						__next40pxDefaultSize
					>
						{ value
							? __( 'Replace Image', 'all-in-one-wp-preloader' )
							: __( 'Select Image', 'all-in-one-wp-preloader' ) }
					</Button>
					{ value && (
						<Button
							variant="tertiary"
							isDestructive
							onClick={ () => onChange( '' ) }
							__next40pxDefaultSize
						>
							{ __( 'Remove', 'all-in-one-wp-preloader' ) }
						</Button>
					) }
				</div>
			</div>
		</BaseControl>
	);
}
