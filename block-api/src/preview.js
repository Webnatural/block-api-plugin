export default function BlockApiPreview({ attributes }) {
	return (
		<>
			{attributes.title && <h2>{attributes.title}</h2>}
			{!!attributes.data ? (
				<div>
					<pre>{attributes.data}</pre>
				</div>
			) : (
				"Loading or no data from transient..."
			)}
		</>
	);
}
