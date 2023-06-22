/**
 * WordPress dependencies
 */
import { __ } from "@wordpress/i18n";
import { useContext, useState, useEffect } from "@wordpress/element";
import { BlockControls, useBlockProps } from "@wordpress/block-editor";
import {
	ToolbarButton,
	Disabled,
	ToolbarGroup,
	TextControl,
	Button,
	Spinner,
} from "@wordpress/components";

import { cog, seen } from "@wordpress/icons";

/**
 * Internal dependencies
 */
import Preview from "./preview";

export default function Edit({ attributes, setAttributes, isSelected }) {
	const { apiUrl, numberOfElements, content } = attributes;
	const [isLoading, setIsLoading] = useState(false);
	const [isPreview, setIsPreview] = useState(true);
	const [apiResponse, setApiResponse] = useState(null);
	const isDisabled = useContext(Disabled.Context);

	const updateApiUrl = (newUrl) => {
		setAttributes({ apiUrl: newUrl });
	};

	const updateNumberOfElements = (newValue) => {
		setAttributes({ numberOfElements: parseInt(newValue) });
	};

	const updateContent = (newValue) => {
		setAttributes({ content: newValue });
	};

	function switchToPreview() {
		setIsPreview(true);
	}

	function switchToSettings() {
		setIsPreview(false);
	}

	// Function to get a transient using the WordPress REST API
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

	// Function to set a transient using the WordPress REST API
	const setTransient = (transientName, transientData) => {
		return fetch(
			`/?rest_route=/block-api-block/v1/transients/${transientName}`,
			{
				method: "POST",
				headers: {
					"Content-Type": "application/json",
				},
				body: JSON.stringify(transientData),
			}
		)
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
		// Fetch API response when apiUrl or numberOfElements changes
		if (isPreview) {
			const transientName = "block_api_transient"; // Transient name
			const expiration = 60 * 60; // Transient expiration time (1 hour)
			const payload = {
				title: "Title 1",
				content: "Lorem ipsum dolor sit amet",
			};

			const fetchAPIResponse = async () => {
				try {
					const response = await fetch(apiUrl, {
						method: "POST",
						headers: {
							"Content-Type": "application/json",
						},
						body: JSON.stringify(payload),
					});

					if (response.ok) {
						const data = await response.json();
						setApiResponse(data);

						// Store the API response in a transient using WordPress functions
						const transientData = {
							data: data,
							expiration: expiration,
						};
						setTransient(transientName, transientData);
					} else {
						throw new Error(response.statusText);
					}
				} catch (error) {
					console.error(error);
				}
				setIsLoading(false);
			};

			// Check if the transient exists and is not expired
			const checkTransient = async () => {
				try {
					const transientData = await getTransient(transientName);

					if (transientData.success) {
						setApiResponse(transientData.data);
						//   attributes.content = transientData.data.json;
						updateContent(transientData.data.json);
						setIsLoading(false);
					} else {
						fetchAPIResponse();
					}
				} catch (error) {
					console.error(error);
				}
			};

			checkTransient();
		}
	}, [apiUrl, numberOfElements, isPreview]);

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
				<Preview content={attributes.content} isSelected={isSelected} />
			) : (
				<div>
					<TextControl
						label={__("API URL", "block-api-block")}
						value={apiUrl}
						onChange={updateApiUrl}
					/>
					<TextControl
						label={__("Number of Elements", "block-api-block")}
						value={numberOfElements}
						type="number"
						onChange={updateNumberOfElements}
					/>
					<Button variant="primary" onClick={switchToPreview}>
						{__("Update", "block-api-block")}
					</Button>
				</div>
			)}
		</div>
	);
}
