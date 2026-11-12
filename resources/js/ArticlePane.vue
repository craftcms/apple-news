<script setup>
  import {computed, onBeforeUnmount, ref, watch} from 'vue';
  import {ActionMenu, actionClient, announce, t} from '@craftcms/cp';

  const props = defineProps({
    payload: {type: Object, required: true},
  });

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

  function actions(channel) {
    const items = [];

    if (
      !info.value.isDraftOrRevision &&
      channel.canPublish &&
      !channel.isQueued
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

  function applyInfos(infos) {
    for (const channel of channels.value) {
      const channelInfo = infos?.[channel.id];
      if (channelInfo) {
        Object.assign(channel, channelInfo);
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
      announce(channel.status.label);
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

    if (
      channels.value.some((channel) => channel.isQueued || channel.isProcessing)
    ) {
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
          :fill="channel.status.color ?? undefined"
          :appearance="channel.status.color ? undefined : 'outline'"
          :label="channel.status.label"
        ></craft-indicator>
        <div class="apple-news-pane__body">
          <div class="apple-news-pane__name">{{ channel.name }}</div>
          <div class="apple-news-pane__status">
            {{ channel.status.label }}
          </div>
          <craft-input-copy
            v-if="channel.isShareable && channel.shareUrl"
            class="apple-news-pane__share-url"
            .value="channel.shareUrl"
          >
            <label slot="label">{{ t('Share URL', {}, 'apple-news') }}</label>
          </craft-input-copy>
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

  .apple-news-pane__share-url {
    margin-block-start: var(--c-spacing-sm, 0.5rem);
  }

  .apple-news-pane__spinner {
    position: absolute;
    inset-block-start: var(--c-spacing-sm, 0.5rem);
    inset-inline-end: var(--cp-container-padding, 1rem);
  }
</style>
