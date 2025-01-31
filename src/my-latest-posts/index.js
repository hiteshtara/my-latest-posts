import { registerBlockType } from '@wordpress/blocks';
import ServerSideRender from '@wordpress/server-side-render';
import metadata from './block.json';

registerBlockType(metadata.name, {
    edit: () => {
        return (
            <ServerSideRender block="latest-posts-block/block" />
        );
    },
});
