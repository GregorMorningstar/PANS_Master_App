import React, { JSX, useState } from "react";
import { router } from "@inertiajs/react";
import { Button } from "@/components/ui/button";
import { Card, CardContent, CardHeader, CardTitle } from "@/components/ui/card";
import { Badge } from "@/components/ui/badge";
import { Edit, Trash2, Check, X, FileText } from "lucide-react";

export default function EducationDetailsCard({ education, auth }: any): JSX.Element | null {
  const [isLoading, setIsLoading] = useState(false);
  if (!education) return null;

  const authUser = auth?.user ?? { id: 0, role: null };
  const isOwner = authUser.id === (education.user_profile?.user_id ?? education.user_profile_id);
  const isAdmin = ["admin", "moderator"].includes(String(authUser.role ?? "").toLowerCase());
  const isPending = (education.status ?? "pending") === "pending";

  const statusVariant = (color: string | undefined) => {
    switch (color) {
      case "green":
        return "secondary";
      case "red":
        return "destructive";
      case "gray":
        return "outline";
      default:
        return "default";
    }
  };

  const handleEdit = () => router.visit(`/employee/education/${education.id}/edit`);
  const handleDelete = () => {
    if (!confirm("Czy na pewno chcesz usunąć ten certyfikat?")) return;
    setIsLoading(true);
    router.delete(`/employee/education/${education.id}`, { onFinish: () => setIsLoading(false) });
  };
  const handleStatusChange = (status: "approved" | "rejected") => {
    if (!confirm(status === "approved" ? "Zatwierdzić certyfikat?" : "Odrzucić certyfikat?")) return;
    setIsLoading(true);
    router.put(`/admin/education/${education.id}/status`, { status }, { onFinish: () => setIsLoading(false) });
  };

  return (
    <Card>
      <CardHeader>
        <div className="flex items-start justify-between">
          <div>
            <CardTitle className="text-lg">Wykształcenie</CardTitle>
            <p className="text-sm text-muted-foreground mt-1">ID: {education.id}</p>
          </div>
          <Badge variant={statusVariant(education.status_color)}>{education.status_label ?? education.status}</Badge>
        </div>
      </CardHeader>

      <CardContent className="space-y-4">
        <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
          <div>
            <div className="mb-3">
              <div className="text-sm font-medium text-gray-700">Szkoła</div>
              <div className="text-sm">{education.school_name}</div>
            </div>

            {education.school_address && (
              <div className="mb-3">
                <div className="text-sm font-medium text-gray-700">Adres</div>
                <div className="text-sm">{education.school_address}</div>
              </div>
            )}

            <div className="mb-3">
              <div className="text-sm font-medium text-gray-700">Poziom</div>
              <div className="text-sm">{education.education_level}</div>
            </div>
          </div>

          <div>
            {education.field_of_study && (
              <div className="mb-3">
                <div className="text-sm font-medium text-gray-700">Kierunek</div>
                <div className="text-sm">{education.field_of_study}</div>
              </div>
            )}

            <div className="mb-3">
              <div className="text-sm font-medium text-gray-700">Okres</div>
              <div className="text-sm">{education.start_year} - {education.end_year ?? "obecnie"}</div>
            </div>

            {education.certificate_path && (
              <div className="mb-3">
                <div className="text-sm font-medium text-gray-700">Certyfikat</div>
                <div className="mt-1 flex items-center gap-3">
                  <a href={`/storage/${education.certificate_path}`} target="_blank" rel="noreferrer" className="text-sm text-blue-600 underline flex items-center gap-2">
                    <FileText className="w-4 h-4" /> {String(education.certificate_path).split("/").pop()}
                  </a>
                </div>
              </div>
            )}
          </div>
        </div>

        <div className="border-t pt-4 flex flex-wrap gap-2">
          {isOwner && isPending && (
            <>
              <Button onClick={handleEdit} disabled={isLoading} variant="outline" size="sm">
                <Edit className="w-4 h-4 mr-2" /> Edytuj
              </Button>
              <Button onClick={handleDelete} disabled={isLoading} variant="destructive" size="sm">
                <Trash2 className="w-4 h-4 mr-2" /> Usuń
              </Button>
            </>
          )}

          {isAdmin && isPending && (
            <>
              <Button onClick={() => handleStatusChange("approved")} disabled={isLoading} variant="default" size="sm" className="bg-green-600 hover:bg-green-700">
                <Check className="w-4 h-4 mr-2" /> Zatwierdź
              </Button>
              <Button onClick={() => handleStatusChange("rejected")} disabled={isLoading} variant="destructive" size="sm">
                <X className="w-4 h-4 mr-2" /> Odrzuć
              </Button>
            </>
          )}
        </div>
      </CardContent>
    </Card>
  );
}
