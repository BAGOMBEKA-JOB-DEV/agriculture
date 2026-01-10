import { createRouter, createWebHistory } from 'vue-router';
import Home from '../Landing/Home.vue';
import Login from '../components/Auth/Login.vue';
import Register from '../components/Auth/Register.vue';
import Dashboard from '../components/Dashboard.vue';

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

import SuperAdminDashboard from '../pages/super-admin/Dashboard.vue';
import SuperAdminCategories from '../pages/super-admin/Categories.vue';
import RoleManagement from '../pages/super-admin/RoleManagement.vue';

import Marketplace from '../pages/Marketplace.vue';
import MarketplaceDetail from '../pages/MarketplaceDetail.vue';
import Settings from '../pages/Settings.vue';

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
    path: '/',
    name: 'Home',
    component: Home,
  },
  {
    path: '/settings',
    name: 'Settings',
    component: Settings,
    meta: {
      requiresAuth: true,
      breadcrumb: [
        { label: 'Dashboard', path: '/dashboard' },
        { label: 'Personal Nexus' }
      ]
    },
  },
  // Marketplace Routes (Public Discovery)
  {
    path: '/marketplace',
    name: 'Marketplace',
    component: Marketplace,
    meta: {
      requiresAuth: false,
      breadcrumb: { label: 'Public Exchange' }
    },
  },
  {
    path: '/marketplace/:id',
    name: 'MarketplaceDetail',
    component: MarketplaceDetail,
    meta: {
      requiresAuth: false,
      breadcrumb: [
        { label: 'Public Exchange', path: '/marketplace' },
        { label: 'Asset Details' }
      ]
    },
  },
  // Dashboard Products Management (Reserved for Authenticated Users)
  {
    path: '/products',
    name: 'Products',
    component: Products,
    meta: {
      requiresAuth: true,
      breadcrumb: { label: 'Global Exchange' }
    },
  },
  {
    path: '/products/:id',
    name: 'ProductDetail',
    component: ProductDetail,
    meta: {
      requiresAuth: true,
      breadcrumb: [
        { label: 'Global Exchange', path: '/products' },
        { label: 'Asset Details' }
      ]
    },
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
  {
    path: '/notifications',
    name: 'UserNotifications',
    component: () => import('../pages/Notifications/Index.vue'),
    meta: {
      requiresAuth: true,
      breadcrumb: { label: 'Inbox' }
    },
  },
  {
    path: '/notifications/:id',
    name: 'UserNotificationDetail',
    component: () => import('../pages/Notifications/Show.vue'),
    meta: {
      requiresAuth: true,
      breadcrumb: [
        { label: 'Inbox', path: '/notifications' },
        { label: 'Message' }
      ]
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
    path: '/admin/notifications/create',
    name: 'AdminCreateNotification',
    component: () => import('../pages/admin/Notifications/Create.vue'),
    meta: {
      requiresAuth: true,
      breadcrumb: [
        { label: 'Broadcast Center', path: '/admin/notifications' },
        { label: 'New Alert' }
      ]
    },
  },
  {
    path: '/admin/notifications',
    name: 'AdminNotifications',
    component: () => import('../pages/admin/Notifications/Index.vue'),
    meta: {
      requiresAuth: true,
      breadcrumb: { label: 'Broadcast Center' }
    },
  },
  {
    path: '/admin/notifications/:id/edit',
    name: 'AdminEditNotification',
    component: () => import('../pages/admin/Notifications/Edit.vue'),
    meta: {
      requiresAuth: true,
      breadcrumb: [
        { label: 'Broadcast Center', path: '/admin/notifications' },
        { label: 'Edit Alert' }
      ]
    },
  },

  // Super Admin Specific Routes
  {
    path: '/super-admin',
    name: 'SuperAdminDashboard',
    component: SuperAdminDashboard,
    meta: {
      requiresAuth: true,
      breadcrumb: { label: 'Governance Nexus' }
    },
  },
  {
    path: '/super-admin/categories',
    name: 'SuperAdminCategories',
    component: SuperAdminCategories,
    meta: {
      requiresAuth: true,
      breadcrumb: { label: 'Asset Classifications' }
    },
  },
  {
    path: '/super-admin/roles',
    name: 'RoleManagement',
    component: RoleManagement,
    meta: {
      requiresAuth: true,
      breadcrumb: { label: 'Role Ecosystem' }
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

import { useAuthStore } from '../stores/auth';

// Navigation guard
router.beforeEach(async (to, from, next) => {
  const authStore = useAuthStore();

  // Restore session if token exists but user is missing (page refresh)
  if (localStorage.getItem('token') && !authStore.user) {
    await authStore.checkAuth();
  }

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
