import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import { Head, router, usePage } from '@inertiajs/react';
import { useState, useMemo } from 'react';

// ─── Types ────────────────────────────────────────────────────────────────────

type Priority = 'urgent' | 'high' | 'normal' | 'low';
type Status = 'received' | 'diagnosing' | 'waiting_approval' | 'in_repair' | 'waiting_parts' | 'ready' | 'completed';

interface Device {
    id: number;
    ticket_number: string;
    brand: string;
    model: string;
    color?: string;
    customer_name: string;
    customer_phone?: string;
    issue_description: string;
    status: Status;
    priority: Priority;
    days_in_shop: number;
    internal_notes?: string;
    serial_number?: string;
    estimated_cost?: string | number;
}

// ─── Config ───────────────────────────────────────────────────────────────────

const PRIORITY_BAR: Record<Priority, string> = {
    urgent: 'bg-rose-400',
    high:   'bg-amber-400',
    normal: 'bg-sky-400',
    low:    'bg-gray-300',
};

const STATUS_LABEL: Record<Status, string> = {
    received:         'Received',
    diagnosing:       'Diagnosing',
    waiting_approval: 'Waiting Approval',
    in_repair:        'In Repair',
    waiting_parts:    'Waiting Parts',
    ready:            'Ready for Pickup',
    completed:        'Completed',
};

const NEXT_STEP: Partial<Record<Status, { status: string; label: string }>> = {
    received:         { status: 'diagnosing',       label: 'Start Diagnosing'   },
    diagnosing:       { status: 'waiting_approval', label: 'Send Quote'         },
    waiting_approval: { status: 'in_repair',        label: 'Start Repair'       },
    in_repair:        { status: 'ready',            label: 'Mark as Ready'      },
    waiting_parts:    { status: 'in_repair',        label: 'Parts Arrived'      },
    ready:            { status: 'completed',        label: 'Close Job'          },
};

const ALL_STATUSES: Status[] = ['received','diagnosing','waiting_approval','in_repair','waiting_parts','ready','completed'];

// ─── Helpers ──────────────────────────────────────────────────────────────────

function agingText(days: number): string {
    if (days === 0) return 'Arrived today';
    if (days === 1) return '1 day in shop';
    return `${days} days in shop`;
}

function advance(id: number, status: string): void {
    router.patch(route('devices.status', id), { status }, {
        preserveScroll: true,
        preserveState: true,
    });
}

// ─── Device Row ───────────────────────────────────────────────────────────────

