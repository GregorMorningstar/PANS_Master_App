import React from 'react';
import { usePage, useForm, Link } from '@inertiajs/react';
import EmployeeLayout from '@/layouts/EmployeeLayout';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select';
import { Checkbox } from '@/components/ui/checkbox';
import { ArrowLeft, MapPin, Home, Building } from 'lucide-react';
import { Alert, AlertDescription } from '@/components/ui/alert';

interface User {
    id: number;
    name: string;
    email: string;
    role: string;
}

interface FormData {
    type: 'home' | 'work' | 'other' | '';
    street: string;
    city: string;
    postal_code: string;
    country: string;
    is_primary: boolean;
}

export default function AddressCreate() {
    const { user } = usePage<{ user: User }>().props;

    const breadcrumbs = [
        { label: 'Panel Pracownika', href: '/employee/dashboard' },
        { label: 'Moje Adresy', href: '/employee/address' },
        { label: 'Dodaj adres' },
    ];

    const { data, setData, post, processing, errors } = useForm<FormData>({
        type: '',
        street: '',
        city: '',
        postal_code: '',
        country: 'Polska',
        is_primary: false,
    });

    const handleSubmit = (e: React.FormEvent) => {
        e.preventDefault();
        post('/employee/address/store', {
            onSuccess: () => {
                // Handle success - redirect to address list
            },
        });
    };

    const getAddressTypeIcon = (type: string) => {
        switch (type) {
            case 'home': return <Home className="w-4 h-4" />;
            case 'work': return <Building className="w-4 h-4" />;
            default: return <MapPin className="w-4 h-4" />;
        }
    };

    return (
        <EmployeeLayout title="Dodaj adres" breadcrumbs={breadcrumbs}>
            <div className="max-w-2xl mx-auto space-y-6 p-6">
                {/* Header */}
                <div className="flex items-center gap-4">
                    <Link href="/employee/address">
                        <Button variant="outline" size="sm">
                            <ArrowLeft className="w-4 h-4 mr-2" />
                            Powrót
                        </Button>
                    </Link>
                    <div>
                        <h1 className="text-3xl font-bold text-gray-900">Dodaj nowy adres</h1>
                        <p className="text-gray-600 mt-1">Wprowadź dane nowego adresu</p>
                    </div>
                </div>

                <form onSubmit={handleSubmit} className="space-y-6">
                    <Card>
                        <CardHeader>
                            <CardTitle className="flex items-center gap-2">
                                <MapPin className="w-5 h-5" />
                                Dane adresu
                            </CardTitle>
                        </CardHeader>
                        <CardContent className="space-y-4">
                            <div>
                                <Label htmlFor="type">Typ adresu</Label>
                                <Select
                                    value={data.type}
                                    onValueChange={(value) => setData('type', value as 'home' | 'work' | 'other')}
                                >
                                    <SelectTrigger className={errors.type ? 'border-red-500' : ''}>
                                        <SelectValue placeholder="Wybierz typ adresu" />
                                    </SelectTrigger>
                                    <SelectContent>
                                        <SelectItem value="home">
                                            <div className="flex items-center gap-2">
                                                <Home className="w-4 h-4" />
                                                <span>Domowy</span>
                                            </div>
                                        </SelectItem>
                                        <SelectItem value="work">
                                            <div className="flex items-center gap-2">
                                                <Building className="w-4 h-4" />
                                                <span>Służbowy</span>
                                            </div>
                                        </SelectItem>
                                        <SelectItem value="other">
                                            <div className="flex items-center gap-2">
                                                <MapPin className="w-4 h-4" />
                                                <span>Inny</span>
                                            </div>
                                        </SelectItem>
                                    </SelectContent>
                                </Select>
                                {errors.type && (
                                    <p className="text-sm text-red-600 mt-1">{errors.type}</p>
                                )}
                            </div>

                            <div>
                                <Label htmlFor="street">Ulica i numer</Label>
                                <Input
                                    id="street"
                                    type="text"
                                    value={data.street}
                                    onChange={(e) => setData('street', e.target.value)}
                                    placeholder="np. ul. Kowalska 15/2"
                                    className={errors.street ? 'border-red-500' : ''}
                                />
                                {errors.street && (
                                    <p className="text-sm text-red-600 mt-1">{errors.street}</p>
                                )}
                            </div>

                            <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <Label htmlFor="postal_code">Kod pocztowy</Label>
                                    <Input
                                        id="postal_code"
                                        type="text"
                                        value={data.postal_code}
                                        onChange={(e) => setData('postal_code', e.target.value)}
                                        placeholder="00-000"
                                        maxLength={6}
                                        className={errors.postal_code ? 'border-red-500' : ''}
                                    />
                                    {errors.postal_code && (
                                        <p className="text-sm text-red-600 mt-1">{errors.postal_code}</p>
                                    )}
                                </div>

                                <div>
                                    <Label htmlFor="city">Miasto</Label>
                                    <Input
                                        id="city"
                                        type="text"
                                        value={data.city}
                                        onChange={(e) => setData('city', e.target.value)}
                                        placeholder="np. Warszawa"
                                        className={errors.city ? 'border-red-500' : ''}
                                    />
                                    {errors.city && (
                                        <p className="text-sm text-red-600 mt-1">{errors.city}</p>
                                    )}
                                </div>
                            </div>

                            <div>
                                <Label htmlFor="country">Kraj</Label>
                                <Input
                                    id="country"
                                    type="text"
                                    value={data.country}
                                    onChange={(e) => setData('country', e.target.value)}
                                    placeholder="np. Polska"
                                    className={errors.country ? 'border-red-500' : ''}
                                />
                                {errors.country && (
                                    <p className="text-sm text-red-600 mt-1">{errors.country}</p>
                                )}
                            </div>

                            <div className="flex items-center space-x-2">
                                <Checkbox
                                    id="is_primary"
                                    checked={data.is_primary}
                                    onCheckedChange={(checked) => setData('is_primary', !!checked)}
                                />
                                <Label htmlFor="is_primary" className="text-sm font-normal">
                                    Ustaw jako adres główny
                                </Label>
                            </div>
                        </CardContent>
                    </Card>

                    {/* Submit Buttons */}
                    <Card>
                        <CardContent className="pt-6">
                            <div className="flex justify-end gap-4">
                                <Link href="/employee/address">
                                    <Button variant="outline" type="button" disabled={processing}>
                                        Anuluj
                                    </Button>
                                </Link>
                                <Button type="submit" disabled={processing}>
                                    {processing ? 'Zapisywanie...' : 'Dodaj adres'}
                                </Button>
                            </div>
                        </CardContent>
                    </Card>
                </form>
            </div>
        </EmployeeLayout>
    );
}
