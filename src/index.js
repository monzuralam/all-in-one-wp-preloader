/**
 * All-in-One WP Preloader - Settings App
 */
import { createRoot } from '@wordpress/element';

import App from './App';
import './admin.scss';

const root = document.getElementById( 'ai1wp-settings-root' );

if ( root ) {
	createRoot( root ).render( <App /> );
}
