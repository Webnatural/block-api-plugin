/**
 * WordPress dependencies
 */
import { __ } from "@wordpress/i18n";
import { useState, useEffect } from "@wordpress/element";
import { BlockControls, useBlockProps } from "@wordpress/block-editor";
import {
	ToolbarButton,
	ToolbarGroup,
	TextControl,
	Button,
} from "@wordpress/components";

import { cog, seen } from "@wordpress/icons";

/**
 * Internal dependencies
 */
import Preview from "./preview";

export default function Edit({ attributes, setAttributes, isSelected }) {
	const { title } = attributes;
	const [isPreview, setIsPreview] = useState(true);

	const updateContent = (newValue) => {
		setAttributes({ data: newValue });
	};

	const updateTitle = (newValue) => {
		setAttributes({ title: newValue });
	};

	function switchToPreview() {
		setIsPreview(true);
	}

	function switchToSettings() {
		setIsPreview(false);
	}

	// Function to get a transient using the WordPress REST API.
	const getTransient = (transientName) => {
		return fetch(`/?rest_route=/block-api-block/v1/transients/${transientName}`)
			.then((response) => {
				if (!response.ok) {
					throw new Error(response.statusText);
				}
				return response.json();
			})
			.then((data) => {
				return data;
			})
			.catch((error) => {
				console.error(error);
				return null;
			});
	};

	useEffect(() => {
		const transientName = "block_api_transient"; // Transient name.
		const checkTransient = async () => {
			try {
				const transientData = await getTransient(transientName);
				if (transientData.success) {
					updateContent(transientData.data);
				} else {
					updateContent(`There was an error in API response.`);
				}
			} catch (error) {
				console.error(error);
			}
		};
		checkTransient();
	}, []);

	return (
		<div {...useBlockProps({ className: "block-library-block_api" })}>
			<BlockControls>
				<ToolbarGroup>
					<ToolbarButton
						className="components-tab-button"
						isPressed={!isPreview}
						onClick={switchToSettings}
						icon={cog}
					></ToolbarButton>
					<ToolbarButton
						className="components-tab-button"
						isPressed={isPreview}
						onClick={switchToPreview}
						icon={seen}
					></ToolbarButton>
				</ToolbarGroup>
			</BlockControls>
			{isPreview ? (
				<Preview attributes={attributes} isSelected={isSelected} />
			) : (
				<div>
					<TextControl
						label={__("Title", "block-api-block")}
						value={title}
						onChange={updateTitle}
					/>
					<Button variant="primary" onClick={switchToPreview}>
						{__("Update", "block-api-block")}
					</Button>
				</div>
			)}
		</div>
	);
}
