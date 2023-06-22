/**
 * React hook that is used to mark the block wrapper element.
 * It provides all the necessary props like the class name.
 *
 * @see https://developer.wordpress.org/block-editor/reference-guides/packages/packages-block-editor/#useblockprops
 */
import { useBlockProps } from '@wordpress/block-editor';

/**
 * Retrieves the translation of text.
 *
 * @see https://developer.wordpress.org/block-editor/reference-guides/packages/packages-i18n/
 */
import { __ } from '@wordpress/i18n';

/**
 * The save function defines the way in which the different attributes should
 * be combined into the final markup, which is then serialized by the block
 * editor into `post_content`.
 *
 * @see https://developer.wordpress.org/block-editor/reference-guides/block-api/block-edit-save/#save
 *
 * @return {WPElement} Element to render.
 */
export default function save({ attributes }) {
	const { apiUrl, numberOfElements, content } = attributes;
	console.log(attributes)

	// Make API request and render the fetched elements
	// Use the fetched data to render the desired output in the frontend

	return (
		<div { ...useBlockProps.save() }>
			<h3>{content.title}</h3>
			<p>{ content.content }</p>
			{/* Render the fetched elements */}
		</div>
	);
}