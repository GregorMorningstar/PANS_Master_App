import React, { useState } from "react";
import AvailableMaterials from "./AvailableMaterials";
import InProductionMaterials from "./InProductionMaterials";
import ToAddMaterials from "./ToAddMaterials";
import MaterialsHistory from "./MaterialsHistory";

type TabKey = "available" | "in_production" | "to_add" | "history";

export default function MaterialTab() {
    const [tab, setTab] = useState<TabKey>("available");

    const renderContent = () => {
        switch (tab) {
            case "available":
                return <AvailableMaterials />;
            case "in_production":
                return <InProductionMaterials />;
            case "to_add":
                return <ToAddMaterials />;
            case "history":
                return <MaterialsHistory />;
            default:
                return null;
        }
    };

    return (
        <div className="bg-white rounded shadow p-4">
            <div className="flex space-x-2 mb-4">
                <button
                    onClick={() => setTab("available")}
                    className={`px-3 py-1 rounded ${tab === "available" ? "bg-blue-600 text-white" : "bg-gray-100"}`}
                >
                    Materiały dostępne
                </button>
                <button
                    onClick={() => setTab("in_production")}
                    className={`px-3 py-1 rounded ${tab === "in_production" ? "bg-blue-600 text-white" : "bg-gray-100"}`}
                >
                    W produkcji
                </button>
                <button
                    onClick={() => setTab("to_add")}
                    className={`px-3 py-1 rounded ${tab === "to_add" ? "bg-blue-600 text-white" : "bg-gray-100"}`}
                >
                    Do dodania
                </button>
                <button
                    onClick={() => setTab("history")}
                    className={`px-3 py-1 rounded ${tab === "history" ? "bg-blue-600 text-white" : "bg-gray-100"}`}
                >
                    Historia
                </button>
            </div>

            <div className="text-sm text-gray-700">{renderContent()}</div>
        </div>
    );
}
