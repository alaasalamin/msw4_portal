import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import { Head, Link } from '@inertiajs/react';

const statusColors = {
    created: 'bg-gray-100 text-gray-700',
    label_created: 'bg-blue-100 text-blue-700',
    in_transit: 'bg-yellow-100 text-yellow-700',
    delivered: 'bg-green-100 text-green-700',
};

export default function Index({ shipments }) {
    return (
        <AuthenticatedLayout
            header={
                <div className="flex items-center justify-between">
                    <h2 className="text-xl font-semibold leading-tight text-gray-800">Shipments</h2>
                    <Link
                        href={route('shipments.create')}
                        className="rounded-md bg-indigo-600 px-4 py-2 text-sm font-medium text-white hover:bg-indigo-700"
                    >
                        New Shipment
                    </Link>
                </div>
            }
        >
            <Head title="Shipments" />

            <div className="py-12">
                <div className="mx-auto max-w-7xl sm:px-6 lg:px-8">
                    <div className="overflow-hidden bg-white shadow sm:rounded-lg">
                        {shipments.length === 0 ? (
                            <div className="px-6 py-12 text-center text-gray-500">
                                No shipments yet.{' '}
                                <Link href={route('shipments.create')} className="text-indigo-600 hover:underline">
                                    Create your first shipment
                                </Link>
                            </div>
                        ) : (
                            <table className="min-w-full divide-y divide-gray-200">
                                <thead className="bg-gray-50">
                                    <tr>
                                        <th className="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Tracking #</th>
                                        <th className="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Recipient</th>
                                        <th className="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Type</th>
                                        <th className="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Status</th>
                                        <th className="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Date</th>
                                        <th className="px-6 py-3" />
                                    </tr>
                                </thead>
                                <tbody className="divide-y divide-gray-200 bg-white">
                                    {shipments.map((s) => (
                                        <tr key={s.id} className="hover:bg-gray-50">
                                            <td className="px-6 py-4 font-mono text-sm text-gray-900">
                                                {s.tracking_number ?? '—'}
                                            </td>
                                            <td className="px-6 py-4 text-sm text-gray-900">
                                                <div className="font-medium">{s.recipient_name}</div>
                                                <div className="text-gray-500">{s.recipient_city}, {s.recipient_country}</div>
                                            </td>
                                            <td className="px-6 py-4 text-sm capitalize text-gray-700">{s.type}</td>
                                            <td className="px-6 py-4 text-sm">
                                                <span className={`inline-flex rounded-full px-2 py-1 text-xs font-semibold ${statusColors[s.status] ?? 'bg-gray-100 text-gray-700'}`}>
                                                    {s.status.replace('_', ' ')}
                                                </span>
                                            </td>
                                            <td className="px-6 py-4 text-sm text-gray-500">
                                                {new Date(s.created_at).toLocaleDateString()}
                                            </td>
                                            <td className="px-6 py-4 text-right text-sm">
                                                <Link
                                                    href={route('shipments.show', s.id)}
                                                    className="text-indigo-600 hover:underline"
                                                >
                                                    View
                                                </Link>
                                            </td>
                                        </tr>
                                    ))}
                                </tbody>
                            </table>
                        )}
                    </div>
                </div>
            </div>
        </AuthenticatedLayout>
    );
}
