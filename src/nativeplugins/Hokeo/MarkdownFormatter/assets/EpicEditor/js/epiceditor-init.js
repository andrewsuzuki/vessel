var editor = new EpicEditor({
	clientSideStorage: false,
	container: 'epiceditor',
	textarea:  'content',
	basePath: '/assets/9f1d03a18f3c85bb96b8e20d105c58d1/EpicEditor/themes',
	theme: {
		base:    '/base/epiceditor.css',
		preview: '/preview/github.css',
		editor:  '/editor/epic-dark.css',
	},
	autogrow: {
		minHeight: 250,
		maxHeight: 600
	}
}).load();