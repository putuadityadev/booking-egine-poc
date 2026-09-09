import { createRouter, createWebHistory } from 'vue-router'
import BookingView from './views/BookingView.vue'
import ExtranetView from './views/ExtranetView.vue'

const routes = [
  {
    path: '/',
    redirect: '/book/1',
  },
  {
    path: '/book/:propertyId?',
    name: 'booking',
    component: BookingView,
    props: true,
  },
  {
    path: '/extranet',
    name: 'extranet',
    component: ExtranetView,
  },
  {
    path: '/:pathMatch(.*)*',
    redirect: '/book/1',
  },
]

const router = createRouter({
  history: createWebHistory(),
  routes,
  scrollBehavior() {
    return { top: 0, behavior: 'smooth' }
  },
})

export default router
