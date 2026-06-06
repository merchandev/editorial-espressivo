import { registerPlugin } from '@wordpress/plugins';
import { PluginSidebar } from '@wordpress/edit-post';
import Sidebar from './components/Sidebar';

registerPlugin('ssivo-seo-sidebar', {
    render: () => (
        <PluginSidebar name="ssivo-seo-panel" title="SSIVO SEO" icon="chart-bar">
            <Sidebar />
        </PluginSidebar>
    ),
});
