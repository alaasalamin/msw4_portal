import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import { Head, Link, router } from '@inertiajs/react';

function InfoRow({ label, value }) {
    if (!value) return null;
    return (
        <div className="flex justify-between py-2 text-sm">
            <span className="text-gray-500">{label}</span>
            <span className="font-medium text-gray-900">{value}</span>
        </div>
    );
}

function AddressCard({ title, data, prefix }) {
    return (
        <div className="rounded-lg border border-gray-200 p-4">
            <h4 className="mb-3 text-sm font-semibold uppercase tracking-wide text-gray-500">{title}</h4>
            <div className="divide-y divide-gray-100">
                <InfoRow label="Name" value={data[`${prefix}_name`]} />
                <InfoRow label="Company" value={data[`${prefix}_company`]} />
                <InfoRow label="Street" value={`${data[`${prefix}_street`]} ${data[`${prefix}_house_number`]}`} />
                <InfoRow label="City" value={`${data[`${prefix}_postal_code`]} ${data[`${prefix}_city`]}`} />
                <InfoRow label="Country" value={data[`${prefix}_country`]} />
                <InfoRow label="Email" value={data[`${prefix}_email`]} />
                <InfoRow label="Phone" value={data[`${prefix}_phone`]} />
            </div>
        </div>
    );
}

export default function Show({ shipment, tracking }) {
    const handleTrack = () => {
        router.get(route('shipments.track', shipment.id));
    };

    const latestEvent = tracking?.[0]?.events?.[0];

    return (
        <AuthenticatedLayout
            header={
                <div className="flex items-center justify-between">
                    <h2 className="text-xl font-semibold leading-tight text-gray-800">
                        Shipment #{shipment.id}
                    </h2>
                    <Link href={route('shipments.index')} className="text-sm text-gray-500 hover:text-gray-700">
                        ← Back to shipments
                    </Link>
                </div>
            }
        >
            <Head title={`Shipment #${shipment.id}`} />

            <div className="py-12">
                <div className="mx-auto max-w-4xl space-y-6 sm:px-6 lg:px-8">

                    {/* Status Card */}
                    <div className="flex items-center justify-between rounded-lg bg-white p-6 shadow sm:rounded-lg">
                        <div>
                            <p className="text-xs uppercase tracking-wide text-gray-500">Tracking Number</p>
                            <p className="mt-1 font-mono text-xl font-bold text-gray-900">
                                {shipment.tracking_number ?? 'Pending'}
                            </p>
                            <p className="mt-1 text-sm capitalize text-gray-500">
                                {shipment.type} · {shipment.status.replace(/_/g, ' ')}
                            </p>
                        </div>
                        <div className="flex flex-col gap-2 text-right">
                            {shipment.label_url && (
                                <a
                                    href={shipment.label_url}
                                    target="_blank"
                                    rel="noreferrer"
                                    className="rounded-md bg-indigo-600 px-4 py-2 text-sm font-medium text-white hover:bg-indigo-700"
                                >
                                    Print Label
                                </a>
                            )}
                            {shipment.tracking_number && (
                                <button
                                    onClick={handleTrack}
                                    className="rounded-md border border-indigo-600 px-4 py-2 text-sm font-medium text-indigo-600 hover:bg-indigo-50"
                                >
                                    Refresh Tracking
                                </button>
                            )}
                        </div>
                    </div>

                    {/* Tracking Events */}
                    {tracking && tracking.length > 0 && (
                        <div className="bg-white p-6 shadow sm:rounded-lg">
                            <h3 className="mb-4 text-base font-semibold text-gray-700">Tracking Events</h3>
                            <ol className="relative border-l border-gray-200">
                                {tracking[0].events?.map((event, i) => (
                                    <li key={i} className="mb-6 ml-4">
                                        <div className="absolute -left-1.5 mt-1.5 h-3 w-3 rounded-full border border-white bg-indigo-500" />
                                        <time className="text-xs text-gray-500">
                                            {new Date(event.timestamp).toLocaleString()}
                                        </time>
                                        <p className="mt-1 text-sm font-medium text-gray-900">{event.description}</p>
                                        {event.location?.address?.addressLocality && (
                                            <p className="text-xs text-gray-500">{event.location.address.addressLocality}</p>
                                        )}
                                    </li>
                                ))}
                            </ol>
                        </div>
                    )}

                    {/* Addresses */}
                    <div className="grid grid-cols-1 gap-6 sm:grid-cols-2">
                        <div className="bg-white p-6 shadow sm:rounded-lg">
                            <AddressCard title="Sender" data={shipment} prefix="sender" />
                        </div>
                        <div className="bg-white p-6 shadow sm:rounded-lg">
                            <AddressCard title="Recipient" data={shipment} prefix="recipient" />
                        </div>
                    </div>

                    {/* Parcel details */}
                    <div className="bg-white p-6 shadow sm:rounded-lg">
                        <h3 className="mb-3 text-base font-semibold text-gray-700">Parcel Details</h3>
                        <div className="divide-y divide-gray-100">
                            <InfoRow label="Weight" value={`${shipment.weight_kg} kg`} />
                            <InfoRow label="Reference" value={shipment.reference} />
                            <InfoRow label="Created" value={new Date(shipment.created_at).toLocaleString()} />
                        </div>
                    </div>

                </div>
            </div>
        </AuthenticatedLayout>
    );
}
