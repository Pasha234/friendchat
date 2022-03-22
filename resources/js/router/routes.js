import { createWebHistory, createRouter, useRoute } from 'vue-router';

import Home from '@/js/views/Home';
import About from '@/js/views/About';
import User from '@/js/views/User';
import NotFound from '@/js/layouts/404';
import SignUp from '@/js/views/SignUp';
import Login from '@/js/views/Login';
import Search from '@/js/views/Search';

import guest from '@/js/router/middleware/guest.js';
import auth from '@/js/router/middleware/auth.js';

const router = createRouter({
  history: createWebHistory(),
  routes: [
    {
      path: '/',
      name: 'home',
      component: Home,
      meta: {
        middleware: [
          auth
        ]
      }
    },
    {
      path: '/about',
      name: 'about',
      component: About,
    },
    {
      path: '/user/:id',
      name: 'user',
      component: User,
      meta: {
        middleware: [
          auth
        ]
      }
    },
    {
      path: '/signup',
      name: 'signup',
      component: SignUp,
    },
    {
      path: '/login',
      name: 'login',
      component: Login,
      meta: {
        middleware: [
          guest
        ]
      }
    },
    {
      path: '/search',
      name: 'search',
      component: Search,
    },

    // 404
    {
      path: '/:catchAll()',
      name: 'NotFound',
      component: NotFound,
    }
  ],
  linkExactActiveClass: "active",
});

export default router;