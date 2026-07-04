import Swal from 'sweetalert2';

export default function () {
    return {
        // ---- State ----
        marks: [],
        loading: false,
        activeTab: 'pending',
        processingIds: new Set(),

        // ---- Init ----
        init() {
            this.loadMarks();
        },

        // ---- Data loading ----
        async loadMarks() {
            this.loading = true;
            try {
                const res = await axios.get('/admin/marks/approval/pending');
                const raw = res.data.data;
                this.marks = (Array.isArray(raw) ? raw : raw.data || []).map(m => ({
                    id: m.id,
                    student_id: m.student?.id ?? null,
                    name: m.student?.name ?? 'Unknown',
                    roll_no: m.student?.roll_no ?? '',
                    photo: m.student?.photo ?? null,
                    obtained_mark: m.obtained_mark,
                    practical_mark: m.practical_mark,
                    viva_mark: m.viva_mark,
                    total_mark: m.total_mark,
                    grade_letter: m.grade?.grade_letter ?? null,
                    approval_status: m.approval_status ?? 'pending',
                    remark: m.remark ?? '',
                    approved_by: m.approved_by ?? null,
                    approved_at: m.approved_at ?? null,
                }));
            } catch (err) {
                const msg = err.response?.data?.message || 'Failed to load marks';
                Swal.fire({
                    icon: 'error',
                    title: msg,
                    toast: true,
                    position: 'top-end',
                    showConfirmButton: false,
                    timer: 3000,
                });
            } finally {
                this.loading = false;
            }
        },

        // ---- Actions ----
        async approveMark(mark) {
            if (this.processingIds.has(mark.id)) return;

            const result = await Swal.fire({
                title: 'Approve Marks?',
                text: `Approve marks for ${mark.name}? This action cannot be undone.`,
                icon: 'question',
                showCancelButton: true,
                confirmButtonText: 'Yes, approve',
                cancelButtonText: 'Cancel',
                confirmButtonColor: '#198754',
            });

            if (!result.isConfirmed) return;

            this.processingIds.add(mark.id);
            try {
                await axios.post(`/admin/marks/approval/${mark.id}/approve`);
                mark.approval_status = 'approved';
                Swal.fire({
                    icon: 'success',
                    title: 'Marks approved successfully',
                    toast: true,
                    position: 'top-end',
                    showConfirmButton: false,
                    timer: 3000,
                });
            } catch (err) {
                const msg = err.response?.data?.message || 'Failed to approve marks';
                Swal.fire({
                    icon: 'error',
                    title: msg,
                    toast: true,
                    position: 'top-end',
                    showConfirmButton: false,
                    timer: 3000,
                });
            } finally {
                this.processingIds.delete(mark.id);
            }
        },

        async rejectMark(mark) {
            if (this.processingIds.has(mark.id)) return;

            const { value: reason, isConfirmed } = await Swal.fire({
                title: 'Reject Marks?',
                text: `Provide a reason for rejecting ${mark.name}'s marks.`,
                icon: 'warning',
                input: 'textarea',
                inputLabel: 'Rejection Reason',
                inputPlaceholder: 'Enter the reason for rejection...',
                inputAttributes: { 'aria-label': 'Rejection reason' },
                showCancelButton: true,
                confirmButtonText: 'Yes, reject',
                cancelButtonText: 'Cancel',
                confirmButtonColor: '#dc3545',
                inputValidator: (value) => {
                    if (!value?.trim()) return 'A rejection reason is required.';
                },
            });

            if (!isConfirmed) return;

            this.processingIds.add(mark.id);
            try {
                await axios.post(`/admin/marks/approval/${mark.id}/reject`, {
                    remark: reason.trim(),
                });
                mark.approval_status = 'rejected';
                mark.remark = reason.trim();
                Swal.fire({
                    icon: 'success',
                    title: 'Marks rejected',
                    toast: true,
                    position: 'top-end',
                    showConfirmButton: false,
                    timer: 3000,
                });
            } catch (err) {
                const msg = err.response?.data?.message || 'Failed to reject marks';
                Swal.fire({
                    icon: 'error',
                    title: msg,
                    toast: true,
                    position: 'top-end',
                    showConfirmButton: false,
                    timer: 3000,
                });
            } finally {
                this.processingIds.delete(mark.id);
            }
        },

        async resetMark(mark) {
            if (this.processingIds.has(mark.id)) return;

            const result = await Swal.fire({
                title: 'Reset Approval?',
                text: `Reset approval status for ${mark.name} back to pending.`,
                icon: 'info',
                showCancelButton: true,
                confirmButtonText: 'Yes, reset',
                cancelButtonText: 'Cancel',
                confirmButtonColor: '#ffc107',
            });

            if (!result.isConfirmed) return;

            this.processingIds.add(mark.id);
            try {
                await axios.post(`/admin/marks/approval/${mark.id}/reset`);
                mark.approval_status = 'pending';
                Swal.fire({
                    icon: 'success',
                    title: 'Approval status reset',
                    toast: true,
                    position: 'top-end',
                    showConfirmButton: false,
                    timer: 3000,
                });
            } catch (err) {
                const msg = err.response?.data?.message || 'Failed to reset approval';
                Swal.fire({
                    icon: 'error',
                    title: msg,
                    toast: true,
                    position: 'top-end',
                    showConfirmButton: false,
                    timer: 3000,
                });
            } finally {
                this.processingIds.delete(mark.id);
            }
        },

        // ---- Computed ----
        get filteredMarks() {
            if (this.activeTab === 'all') return this.marks;
            return this.marks.filter(m => m.approval_status === this.activeTab);
        },

        get stats() {
            const total = this.marks.length;
            const pending = this.marks.filter(m => m.approval_status === 'pending').length;
            const approved = this.marks.filter(m => m.approval_status === 'approved').length;
            const rejected = this.marks.filter(m => m.approval_status === 'rejected').length;
            return { total, pending, approved, rejected };
        },

        // ---- Helpers ----
        isProcessing(mark) {
            return this.processingIds.has(mark.id);
        },

        statusBadgeClass(status) {
            const map = {
                pending: 'bg-warning text-dark',
                approved: 'bg-success',
                rejected: 'bg-danger',
            };
            return map[status] || 'bg-secondary';
        },

        statusIcon(status) {
            const map = {
                pending: 'bi-clock',
                approved: 'bi-check-circle',
                rejected: 'bi-x-circle',
            };
            return map[status] || 'bi-question-circle';
        },

        getAvatarUrl(mark) {
            if (mark.photo) return mark.photo;
            return `https://ui-avatars.com/api/?name=${encodeURIComponent(mark.name)}&size=40&rounded=true&background=f0f0f0&color=333`;
        },

        formatDate(dateStr) {
            if (!dateStr) return '--';
            const d = new Date(dateStr);
            return d.toLocaleDateString('en-US', {
                year: 'numeric', month: 'short', day: 'numeric',
                hour: '2-digit', minute: '2-digit',
            });
        },
    };
}
