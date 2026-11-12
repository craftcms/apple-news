import {t} from '@craftcms/cp';
import AppleNewsInfo from './AppleNewsInfo.vue';
import ArticlePane from './ArticlePane.vue';

window.Cp.booting((cp) => {
  // The Apple News Info utility's content (see `AppleNewsInfo::contentHtml()`)
  cp.$components.register('AppleNewsInfo', AppleNewsInfo);

  cp.$elementDetailsPanels.register({
    id: 'apple-news:channels',
    get label() {
      return t('Apple News', {}, 'apple-news');
    },
    icon: 'newspaper',
    component: ArticlePane,
    order: 40,
    visible: (payload) => Boolean(payload.appleNews?.channels?.length),
    props: ({payload}) => ({payload}),
  });
});
