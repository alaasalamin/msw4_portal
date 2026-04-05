import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import InputError from '@/Components/InputError';
import InputLabel from '@/Components/InputLabel';
import PrimaryButton from '@/Components/PrimaryButton';
import TextInput from '@/Components/TextInput';
import { Head, useForm } from '@inertiajs/react';

function Section({ title, children }) {
    return (
        <div className="bg-white p-6 shadow sm:rounded-lg">
            <h3 className="mb-4 text-base font-semibold text-gray-700">{title}</h3>
            <div className="grid grid-cols-1 gap-4 sm:grid-cols-2">{children}</div>
        </div>
    );
}

function Field({ label, error, children }) {
    return (
        <div>
            <InputLabel value={label} />
            {children}
            <InputError message={error} className="mt-1" />
        </div>
    );
}

const TEST_DATA = {
    type: 'domestic',
    sender_name: 'Max Mustermann',
    sender_company: 'Moon Repair GmbH',
    sender_street: 'Mustergasse',
    sender_house_number: '12',
    sender_postal_code: '10115',
    sender_city: 'Berlin',
    sender_country: 'DEU',
    sender_email: 'sender@moonrepair.de',
    sender_phone: '+4930123456',
    recipient_name: 'Erika Musterfrau',
    recipient_company: '',
    recipient_street: 'Beispielstraße',
    recipient_house_number: '5',
    recipient_postal_code: '80331',
    recipient_city: 'München',
    recipient_country: 'DEU',
    recipient_email: 'recipient@example.de',
    recipient_phone: '+4989654321',
    weight_kg: '1.5',
    reference: 'TEST-DEVICE-001',
};

