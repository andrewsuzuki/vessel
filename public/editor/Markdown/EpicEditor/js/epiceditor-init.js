var editor = new EpicEditor({
	clientSideStorage: false,
	container: 'epiceditor',
	textarea:  'content',
	basePath: '/packages/hokeo/vessel/editor/Markdown/EpicEditor/themes',
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