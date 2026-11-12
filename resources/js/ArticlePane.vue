<script setup>
  import {computed, onBeforeUnmount, ref, watch} from 'vue';
  import {ActionMenu, actionClient, announce, t} from '@craftcms/cp';

  const props = defineProps({
    payload: {type: Object, required: true},
  });

  const info = computed(() => props.payload.appleNews);
  const channels = ref([]);
  const loading = ref(false);
  /** The channel an article is being published to, which shows a spinner in its badge. */
  const publishingId = ref(null);
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
    publishingId.value = channel.id;
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
      publishingId.value = null;
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
      <li v-for="channel in channels" :key="channel.id">
        <craft-card>
          <span slot="label" class="apple-news-pane__name">{{
            channel.name
          }}</span>
          <craft-badge slot="badge" :fill="channel.status.color" size="small">
            <craft-spinner
              v-if="publishingId === channel.id"
              slot="prefix"
            ></craft-spinner>
            {{
              publishingId === channel.id
                ? t('Publishing…', {}, 'apple-news')
                : channel.status.label
            }}
          </craft-badge>
          <ActionMenu
            slot="actions"
            :actions="actions(channel)"
            :label="
              t('Actions for {channel}', {channel: channel.name}, 'apple-news')
            "
          />
          <p class="apple-news-pane__status">
            {{ channel.status.description }}
          </p>
          <craft-input-copy
            v-if="channel.isShareable && channel.shareUrl"
            class="apple-news-pane__share-url"
            .value="channel.shareUrl"
          >
            <label slot="label">{{ t('Share URL', {}, 'apple-news') }}</label>
          </craft-input-copy>
        </craft-card>
      </li>
    </ul>
  </div>
</template>

<style>
  .apple-news-pane {
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

  .apple-news-pane__name {
    font-weight: 600;
  }

  .apple-news-pane__status {
    margin: 0;
    color: var(--c-text-quiet, inherit);
  }

  .apple-news-pane craft-badge craft-spinner {
    --size: 0.75em;
  }

  .apple-news-pane__share-url {
    display: block;
    margin-block-start: var(--c-spacing-md, 0.75rem);
  }
</style>
