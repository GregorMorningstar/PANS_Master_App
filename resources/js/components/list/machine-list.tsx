import React, { useState } from "react";
import { usePage, router } from "@inertiajs/react";
import ModeratorOperationMachineNav from "../menu/moderator-operation-machine-nav";
import MachineCardSimple from "../card/machineCardSimple";

type Props = {
    allOperations?: any[] | null;
};

export default function MachineList({ allOperations }: Props) {
    const page = usePage<any>();
    const authUser = page.props?.auth?.user ?? null;

    const items = Array.isArray(allOperations) ? allOperations : [];

    const handleEditOperation = (operation: any) => {
        router.visit(`/moderator/machines/operations/${operation.id}/edit`);
    };

    const handleDeleteOperation = (operation: any) => {
        if (confirm(`Czy na pewno usunąć operację "${operation.name}"?`)) {
            router.delete(`/moderator/machines/operations/${operation.id}`);
        }
    };

    const handleShowAllOperations = (machineId: number) => {
        // otwórz w nowym oknie/karcie
        window.open(`/moderator/machines/${machineId}/operations`, '_blank');
    };

    return (
        <div className="space-y-6">
            <ModeratorOperationMachineNav />

            <h1 className="text-2xl font-semibold">Lista urządzeń</h1>

            <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                {items.length > 0 ? (
                    items.map((machine: any) => {
                        // pobierz operacje z tabeli operationmachines (relacja many-to-many)
                        const allOps = Array.isArray(machine.operationmachines) ? machine.operationmachines :
                                      Array.isArray(machine.operations) ? machine.operations : [];
                        const firstOps = allOps.slice(0, 5);
                        const restCount = Math.max(0, allOps.length - firstOps.length);
                        const id = machine.id ?? machine.barcode ?? Math.random();

                        return (
                            <div key={id} className="bg-white border border-gray-100 rounded-lg shadow-sm p-4">
                                <MachineCardSimple machine={machine} user={authUser} />

                                {/* operacje pod kartą urządzenia */}
                                <div className="mt-4 border-t pt-4">
                                    <div className="flex items-center justify-between mb-3">
                                        <div className="text-sm font-medium text-gray-700">
                                            Operacje ({allOps.length})
                                        </div>
                                        <button
                                            onClick={() => router.visit(`/moderator/machines/${machine.id}/operations/create`)}
                                            className="text-xs bg-blue-600 text-white px-2 py-1 rounded hover:bg-blue-700"
                                        >
                                            Dodaj
                                        </button>
                                    </div>

                                    <div className="space-y-2">
                                        {firstOps.map((op: any, idx: number) => {
                                            const opName = op.name ?? op.operation_name ?? "—";
                                            const opNorm = op.norm ?? op.standard ?? "—";
                                            const opBarcode = op.barcode ?? op.operation_barcode ?? "—";
                                            const changeover = op.changeover_time ?? op.changeoverTime ?? op.time ?? "—";

                                            return (
                                                <div key={op.id ?? `${id}-op-${idx}`} className="flex items-start justify-between text-sm bg-gray-50 p-2 rounded">
                                                    <div className="flex-1">
                                                        <div className="font-medium text-gray-800">{opName}</div>
                                                        <div className="text-xs text-gray-500">
                                                            Norma: {opNorm} • Barcode: {opBarcode}
                                                        </div>
                                                        <div className="text-xs text-gray-500">
                                                            Przezbrojenie: <span className="font-medium text-gray-800">{changeover}</span>
                                                        </div>
                                                    </div>

                                                    <div className="flex gap-1 ml-2">
                                                        <button
                                                            onClick={() => handleEditOperation(op)}
                                                            className="text-xs text-blue-600 hover:text-blue-800 px-2 py-1 hover:bg-blue-50 rounded"
                                                            title="Edytuj operację"
                                                        >
                                                            ✏️
                                                        </button>
                                                        <button
                                                            onClick={() => handleDeleteOperation(op)}
                                                            className="text-xs text-red-600 hover:text-red-800 px-2 py-1 hover:bg-red-50 rounded"
                                                            title="Usuń operację"
                                                        >
                                                            🗑️
                                                        </button>
                                                    </div>
                                                </div>
                                            );
                                        })}

                                        {restCount > 0 && (
                                            <div className="mt-2">
                                                <button
                                                    onClick={() => handleShowAllOperations(machine.id)}
                                                    className="text-sm text-blue-600 hover:underline focus:outline-none"
                                                >
                                                    Zobacz wszystkie operacje ({allOps.length}) →
                                                </button>
                                            </div>
                                        )}

                                        {allOps.length === 0 && (
                                            <div className="text-sm text-gray-500 italic">
                                                Brak operacji dla tego urządzenia
                                            </div>
                                        )}
                                    </div>
                                </div>
                            </div>
                        );
                    })
                ) : (
                    <div className="col-span-full text-gray-600">Brak urządzeń.</div>
                )}
            </div>
        </div>
    );
}
