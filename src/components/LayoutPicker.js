/**
 * WordPress dependencies
 */
import { __ } from '@wordpress/i18n';
import { BaseControl } from '@wordpress/components';

const { layouts } = window.ai1wpAdmin;

const descriptions = {
	1: __( 'Logo inside a spinning ring', 'all-in-one-wp-preloader' ),
	2: __( 'Centered image that slides away', 'all-in-one-wp-preloader' ),
	3: __( 'Image with a three-panel reveal', 'all-in-one-wp-preloader' ),
	4: __( 'Logo with bouncing dots, fades out', 'all-in-one-wp-preloader' ),
	5: __( 'Logo with a progress bar, slides up', 'all-in-one-wp-preloader' ),
	6: __( 'Logo with orbiting dot, curtains open', 'all-in-one-wp-preloader' ),
	7: __( 'Logo with ripples, circle reveal', 'all-in-one-wp-preloader' ),
};

export default function LayoutPicker( { value, onChange } ) {
	return (
		<BaseControl
			__nextHasNoMarginBottom
			id="ai1wp-layout"
			help={ __(
				'Choose how the preloader looks while the page loads.',
				'all-in-one-wp-preloader'
			) }
		>
			<div
				className="ai1wp-layouts"
				role="radiogroup"
				aria-label={ __(
					'Preloader Design',
					'all-in-one-wp-preloader'
				) }
			>
				{ layouts.map( ( layout ) => {
					const isSelected = value === layout.value;
					return (
						<button
							key={ layout.value }
							type="button"
							role="radio"
							aria-checked={ isSelected }
							className={
								'ai1wp-layout' +
								( isSelected ? ' is-selected' : '' )
							}
							onClick={ () => onChange( layout.value ) }
						>
							<span
								className={ `ai1wp-layout__thumb ai1wp-layout__thumb--${ layout.value }` }
								aria-hidden="true"
							>
								<span />
								<span />
								<span />
							</span>
							<span className="ai1wp-layout__label">
								{ layout.label }
							</span>
							<span className="ai1wp-layout__desc">
								{ descriptions[ layout.value ] }
							</span>
						</button>
					);
				} ) }
			</div>
		</BaseControl>
	);
}
