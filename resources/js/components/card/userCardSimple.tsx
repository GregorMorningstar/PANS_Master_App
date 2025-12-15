import React from 'react';
import Barcode from 'react-barcode';
import { Eye, FileText, BarChart2, BookOpen, Briefcase, MessageCircle } from 'lucide-react';

interface UserCardProps {
  user: {
    id: number | string;
    name?: string;
    barcode?: string;
    email?: string;
    phone?: string;
    department?: {
      id: number;
      name: string;
      barcode?: string;
    } | string;
    role?: string;
    [key: string]: any;
  };
  className?: string;
}

export default function UserCardSimple({ user, className = '' }: UserCardProps) {
  const dept = typeof user.department === 'object' && user.department?.name
    ? user.department.name
    : (typeof user.department === 'string' ? user.department : 'Magazyn');
  const isStaffRole = ['moderator', 'admin'].includes((user.role ?? '').toLowerCase());
  const code = user.barcode ?? String(user.id ?? '—');

  return (
    <article
      className={`flex flex-col w-full max-w-xs rounded-lg overflow-hidden bg-[#f6f5ef] border border-gray-200 shadow text-gray-800 ${className}`}
    >
      {/* Top: name + realistic barcode */}
      <div className="px-4 py-3 flex flex-col items-center" style={{ backgroundColor: '#f0efe6' }}>
        <h3 className="text-lg font-semibold tracking-wide text-gray-900">{user.name ?? '—'}</h3>

        <div
          className="mt-3 flex flex-col items-center justify-center select-all"
          style={{ width: '15vw', minWidth: 160, flex: '0 0 20%' }}
          aria-hidden
        >
          <div className="w-full h-full flex items-center justify-center">
            <Barcode
              value={code}
              format="CODE128"
              width={2}
              height={80}
              displayValue={true}
              background="transparent"
              renderer="svg"
              margin={0}
              lineColor="#0b1220"
              fontOptions="bold"
              font="monospace"
              textAlign="center"
              textPosition="bottom"
            />
          </div>
        </div>
      </div>

      {/* Body: user details */}
      <div className="flex-1 px-4 py-3 space-y-2" style={{ backgroundColor: 'transparent' }}>
        <div className="text-sm text-gray-700">
          <span className="font-medium text-gray-800">Dział:</span>{' '}
          <span className="ml-1">{dept}</span>
        </div>

        {user.email && (
          <div className="text-sm text-gray-700">
            <span className="font-medium text-gray-800">Email:</span>{' '}
            <span className="ml-1">{user.email}</span>
          </div>
        )}

        {user.phone && (
          <div className="text-sm text-gray-700">
            <span className="font-medium text-gray-800">Telefon:</span>{' '}
            <span className="ml-1">{user.phone}</span>
          </div>
        )}
      </div>

      {/* Footer: actions */}
      <div className="px-3 py-3 border-t border-gray-200 bg-transparent">
        <div className="flex items-center justify-between gap-2">
          <div className="flex items-center gap-2">
            <a
              href={`/moderator/users/${user.id}`}
              className="inline-flex items-center gap-2 px-3 py-1 rounded-md bg-gray-100 hover:bg-gray-200 transition text-sm text-gray-900"
              title="Podgląd"
            >
              <Eye className="h-4 w-4" /> Podgląd
            </a>

            <a
              href={`/moderator/users/${user.id}/files`}
              className="inline-flex items-center gap-2 px-3 py-1 rounded-md bg-gray-100 hover:bg-gray-200 transition text-sm text-gray-900"
              title="Pliki"
            >
              <FileText className="h-4 w-4" /> Pliki
            </a>

            <a
              href={`/moderator/users/${user.id}/performance`}
              className="inline-flex items-center gap-2 px-3 py-1 rounded-md bg-gray-100 hover:bg-gray-200 transition text-sm text-gray-900"
              title="Wydajność"
            >
              <BarChart2 className="h-4 w-4" /> Wydajność
            </a>
          </div>

          <div className="flex items-center gap-2">
            <button
              type="button"
              onClick={() => (window.location.href = `/moderator/users/${user.id}/chat`)}
              className="inline-flex items-center gap-2 px-3 py-1 rounded-md bg-amber-500 hover:bg-amber-600 transition text-sm text-white"
              title="Chat"
            >
              <MessageCircle className="h-4 w-4" /> Chat
            </button>

            {isStaffRole ? (
              <>
                <span className="inline-flex items-center gap-2 px-2 py-1 rounded-full bg-indigo-100 text-xs font-medium text-gray-900">
                  <BookOpen className="h-3.5 w-3.5" /> Edukacja
                </span>
                <span className="inline-flex items-center gap-2 px-2 py-1 rounded-full bg-emerald-100 text-xs font-medium text-gray-900">
                  <Briefcase className="h-3.5 w-3.5" /> Praca
                </span>
              </>
            ) : (
              <div />
            )}
          </div>
        </div>
      </div>
    </article>
  );
}
