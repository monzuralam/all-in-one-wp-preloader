/**
 * WordPress dependencies
 */
import { useCallback, useEffect, useRef, useState } from '@wordpress/element';
import { __ } from '@wordpress/i18n';
import {
	Button,
	Card,
	CardBody,
	CardHeader,
	Notice,
} from '@wordpress/components';

const { templates, styleUrl, defaultImage, siteTitle, siteUrl } =
	window.ai1wpAdmin;

// Delay before the "page loaded" state, same as visitors waiting for the page.
const PLAY_DELAY = 1200;

// Same rules as Frontend::dynamic_style().
function dynamicStyle( layout, color, image ) {
	let rules = '';
	if ( color ) {
		// Custom property for loaders that color inner panels ( Loader 6 ).
		rules += `--ai1wp-bg: ${ color };`;
		if ( layout !== '6' ) {
			rules += `background-color: ${ color };`;
		}
	}
	// Loader 2 & 3 show the image as background, others use an <img> tag.
	if ( [ '2', '3' ].includes( layout ) ) {
		rules += `background-image: url("${ image.replace( /"/g, '%22' ) }");`;
	}
	return rules ? `.ai1wp-style-${ layout }{${ rules }}` : '';
}

function escapeHtml( text ) {
	const div = document.createElement( 'div' );
	div.textContent = text;
	return div.innerHTML;
}

function previewDocument( layout ) {
	return (
		'<!doctype html><html><head>' +
		`<link rel="stylesheet" href="${ styleUrl }">` +
		'<style>' +
		'body{margin:0;padding:40px;font:14px/1.6 -apple-system,BlinkMacSystemFont,"Segoe UI",Roboto,sans-serif;color:#50575e;}' +
		'h2{margin:0 0 8px;color:#1e1e1e;}' +
		// main.js fades loader 3 out with jQuery.
		'.ai1wp-style-3{transition:opacity .6s;}.page-loaded .ai1wp-style-3{opacity:0;pointer-events:none;}' +
		'</style>' +
		'<style id="ai1wp-dynamic"></style>' +
		'</head><body>' +
		`<h2>${ escapeHtml( siteTitle ) }</h2>` +
		`<p>${ escapeHtml(
			__(
				'Your page content appears here once the preloader hides.',
				'all-in-one-wp-preloader'
			)
		) }</p>` +
		( templates[ layout ] || '' ) +
		'</body></html>'
	);
}

export default function Preview( { settings } ) {
	const frame = useRef();
	const timer = useRef();
	const playing = useRef( false );
	const [ run, setRun ] = useState( 0 );

	const image = settings.image || defaultImage;

	// Apply color / image to the current preview document.
	const apply = useCallback( () => {
		const doc = frame.current && frame.current.contentDocument;
		const style = doc && doc.getElementById( 'ai1wp-dynamic' );
		if ( ! style ) {
			return;
		}
		style.textContent = dynamicStyle(
			settings.layout,
			settings.bg_color,
			image
		);
		doc.querySelectorAll( '.preloader img' ).forEach( ( img ) => {
			img.src = image;
		} );
	}, [ settings.layout, settings.bg_color, image ] );

	useEffect( apply, [ apply ] );
	useEffect( () => () => clearTimeout( timer.current ), [] );

	const onLoad = () => {
		apply();
		if ( playing.current ) {
			playing.current = false;
			const doc = frame.current.contentDocument;
			timer.current = setTimeout(
				() => doc.body.classList.add( 'page-loaded' ),
				PLAY_DELAY
			);
		}
	};

	const play = () => {
		clearTimeout( timer.current );
		playing.current = true;
		setRun( ( value ) => value + 1 );
	};

	return (
		<Card className="ai1wp-preview">
			<CardHeader>
				<h3>{ __( 'Live Preview', 'all-in-one-wp-preloader' ) }</h3>
				<Button variant="secondary" size="compact" onClick={ play }>
					{ __( 'Play Animation', 'all-in-one-wp-preloader' ) }
				</Button>
			</CardHeader>
			<CardBody>
				{ ! settings.enable && (
					<Notice status="warning" isDismissible={ false }>
						{ __(
							'Preloader is disabled. Visitors will not see it until you enable it.',
							'all-in-one-wp-preloader'
						) }
					</Notice>
				) }
				<div className="ai1wp-browser">
					<div className="ai1wp-browser__bar" aria-hidden="true">
						<span className="ai1wp-browser__dots">
							<span />
							<span />
							<span />
						</span>
						<span className="ai1wp-browser__url">{ siteUrl }</span>
					</div>
					<iframe
						key={ `${ settings.layout }-${ run }` }
						ref={ frame }
						className="ai1wp-browser__frame"
						title={ __(
							'Preloader Preview',
							'all-in-one-wp-preloader'
						) }
						srcDoc={ previewDocument( settings.layout ) }
						onLoad={ onLoad }
					/>
				</div>
				<p className="ai1wp-preview__help">
					{ __(
						'Preview updates as you change settings. Changes are not saved until you click Save Changes.',
						'all-in-one-wp-preloader'
					) }
				</p>
			</CardBody>
		</Card>
	);
}
