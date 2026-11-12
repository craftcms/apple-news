<script setup>
  import {h} from 'vue';
  import {
    AdminTable,
    TableAppearance,
    createCraftColumnHelper,
    t,
  } from '@craftcms/cp';

  defineProps({
    channels: {type: Array, required: true},
  });

  const columnHelper = createCraftColumnHelper();
  const columns = columnHelper.columns([
    columnHelper.accessor('name', {
      header: t('Section', {}, 'apple-news'),
      meta: {cellTag: 'th'},
      enableSorting: false,
      cell: ({row}) =>
        h('span', [
          row.original.name,
          row.original.isDefault
            ? h(
                'craft-badge',
                {
                  fill: 'blue',
                  size: 'small',
                  class: 'apple-news-info__default',
                },
                t('Default', {}, 'apple-news')
              )
            : null,
        ]),
    }),
    columnHelper.handle('id', {
      header: t('ID', {}, 'apple-news'),
      enableSorting: false,
      meta: {trackSize: 'minmax(min-content, 2fr)'},
    }),
    columnHelper.accessor('link', {
      header: t('API Link', {}, 'apple-news'),
      enableSorting: false,
      meta: {trackSize: 'min-content'},
      cell: ({row}) =>
        row.original.link
          ? h('craft-copy-button', {
              value: row.original.link,
              icon: 'link',
              label: t(
                'Copy {name} section API link',
                {name: row.original.name},
                'apple-news'
              ),
            })
          : null,
    }),
  ]);
</script>

<template>
  <craft-empty
    v-if="!channels.length"
    :label="t('You haven’t defined any channels yet.', {}, 'apple-news')"
  ></craft-empty>
  <div v-else class="apple-news-info">
    <AdminTable
      v-for="channel in channels"
      :key="channel.id"
      :rows="channel.sections"
      :columns="columns"
      :title="t('{name} sections', {name: channel.name}, 'apple-news')"
      :appearance="TableAppearance.Raised"
      :show-footer="false"
      :empty-message="t('This channel has no sections.', {}, 'apple-news')"
    >
      <template #table-header>
        <div class="apple-news-info__header">
          <h2 class="apple-news-info__name">{{ channel.name }}</h2>
          <craft-copy-attribute :value="channel.id">
            {{ channel.id }}
          </craft-copy-attribute>
        </div>
      </template>
    </AdminTable>
  </div>
</template>

<style>
  .apple-news-info {
    display: grid;
    gap: var(--c-spacing-lg, 1rem);
  }

  .apple-news-info__header {
    display: flex;
    flex-wrap: wrap;
    align-items: center;
    gap: var(--c-spacing-xs, 0.25rem) var(--c-spacing-md, 0.75rem);
    padding-block-start: var(--c-spacing-md, 0.75rem);
    padding-inline: var(--c-spacing-md, 0.75rem);
  }

  .apple-news-info__name {
    margin: 0;
    font-size: var(--c-text-lg, 1rem);
  }

  .apple-news-info__default {
    margin-inline-start: var(--c-spacing-xs, 0.25rem);
  }
</style>
