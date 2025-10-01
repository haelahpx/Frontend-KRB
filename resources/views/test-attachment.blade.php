<!DOCTYPE html>
<html>
<head>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Test Upload Attachment</title>
    <style>
        input { margin: 4px; padding: 6px; }
        textarea { width: 100%; height: 120px; margin: 6px 0; }
        pre { background: #f3f3f3; padding: 10px; border-radius: 5px; }
    </style>
</head>
<body>
    <h1>Test Upload Attachment</h1>

    <p>Paste JSON dari Cloudinary di bawah, lalu klik <b>Auto Fill</b></p>
    <textarea id="cloudinaryJson" placeholder="Paste response Cloudinary di sini..."></textarea>
    <button type="button" id="autofillBtn">Auto Fill</button>

    <form id="attachForm">
        <input type="hidden" name="ticket_id" value="1">

        <input type="text" name="file_url" placeholder="File URL">
        <input type="text" name="file_type" placeholder="File Type">
        <input type="text" name="cloudinary_public_id" placeholder="Public ID">
        <input type="number" name="bytes" placeholder="Bytes">
        <input type="text" name="original_filename" placeholder="Filename">
        <button type="submit">Save</button>
    </form>

    <h3>Result</h3>
    <pre id="result"></pre>

    <script>
    document.getElementById('autofillBtn').addEventListener('click', () => {
        try {
            const json = JSON.parse(document.getElementById('cloudinaryJson').value);
            const form = document.getElementById('attachForm');

            form.file_url.value = json.secure_url || json.url || '';
            form.file_type.value = json.resource_type === 'image'
                ? `image/${json.format}` : json.format || '';
            form.cloudinary_public_id.value = json.public_id || '';
            form.bytes.value = json.bytes || '';
            form.original_filename.value = (json.original_filename || '') + '.' + (json.format || '');
        } catch (e) {
            alert('JSON tidak valid');
        }
    });

    document.getElementById('attachForm').addEventListener('submit', async (e) => {
        e.preventDefault();
        let form = e.target;
        let data = {
            ticket_id: form.ticket_id.value,
            file_url: form.file_url.value,
            file_type: form.file_type.value,
            cloudinary_public_id: form.cloudinary_public_id.value,
            bytes: form.bytes.value,
            original_filename: form.original_filename.value
        };

        let resp = await fetch('/attachments', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Accept': 'application/json'
            },
            body: JSON.stringify(data)
        });
        let json = await resp.json();
        document.getElementById('result').innerText = JSON.stringify(json, null, 2);
    });
    </script>
</body>
</html>
