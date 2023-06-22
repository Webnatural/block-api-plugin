/**
 * WordPress dependencies
 */
import { useMemo } from "@wordpress/element";
import {
	transformStyles,
	store as blockEditorStore,
} from "@wordpress/block-editor";
import { useSelect } from "@wordpress/data";

// Default styles used to unset some of the styles
// that might be inherited from the editor style.
const DEFAULT_STYLES = `
	html,body,:root {
		margin: 0 !important;
		padding: 0 !important;
		overflow: visible !important;
		min-height: auto !important;
	}
`;

export default function BlockApiPreview({ data, isSelected }) {
	const settingStyles = useSelect((select) => {
		return select(blockEditorStore).getSettings()?.styles;
	}, []);

	const styles = useMemo(
		() => [DEFAULT_STYLES, ...transformStyles(settingStyles)],
		[settingStyles]
	);

	return (
		<>
			{data && data.title && data.content ? (
				<div>
					<h3>{data.title}</h3>
					<p>{data.content}</p>
				</div>
			) : (
				"No content"
			)}
		</>
	);
}
