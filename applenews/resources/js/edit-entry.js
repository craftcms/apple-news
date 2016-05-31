Garnish.$doc.on('ready', function() {
	$('#apple-news-pane .menubtn').each(function() {
		var menuBtn = $(this).data('menubtn');
		menuBtn.on('optionSelect', function(ev) {
			var $a = $(ev.option);
			switch ($a.data('action')) {
				case 'copy-share-url': {
					var message = Craft.t('{ctrl}C to copy.', {
						ctrl: (navigator.appVersion.indexOf('Mac') ? '⌘' : 'Ctrl-')
					});

					prompt(message, $a.data('url'));
					break;
				}
				case 'download-preview': {
					break;
				}
			}
		});
	});
});
