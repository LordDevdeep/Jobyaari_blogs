<script>
    // Custom UploadAdapter for CKEditor → posts to our /admin/blogs/upload-image endpoint.
    class JYUploadAdapter {
        constructor(loader) { this.loader = loader; }
        upload() {
            return this.loader.file.then(file => new Promise((resolve, reject) => {
                const formData = new FormData();
                formData.append('upload', file);
                formData.append('_token', '{{ csrf_token() }}');
                fetch('{{ route('admin.blogs.upload-image') }}', {
                    method: 'POST',
                    body: formData,
                    headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                }).then(r => r.json())
                  .then(json => json.url ? resolve({ default: json.url }) : reject('Upload failed'))
                  .catch(err => reject(err));
            }));
        }
        abort() {}
    }
    function JYUploadAdapterPlugin(editor) {
        editor.plugins.get('FileRepository').createUploadAdapter = loader => new JYUploadAdapter(loader);
    }

    let editorInstance;
    ClassicEditor.create(document.querySelector('#content'), {
        extraPlugins: [JYUploadAdapterPlugin],
        toolbar: {
            items: [
                'heading', '|',
                'bold', 'italic', 'underline', 'link', '|',
                'bulletedList', 'numberedList', 'blockQuote', '|',
                'insertTable', 'imageUpload', '|',
                'undo', 'redo', 'removeFormat', 'sourceEditing'
            ]
        },
        heading: {
            options: [
                { model: 'paragraph', title: 'Paragraph', class: 'ck-heading_paragraph' },
                { model: 'heading1', view: 'h1', title: 'Heading 1', class: 'ck-heading_heading1' },
                { model: 'heading2', view: 'h2', title: 'Heading 2', class: 'ck-heading_heading2' },
                { model: 'heading3', view: 'h3', title: 'Heading 3', class: 'ck-heading_heading3' },
                { model: 'heading4', view: 'h4', title: 'Heading 4', class: 'ck-heading_heading4' },
            ]
        },
        table: { contentToolbar: ['tableColumn', 'tableRow', 'mergeTableCells'] },
    }).then(editor => { editorInstance = editor; })
      .catch(err => console.error(err));

    // Short description char counter
    const shortInput = document.getElementById('short_description');
    const shortCount = document.getElementById('shortCount');
    function updateShortCount() { shortCount.textContent = shortInput.value.length; }
    if (shortInput && shortCount) {
        shortInput.addEventListener('input', updateShortCount);
        updateShortCount();
    }

    // Image preview
    const imageInput = document.getElementById('image');
    const imagePreview = document.getElementById('imagePreview');
    if (imageInput) {
        imageInput.addEventListener('change', e => {
            const file = e.target.files?.[0];
            if (!file) return;
            const reader = new FileReader();
            reader.onload = ev => {
                imagePreview.style.display = '';
                imagePreview.querySelector('img').src = ev.target.result;
            };
            reader.readAsDataURL(file);
        });
    }
</script>
