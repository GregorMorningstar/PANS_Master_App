import ModeratorLayout from "@/layouts/ModeratorLayout";
import { useEffect } from "react";
import AvailableProductsSection from "./available-products-section";
    import OrderHeaderItem from "../component/order-header-item";
export default function CreateOneItems(props: { order: any, addedProducts: any[], availableProducts: any[] }) {
    useEffect(() => {
        console.log("Przekazany obiekt zamówienia:", props.order);
    }, [props.order]);

    const { order, addedProducts = [], availableProducts = [] } = props;

    return (
      <ModeratorLayout title="Dodaj pozycję do zamówienia">
            <OrderHeaderItem order={order} />
        <div className="flex flex-col md:flex-row gap-6 w-full">
          {/* Lewa sekcja: 65% szerokości - dodane produkty */}
          <div className="md:w-2/3 w-full">
            <h2 className="text-xl font-semibold mb-4">Dodane produkty</h2>
            <ul className="mb-6">
              {addedProducts.length === 0 ? (
                <li className="text-gray-500">Brak dodanych produktów</li>
              ) : (
                addedProducts.map((product, idx) => (
                  <li key={idx} className="mb-2 p-2 bg-blue-50 rounded">{product.name}</li>
                ))
              )}
            </ul>
            <select className="w-full p-2 border rounded">
              <option value="">Wybierz produkt do dodania</option>
              {availableProducts.map((product, idx) => (
                <option key={idx} value={product.id}>{product.name}</option>
              ))}
            </select>
          </div>
          {/* Prawa sekcja: 35% szerokości - dostępne produkty */}
          <div className="md:w-1/3 w-full">
            <AvailableProductsSection availableProducts={availableProducts} />
          </div>
        </div>
      </ModeratorLayout>
    );
}
