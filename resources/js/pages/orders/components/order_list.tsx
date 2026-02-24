import React from 'react';

type Order = {
    id: number;
    name: string;
    status: string;
    created_at: string;
};

type OrderListProps = {
    orders: Order[];
};

export default function OrderList({ orders }: OrderListProps) {
    const hasActiveOrders = orders && orders.length > 0;

    if (!hasActiveOrders) {
        return (
            <div>
                <div>Brak zamówień.</div>
                <a href="/orders/create">
                    <button>Wprowadź zamówienie</button>
                </a>
            </div>
        );
    }

    return (
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nazwa</th>
                    <th>Status</th>
                    <th>Data utworzenia</th>
                </tr>
            </thead>
            <tbody>
                {orders.map((order) => (
                    <tr key={order.id}>
                        <td>{order.id}</td>
                        <td>{order.name}</td>
                        <td>{order.status}</td>
                        <td>{order.created_at}</td>
                    </tr>
                ))}
            </tbody>
        </table>
    );
}
