import EmployeeLayout from "@/layouts/EmployeeLayout";
import FullCalendar from '@fullcalendar/react';
import dayGridPlugin from '@fullcalendar/daygrid';
import timeGridPlugin from '@fullcalendar/timegrid';
import interactionPlugin from '@fullcalendar/interaction';
import plLocale from '@fullcalendar/core/locales/pl';
import { useState, useEffect, useRef } from 'react';
import { usePage } from '@inertiajs/react';
import Event from '@/pages/employee/calendar/event';

export default function EmployeeCalendar() {
    const { props: pageProps } = usePage<any>();
    const userLeaves = pageProps?.userleavesById ?? [];

    // Mapa kolorów dla różnych typów urlopów
    const LEAVE_TYPE_COLORS: Record<string, string> = {
        annual: '#3b82f6',        // niebieski
        sick: '#ef4444',          // czerwony
        unpaid: '#6b7280',        // szary
        parental: '#8b5cf6',      // fioletowy
        compassionate: '#000000ff', // żółty
        on_demand: '#10b981',     // zielony
    };

    // Mapa nazw typów urlopów
    const LEAVE_TYPE_LABELS: Record<string, string> = {
        annual: 'Urlop wypoczynkowy',
        sick: 'Urlop zdrowotny',
        unpaid: 'Urlop bezpłatny',
        parental: 'Urlop rodzicielski',
        compassionate: 'Urlop okolicznościowy',
        on_demand: 'Urlop na żądanie',
    };

    const mapLeavesToEvents = (leaves: any[]) => leaves.map(l => {
        const leaveType = l.type || l.leave_type || l.type_label || 'annual';
        const typeLabel = LEAVE_TYPE_LABELS[leaveType] || 'Urlop';
        const description = l.description || l.reason || '';

        // Tworzenie tytułu z typem urlopu i opisem
        let title = typeLabel;
        if (description && description.trim()) {
            title += `: ${description}`;
        }

        return {
            id: String(l.id),
            title: title,
            start: l.start_date ?? l.start,
            end: l.end_date ?? l.end,
            allDay: true,
            backgroundColor: LEAVE_TYPE_COLORS[leaveType] || '#8b5cf6',
            borderColor: LEAVE_TYPE_COLORS[leaveType] || '#8b5cf6',
            textColor: '#ffffff',
            extendedProps: {
                description: description,
                type_label: leaveType,
                typeLabel: typeLabel,
                user_id: l.user_id ?? l.user?.id,
                workingDays: l.working_days_count ?? l.workingDays,
            },
            classNames: ['leave-event']
        };
    });



    const [events, setEvents] = useState<any[]>(() => [
        ...mapLeavesToEvents(userLeaves),
    ]);

    const calendarRef = useRef<any>(null);
    const [selectedEvent, setSelectedEvent] = useState<any>(null);

    const breadcrumbs = [
        { label: 'Panel pracownika', href: 'employee.dashboard' },
        { label: 'Kalendarz', href: 'employee.calendar.index' },
    ];

    // Funkcja do sprawdzania czy tekst się nie mieści i dodawania animacji
    const checkTextOverflow = () => {
        setTimeout(() => {
            const eventElements = document.querySelectorAll('.fc-event-title');
            eventElements.forEach((element: any) => {
                const parent = element.parentElement;
                if (element.scrollWidth > parent.clientWidth) {
                    element.classList.add('text-overflow');
                    element.setAttribute('data-overflow', 'true');
                } else {
                    element.classList.remove('text-overflow');
                    element.setAttribute('data-overflow', 'false');
                }
            });
        }, 100);
    };

    useEffect(() => {
        setEvents(prev => {
            const mapped = mapLeavesToEvents(userLeaves);
            const others = prev.filter(e => !mapped.find(m => m.id === e.id));
            return [...mapped, ...others];
        });
        checkTextOverflow();
    }, [JSON.stringify(userLeaves)]);

    // Sprawdzanie overflow po renderowaniu kalendarza
    useEffect(() => {
        checkTextOverflow();

        // Obserwator zmian w kalendarzu
        const observer = new MutationObserver(() => {
            checkTextOverflow();
        });

        // Pobierz element DOM kalendarza po jego zamontowaniu
        const calendarEl = calendarRef.current?.getApi()?.el;
        if (calendarEl && calendarEl instanceof Element) {
            observer.observe(calendarEl, {
                childList: true,
                subtree: true
            });
        }

        return () => observer.disconnect();
    }, [events]);

    // Dni wolne (święta)
    const holidays = [
        '2025-01-01', // Nowy Rok
        '2025-01-06', // Trzech Króli
        '2025-05-01', // Święto Pracy
        '2025-05-03', // Święto Konstytucji
        '2025-08-15', // Wniebowzięcie NMP
        '2025-11-01', // Wszystkich Świętych
        '2025-11-11', // Święto Niepodległości
        '2025-12-25', // Boże Narodzenie
        '2025-12-26', // Drugi dzień Świąt
    ];

    // Funkcja sprawdzająca czy dzień jest weekendem
    const isWeekend = (date: Date) => {
        const day = date.getDay();
        return day === 0 || day === 6; // 0 = niedziela, 6 = sobota
    };

    // Funkcja sprawdzająca czy dzień jest świętem
    const isHoliday = (dateStr: string) => {
        return holidays.includes(dateStr);
    };

    // Funkcja zwracająca tylko dni robocze z zakresu
    const getWorkingDaysInRange = (startStr: string, endStr: string) => {
        const start = new Date(startStr);
        const end = new Date(endStr);
        const workingDays = [];

        for (let d = new Date(start); d < end; d.setDate(d.getDate() + 1)) {
            const dateStr = d.toISOString().split('T')[0];
            if (!isWeekend(d) && !isHoliday(dateStr)) {
                workingDays.push(dateStr);
            }
        }
        return workingDays;
    };

    const selectAllow = (selectInfo: any) => {
        return true;
    };

    // Obsługa zaznaczenia kilku dni
    const handleDateSelect = (selectInfo: any) => {
        const workingDays = getWorkingDaysInRange(selectInfo.startStr, selectInfo.endStr);

        if (workingDays.length === 0) {
            const newEvent = {
                id: String(events.length + 1),
                title: 'W wybranym zakresie nie ma dni roboczych!',
                start: selectInfo.startStr,
                end: selectInfo.endStr,
                allDay: true,
                backgroundColor: '#ef4444',
                type_label: 'error',
                description: 'Wybierz tylko dni robocze',
            };
            setSelectedEvent(newEvent);
            selectInfo.view.calendar.unselect();
            return;
        }

        const newEvent = {
            id: String(events.length + 1),
            title: '',
            start: workingDays[0],
            end: workingDays[workingDays.length - 1],
            allDay: true,
            backgroundColor: LEAVE_TYPE_COLORS.annual,
            borderColor: LEAVE_TYPE_COLORS.annual,
            textColor: '#ffffff',
            type_label: 'annual',
            description: `Wybrane dni robocze: ${workingDays.length}`,
            workingDays: workingDays,
        };
        setSelectedEvent(newEvent);
    };
    const handleDateClick = (arg: any) => {
        const newEvent = {
            id: String(events.length + 1),
            title: '',
            start: arg.dateStr,
            end: arg.dateStr,
            allDay: arg.allDay,
            backgroundColor: LEAVE_TYPE_COLORS.annual,
            borderColor: LEAVE_TYPE_COLORS.annual,
            textColor: '#ffffff',
            type_label: 'annual',
            description: '',
        };
        setSelectedEvent(newEvent);
    };

    const handleEventClick = (clickInfo: any) => {
        const id = clickInfo.event.id;
        window.location.href = `/employee/calendar/show/${id}`;
    };

    return (
        <EmployeeLayout title="Kalendarz pracownika" breadcrumbs={breadcrumbs}>
            <div className="p-6 bg-white rounded-lg shadow">
                <div className="mb-4">
                    <h2 className="text-2xl font-bold text-gray-800">Kalendarz</h2>
                    <p className="text-sm text-gray-600 mt-1">
                        Kliknij w datę, aby dodać nowe wydarzenie
                    </p>
                </div>

                <div className="calendar-container">
                    <FullCalendar
                        ref={calendarRef}
                        plugins={[dayGridPlugin, timeGridPlugin, interactionPlugin]}
                        initialView="dayGridMonth"
                        locale={plLocale}
                        headerToolbar={{
                            left: 'prev,next today',
                            center: 'title',
                            right: 'dayGridMonth,timeGridWeek,timeGridDay',
                        }}
                        buttonText={{
                            today: 'Dziś',
                            month: 'Miesiąc',
                            week: 'Tydzień',
                            day: 'Dzień',
                        }}
                        events={events}
                        editable={true}
                        selectable={true}
                        selectMirror={true}
                        selectAllow={selectAllow}
                        select={handleDateSelect}
                        dayMaxEvents={true}
                        weekends={true}
                        dateClick={handleDateClick}
                        eventClick={handleEventClick}
                        height="auto"
                        nowIndicator={true}
                        eventTimeFormat={{
                            hour: '2-digit',
                            minute: '2-digit',
                            meridiem: false,
                        }}
                        slotLabelFormat={{
                            hour: '2-digit',
                            minute: '2-digit',
                            meridiem: false,
                        }}
                        // Wyróżnij weekendy i święta
                        dayCellClassNames={(arg) => {
                            const dateStr = arg.dateStr;
                            const isWknd = isWeekend(arg.date);
                            const isHol = isHoliday(dateStr);

                            let classes = [];
                            if (isWknd) classes.push('weekend-day');
                            if (isHol) classes.push('holiday-day');
                            if (isWknd || isHol) classes.push('non-selectable');

                            return classes;
                        }}
                    />
                </div>
            </div>

            {selectedEvent && (
                <div
                    className="fixed inset-0 flex items-center justify-center z-50 p-4"
                    onClick={() => setSelectedEvent(null)}
                >
                    <div
                        onClick={(e) => e.stopPropagation()}
                    >
                        <Event
                            event={selectedEvent}
                            onClose={() => setSelectedEvent(null)}
                        />
                    </div>
                </div>
            )}

            <style>{`
                .fc {
                    font-family: inherit;
                }
                .fc-toolbar-title {
                    font-size: 1.5rem !important;
                    font-weight: 600;
                }
                .fc-button {
                    background-color: #3b82f6 !important;
                    border-color: #3b82f6 !important;
                    text-transform: capitalize;
                }
                .fc-button:hover {
                    background-color: #2563eb !important;
                }
                .fc-button-active {
                    background-color: #1e40af !important;
                }
                .fc-event {
                    cursor: pointer;
                    border-radius: 4px !important;
                    font-size: 0.75rem !important;
                    font-weight: 500 !important;
                    padding: 2px 4px !important;
                }

                /* Animacja przewijania dla długich tytułów */
                .leave-event .fc-event-title {
                    white-space: nowrap;
                    overflow: hidden;
                    position: relative;
                    max-width: 100%;
                    display: block;
                }

                /* Animacja tylko dla tekstów które się nie mieszczą */
                .leave-event .fc-event-title.text-overflow {
                    animation: scrollTextLoop 8s ease-in-out infinite;
                }

                @keyframes scrollTextLoop {
                    0% {
                        transform: translateX(0%);
                    }
                    20% {
                        transform: translateX(0%);
                    }
                    50% {
                        transform: translateX(calc(-100% + 80px));
                    }
                    80% {
                        transform: translateX(calc(-100% + 80px));
                    }
                    100% {
                        transform: translateX(0%);
                    }
                }

                /* Płynniejsza animacja na hover */
                .leave-event:hover .fc-event-title.text-overflow {
                    animation: scrollTextFast 4s ease-in-out infinite;
                }

                @keyframes scrollTextFast {
                    0% { transform: translateX(0%); }
                    25% { transform: translateX(calc(-100% + 70px)); }
                    75% { transform: translateX(calc(-100% + 70px)); }
                    100% { transform: translateX(0%); }
                .holiday-day {
                    background-color: #fef3c7 !important;
                    color: #92400e !important;
                }

                /* Dni niemożliwe do zaznaczenia */
                .non-selectable {
                    cursor: not-allowed !important;
                }
                .non-selectable:hover {
                    background-color: inherit !important;
                }

                /* Stylowanie selekcji */
                .fc-highlight {
                    background-color: #dbeafe !important;
                }

                /* Lepsze stylowanie eventów */
                .fc-daygrid-event {
                    margin: 1px 2px !important;
                    border-radius: 3px !important;
                }

                .fc-event-main {
                    padding: 1px 3px !important;
                }
            `}</style>
        </EmployeeLayout>
    );
}
