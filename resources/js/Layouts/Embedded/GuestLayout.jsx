import ApplicationLogo from '@/Components/ApplicationLogo';
import { Link } from '@inertiajs/react';
import {
    Grid2
} from '@mui/material';
import MainLogo from '@/Images/MainLogo.svg';


export default function GuestLayout({ children }) {
    return (
        <div className="min-h-screen  items-center bg-white  sm:justify-center sm:pt-0">
            <Grid2 container spacing={3} height={'100dvh'} alignItems="center">
                <Grid2 size={{ xs: 12, md: 6 }} height={'100%'}  sx={{background:'#023F80', display: 'flex', justifyContent: 'center', alignItems: 'center', display:{ xs: 'none', md: 'flex' }}}>

                    <Link href="/">
                    <img src={MainLogo} className='w-60' alt="Frago Logo" />
                        {/* <ApplicationLogo className="h-20 w-20 fill-current text-gray-500" /> */}
                    </Link>

                </Grid2>

                <Grid2 size={{ xs: 12, md: 6 }} sx={{ display: 'flex', justifyContent: 'center', alignItems: 'center' }}>

                    <div className="mt-6 w-full overflow-hidden px-6   sm:max-w-md sm:rounded-lg">
                        {children}
                    </div>


                </Grid2>
            </Grid2>
        </div>
    );
}
