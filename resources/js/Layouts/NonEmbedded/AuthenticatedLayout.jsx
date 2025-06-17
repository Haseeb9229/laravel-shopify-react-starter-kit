import { Toaster } from 'react-hot-toast';

export default function AuthenticatedLayout({ children }) {

    return (
        <div className="min-h-screen bg-gray-100">
            <Toaster
                position="top-right"
                reverseOrder={false}
            />
            <main>{children}</main>
        </div>
    );
}
