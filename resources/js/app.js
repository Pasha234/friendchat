import './bootstrap';

// Route information for Vue Router

// Component File
import App from '@/js/App';
import router from '@/js/router/routes.js';

import { createApp } from 'vue';
import store from '@/js/stores/index.js';

const app = createApp(App)

store.dispatch('getUserFromServer')
  .then(() => {
    router.beforeEach(async (to, from) => {
      if (to.meta.middleware) {
        const middleware = to.meta.middleware
        const context = {
            to,
            from,
            store
        }
        return await middleware[0]({
            ...context
        })
      }
    })
    
    // Install the store instance as a plugin
    app.use(store)
    app.use(router);
    
    app.mount('#app');
  })

export default app;
