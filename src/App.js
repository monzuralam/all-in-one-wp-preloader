/**
 * WordPress dependencies
 */
import { useEffect, useState } from '@wordpress/element';
import { __ } from '@wordpress/i18n';
import apiFetch from '@wordpress/api-fetch';
import {
	Button,
	Card,
	CardBody,
	CardHeader,
	Notice,
	Snackbar,
	ToggleControl,
} from '@wordpress/components';

/**
 * Internal dependencies
 */
import LayoutPicker from './components/LayoutPicker';
import ImageControl from './components/ImageControl';
import ColorControl from './components/ColorControl';
import Preview from './components/Preview';

const { settings: initialSettings, version } = window.ai1wpAdmin;

export default function App() {
	const [ settings, setSettings ] = useState( initialSettings );
	const [ saved, setSaved ] = useState( initialSettings );
	const [ isSaving, setIsSaving ] = useState( false );
	const [ success, setSuccess ] = useState( '' );
	const [ error, setError ] = useState( '' );

	const isDirty = JSON.stringify( settings ) !== JSON.stringify( saved );

	const update = ( key ) => ( value ) =>
		setSettings( ( prev ) => ( { ...prev, [ key ]: value } ) );

	// Warn before leaving with unsaved changes.
	useEffect( () => {
		if ( ! isDirty ) {
			return;
		}
		const warn = ( event ) => {
			event.preventDefault();
			event.returnValue = '';
		};
		window.addEventListener( 'beforeunload', warn );
		return () => window.removeEventListener( 'beforeunload', warn );
	}, [ isDirty ] );

	const save = async () => {
		setIsSaving( true );
		setError( '' );
		try {
			const response = await apiFetch( {
				path: '/wp/v2/settings',
				method: 'POST',
				data: { ai1wp_settings: settings },
			} );
			setSettings( response.ai1wp_settings );
			setSaved( response.ai1wp_settings );
			setSuccess( __( 'Settings saved.', 'all-in-one-wp-preloader' ) );
		} catch ( e ) {
			setError(
				e.message ||
					__(
						'Settings could not be saved.',
						'all-in-one-wp-preloader'
					)
			);
		} finally {
			setIsSaving( false );
		}
	};

	return (
		<div className="ai1wp-app">
			<header className="ai1wp-header">
				<div className="ai1wp-header__brand">
					<span className="ai1wp-header__logo" aria-hidden="true" />
					<h2 className="ai1wp-header__title">
						{ __(
							'All-in-One WP Preloader',
							'all-in-one-wp-preloader'
						) }
					</h2>
					<span className="ai1wp-badge">v{ version }</span>
				</div>
				<div className="ai1wp-header__actions">
					{ isDirty && (
						<span className="ai1wp-header__unsaved">
							{ __(
								'Unsaved changes',
								'all-in-one-wp-preloader'
							) }
						</span>
					) }
					<Button
						variant="primary"
						isBusy={ isSaving }
						disabled={ ! isDirty || isSaving }
						onClick={ save }
						__next40pxDefaultSize
					>
						{ isSaving
							? __( 'Saving…', 'all-in-one-wp-preloader' )
							: __( 'Save Changes', 'all-in-one-wp-preloader' ) }
					</Button>
				</div>
			</header>

			{ error && (
				<Notice
					className="ai1wp-error"
					status="error"
					onRemove={ () => setError( '' ) }
				>
					{ error }
				</Notice>
			) }

			<div className="ai1wp-body">
				<div className="ai1wp-main">
					<Card>
						<CardHeader>
							<h3>
								{ __( 'General', 'all-in-one-wp-preloader' ) }
							</h3>
						</CardHeader>
						<CardBody>
							<ToggleControl
								__nextHasNoMarginBottom
								label={ __(
									'Enable Preloader',
									'all-in-one-wp-preloader'
								) }
								help={ __(
									'Show the preloader to visitors while the page loads.',
									'all-in-one-wp-preloader'
								) }
								checked={ settings.enable }
								onChange={ update( 'enable' ) }
							/>
						</CardBody>
					</Card>

					<Card>
						<CardHeader>
							<h3>
								{ __(
									'Preloader Design',
									'all-in-one-wp-preloader'
								) }
							</h3>
						</CardHeader>
						<CardBody>
							<LayoutPicker
								value={ settings.layout }
								onChange={ update( 'layout' ) }
							/>
						</CardBody>
					</Card>

					<Card>
						<CardHeader>
							<h3>
								{ __(
									'Appearance',
									'all-in-one-wp-preloader'
								) }
							</h3>
						</CardHeader>
						<CardBody className="ai1wp-appearance">
							<ImageControl
								value={ settings.image }
								onChange={ update( 'image' ) }
							/>
							<ColorControl
								value={ settings.bg_color }
								onChange={ update( 'bg_color' ) }
							/>
						</CardBody>
					</Card>

					<Card>
						<CardHeader>
							<h3>
								{ __( 'Advanced', 'all-in-one-wp-preloader' ) }
							</h3>
						</CardHeader>
						<CardBody>
							<ToggleControl
								__nextHasNoMarginBottom
								label={ __(
									'Delete data on uninstall',
									'all-in-one-wp-preloader'
								) }
								help={
									settings.delete_data
										? __(
												'All plugin settings will be removed when the plugin is deleted.',
												'all-in-one-wp-preloader'
											)
										: __(
												'Settings are kept when the plugin is deleted, so you can reinstall it without losing them.',
												'all-in-one-wp-preloader'
											)
								}
								checked={ settings.delete_data }
								onChange={ update( 'delete_data' ) }
							/>
						</CardBody>
					</Card>
				</div>

				<aside className="ai1wp-aside">
					<Preview settings={ settings } />
				</aside>
			</div>

			{ success && (
				<div className="ai1wp-snackbar">
					<Snackbar onRemove={ () => setSuccess( '' ) }>
						{ success }
					</Snackbar>
				</div>
			) }
		</div>
	);
}
