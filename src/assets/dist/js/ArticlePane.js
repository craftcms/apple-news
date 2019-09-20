if (typeof Craft.AppleNews == typeof undefined) {
	Craft.AppleNews = {};
}

Craft.AppleNews.ArticlePane = Garnish.Base.extend({
	entryId: null,
	siteId: null,
	draftId: null,
	revisionId: null,
	infos: null,

	$pane: null,
	$spinner: null,
	$channelsById: null,
	menusByChannelId: null,
	refreshTimeout: null,

	init: function(entryId, siteId, draftId, revisionId, infos) {
		this.entryId = entryId;
		this.siteId = siteId;
		this.draftId = draftId;
		this.revisionId = revisionId;
		this.infos = infos;

		this.$pane = $('#apple-news-pane');
		this.$spinner = this.$pane.children('.spinner');

		this.$channelsById = {};
		this.menusByChannelId = {};
		var $channels = this.$pane.children('.data');

		for (var i = 0; i < $channels.length; i++) {
			var $channel = $channels.eq(i),
				$menuBtn = $channel.find('.menubtn'),
				channelId = $channel.data('channel-id'),
				menuBtn = $menuBtn.data('menubtn');

			this.$channelsById[channelId] = $channel;
			this.menusByChannelId[channelId] = menuBtn.menu;
			menuBtn.on('optionSelect', {channelId: channelId}, $.proxy(this, 'handleOptionSelect'))
		}

		this.refreshIfProcessing(false);
	},

	handleOptionSelect: function(ev) {
		var $a = $(ev.option);
		switch ($a.data('action')) {
			case 'copy-share-url': {
				var message = Craft.t('apple-news', '{ctrl}C to copy.', {
					ctrl: (navigator.appVersion.indexOf('Mac') ? '⌘' : 'Ctrl-')
				});

				prompt(message, $a.data('url'));
				break;
			}
			case 'publish-article': {
				this.$spinner.removeClass('hidden');
				var data = {
					entryId: this.entryId,
					siteId: this.siteId,
					channelId: ev.data.channelId
				};
				Craft.postActionRequest('apple-news/article/publish', data, $.proxy(function(response, textStatus) {
					this.$spinner.addClass('hidden');
					if (textStatus == 'success') {
						if (response.success) {
							this.updatePane(response.infos);
							Craft.cp.runQueue();
						}
					}
				}, this));
				break;
			}
			case 'download-preview': {
				break;
			}
		}
	},

	updatePane: function(newInfos) {
		for (var channelId in newInfos) {
			// Skip if not a property of newInfos
			if (!newInfos.hasOwnProperty(channelId)) {
				continue;
			}

			// Skip if we didn't know about this channel to begin with
			if (typeof this.$channelsById[channelId] == typeof undefined) {
				continue;
			}

			// Has the status changed?
			if (this.hasInfoChanged(channelId, 'state', newInfos)) {
				// Update the status indicator
				var statusColor, statusMessage;
				switch (newInfos[channelId]['state']) {
					case 'QUEUED':
						statusColor = 'grey';
						statusMessage = Craft.t('apple-news', 'The article is in the queue to be published.');
						break;
					case 'QUEUED_UPDATE':
						statusColor = 'grey';
						statusMessage = Craft.t('apple-news', 'A previous version of the article has been published, and an update is currently in the queue to be published.');
						break;
					case 'PROCESSING':
						statusColor = 'orange';
						statusMessage = Craft.t('apple-news', 'The article has been published and is going through processing.');
						break;
					case 'PROCESSING_UPDATE':
						statusColor = 'orange';
						statusMessage = Craft.t('apple-news', 'A previous version of the article is visible in the News app, and an update is currently in processing.');
						break;
					case 'LIVE':
						statusColor = 'green';
						statusMessage = Craft.t('apple-news', 'The article has been published, finished processing, and is visible in the News app.');
						break;
					case 'FAILED_PROCESSING':
						statusColor = 'red';
						statusMessage = Craft.t('apple-news', 'The article failed during processing and is not visible in the News app.');
						break;
					case 'FAILED_PROCESSING_UPDATE':
						statusColor = 'red';
						statusMessage = Craft.t('apple-news', 'A previous version of the article is visible in the News app, but an update failed during processing.');
						break;
					case 'TAKEN_DOWN':
						statusColor = null;
						statusMessage = Craft.t('apple-news', 'The article was previously visible in the News app, but was taken down.');
						break;
					default:
						statusColor = null;
						statusMessage = Craft.t('apple-news', 'The article has not been published yet.');
				}
				this.$channelsById[channelId].find('.status').attr({
					'class': 'status '+statusColor,
					'title': statusMessage
				});

				var menu = this.menusByChannelId[channelId],
					$menu = menu.$container.find('ul');

				// Clear the old actions
				$menu.children().remove();

				if ($.inArray(newInfos[channelId]['state'], ['QUEUED_UPDATE', 'PROCESSING', 'PROCESSING_UPDATE', 'LIVE']) != -1) {
					var shareUrl = newInfos[channelId]['shareUrl'];
					$menu.append($('<li><a data-action="copy-share-url" data-url="'+shareUrl+'">'+Craft.t('apple-news', 'Copy share URL')+'</a></li>'));
				}

				if ($.inArray(newInfos[channelId]['state'], ['QUEUED', 'QUEUED_UPDATE']) == -1 && !this.draftId && !this.revisionId && newInfos[channelId]['canPublish']) {
					$menu.append($('<li><a data-action="publish-article">'+Craft.t('apple-news', 'Publish to Apple News')+'</a></li>'));
				}

				var downloadUrl = Craft.getActionUrl('apple-news/article/download', {
					entryId: this.entryId,
					draftId: this.draftId,
					revisionId: this.revisionId,
					siteId: this.siteId,
					channelId: channelId
				});

				$menu.append($('<li><a href="'+downloadUrl+'" target="_blank">'+Craft.t('apple-news', 'Download for News Preview')+'</a></li>'));

				menu.addOptions($menu.children().children());
			}

			this.infos[channelId] = newInfos[channelId];
		}

		this.refreshIfProcessing(true);
	},

	hasInfoChanged: function (channelId, property, newInfos) {
		return (typeof this.infos[channelId] == typeof undefined || this.infos[channelId][property] != newInfos[channelId][property]);
	},

	refreshIfProcessing: function(delayed)
	{
		for (var channelId in this.infos) {
			// Skip if not a property of newInfos
			if (!this.infos.hasOwnProperty(channelId)) {
				continue;
			}

			if ($.inArray(this.infos[channelId]['state'], ['QUEUED', 'QUEUED_UPDATE', 'PROCESSING', 'PROCESSING_UPDATE']) != -1) {
				if (!delayed) {
					this.refresh();
				} else {
					this.refreshTimeout = setTimeout($.proxy(this, 'refresh'), 5000);
				}

				break;
			}
		}
	},

	refresh: function() {
		// Clear the refreshTimeout value so nothing thinks it's active
		this.refreshTimeout = null;

		this.$spinner.removeClass('hidden');
		var data = {
			entryId: this.entryId,
			siteId: this.siteId
		};
		Craft.postActionRequest('apple-news/article/get-info', data, $.proxy(function(response, textStatus) {
			this.$spinner.addClass('hidden');
			if (textStatus == 'success') {
				this.updatePane(response.infos);
			}
		}, this));
	}
});
