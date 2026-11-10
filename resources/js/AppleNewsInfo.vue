<script setup>
  import {t} from '@craftcms/cp';

  defineProps({
    channels: {type: Array, required: true},
  });
</script>

<template>
  <craft-empty
    v-if="!channels.length"
    :label="t('You haven’t defined any channels yet.', {}, 'apple-news')"
  ></craft-empty>
  <div v-else class="grid gap-8">
    <div v-for="channel in channels" :key="channel.id">
      <h2 class="mb-3">{{ channel.name }}</h2>

      <table class="cp-table cp-table--aligned" dir="ltr">
        <tbody>
          <tr>
            <th class="light">{{ t('ID', {}, 'apple-news') }}</th>
            <td>
              <code>{{ channel.id }}</code>
            </td>
          </tr>
        </tbody>
      </table>

      <h3 class="mt-6 mb-3">{{ t('Channel Sections', {}, 'apple-news') }}</h3>

      <template v-for="section in channel.sections" :key="section.id">
        <h4 class="mb-2">
          {{ section.name }}
          <template v-if="section.isDefault">
            {{ t('(Default)', {}, 'apple-news') }}
          </template>
        </h4>
        <table class="cp-table cp-table--aligned mb-3" dir="ltr">
          <tbody>
            <tr>
              <th class="light">{{ t('ID', {}, 'apple-news') }}</th>
              <td>
                <code>{{ section.id }}</code>
              </td>
            </tr>
            <tr>
              <th class="light">{{ t('Link', {}, 'apple-news') }}</th>
              <td>
                <code>{{ section.link }}</code>
              </td>
            </tr>
          </tbody>
        </table>
      </template>
    </div>
  </div>
</template>
