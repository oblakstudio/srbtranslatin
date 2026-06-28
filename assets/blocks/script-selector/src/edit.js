import { InspectorControls, useBlockProps } from '@wordpress/block-editor';
import { PanelBody, SelectControl, TextControl } from '@wordpress/components';
import { __ } from '@wordpress/i18n';
import ServerSideRender from '@wordpress/server-side-render';

const DISPLAY_MODE_OPTIONS = [
    { label: __( 'Inline', 'srbtranslatin' ), value: 'inline' },
    { label: __( 'List', 'srbtranslatin' ), value: 'list' },
    { label: __( 'Dropdown', 'srbtranslatin' ), value: 'dropdown' },
];

export default function Edit( { attributes, setAttributes } ) {
    const blockProps = useBlockProps( {
        className: 'stl-script-selector-block-preview',
    } );
    const previewAttributes = {
        displayMode: attributes.displayMode || 'inline',
        cyrillicLabel: attributes.cyrillicLabel || '',
        latinLabel: attributes.latinLabel || '',
    };

    return (
        <>
            <InspectorControls>
                <PanelBody title={ __( 'Display settings', 'srbtranslatin' ) }>
                    <SelectControl
                        label={ __( 'Display mode', 'srbtranslatin' ) }
                        value={ previewAttributes.displayMode }
                        options={ DISPLAY_MODE_OPTIONS }
                        onChange={ ( displayMode ) => setAttributes( { displayMode } ) }
                    />
                    <TextControl
                        label={ __( 'Cyrillic label', 'srbtranslatin' ) }
                        value={ attributes.cyrillicLabel || '' }
                        placeholder={ __( 'Cyrillic', 'srbtranslatin' ) }
                        help={ __( 'Leave empty to use the default label.', 'srbtranslatin' ) }
                        onChange={ ( cyrillicLabel ) => setAttributes( { cyrillicLabel } ) }
                    />
                    <TextControl
                        label={ __( 'Latin label', 'srbtranslatin' ) }
                        value={ attributes.latinLabel || '' }
                        placeholder={ __( 'Latin', 'srbtranslatin' ) }
                        help={ __( 'Leave empty to use the default label.', 'srbtranslatin' ) }
                        onChange={ ( latinLabel ) => setAttributes( { latinLabel } ) }
                    />
                </PanelBody>
            </InspectorControls>
            <div { ...blockProps }>
                { ServerSideRender ? (
                    <ServerSideRender
                        block="srbtranslatin/script-selector"
                        attributes={ previewAttributes }
                    />
                ) : (
                    <p>{ __( 'Script selector preview is unavailable.', 'srbtranslatin' ) }</p>
                ) }
            </div>
        </>
    );
}
