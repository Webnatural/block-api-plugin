/**
 * WordPress dependencies
 */
import { createBlock } from '@wordpress/blocks';

const transforms = {
	from: [
		{
			type: 'block',
			blocks: [ 'create-block/block-api' ],
			transform: ( { content } ) => {
				return createBlock( 'core/html', {
					content,
				} );
			},
		},
	],
};

export default transforms;