{{-- Shared admin post form fields. --}}
@csrf

<div class="row g-4">
    <div class="col-lg-8">
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-body">
                <div class="mb-3">
                    <label class="form-label">عنوان <span class="text-danger">*</span></label>
                    <input type="text" name="title" id="title" class="form-control" value="{{ old('title', $post->title ?? '') }}" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">نامک (Slug)</label>
                    <input type="text" name="slug" id="slug" class="form-control" value="{{ old('slug', $post->slug ?? '') }}" placeholder="auto-generated" dir="ltr">
                    <small class="text-muted">پیش‌نمایش: <span id="slug-preview"></span></small>
                </div>

                <div class="mb-3">
                    <label class="form-label">خلاصه</label>
                    <textarea name="excerpt" class="form-control" rows="3">{{ old('excerpt', $post->excerpt ?? '') }}</textarea>
                </div>

                <div class="mb-3">
                    <label class="form-label">محتوا <span class="text-danger">*</span></label>
                    <textarea name="content" id="content-editor" class="form-control" rows="10">{{ old('content', $post->content ?? '') }}</textarea>
                </div>
            </div>
        </div>

        <div class="card border-0 shadow-sm mb-4">
            <div class="card-body">
                <h6 class="fw-bold mb-3"><i class="bi bi-search text-primary me-2"></i> تنظیمات سئو</h6>
                <div class="mb-3">
                    <label class="form-label">عنوان سئو</label>
                    <input type="text" name="seo_title" id="seo_title" class="form-control" value="{{ old('seo_title', $post->seo_title ?? '') }}">
                </div>
                <div class="mb-3">
                    <label class="form-label">توضیحات سئو</label>
                    <textarea name="seo_description" id="seo_description" class="form-control" rows="2">{{ old('seo_description', $post->seo_description ?? '') }}</textarea>
                </div>
                <div class="mb-3">
                    <label class="form-label">کلمات کلیدی (با کاما جدا کنید)</label>
                    <textarea name="seo_keywords" class="form-control" rows="2">{{ old('seo_keywords', $post->seo_keywords ?? '') }}</textarea>
                </div>
                <div class="mb-3">
                    <label class="form-label">آدرس canonical</label>
                    <input type="url" name="canonical_url" id="canonical_url" class="form-control" value="{{ old('canonical_url', $post->canonical_url ?? '') }}" dir="ltr">
                </div>

                <div class="bg-light rounded p-3">
                    <div class="mb-2 text-muted" style="font-size: 13px;">پیش‌نمایش نتایج گوگل</div>
                    <div class="seo-preview">
                        <div id="seo-preview-title" class="fw-bold text-primary" style="font-size:18px;">{{ old('seo_title', $post->seo_title ?? $post->title ?? 'عنوان صفحه') }}</div>
                        <div id="seo-preview-url" class="text-success" style="font-size:13px;">{{ rtrim(config('app.url'), '/') }}/blog/<span id="seo-preview-slug">{{ old('slug', $post->slug ?? '') }}</span></div>
                        <div id="seo-preview-desc" class="text-muted" style="font-size:14px;">{{ old('seo_description', $post->seo_description ?? $post->excerpt ?? 'توضیحات متا اینجا نمایش داده می‌شود.') }}</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-body">
                <div class="mb-3">
                    <label class="form-label">وضعیت</label>
                    <select name="status" id="status" class="form-select">
                        <option value="draft" {{ old('status', $post->status ?? 'draft') === 'draft' ? 'selected' : '' }}>پیش‌نویس</option>
                        <option value="published" {{ old('status', $post->status ?? 'draft') === 'published' ? 'selected' : '' }}>منتشر شده</option>
                    </select>
                </div>
                <div class="mb-3">
                    <label class="form-label">تاریخ انتشار</label>
                    <div class="input-group">
                        <input type="text" name="published_at" id="published_at" class="form-control" data-jdp data-jdp-time="true" value="{{ old('published_at', isset($post) && $post->published_at ? $post->published_at->format('Y-m-d H:i') : '') }}" autocomplete="off">
                        <span class="input-group-text"><i class="bi bi-calendar"></i></span>
                    </div>
                </div>
                <div class="form-check form-switch mb-2">
                    <input class="form-check-input" type="checkbox" name="is_indexable" id="is_indexable" value="1" {{ old('is_indexable', $post->is_indexable ?? true) ? 'checked' : '' }}>
                    <label class="form-check-label" for="is_indexable">اجازه ایندکس</label>
                </div>
                <div class="form-check form-switch mb-3">
                    <input class="form-check-input" type="checkbox" name="is_followable" id="is_followable" value="1" {{ old('is_followable', $post->is_followable ?? true) ? 'checked' : '' }}>
                    <label class="form-check-label" for="is_followable">اجازه فالو</label>
                </div>

                <div class="mb-3">
                    <label class="form-label">دسته‌بندی‌ها</label>
                    <select name="categories[]" id="categories" class="form-select" multiple>
                        @foreach($categories as $category)
                            <option value="{{ $category->id }}" {{ in_array($category->id, old('categories', isset($post) ? $post->categories->pluck('id')->toArray() : [])) ? 'selected' : '' }}>{{ $category->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="mb-3">
                    <label class="form-label">تصویر شاخص</label>
                    <input type="file" name="featured_image" class="form-control" accept="image/*">
                    @if(isset($post) && $post->featured_image)
                        <div class="mt-2">
                            <img src="{{ asset('storage/'.$post->featured_image) }}" alt="{{ $post->featured_image_alt }}" class="img-fluid rounded" style="max-height: 140px;">
                        </div>
                    @endif
                </div>
                <div class="mb-3">
                    <label class="form-label">متن جایگزین تصویر شاخص</label>
                    <input type="text" name="featured_image_alt" class="form-control" value="{{ old('featured_image_alt', $post->featured_image_alt ?? '') }}">
                </div>
            </div>
        </div>

        <div class="d-flex justify-content-between">
            <a href="{{ route('admin.posts.index') }}" class="btn btn-light border"><i class="bi bi-arrow-right"></i> بازگشت</a>
            <button type="submit" class="btn btn-primary"><i class="bi bi-save"></i> ذخیره</button>
        </div>
    </div>
</div>

@push('scripts')
<!--
<script src="https://cdn.ckeditor.com/ckeditor5/40.2.0/classic/ckeditor.js"></script>
<script>
-->
    const appUrl = @json(rtrim(config('app.url'), '/'));
    const csrfToken = @json(csrf_token());
    const slugInput = document.getElementById('slug');
    const titleInput = document.getElementById('title');
    const seoTitleInput = document.getElementById('seo_title');
    const seoDescInput = document.getElementById('seo_description');
    const slugPreview = document.getElementById('slug-preview');
    const seoPreviewSlug = document.getElementById('seo-preview-slug');
    const seoPreviewTitle = document.getElementById('seo-preview-title');
    const seoPreviewDesc = document.getElementById('seo-preview-desc');
    const seoPreviewUrl = document.getElementById('seo-preview-url');

    function slugify(text) {
        return text
            .toString()
            .toLowerCase()
            .trim()
            .replace(/\s+/g, '-')
            .replace(/[^\w\-]+/g, '')
            .replace(/\-\-+/g, '-');
    }

    function updateSlugPreview() {
        const slug = slugInput.value || slugify(titleInput.value);
        slugPreview.innerText = `${appUrl}/blog/${slug}`;
        seoPreviewSlug.innerText = slug;
        seoPreviewUrl.innerText = `${appUrl}/blog/${slug}`;
    }

    function updateSeoPreview() {
        seoPreviewTitle.innerText = seoTitleInput.value || titleInput.value || 'عنوان صفحه';
        seoPreviewDesc.innerText = seoDescInput.value || document.querySelector('textarea[name="excerpt"]').value || 'توضیحات متا اینجا نمایش داده می‌شود.';
    }

    titleInput.addEventListener('input', () => {
        if (!slugInput.value) {
            slugInput.value = slugify(titleInput.value);
        }
        updateSlugPreview();
        updateSeoPreview();
    });
    slugInput.addEventListener('input', updateSlugPreview);
    seoTitleInput.addEventListener('input', updateSeoPreview);
    seoDescInput.addEventListener('input', updateSeoPreview);
    document.querySelector('textarea[name="excerpt"]').addEventListener('input', updateSeoPreview);

    updateSlugPreview();
    updateSeoPreview();

    $('#categories').select2({
        theme: 'bootstrap-5',
        width: '100%',
        placeholder: 'انتخاب دسته‌بندی'
    });

    class CustomUploadAdapter {
        constructor(loader, editor) {
            this.loader = loader;
            this.editor = editor;
        }

        upload() {
            return this.loader.file.then(file => new Promise((resolve, reject) => {
                const alt = prompt('لطفاً متن جایگزین تصویر (alt) را وارد کنید');
                if (!alt) {
                    reject('متن جایگزین تصویر الزامی است.');
                    return;
                }

                const data = new FormData();
                data.append('upload', file);
                data.append('alt', alt);

                fetch(@json(route('admin.posts.uploadImage')), {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': csrfToken,
                    },
                    body: data
                })
                .then(response => response.json())
                .then(result => {
                    if (result && result.uploaded) {
                        this.editor.model.change(writer => {
                            const imageElement = this.editor.model.document.selection.getSelectedElement();
                            if (imageElement && imageElement.is('element', 'image')) {
                                writer.setAttribute('alt', alt, imageElement);
                            }
                        });
                        resolve({ default: result.url });
                    } else {
                        reject(result?.error?.message || 'آپلود تصویر ناموفق بود');
                    }
                })
                .catch(() => reject('خطا در آپلود تصویر'));
            }));
        }

        abort() {}
    }

    function CustomUploadAdapterPlugin(editor) {
        editor.plugins.get('FileRepository').createUploadAdapter = (loader) => new CustomUploadAdapter(loader, editor);
    }

    ClassicEditor.create(document.querySelector('#content-editor'), {
        extraPlugins: [CustomUploadAdapterPlugin],
        language: 'fa',
        toolbar: {
            items: [
                'heading', '|', 'bold', 'italic', 'link', 'bulletedList', 'numberedList', 'blockQuote', '|',
                'insertTable', 'imageUpload', 'mediaEmbed', 'undo', 'redo'
            ]
        },
        image: {
            toolbar: ['imageTextAlternative', 'imageStyle:full', 'imageStyle:side']
        }
    }).catch(error => console.error(error));
</script>
@endpush
