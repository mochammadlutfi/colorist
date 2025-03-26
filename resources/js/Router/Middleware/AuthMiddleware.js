import { useAuthStore } from '@/Stores/auth.js';
import { useAbility } from '@casl/vue';

export default async (to, from, next) => {
  const auth = useAuthStore();
  const { can } = useAbility(); // CASL ability

  // Daftar route yang bisa diakses tanpa login
  const exceptionalRoutes = ['login', 'register', 'forgot-password'];
  const isGoingExceptionalRoutes = exceptionalRoutes.includes(to.name);

  /**
   * IF THE USER IS NOT LOGGED IN
   */
  if (!auth.isLoggedIn) {
    if (isGoingExceptionalRoutes) {
      next(); // User belum login, tetapi mengakses route yang diizinkan
      return;
    } else {
      next({ name: 'login' }); // Redirect ke login untuk route yang memerlukan login
      return;
    }
  }

  /**
   * IF THE USER IS LOGGED IN
   */
  if (auth.isLoggedIn && isGoingExceptionalRoutes) {
    next({ name: 'dashboard', query: { 'redirect-reason': 'already logged' } });
    return;
  }

  /**
   * FETCH USER DATA IF NOT ALREADY FETCHED
   */
  if (!auth.user) {
    try {
      await auth.getUser(); // Tunggu sampai fetch selesai
    } catch (error) {
      console.error('Failed to fetch user data:', error);
      // Jika fetch gagal, logout dan redirect ke login
      await auth.logout();
      next({ name: 'login' });
      return;
    }
  }

  /**
   * CHECK PERMISSION FOR THE ROUTE
   */
  if (to.meta.permission) {
    const [subject, action] = to.meta.permission.split('.');
    const hasPermission = can(action, subject);

    // Jika user tidak memiliki permission, redirect ke halaman 403 atau dashboard
    if (!hasPermission) {
      next({ name: 'forbidden' }); // Redirect ke halaman 403
      return;
    }
  }

  // Lanjutkan navigasi
  next();
};