function DeviceRow({ device }: { device: Device }) {
    const [open, setOpen]     = useState(false);
    const [notes, setNotes]   = useState(device.internal_notes ?? '');
    const [saving, setSaving] = useState(false);

    const next = NEXT_STEP[device.status];

    const saveNotes = () => {
        setSaving(true);
        router.patch(route('devices.notes', device.id), { internal_notes: notes }, {
            preserveScroll: true,
            onFinish: () => setSaving(false),
        });
    };

    return (
        <div className="overflow-hidden rounded-xl border border-gray-200 bg-white">
            {/* Main row */}
            <div className="flex items-stretch">
                {/* Priority stripe */}
                <div className={`w-1 shrink-0 ${PRIORITY_BAR[device.priority]}`} />

                <div className="flex flex-1 flex-wrap items-center gap-x-6 gap-y-3 px-5 py-4">
                    {/* Device */}
                    <div className="min-w-[180px] flex-1">
                        <p className="text-[11px] font-medium uppercase tracking-widest text-gray-400">
                            {device.ticket_number}
                        </p>
                        <p className="mt-0.5 text-[15px] font-semibold text-gray-900">
                            {device.brand} {device.model}
                        </p>
                        {device.color && (
                            <p className="text-[12px] text-gray-400">{device.color}</p>
                        )}
                    </div>

                    {/* Customer */}
                    <div className="min-w-[140px]">
                        <p className="text-[11px] text-gray-400">Customer</p>
                        <p className="text-[14px] font-medium text-gray-700">{device.customer_name}</p>
                        {device.customer_phone && (
                            <a
                                href={`tel:${device.customer_phone}`}
                                className="text-[12px] text-sky-600 hover:underline"
                            >
                                {device.customer_phone}
                            </a>
                        )}
                    </div>

                    {/* Issue */}
                    <div className="min-w-[200px] flex-[2]">
                        <p className="text-[11px] text-gray-400">Issue</p>
                        <p className="text-[13px] text-gray-600 line-clamp-2">{device.issue_description}</p>
                    </div>

                    {/* Status + Age */}
                    <div className="min-w-[130px]">
                        <p className="text-[13px] font-medium text-gray-700">
                            {STATUS_LABEL[device.status]}
                        </p>
                        <p className="text-[12px] text-gray-400">{agingText(device.days_in_shop)}</p>
                    </div>

                    {/* Actions */}
                    <div className="flex items-center gap-2">
                        {next && (
                            <button
                                onClick={() => advance(device.id, next.status)}
                                className="rounded-lg bg-gray-900 px-4 py-2 text-[12px] font-semibold text-white
                                           hover:bg-gray-700 transition active:scale-95"
                            >
                                {next.label}
                            </button>
                        )}

                        {/* Need parts side-action */}
                        {device.status === 'in_repair' && (
                            <button
                                onClick={() => advance(device.id, 'waiting_parts')}
                                className="rounded-lg border border-gray-200 px-3 py-2 text-[12px] text-gray-500
                                           hover:border-gray-300 hover:text-gray-700 transition"
                            >
                                Need Parts
                            </button>
                        )}

                        {/* More */}
                        <div className="relative group">
                            <button className="rounded-lg border border-gray-200 px-3 py-2 text-[12px] text-gray-400 hover:text-gray-600 transition">
                                ···
                            </button>
                            <div className="absolute right-0 top-full z-20 mt-1 hidden w-48 rounded-xl border border-gray-200 bg-white py-1 shadow-xl group-hover:block">
                                {ALL_STATUSES.filter(s => s !== device.status).map(s => (
                                    <button
                                        key={s}
                                        onClick={() => advance(device.id, s)}
                                        className="block w-full px-4 py-2 text-left text-[12px] text-gray-600 hover:bg-gray-50"
                                    >
                                        {STATUS_LABEL[s]}
                                    </button>
                                ))}
                            </div>
                        </div>

                        {/* Notes toggle */}
                        <button
                            onClick={() => setOpen(!open)}
                            className={`rounded-lg border px-3 py-2 text-[12px] transition
                                ${open
                                    ? 'border-gray-900 bg-gray-900 text-white'
                                    : 'border-gray-200 text-gray-400 hover:text-gray-600'}`}
                        >
                            Notes
                        </button>
                    </div>
                </div>
            </div>

            {/* Notes panel */}
            {open && (
                <div className="border-t border-gray-100 bg-gray-50 px-6 py-4">
                    <div className="flex gap-4">
                        <div className="flex-1">
                            {device.serial_number && (
                                <p className="mb-3 text-[12px] text-gray-500">
                                    Serial number:{' '}
                                    <span className="font-mono font-medium text-gray-700">{device.serial_number}</span>
                                </p>
                            )}
                            {device.estimated_cost && (
                                <p className="mb-3 text-[12px] text-gray-500">
                                    Estimated cost:{' '}
                                    <span className="font-medium text-gray-700">€{device.estimated_cost}</span>
                                </p>
                            )}
                            <label className="mb-1.5 block text-[11px] font-semibold uppercase tracking-widest text-gray-400">
                                Internal Notes
                            </label>
                            <textarea
                                rows={3}
                                value={notes}
                                onChange={e => setNotes(e.target.value)}
                                placeholder="Notes visible only to the team…"
                                className="w-full rounded-lg border-gray-200 bg-white text-[13px] text-gray-700
                                           focus:border-gray-400 focus:ring-0 transition"
                            />
                            <div className="mt-2 flex justify-end">
                                <button
                                    onClick={saveNotes}
                                    disabled={saving}
                                    className="rounded-lg bg-gray-900 px-4 py-1.5 text-[12px] font-semibold
                                               text-white hover:bg-gray-700 disabled:opacity-40 transition"
                                >
                                    {saving ? 'Saving…' : 'Save'}
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            )}
        </div>
    );
}

