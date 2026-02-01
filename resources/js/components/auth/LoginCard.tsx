import React, { useState } from 'react';
import { router } from '@inertiajs/react';
import '../../../css/auth.css';

export default function LoginCard() {
    const [email, setEmail] = useState('');
    const [password, setPassword] = useState('');
    const [loading, setLoading] = useState(false);
    const [logoSrc, setLogoSrc] = useState('/storage/image/logo/Logo-PANS-2022-pelne-2-scaled.jpg');

    const submit = (e: React.FormEvent) => {
        e.preventDefault();
        setLoading(true);
        router.post('/login', { email, password }, {
            onFinish: () => setLoading(false)
        });
    };

    const onLogoError = () => {
        // fallback to a public images path if storage link isn't present
        if (logoSrc !== '/images/Logo-PANS-2022-pelne-2-scaled.jpg') {
            setLogoSrc('/images/Logo-PANS-2022-pelne-2-scaled.jpg');
        }
    };

    return (
        <div className="auth-root">
            <div className="auth-header">
                <img src={logoSrc} alt="Logo PANS" className="auth-logo-top" onError={onLogoError} />
                <h1 className="auth-h1-top">Praca magisterska</h1>
            </div>

            <div className="rings" aria-hidden>
                <span className="ring ring-1" />
                <span className="ring ring-2" />
                <span className="ring ring-3" />
            </div>

            <div className="auth-card">
                <h2 className="auth-title">Logowanie</h2>

                <form onSubmit={submit} className="auth-form">
                    <input
                        type="email"
                        placeholder="Email"
                        value={email}
                        onChange={e => setEmail(e.target.value)}
                        className="auth-input"
                        required
                    />
                    <input
                        type="password"
                        placeholder="Hasło"
                        value={password}
                        onChange={e => setPassword(e.target.value)}
                        className="auth-input"
                        required
                    />

                    <button type="submit" className="auth-submit" disabled={loading}>
                        {loading ? 'Logowanie...' : 'Zaloguj się'}
                    </button>
                </form>

                <div className="auth-footer split">
                    <a href="/password/reset" className="auth-link">Zapomniałeś hasła?</a>
                    <a href="/register" className="auth-link">Zarejestruj się</a>
                </div>
            </div>
        </div>
    );
}
