const { domReady } = wp;
const { registerBlockStyle, unregisterBlockStyle } = wp.blocks;

domReady(() => {
	registerBlockStyle('core/quote', [
		{
			name: 'small',
			label: 'Small',
		},
	]);

	unregisterBlockStyle('core/quote', ['large']);
});
