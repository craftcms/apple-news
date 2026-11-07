import {t} from '@craftcms/cp';
import ArticlePane from './ArticlePane.vue';

window.Cp.booting((cp) => {
  cp.$elementDetailsPanels.register({
    id: 'apple-news:channels',
    get label() {
      return t('Apple News Channels', {}, 'apple-news');
    },
    icon: 'newspaper',
    component: ArticlePane,
    order: 40,
    visible: (payload) => Boolean(payload.appleNews?.channels?.length),
    props: ({payload}) => ({payload}),
  });
});
