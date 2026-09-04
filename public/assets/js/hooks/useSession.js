import { authService } from '../services/authService.js';
import { setCsrfToken } from '../services/httpClient.js';

export function useSession() {
    let user = null;

    async function load() {
        const session = await authService.session();
        setCsrfToken(session.csrf_token);
        user = session.user;

        return user;
    }

    function current() {
        return user;
    }

    return { load, current };
}
