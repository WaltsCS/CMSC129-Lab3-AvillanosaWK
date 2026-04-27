<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'LibAlexandria')</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f5f7fb;
            margin: 0;
            padding: 0;
            color: #222;
        }

        .container {
            max-width: 1100px;
            margin: 40px auto;
            background: #fff;
            padding: 24px;
            border-radius: 12px;
            box-shadow: 0 4px 14px rgba(0, 0, 0, 0.08);
        }

        h1 {
            margin-top: 0;
            margin-bottom: 20px;
            color: #1f3b73;
        }

        .top-links {
            margin-bottom: 20px;
        }

        .top-links a,
        .btn,
        button {
            display: inline-block;
            padding: 8px 14px;
            margin-right: 8px;
            margin-bottom: 8px;
            border: none;
            border-radius: 6px;
            text-decoration: none;
            background: #1f6feb;
            color: white;
            cursor: pointer;
            font-size: 14px;
        }

        .top-links a.secondary,
        .btn.secondary {
            background: #6c757d;
        }

        .btn.warning,
        button.warning {
            background: #d97706;
        }

        .btn.danger,
        button.danger {
            background: #dc3545;
        }

        .btn.success,
        button.success {
            background: #198754;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 12px;
            background: white;
        }

        th, td {
            border: 1px solid #d0d7de;
            padding: 10px;
            text-align: left;
            vertical-align: middle;
        }

        th {
            background: #eef3fb;
            color: #1f3b73;
        }

        tr:nth-child(even) {
            background: #fafbfc;
        }

        .alert-success {
            background: #d1e7dd;
            color: #0f5132;
            padding: 12px;
            border-radius: 6px;
            margin-bottom: 16px;
        }

        .alert-warning {
            background: #fff3cd;
            color: #664d03;
            padding: 12px;
            border-radius: 6px;
            margin-bottom: 16px;
        }

        .form-group {
            margin-bottom: 16px;
        }

        label {
            display: block;
            font-weight: bold;
            margin-bottom: 6px;
        }

        input[type="text"],
        input[type="number"],
        input[type="file"],
        textarea {
            width: 100%;
            max-width: 500px;
            padding: 10px;
            border: 1px solid #cbd5e1;
            border-radius: 6px;
            box-sizing: border-box;
        }

        textarea {
            min-height: 100px;
            resize: vertical;
        }

        .error {
            color: #dc3545;
            font-size: 14px;
            margin-top: 5px;
        }

        .cover-thumb {
            width: 80px;
            height: auto;
            border-radius: 6px;
            border: 1px solid #ddd;
        }

        .cover-large {
            width: 180px;
            height: auto;
            border-radius: 8px;
            border: 1px solid #ddd;
            margin-top: 8px;
        }

        .actions-inline {
            display: flex;
            flex-wrap: wrap;
            gap: 6px;
            align-items: center;
        }

        .actions-inline form {
            display: inline;
            margin: 0;
        }

        .details p {
            margin: 10px 0;
        }

        .muted {
            color: #666;
        }

        .modal-overlay {
            position: fixed;
            inset: 0;
            background: rgba(0, 0, 0, 0.45);
            display: none;
            align-items: center;
            justify-content: center;
            z-index: 9999;
        }

        .modal-box {
            background: #fff;
            width: 90%;
            max-width: 420px;
            border-radius: 12px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.2);
            padding: 24px;
        }

        .modal-box h3 {
            margin-top: 0;
            margin-bottom: 12px;
            color: #1f3b73;
        }

        .modal-box p {
            margin-bottom: 20px;
            color: #444;
        }

        .modal-actions {
            display: flex;
            justify-content: flex-end;
            gap: 10px;
        }

        .modal-actions button {
            margin: 0;
        }
    </style>
</head>
<div id="confirmModal" class="modal-overlay">
    <div class="modal-box">
        <h3 id="confirmTitle">Confirm Action</h3>
        <p id="confirmMessage">Are you sure you want to continue?</p>

        <div class="modal-actions">
            <button type="button" class="btn secondary" onclick="closeConfirmModal()">Cancel</button>
            <button type="button" class="btn danger" id="confirmActionBtn">Confirm</button>
        </div>
    </div>
</div>
<script>
    let currentFormToSubmit = null;

    function openConfirmModal(formId, title, message, buttonText = 'Confirm') {
        currentFormToSubmit = document.getElementById(formId);

        document.getElementById('confirmTitle').textContent = title;
        document.getElementById('confirmMessage').textContent = message;
        document.getElementById('confirmActionBtn').textContent = buttonText;

        document.getElementById('confirmModal').style.display = 'flex';
    }

    function closeConfirmModal() {
        currentFormToSubmit = null;
        document.getElementById('confirmModal').style.display = 'none';
    }

    document.getElementById('confirmActionBtn').addEventListener('click', function () {
        if (currentFormToSubmit) {
            currentFormToSubmit.submit();
        }
    });

    window.addEventListener('click', function (e) {
        const modal = document.getElementById('confirmModal');
        if (e.target === modal) {
            closeConfirmModal();
        }
    });
</script>
<body>
    <div class="container">
        @yield('content')
    </div>
</body>
</html>