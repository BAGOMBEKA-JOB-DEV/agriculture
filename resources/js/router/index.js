import { createRouter, createWebHistory } from 'vue-router';
import Home from '../Landing/Home.vue';
import Login from '../components/Auth/Login.vue';
import Register from '../components/Auth/Register.vue';
import Dashboard from '../components/Dashboard.vue';
import Profile from '../components/Profile.vue';

// Agriculture Marketplace Components
import Products from '../pages/Products.vue';
import ProductDetail from '../pages/ProductDetail.vue';
import Subscriptions from '../pages/Subscriptions.vue';

// Role-specific Components
import BuyerRfqs from '../pages/buyer/Rfqs.vue';
import BuyerMessages from '../pages/buyer/Messages.vue';

import VendorProducts from '../pages/vendor/Products.vue';
import VendorProductCreate from '../pages/vendor/ProductCreate.vue';
import VendorProductEdit from '../pages/vendor/ProductEdit.vue';

import AdminProducts from '../pages/admin/Products.vue';
import AdminUsers from '../pages/admin/Users.vue';

const routes = [
  {
    path: '/login',
    name: 'Login',
    component: Login,
  },
  {
    path: '/register',
    name: 'Register',
    component: Register,
  },
  {
    path: '/dashboard',
    name: 'Dashboard',
    component: Dashboard,
    meta: {
      requiresAuth: true,
      breadcrumb: { label: 'Dashboard' }
    },
  },
  {
    path: '/profile',
    name: 'Profile',
    component: Profile,
    meta: {
      requiresAuth: true,
      breadcrumb: { label: 'Profile' }
    },
  },
  // Marketplace Routes
  {
    path: '/products',
    name: 'Products',
    component: Products,
    meta: {
      requiresAuth: true,
      breadcrumb: { label: 'Marketplace' }
    },
  },
  {
    path: '/products/:id',
    name: 'ProductDetail',
    component: ProductDetail,
    meta: {
      requiresAuth: true,
      breadcrumb: [
        { label: 'Marketplace', path: '/products' },
        { label: 'Product Details' }
      ]
    },
  },
  {
    path: '/rfqs',
    name: 'RFQs',
    component: BuyerRfqs,
    meta: {
      requiresAuth: true,
      breadcrumb: { label: 'My Requests' }
    },
  },
  {
    path: '/messages',
    name: 'Messages',
    component: BuyerMessages,
    meta: {
      requiresAuth: true,
      breadcrumb: { label: 'Trade Discussions' }
    },
  },
  {
    path: '/subscriptions',
    name: 'Subscriptions',
    component: Subscriptions,
    meta: {
      requiresAuth: true,
      breadcrumb: { label: 'Membership Plan' }
    },
  },

  // Vendor Specific Routes
  {
    path: '/vendor/products',
    name: 'MyProducts',
    component: VendorProducts,
    meta: {
      requiresAuth: true,
      breadcrumb: { label: 'Inventory' }
    },
  },
  {
    path: '/vendor/products/create',
    name: 'ProductCreate',
    component: VendorProductCreate,
    meta: {
      requiresAuth: true,
      breadcrumb: [
        { label: 'Inventory', path: '/vendor/products' },
        { label: 'Add Product' }
      ]
    },
  },
  {
    path: '/vendor/products/:id/edit',
    name: 'ProductEdit',
    component: VendorProductEdit,
    meta: {
      requiresAuth: true,
      breadcrumb: [
        { label: 'Inventory', path: '/vendor/products' },
        { label: 'Edit Product' }
      ]
    },
  },

  // Admin Specific Routes
  {
    path: '/admin/products',
    name: 'AdminProducts',
    component: AdminProducts,
    meta: {
      requiresAuth: true,
      breadcrumb: { label: 'Product Moderation' }
    },
  },
  {
    path: '/admin/users',
    name: 'AdminUsers',
    component: AdminUsers,
    meta: {
      requiresAuth: true,
      breadcrumb: { label: 'Identity Nexus' }
    },
  },

  {
    path: '/',
    name: 'Home',
    component: Home,
  },
];

const router = createRouter({
  history: createWebHistory(import.meta.env.BASE_URL),
  routes,
});

// Navigation guard
router.beforeEach((to, from, next) => {
  const token = localStorage.getItem('token');
  const requiresAuth = to.matched.some(record => record.meta.requiresAuth);

  if (requiresAuth && !token) {
    next('/login');
  } else if ((to.path === '/login' || to.path === '/register') && token) {
    next('/dashboard');
  } else {
    next();
  }
});

export default router;
