import { createRouter, createWebHistory } from 'vue-router'
import BookingView from './views/BookingView.vue'
import CheckoutView from './views/CheckoutView.vue'
import ExtranetView from './views/ExtranetView.vue'

const routes = [
  {
    path: '/',
    redirect: '/book/5',
  },
  {
    path: '/book/:propertyId?',
    name: 'booking',
    component: BookingView,
    props: true,
  },
  {
    path: '/checkout',
    name: 'checkout',
    component: CheckoutView,
  },
  {
    path: '/book/:propertyId/checkout',
    name: 'property-checkout',
    component: CheckoutView,
    props: true,
  },
  {
    path: '/extranet',
    name: 'extranet',
    component: ExtranetView,
  },
  {
    path: '/:pathMatch(.*)*',
    redirect: '/book/5',
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
