/**
 * WordPress Dependencies
 */
import { registerBlockType } from '@wordpress/blocks';

/**
 * Internal dependencies
 */
import Edit from './edit';
import metadata from './block.json';

/**
 * Block Registration
 */
registerBlockType( metadata.name, {
	/**
	 * See file ./edit.js
	 */
	edit: Edit,
} );
