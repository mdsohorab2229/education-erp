@extends('admin.layouts.master')

@section('title', 'Content Detail')

@section('content')
<div class="container-fluid py-4" x-data="contentDetail()">
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-2">
        <div>
            <h1 class="h3 mb-1" x-text="content.title || 'Content Detail'"></h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.contents.index') }}">Content</a></li>
                    <li class="breadcrumb-item active" x-text="content.title || 'Detail'"></li>
                </ol>
            </nav>
        </div>
        <a :href="`/admin/contents/${contentId}/download`" class="btn btn-success" x-show="content.file_name">
            <i class="bi bi-download me-1"></i> Download
        </a>
    </div>

    <div class="row g-4">
        <div class="col-lg-8">
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-white py-3">
                    <h5 class="card-title mb-0"><i class="bi bi-info-circle me-2"></i>Content Info</h5>
                </div>
                <div class="card-body">
                    <template x-if="!loaded">
                        <div class="text-center py-4">
                            <div class="spinner-border text-primary"><span class="visually-hidden">Loading...</span></div>
                        </div>
                    </template>
                    <template x-if="loaded">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="fw-semibold small text-muted">Type</label>
                                <p>
                                    <span class="badge" :class="{
                                        'bg-danger': content.type === 'pdf',
                                        'bg-primary': content.type === 'video',
                                        'bg-success': content.type === 'notes'
                                    }" x-text="content.type?.toUpperCase()"></span>
                                </p>
                            </div>
                            <div class="col-md-6">
                                <label class="fw-semibold small text-muted">Status</label>
                                <p>
                                    <span class="badge" :class="{
                                        'bg-success': content.status === 'active',
                                        'bg-warning text-dark': content.status === 'processing',
                                        'bg-danger': content.status === 'failed'
                                    }" x-text="content.status"></span>
                                </p>
                            </div>
                            <div class="col-md-6">
                                <label class="fw-semibold small text-muted">Section</label>
                                <p x-text="content.section?.name || '-'"></p>
                            </div>
                            <div class="col-md-6">
                                <label class="fw-semibold small text-muted">Subject</label>
                                <p x-text="content.subject?.name || '-'"></p>
                            </div>
                            <div class="col-md-6">
                                <label class="fw-semibold small text-muted">File Name</label>
                                <p x-text="content.file_name || '-'"></p>
                            </div>
                            <div class="col-md-6">
                                <label class="fw-semibold small text-muted">File Size</label>
                                <p x-text="content.file_size ? (content.file_size / 1024).toFixed(1) + ' KB' : '-'"></p>
                            </div>
                            <div class="col-12" x-show="content.description">
                                <label class="fw-semibold small text-muted">Description</label>
                                <p x-text="content.description"></p>
                            </div>
                            <div class="col-12">
                                <label class="fw-semibold small text-muted">Uploaded By</label>
                                <p x-text="content.teacher?.name || '-'"></p>
                            </div>
                        </div>
                    </template>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card shadow-sm">
                <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0"><i class="bi bi-chat-dots me-2"></i>Comments</h5>
                    <span class="badge bg-primary" x-text="comments.length"></span>
                </div>
                <div class="card-body">
                    <form @@submit.prevent="addComment" class="mb-3">
                        <div class="input-group">
                            <input type="text" class="form-control" x-model="newComment" placeholder="Write a comment..." maxlength="2000">
                            <button class="btn btn-primary" type="submit" :disabled="!newComment.trim() || commenting">
                                <span x-show="!commenting"><i class="bi bi-send"></i></span>
                                <span x-show="commenting"><span class="spinner-border spinner-border-sm"></span></span>
                            </button>
                        </div>
                    </form>

                    <template x-if="loadingComments">
                        <div class="text-center py-3">
                            <div class="spinner-border spinner-border-sm text-primary"></div>
                        </div>
                    </template>

                    <template x-if="!loadingComments && comments.length === 0">
                        <p class="text-muted text-center py-3 mb-0">No comments yet. Be the first!</p>
                    </template>

                    <div class="comments-list" x-show="comments.length > 0" style="max-height: 400px; overflow-y: auto;">
                        <template x-for="c in comments" :key="c.id">
                            <div class="d-flex gap-2 mb-3 pb-2 border-bottom">
                                <div class="flex-shrink-0">
                                    <div class="rounded-circle bg-secondary d-flex align-items-center justify-content-center text-white" style="width: 36px; height: 36px; font-size: 14px;" x-text="(c.user?.name || '?')[0].toUpperCase()"></div>
                                </div>
                                <div class="flex-grow-1">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <small class="fw-semibold" x-text="c.user?.name || 'Anonymous'"></small>
                                        <small class="text-muted" x-text="c.created_at ? new Date(c.created_at).toLocaleString() : ''"></small>
                                    </div>
                                    <p class="mb-0 small" x-text="c.comment"></p>
                                </div>
                            </div>
                        </template>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('alpine:init', () => {
        Alpine.data('contentDetail', () => ({
            contentId: {{ $id ?? 'null' }},
            loaded: false,
            content: {},
            comments: [],
            newComment: '',
            commenting: false,
            loadingComments: false,
            init() {
                if (this.contentId) {
                    this.loadContent();
                    this.loadComments();
                }
            },
            loadContent() {
                axios.get(`/admin/contents/${this.contentId}`).then(r => {
                    this.content = r.data.data || {};
                    this.loaded = true;
                }).catch(() => {
                    Swal.fire({ icon: 'error', title: 'Error', text: 'Failed to load content.' });
                });
            },
            loadComments() {
                this.loadingComments = true;
                axios.get(`/admin/contents/${this.contentId}/comments`).then(r => {
                    this.comments = r.data.data || [];
                }).finally(() => { this.loadingComments = false; });
            },
            addComment() {
                if (!this.newComment.trim()) return;
                this.commenting = true;
                axios.post(`/admin/contents/${this.contentId}/comments`, { comment: this.newComment }).then(r => {
                    this.comments.push(r.data.data);
                    this.newComment = '';
                }).catch(() => {
                    Swal.fire({ icon: 'error', title: 'Failed', text: 'Could not add comment.' });
                }).finally(() => { this.commenting = false; });
            },
        }));
    });
</script>
@endsection