// ─── Main Page ────────────────────────────────────────────────────────────────

const PRIORITY_ORDER: Record<Priority, number> = { urgent: 0, high: 1, normal: 2, low: 3 };

interface BoardProps { devices: Device[]; }

export default function Board({ devices }: BoardProps) {
    const { auth } = usePage().props;
    const [search, setSearch] = useState('');
    const [status, setStatus] = useState<Status | 'all'>('all');

    const filtered = useMemo(() => {
        let d = [...devices];

        if (status !== 'all') {
            d = d.filter(x => x.status === status);
        }

        if (search.trim()) {
            const q = search.toLowerCase();
            d = d.filter(x =>
                x.brand.toLowerCase().includes(q) ||
                x.model.toLowerCase().includes(q) ||
                x.customer_name.toLowerCase().includes(q) ||
                x.ticket_number.toLowerCase().includes(q)
            );
        }

        return d.sort((a, b) =>
            PRIORITY_ORDER[a.priority] - PRIORITY_ORDER[b.priority] ||
            b.days_in_shop - a.days_in_shop
        );
    }, [devices, search, status]);

    const counts = useMemo(() => {
        const c: Record<string, number> = {};
        ALL_STATUSES.forEach(s => { c[s] = devices.filter(d => d.status === s).length; });
        return c;
    }, [devices]);

    // suppress unused warning — auth is available for future use
    void auth;

    return (
        <AuthenticatedLayout
            header={
                <div>
                    <h2 className="text-xl font-semibold text-gray-900">
                        My Devices
                    </h2>
                    <p className="mt-0.5 text-sm text-gray-500">
                        {devices.length} device{devices.length !== 1 ? 's' : ''} assigned to you
                    </p>
                </div>
            }
        >
            <Head title="My Devices" />

            <div className="min-h-screen bg-gray-50 py-8">
                <div className="mx-auto max-w-6xl px-4 sm:px-6 lg:px-8">

                    {/* Search + status filter */}
                    <div className="mb-6 flex flex-wrap items-center gap-3">
                        <input
                            type="text"
                            placeholder="Search device, customer, ticket…"
                            value={search}
                            onChange={e => setSearch(e.target.value)}
                            className="w-64 rounded-lg border-gray-200 bg-white py-2 text-[13px] text-gray-700
                                       shadow-sm focus:border-gray-400 focus:ring-0 transition"
                        />

                        <div className="flex flex-wrap gap-1.5">
                            <button
                                onClick={() => setStatus('all')}
                                className={`rounded-lg px-3 py-2 text-[12px] font-medium transition
                                    ${status === 'all'
                                        ? 'bg-gray-900 text-white'
                                        : 'border border-gray-200 bg-white text-gray-500 hover:text-gray-800'}`}
                            >
                                All ({devices.length})
                            </button>
                            {ALL_STATUSES.filter(s => s !== 'completed').map(s => (
                                counts[s] > 0 && (
                                    <button
                                        key={s}
                                        onClick={() => setStatus(s)}
                                        className={`rounded-lg px-3 py-2 text-[12px] font-medium transition
                                            ${status === s
                                                ? 'bg-gray-900 text-white'
                                                : 'border border-gray-200 bg-white text-gray-500 hover:text-gray-800'}`}
                                    >
                                        {STATUS_LABEL[s]} ({counts[s]})
                                    </button>
                                )
                            ))}
                        </div>
                    </div>

                    {/* Device list */}
                    {filtered.length === 0 ? (
                        <div className="py-24 text-center">
                            <p className="text-gray-400">No devices found.</p>
                        </div>
                    ) : (
                        <div className="flex flex-col gap-3">
                            {filtered.map(d => <DeviceRow key={d.id} device={d} />)}
                        </div>
                    )}

                </div>
            </div>
        </AuthenticatedLayout>
    );
}
