import { createRouter, createWebHistory } from 'vue-router';
import Home from '../Landing/Home.vue';
import Login from '../components/Auth/Login.vue';
import Register from '../components/Auth/Register.vue';
import Dashboard from '../components/Dashboard.vue';

// Agriculture Marketplace Components
import Products from '../pages/Products.vue';
import ProductDetail from '../pages/ProductDetail.vue';
import Subscriptions from '../pages/Subscriptions.vue';
import LandingSubscriptions from '../Landing/Subscription.vue';

// Role-specific Components
import BuyerRfqs from '../pages/buyer/Rfqs.vue';
import RfqCreate from '../pages/buyer/RfqCreate.vue';
import BuyerMessages from '../pages/buyer/Messages.vue';

import VendorProducts from '../pages/vendor/Products.vue';
import VendorRfqs from '../pages/vendor/Rfqs.vue';
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
      // No permission required - dashboard is accessible to all authenticated users
      // Content displayed is based on user's permissions
      breadcrumb: { label: 'Dashboard' }
    },
  },
  {
    path: '/rfqs/create',
    name: 'RfqCreate',
    component: RfqCreate,
    meta: {
      requiresAuth: true,
      permission: 'view_rfqs',
      breadcrumb: [
        { label: 'My Requests', path: '/rfqs' },
        { label: 'Initiate Request' }
      ]
    },
  },
  {
    path: '/rfqs',
    name: 'RFQs',
    component: BuyerRfqs,
    meta: {
      requiresAuth: true,
      permission: 'view_rfqs',
      breadcrumb: { label: 'My Requests' }
    },
  },
  {
    path: '/messages',
    name: 'Messages',
    component: BuyerMessages,
    meta: {
      requiresAuth: true,
      permission: 'access_conversations',
      breadcrumb: { label: 'Trade Discussions' }
    },
  },
  {
    path: '/subscriptions',
    name: 'Subscriptions',
    component: Subscriptions,
    meta: {
      requiresAuth: true,
      permission: 'view_subscriptions',
      breadcrumb: { label: 'Membership Plan' }
    },
  },
  {
    path: '/membership-plans',
    name: 'LandingSubscriptions',
    component: LandingSubscriptions,
    meta: {
      requiresAuth: false,
    },
  },
  {
    path: '/notifications',
    name: 'UserNotifications',
    component: () => import('../pages/Notifications/Index.vue'),
    meta: {
      requiresAuth: true,
      permission: 'access_inbox',
      breadcrumb: { label: 'Inbox' }
    },
  },
  {
    path: '/notifications/:id',
    name: 'UserNotificationDetail',
    component: () => import('../pages/Notifications/Show.vue'),
    meta: {
      requiresAuth: true,
      permission: 'access_inbox',
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
      permission: 'manage_warehouse',
      breadcrumb: { label: 'Inventory' }
    },
  },
  {
    path: '/vendor/products/create',
    name: 'ProductCreate',
    component: VendorProductCreate,
    meta: {
      requiresAuth: true,
      permission: 'manage_warehouse',
      breadcrumb: [
        { label: 'Inventory', path: '/vendor/products' },
        { label: 'Add Product' }
      ]
    },
  },
  {
    path: '/vendor/products/:id/edit',
    name: 'VendorProductEdit',
    component: VendorProductEdit,
    meta: {
      requiresAuth: true,
      permission: 'manage_warehouse'
    },
  },
  {
    path: '/vendor/rfqs',
    name: 'VendorRfqs',
    component: VendorRfqs,
    meta: {
      requiresAuth: true,
      permission: 'access_vendor',
      breadcrumb: [{ label: 'Incoming Requests' }]
    },
  },

  // Admin Specific Routes
  {
    path: '/admin/products',
    name: 'AdminProducts',
    component: AdminProducts,
    meta: {
      requiresAuth: true,
      permission: 'moderate_products',
      breadcrumb: { label: 'Product Moderation' }
    },
  },
  {
    path: '/admin/users',
    name: 'AdminUsers',
    component: AdminUsers,
    meta: {
      requiresAuth: true,
      permission: 'manage_users',
      breadcrumb: { label: 'Identity Nexus' }
    },
  },
  {
    path: '/admin/notifications/create',
    name: 'AdminCreateNotification',
    component: () => import('../pages/admin/Notifications/Create.vue'),
    meta: {
      requiresAuth: true,
      permission: 'broadcast_notifications',
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
      permission: 'broadcast_notifications',
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

  // Reporting & Analytics Routes
  {
    path: '/reports/sales',
    name: 'ReportsSales',
    component: () => import('../pages/Reports/Index.vue'),
    props: { type: 'sales' },
    meta: {
      requiresAuth: true,
      permission: 'view_reports_sales',
      breadcrumb: { label: 'Sales Reports' }
    }
  },
  {
    path: '/reports/products',
    name: 'ReportsProducts',
    component: () => import('../pages/Reports/Index.vue'),
    props: { type: 'products' },
    meta: {
      requiresAuth: true,
      permission: 'view_reports_products',
      breadcrumb: { label: 'Product Reports' }
    }
  },
  {
    path: '/reports/financial',
    name: 'ReportsFinancial',
    component: () => import('../pages/Reports/Index.vue'),
    props: { type: 'financial' },
    meta: {
      requiresAuth: true,
      permission: 'view_reports_financial',
      breadcrumb: { label: 'Financial Reports' }
    }
  },
  {
    path: '/reports/market',
    name: 'ReportsMarket',
    component: () => import('../pages/Reports/Index.vue'),
    props: { type: 'market' },
    meta: {
      requiresAuth: true,
      permission: 'view_reports_market',
      breadcrumb: { label: 'Market Reports' }
    }
  },
  {
    path: '/reports/users',
    name: 'ReportsUser',
    component: () => import('../pages/Reports/Index.vue'),
    props: { type: 'users' },
    meta: {
      requiresAuth: true,
      permission: 'view_reports_users',
      breadcrumb: { label: 'User Reports' }
    }
  },
  {
    path: '/reports/compliance',
    name: 'ReportsCompliance',
    component: () => import('../pages/Reports/Index.vue'),
    props: { type: 'compliance' },
    meta: {
      requiresAuth: true,
      permission: 'view_reports_compliance',
      breadcrumb: { label: 'Compliance Reports' }
    }
  },
  {
    path: '/reports/operations',
    name: 'ReportsOperations',
    component: () => import('../pages/Reports/Index.vue'),
    props: { type: 'operations' },
    meta: {
      requiresAuth: true,
      permission: 'view_reports_trade_requests',
      breadcrumb: { label: 'Operations Reports' }
    }
  },
  {
    path: '/reports/system',
    name: 'ReportsSystem',
    component: () => import('../pages/Reports/Index.vue'),
    props: { type: 'system' },
    meta: {
      requiresAuth: true,
      permission: 'view_reports_system',
      breadcrumb: { label: 'System Health' }
    }
  },

  // Super Admin Specific Routes
  {
    path: '/super-admin',
    name: 'SuperAdminDashboard',
    component: SuperAdminDashboard,
    meta: {
      requiresAuth: true,
      permission: 'access_super_admin',
      breadcrumb: { label: 'Governance Nexus' }
    },
  },
  {
    path: '/super-admin/categories',
    name: 'SuperAdminCategories',
    component: SuperAdminCategories,
    meta: {
      requiresAuth: true,
      permission: 'manage_categories',
      breadcrumb: [
        { label: 'Governance Nexus', path: '/super-admin' },
        { label: 'Asset Classifications' }
      ]
    },
  },
  {
    path: '/super-admin/roles',
    name: 'RoleManagement',
    component: RoleManagement,
    meta: {
      requiresAuth: true,
      permission: 'manage_roles',
      breadcrumb: [
        { label: 'Governance Nexus', path: '/super-admin' },
        { label: 'Role Ecosystem' }
      ]
    },
  },

  {
    path: '/',
    component: Home,
  },

  // Global Network Hub
  {
    path: '/network/:type',
    name: 'Network',
    component: () => import('../pages/Network/Index.vue'),
    meta: {
      requiresAuth: true,
      breadcrumb: { label: 'Global Network' }
    },
    props: true
  },
];

const router = createRouter({
  history: createWebHistory(import.meta.env.BASE_URL),
  routes,
});

import { useAuthStore } from '../stores/auth';

// Navigation guard with permission checking
router.beforeEach(async (to, from, next) => {
  const authStore = useAuthStore();

  // Restore session if token exists but user is missing (page refresh)
  if (localStorage.getItem('token') && !authStore.user) {
    await authStore.checkAuth();
  }

  const token = localStorage.getItem('token');
  const requiresAuth = to.matched.some(record => record.meta.requiresAuth);

  // Check authentication
  if (requiresAuth && !token) {
    return next('/login');
  }

  if ((to.path === '/login' || to.path === '/register') && token) {
    return next('/dashboard');
  }

  // Check permission requirements
  if (to.meta.permission && authStore.user) {
    const hasPermission = authStore.user.permissions_list?.includes(to.meta.permission);
    const isSuperAdmin = authStore.user.roles?.some(r => r.slug === 'super_admin');

    if (!hasPermission && !isSuperAdmin) {
      console.warn(`Access denied: Missing permission '${to.meta.permission}'`);
      return next('/dashboard'); // Redirect to dashboard if no permission
    }
  }

  next();
});

export default router;
