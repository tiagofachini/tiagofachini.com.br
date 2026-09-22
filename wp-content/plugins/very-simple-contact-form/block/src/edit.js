/**
 * WordPress Dependencies
 */
import { __ } from '@wordpress/i18n';
import { InspectorControls, useBlockProps } from '@wordpress/block-editor';
import { Disabled, PanelBody, TextareaControl, ExternalLink } from '@wordpress/components';
import ServerSideRender from '@wordpress/server-side-render';

/**
 * Block in Editor
 */
export default function Edit( props ) {
	const { attributes, setAttributes } = props;
	return (
		<>
			<InspectorControls>
				<PanelBody
					title={ __( 'Settings', 'very-simple-contact-form' ) }
				>
					<TextareaControl
						label={ __( 'Attributes', 'very-simple-contact-form' ) }
						help={ __( 'Example', 'very-simple-contact-form' ) + ": email_to=\"info@example.com\"" }
						value={ attributes.shortcodeSettings }
						onChange={ ( shortcodeSettings ) => setAttributes( { shortcodeSettings } ) }
						__nextHasNoMarginBottom
					/>
					<div> { __( 'For info and available attributes', 'very-simple-contact-form' ) } {' '}
						<ExternalLink href="https://wordpress.org/plugins/very-simple-contact-form">
							{ __( 'click here', 'very-simple-contact-form' ) }
						</ExternalLink>
					</div>
				</PanelBody>
			</InspectorControls>
			<div { ...useBlockProps() }>
				<Disabled>
					<ServerSideRender
						skipBlockSupportAttributes
						block="vscf/vscf-block"
						attributes={ attributes }
					/>
				</Disabled>
			</div>
		</>
	);
}
