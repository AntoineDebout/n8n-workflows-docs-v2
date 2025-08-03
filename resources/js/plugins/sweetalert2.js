import Swal from 'sweetalert2';

const Toast = Swal.mixin({
    toast: true,
    position: 'top-end',
    showConfirmButton: false,
    timer: 3000,
    timerProgressBar: true,
    didOpen: (toast) => {
        toast.onmouseenter = Swal.stopTimer;
        toast.onmouseleave = Swal.resumeTimer;
    }
});

export default {
    install: (app) => {
        // Provide both Swal and Toast as injectable
        app.provide('swal', Swal);
        app.provide('toast', Toast);
        
        // Also keep global properties for backward compatibility
        app.config.globalProperties.$swal = Swal;
        app.config.globalProperties.$toast = Toast;
    }
};
