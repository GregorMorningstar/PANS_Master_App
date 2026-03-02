import { usePage } from '@inertiajs/react';
import EmployeeLayout from '@/layouts/EmployeeLayout';
import ModeratorLayout from '@/layouts/ModeratorLayout';
import ModeratorNavMenuOrderItemsAdd from './component/moderator_nav_menu._order_items_add';
import OrderDetails from './component/order-detalis';
import OrderHeaderItem from './component/order-header-item';

type Order = {
    customer_name?: string;
    received_at?: string;
    barcode?: string;
    id: number;
    planned_start_at?: string;
    planned_end_at?: string;
    // add other properties as needed
};

export default function OrderShow() {
    const { props } = usePage<{ order: Order, auth: { user: { role: string } } }>();
    const order = props.order;
    const userRole = String(props.auth?.user?.role || '').toLowerCase();


    const breadcrumbsModerator = [
        { label: 'Moderator', href: '/moderator'},
        { label: 'Zamówienia', href: '/moderator/orders' },
        { label: 'Szczegóły zamówienia', href: `/moderator/orders/${order?.id}` },
    ];

    const breadcrumbsEmployee = [
        { label: 'Pracownik', href: '/employee'},
        { label: 'Zamówienia', href: '/employee/orders' },
        { label: 'Szczegóły zamówienia', href: `/employee/orders/${order?.id}` },
    ];
              <OrderDetails order={order} />


    if (userRole === 'moderator') return (
        <ModeratorLayout breadcrumbs={breadcrumbsModerator} title="Szczegóły zamówienia">
            <ModeratorNavMenuOrderItemsAdd />
            <OrderHeaderItem order={order} />
            <OrderDetails order={order} />
        </ModeratorLayout>
    );
    if (userRole === 'employee') return (
        <EmployeeLayout breadcrumbs={breadcrumbsEmployee} title="Szczegóły zamówienia">
            <OrderHeaderItem order={order} />
        </EmployeeLayout>
    );
    // fallback
    return <OrderDetails order={order} />;
}