export default function Create() {
    const { data, setData, post, processing, errors } = useForm({
        type: 'domestic',
        sender_name: '',
        sender_company: '',
        sender_street: '',
        sender_house_number: '',
        sender_postal_code: '',
        sender_city: '',
        sender_country: 'DEU',
        sender_email: '',
        sender_phone: '',
        recipient_name: '',
        recipient_company: '',
        recipient_street: '',
        recipient_house_number: '',
        recipient_postal_code: '',
        recipient_city: '',
        recipient_country: 'DEU',
        recipient_email: '',
        recipient_phone: '',
        weight_kg: '',
        reference: '',
    });

    const submit = (e) => {
        e.preventDefault();
        post(route('shipments.store'));
    };

    return (
        <AuthenticatedLayout
            header={
                <div className="flex items-center justify-between">
                    <h2 className="text-xl font-semibold leading-tight text-gray-800">New Shipment</h2>
                    <button
                        type="button"
                        onClick={() => Object.entries(TEST_DATA).forEach(([k, v]) => setData(k, v))}
                        className="rounded-md border border-dashed border-yellow-400 bg-yellow-50 px-3 py-1.5 text-xs font-medium text-yellow-700 hover:bg-yellow-100"
                    >
                        Fill Test Data
                    </button>
                </div>
            }
        >
            <Head title="New Shipment" />

            <div className="py-12">
                <div className="mx-auto max-w-4xl space-y-6 sm:px-6 lg:px-8">
                    <form onSubmit={submit} className="space-y-6">

                        {/* Shipment Type */}
                        <div className="bg-white p-6 shadow sm:rounded-lg">
                            <h3 className="mb-4 text-base font-semibold text-gray-700">Shipment Type</h3>
                            <div className="flex gap-4">
                                {['domestic', 'international'].map((t) => (
                                    <label key={t} className="flex cursor-pointer items-center gap-2">
                                        <input
                                            type="radio"
                                            value={t}
                                            checked={data.type === t}
                                            onChange={() => setData('type', t)}
                                            className="text-indigo-600"
                                        />
                                        <span className="capitalize text-sm font-medium text-gray-700">{t}</span>
                                    </label>
                                ))}
                            </div>
                        </div>

                        {/* Sender */}
                        <Section title="Sender">
                            <Field label="Full Name *" error={errors.sender_name}>
                                <TextInput className="mt-1 w-full" value={data.sender_name} onChange={e => setData('sender_name', e.target.value)} required />
                            </Field>
                            <Field label="Company" error={errors.sender_company}>
                                <TextInput className="mt-1 w-full" value={data.sender_company} onChange={e => setData('sender_company', e.target.value)} />
                            </Field>
                            <Field label="Street *" error={errors.sender_street}>
                                <TextInput className="mt-1 w-full" value={data.sender_street} onChange={e => setData('sender_street', e.target.value)} required />
                            </Field>
                            <Field label="House Number *" error={errors.sender_house_number}>
                                <TextInput className="mt-1 w-full" value={data.sender_house_number} onChange={e => setData('sender_house_number', e.target.value)} required />
                            </Field>
                            <Field label="Postal Code *" error={errors.sender_postal_code}>
                                <TextInput className="mt-1 w-full" value={data.sender_postal_code} onChange={e => setData('sender_postal_code', e.target.value)} required />
                            </Field>
                            <Field label="City *" error={errors.sender_city}>
                                <TextInput className="mt-1 w-full" value={data.sender_city} onChange={e => setData('sender_city', e.target.value)} required />
                            </Field>
                            <Field label="Country (3-letter code) *" error={errors.sender_country}>
                                <TextInput className="mt-1 w-full" value={data.sender_country} onChange={e => setData('sender_country', e.target.value)} required maxLength={3} />
                            </Field>
                            <Field label="Email" error={errors.sender_email}>
                                <TextInput type="email" className="mt-1 w-full" value={data.sender_email} onChange={e => setData('sender_email', e.target.value)} />
                            </Field>
                            <Field label="Phone" error={errors.sender_phone}>
                                <TextInput className="mt-1 w-full" value={data.sender_phone} onChange={e => setData('sender_phone', e.target.value)} />
                            </Field>
                        </Section>

                        {/* Recipient */}
                        <Section title="Recipient">
                            <Field label="Full Name *" error={errors.recipient_name}>
                                <TextInput className="mt-1 w-full" value={data.recipient_name} onChange={e => setData('recipient_name', e.target.value)} required />
                            </Field>
                            <Field label="Company" error={errors.recipient_company}>
                                <TextInput className="mt-1 w-full" value={data.recipient_company} onChange={e => setData('recipient_company', e.target.value)} />
                            </Field>
                            <Field label="Street *" error={errors.recipient_street}>
                                <TextInput className="mt-1 w-full" value={data.recipient_street} onChange={e => setData('recipient_street', e.target.value)} required />
                            </Field>
                            <Field label="House Number *" error={errors.recipient_house_number}>
                                <TextInput className="mt-1 w-full" value={data.recipient_house_number} onChange={e => setData('recipient_house_number', e.target.value)} required />
                            </Field>
                            <Field label="Postal Code *" error={errors.recipient_postal_code}>
                                <TextInput className="mt-1 w-full" value={data.recipient_postal_code} onChange={e => setData('recipient_postal_code', e.target.value)} required />
                            </Field>
                            <Field label="City *" error={errors.recipient_city}>
                                <TextInput className="mt-1 w-full" value={data.recipient_city} onChange={e => setData('recipient_city', e.target.value)} required />
                            </Field>
                            <Field label="Country (3-letter code) *" error={errors.recipient_country}>
                                <TextInput className="mt-1 w-full" value={data.recipient_country} onChange={e => setData('recipient_country', e.target.value)} required maxLength={3} />
                            </Field>
                            <Field label="Email" error={errors.recipient_email}>
                                <TextInput type="email" className="mt-1 w-full" value={data.recipient_email} onChange={e => setData('recipient_email', e.target.value)} />
                            </Field>
                            <Field label="Phone" error={errors.recipient_phone}>
                                <TextInput className="mt-1 w-full" value={data.recipient_phone} onChange={e => setData('recipient_phone', e.target.value)} />
                            </Field>
                        </Section>

                        {/* Parcel Details */}
                        <Section title="Parcel Details">
                            <Field label="Weight (kg) *" error={errors.weight_kg}>
                                <TextInput type="number" step="0.1" min="0.1" max="31.5" className="mt-1 w-full" value={data.weight_kg} onChange={e => setData('weight_kg', e.target.value)} required />
                            </Field>
                            <Field label="Reference" error={errors.reference}>
                                <TextInput className="mt-1 w-full" value={data.reference} onChange={e => setData('reference', e.target.value)} placeholder="e.g. device serial or order ID" />
                            </Field>
                        </Section>

                        <div className="flex justify-end">
                            <PrimaryButton disabled={processing}>
                                {processing ? 'Creating…' : 'Create Shipment & Get Label'}
                            </PrimaryButton>
                        </div>
                    </form>
                </div>
            </div>
        </AuthenticatedLayout>
    );
}
