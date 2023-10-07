import { createRouter, createWebHistory } from 'vue-router'
import Home from '@/Pages/Home.vue'


const router = createRouter({
  history: createWebHistory(),
  routes: [
    {
      path: '/',
      name: 'base',
      component: Home,
    },
    {
      path: '/:pathMatch(.*)*',
      name: 'page-not-found',
      component: () => import("@/components/PageNotFound.vue")
    },
  ]
})


router.beforeEach(async (to, from, next) => {
  const guards: {} | any = to.meta;
  for (const i in guards) {
    if (Object.prototype.hasOwnProperty.call(guards, i)) {
      const g = guards[i];
      if (typeof g == "function") {
        await g(
          to,
          from,
          next,
        );
      }
    }
  }
  next();
})

export default router
