<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
    (function() {
        const run = async () => {
        if (typeof Swal === 'undefined') {
            return;
        }

        const escapeHtml = (value) => {
            return String(value ?? '').replace(/[&<>"']/g, (char) => ({
                '&': '&amp;',
                '<': '&lt;',
                '>': '&gt;',
                '"': '&quot;',
                "'": '&#039;'
            } [char]));
        };

        // Common flash messages
        const errorMessage = @json(session('errorMessage') ?? session('error'));
        const duplicate = @json(session('duplicate'));
        const deleteSchedule = @json(session('deletschedule'));
        const warning = @json(session('warning'));
        const save = @json(session('save'));

        // Near-expiry payload (set by App\Http\Middleware\NearExpiryAlert)
        const nearExpiry = @json(session('nearExpiry'));

        if (errorMessage) {
            await Swal.fire({
                icon: 'error',
                title: 'Access Denied',
                text: errorMessage,
                confirmButtonColor: '#4e73df',
                timerProgressBar: true
            });
        }

        if (duplicate) {
            await Swal.fire({
                icon: 'warning',
                title: 'Opsss!',
                text: duplicate,
                confirmButtonColor: '#4e73df',
                timer: 5000,
                timerProgressBar: true
            });
        }

        if (deleteSchedule) {
            await Swal.fire({
                icon: 'warning',
                title: 'Be informed',
                text: deleteSchedule,
                confirmButtonColor: '#4e73df',
                timer: 5000,
                timerProgressBar: true
            });
        }

        if (warning) {
            await Swal.fire({
                icon: 'error',
                title: 'Be informed',
                text: warning
            });
        }

        if (save) {
            await Swal.fire({
                icon: 'success',
                title: 'Success',
                text: save
            });
        }

        if (nearExpiry && Number(nearExpiry.count ?? 0) > 0) {
            const days = Number(nearExpiry.days ?? 0);
            const items = Array.isArray(nearExpiry.items) ? nearExpiry.items : [];

            const listHtml = items
                .slice(0, 10)
                .map((item) => {
                    const name = escapeHtml(item?.name);
                    const expiry = escapeHtml(item?.expiry);
                    const daysLeft = (item?.daysLeft === null || item?.daysLeft === undefined)
                        ? ''
                        : ` (${escapeHtml(item.daysLeft)} day(s) left)`;

                    return `<li><strong>${name}</strong> — ${expiry}${daysLeft}</li>`;
                })
                .join('');

            const html = listHtml
                ? `<div style="text-align:left">
                        <div><strong>${escapeHtml(nearExpiry.count)}</strong> product(s) expiring within <strong>${escapeHtml(days)}</strong> day(s):</div>
                        <ul style="margin:10px 0 0 18px">${listHtml}</ul>
                    </div>`
                : `There are <strong>${escapeHtml(nearExpiry.count)}</strong> product(s) expiring within <strong>${escapeHtml(days)}</strong> day(s).`;

            await Swal.fire({
                icon: 'warning',
                title: 'Near Expiry Alert',
                html,
                confirmButtonColor: '#4e73df'
            });
        }
        };

        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', run);
        } else {
            run();
        }
    })();
</script>
