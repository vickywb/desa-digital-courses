export function formatRupiah(value) {
    if (!value) return '0';
    return Number(value).toLocaleString('id-ID');
}

export function formatToClientTimezone(date) {
    if (!date) return '';
    return new Date(date).toLocaleDateString('id-ID', {
        year: 'numeric',
        month: 'long',
        day: 'numeric',
    });
}
