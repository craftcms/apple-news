<script setup>
  import {computed, onBeforeUnmount, ref, watch} from 'vue';
  import {ActionMenu, actionClient, announce, t} from '@craftcms/cp';

  const props = defineProps({
    payload: {type: Object, required: true},
  });

  const busyStates = [
    'QUEUED',
    'QUEUED_UPDATE',
    'PROCESSING',
    'PROCESSING_UPDATE',
  ];
  const sharedStates = [
    'QUEUED_UPDATE',
    'PROCESSING',
    'PROCESSING_UPDATE',
    'LIVE',
  ];
  const queuedStates = ['QUEUED', 'QUEUED_UPDATE'];

  const info = computed(() => props.payload.appleNews);
  const channels = ref([]);
  const loading = ref(false);
  let refreshTimeout = null;

  watch(
    info,
    (value) => {
      channels.value = (value?.channels ?? []).map((channel) => ({...channel}));
      scheduleRefresh(0);
    },
    {immediate: true}
  );

  onBeforeUnmount(() => clearTimeout(refreshTimeout));

  function status(state) {
    switch (state) {
      case 'QUEUED':
        return {
          fill: 'gray',
          label: t(
            'The article is in the queue to be published.',
            {},
            'apple-news'
          ),
        };
      case 'QUEUED_UPDATE':
        return {
          fill: 'gray',
          label: t(
            'A previous version of the article has been published, and an update is currently in the queue to be published.',
            {},
            'apple-news'
          ),
        };
      case 'PROCESSING':
        return {
          fill: 'orange',
          label: t(
            'The article has been published and is going through processing.',
            {},
            'apple-news'
          ),
        };
      case 'PROCESSING_UPDATE':
        return {
          fill: 'orange',
          label: t(
            'A previous version of the article is visible in the News app, and an update is currently in processing.',
            {},
            'apple-news'
          ),
        };
      case 'LIVE':
        return {
          fill: 'green',
          label: t(
            'The article has been published, finished processing, and is visible in the News app.',
            {},
            'apple-news'
          ),
        };
      case 'FAILED_PROCESSING':
        return {
          fill: 'red',
          label: t(
            'The article failed during processing and is not visible in the News app.',
            {},
            'apple-news'
          ),
        };
      case 'FAILED_PROCESSING_UPDATE':
        return {
          fill: 'red',
          label: t(
            'A previous version of the article is visible in the News app, but an update failed during processing.',
            {},
            'apple-news'
          ),
        };
      case 'TAKEN_DOWN':
        return {
          fill: null,
          label: t(
            'The article was previously visible in the News app, but was taken down.',
            {},
            'apple-news'
          ),
        };
      default:
        return {
          fill: null,
          label: t('The article has not been published yet.', {}, 'apple-news'),
        };
    }
  }

  function actions(channel) {
    const items = [];

    if (sharedStates.includes(channel.state) && channel.shareUrl) {
      items.push({
        label: t('Copy share URL', {}, 'apple-news'),
        icon: 'clipboard',
        onClick: () => copyShareUrl(channel.shareUrl),
      });
    }

    if (
      !info.value.isDraftOrRevision &&
      channel.canPublish &&
      !queuedStates.includes(channel.state)
    ) {
      items.push({
        label: t('Publish to Apple News', {}, 'apple-news'),
        icon: 'paper-plane',
        onClick: () => publish(channel),
      });
    }

    items.push({
      type: 'link',
      label: t('Download for News Preview', {}, 'apple-news'),
      icon: 'download',
      href: channel.downloadUrl,
      target: '_blank',
    });

    return items;
  }

  async function copyShareUrl(url) {
    await navigator.clipboard.writeText(url);
    announce(t('Copied to clipboard.', {}, 'app'));
  }

  function applyInfos(infos) {
    for (const channel of channels.value) {
      const channelInfo = infos?.[channel.id];
      if (channelInfo) {
        channel.state = channelInfo.state;
        channel.shareUrl = channelInfo.shareUrl;
        channel.canPublish = channelInfo.canPublish ?? channel.canPublish;
      }
    }
  }

  async function publish(channel) {
    loading.value = true;
    try {
      const {data} = await actionClient.post('apple-news/article/publish', {
        entryId: info.value.entryId,
        siteId: info.value.siteId,
        channelId: channel.id,
      });
      applyInfos(data.infos);
      announce(status(channel.state).label);
      scheduleRefresh();
    } finally {
      loading.value = false;
    }
  }

  async function refresh() {
    refreshTimeout = null;
    loading.value = true;
    try {
      const {data} = await actionClient.post('apple-news/article/get-info', {
        entryId: info.value.entryId,
        siteId: info.value.siteId,
      });
      applyInfos(data.infos);
    } finally {
      loading.value = false;
    }
    scheduleRefresh();
  }

  /** Keeps refreshing while any article is queued or processing. */
  function scheduleRefresh(delay = 5000) {
    clearTimeout(refreshTimeout);
    refreshTimeout = null;

    if (channels.value.some((channel) => busyStates.includes(channel.state))) {
      refreshTimeout = setTimeout(refresh, delay);
    }
  }
</script>

<template>
  <div class="apple-news-pane" :aria-busy="loading">
    <ul class="apple-news-pane__channels">
      <li
        v-for="channel in channels"
        :key="channel.id"
        class="apple-news-pane__channel"
      >
        <craft-indicator
          :fill="status(channel.state).fill ?? undefined"
          :appearance="status(channel.state).fill ? undefined : 'outline'"
          :label="status(channel.state).label"
        ></craft-indicator>
        <div class="apple-news-pane__body">
          <div class="apple-news-pane__name">{{ channel.name }}</div>
          <div class="apple-news-pane__status">
            {{ status(channel.state).label }}
          </div>
        </div>
        <ActionMenu
          :actions="actions(channel)"
          :label="
            t('Actions for {channel}', {channel: channel.name}, 'apple-news')
          "
        />
      </li>
    </ul>
    <craft-spinner
      v-if="loading"
      class="apple-news-pane__spinner"
    ></craft-spinner>
  </div>
</template>

<style>
  .apple-news-pane {
    position: relative;
    padding-block: var(--c-spacing-lg, 1rem);
    padding-inline: var(--cp-container-padding, 1rem);
  }

  .apple-news-pane__channels {
    display: grid;
    gap: var(--c-spacing-md, 0.75rem);
    margin: 0;
    padding: 0;
    list-style: none;
  }

  .apple-news-pane__channel {
    display: flex;
    align-items: flex-start;
    gap: var(--c-spacing-sm, 0.5rem);
  }

  .apple-news-pane__channel > craft-indicator {
    margin-block-start: 0.4em;
  }

  .apple-news-pane__body {
    flex: 1;
    min-width: 0;
  }

  .apple-news-pane__name {
    font-weight: 600;
  }

  .apple-news-pane__status {
    color: var(--c-color-text-subtle, inherit);
    font-size: 0.875em;
  }

  .apple-news-pane__spinner {
    position: absolute;
    inset-block-start: var(--c-spacing-sm, 0.5rem);
    inset-inline-end: var(--cp-container-padding, 1rem);
  }
</style>
