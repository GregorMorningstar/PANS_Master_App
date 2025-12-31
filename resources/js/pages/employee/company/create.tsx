import React, { useState } from 'react';
import { useForm, router } from '@inertiajs/react';
import EmployeeLayout from '@/layouts/EmployeeLayout';

interface CompanyData {
    ok: boolean;
    nip?: string;
    name?: string;
    address?: string;
    street?: string;
    zip?: string;
    city?: string;
    error?: string;
}

export default function EmployeeCompanyCreate() {
    const [loading, setLoading] = useState(false);
    const [nipError, setNipError] = useState<string>('');

    const breadcrumbs = [
        { label: 'Pracownik', href: '/employee' },
        { label: 'Firma', href: '/employee/company' },
        { label: 'Dodaj', href: '/employee/company/create' }
    ];

    const { data, setData, post, processing, errors, reset } = useForm({
        nip: '',
        name: '',
        address: '',
        street: '',
        zip: '',
        city: '',
    });

    const fetchCompanyData = async (nip: string) => {
        if (nip.length < 10) return;

        setLoading(true);
        setNipError('');

        try {
            const response = await fetch(`/api/company/nip-lookup/${nip}`, {
                method: 'GET',
                headers: {
                    'Accept': 'application/json',
                },
            });

            const result: CompanyData = await response.json();

            if (result.ok && result.name) {
                setData({
                    ...data,
                    nip: result.nip || nip,
                    name: result.name,
                    address: result.address || '',
                    street: result.street || '',
                    zip: result.zip || '',
                    city: result.city || '',
                });
            } else {
                // W przypadku braku danych z API, ustaw NIP na sztywno i pozwól na ręczne wypełnienie
                setData({
                    ...data,
                    nip: nip, // NIP na sztywno
                });
                setNipError(result.error || 'Nie znaleziono danych dla podanego NIP. Możesz wypełnić dane ręcznie.');
            }
        } catch (error) {
            // W przypadku błędu połączenia, ustaw NIP na sztywno i pozwól na ręczne wypełnienie
            setData({
                ...data,
                nip: nip, // NIP na sztywno
            });
            setNipError('Błąd podczas pobierania danych. Możesz wypełnić dane ręcznie.');
            console.error('Error fetching company data:', error);
        } finally {
            setLoading(false);
        }
    };

    const handleNipChange = (e: React.ChangeEvent<HTMLInputElement>) => {
        const nipValue = e.target.value.replace(/\D/g, ''); // Tylko cyfry
        setData('nip', nipValue);
        setNipError('');

        // Automatyczne pobieranie danych gdy NIP ma 10 cyfr
        if (nipValue.length === 10) {
            fetchCompanyData(nipValue);
        }
    };

    const formatNip = (nip: string) => {
        return nip.replace(/(\d{3})(\d{3})(\d{2})(\d{2})/, '$1-$2-$3-$4');
    };

    const submit = (e: React.FormEvent) => {
        e.preventDefault();
        post('/employee/company', {
            onSuccess: () => {
                reset();
                alert('Firma została pomyślnie dodana!');
            },
            onError: (errors) => {
                console.log('Błędy walidacji:', errors);
            }
        });
    };

    return (
        <EmployeeLayout breadcrumbs={breadcrumbs} title="Dodaj firmę">
            <div className="max-w-3xl mx-auto p-4 sm:p-6 bg-white rounded shadow mt-4">
                <header className="mb-4">
                    <h1 className="text-xl font-semibold text-gray-800">Dodaj nową firmę</h1>
                    <p className="text-sm text-gray-500">Wpisz NIP firmy, a dane zostaną pobrane automatycznie.</p>
                </header>

                <form onSubmit={submit} className="space-y-4">
                    <div>
                        <label className="block text-sm font-medium text-gray-700">
                            NIP <span className="text-red-500">*</span>
                        </label>
                        <div className="relative">
                            <input
                                type="text"
                                value={data.nip}
                                onChange={handleNipChange}
                                placeholder="Wpisz 10-cyfrowy NIP"
                                maxLength={10}
                                className={`mt-1 block w-full rounded-md border px-3 py-2 text-sm shadow-sm focus:outline-none focus:ring-2 focus:ring-sky-500 ${
                                    errors.nip || nipError ? 'border-red-300' : 'border-gray-200'
                                }`}
                            />
                            {loading && (
                                <div className="absolute right-3 top-1/2 transform -translate-y-1/2">
                                    <div className="animate-spin rounded-full h-4 w-4 border-b-2 border-sky-600"></div>
                                </div>
                            )}
                        </div>
                        {data.nip && (
                            <div className="text-xs text-gray-500 mt-1">
                                Formatowany: {formatNip(data.nip)}
                            </div>
                        )}
                        {errors.nip && <div className="text-xs text-red-600 mt-1">{errors.nip}</div>}
                        {nipError && <div className="text-xs text-red-600 mt-1">{nipError}</div>}
                    </div>

                    <div>
                        <label className="block text-sm font-medium text-gray-700">
                            Nazwa firmy <span className="text-red-500">*</span>
                        </label>
                        <input
                            type="text"
                            value={data.name}
                            onChange={(e) => setData('name', e.target.value)}
                            className={`mt-1 block w-full rounded-md border px-3 py-2 text-sm shadow-sm focus:outline-none focus:ring-2 focus:ring-sky-500 ${
                                errors.name ? 'border-red-300' : 'border-gray-200'
                            }`}
                        />
                        {errors.name && <div className="text-xs text-red-600 mt-1">{errors.name}</div>}
                    </div>

                    <div>
                        <label className="block text-sm font-medium text-gray-700">Pełny adres</label>
                        <textarea
                            value={data.address}
                            onChange={(e) => setData('address', e.target.value)}
                            rows={2}
                            className={`mt-1 block w-full rounded-md border px-3 py-2 text-sm shadow-sm focus:outline-none focus:ring-2 focus:ring-sky-500 ${
                                errors.address ? 'border-red-300' : 'border-gray-200'
                            }`}
                        />
                        {errors.address && <div className="text-xs text-red-600 mt-1">{errors.address}</div>}
                    </div>

                    <div className="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div>
                            <label className="block text-sm font-medium text-gray-700">Ulica</label>
                            <input
                                type="text"
                                value={data.street}
                                onChange={(e) => setData('street', e.target.value)}
                                className={`mt-1 block w-full rounded-md border px-3 py-2 text-sm shadow-sm focus:outline-none focus:ring-2 focus:ring-sky-500 ${
                                    errors.street ? 'border-red-300' : 'border-gray-200'
                                }`}
                            />
                            {errors.street && <div className="text-xs text-red-600 mt-1">{errors.street}</div>}
                        </div>

                        <div>
                            <label className="block text-sm font-medium text-gray-700">Kod pocztowy</label>
                            <input
                                type="text"
                                value={data.zip}
                                onChange={(e) => setData('zip', e.target.value)}
                                placeholder="00-000"
                                className={`mt-1 block w-full rounded-md border px-3 py-2 text-sm shadow-sm focus:outline-none focus:ring-2 focus:ring-sky-500 ${
                                    errors.zip ? 'border-red-300' : 'border-gray-200'
                                }`}
                            />
                            {errors.zip && <div className="text-xs text-red-600 mt-1">{errors.zip}</div>}
                        </div>

                        <div>
                            <label className="block text-sm font-medium text-gray-700">Miasto</label>
                            <input
                                type="text"
                                value={data.city}
                                onChange={(e) => setData('city', e.target.value)}
                                className={`mt-1 block w-full rounded-md border px-3 py-2 text-sm shadow-sm focus:outline-none focus:ring-2 focus:ring-sky-500 ${
                                    errors.city ? 'border-red-300' : 'border-gray-200'
                                }`}
                            />
                            {errors.city && <div className="text-xs text-red-600 mt-1">{errors.city}</div>}
                        </div>
                    </div>

                    <div className="flex items-center gap-3 pt-4">
                        <button
                            type="submit"
                            disabled={processing || loading}
                            className="px-4 py-2 bg-sky-600 text-white rounded hover:bg-sky-700 disabled:opacity-60"
                        >
                            {processing ? 'Zapisywanie...' : 'Zapisz firmę'}
                        </button>

                        <button
                            type="button"
                            onClick={() => router.get('/employee/company')}
                            className="px-3 py-2 border rounded text-sm text-gray-700 hover:bg-gray-50"
                        >
                            Anuluj
                        </button>
                    </div>
                </form>
            </div>
        </EmployeeLayout>
    );
}
