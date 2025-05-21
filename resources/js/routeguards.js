import { router } from '@inertiajs/vue3'

router.on('before', (event) => {
    const isAuth = !!event.page.props.auth.user;
    const userRole = event.page.props.auth.user?.role || 'guest';

    // Redirect unauthorized users trying to access protected routes
    if (!isAuth && !event.page.component.startsWith('Auth/')) {
        event.visit(route('login'));
    }

    // Role-based redirection
    if (isAuth) {
        const rolePrefix = `/${userRole}`;
        if (!event.url.startsWith(rolePrefix)) {
            event.visit(route(`${userRole}.dashboard`));
        }
    }
});
