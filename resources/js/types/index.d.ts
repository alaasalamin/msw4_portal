export interface User {
    id: number;
    name: string;
    email: string;
    email_verified_at?: string;
    type?: 'customer' | 'partner' | 'employee';
}

export type PageProps<
    T extends Record<string, unknown> = Record<string, unknown>,
> = T & {
    auth: {
        user: User;
    };
};
