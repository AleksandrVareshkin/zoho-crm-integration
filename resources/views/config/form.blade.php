<!-- resources/views/config/form.blade.php -->
<!DOCTYPE html>
<html>
<head>
    <title>Zoho Configuration</title>
</head>
<body>
<h1>Zoho Configuration</h1>
@if (session('status'))
    <p>{{ session('status') }}</p>
@endif
<form action="{{ route('config.save') }}" method="POST">
    @csrf
    <div>
        <label for="client_id">Client ID</label>
        <input type="text" id="client_id" name="client_id" required>
    </div>
    <div>
        <label for="client_secret">Client Secret</label>
        <input type="text" id="client_secret" name="client_secret" required>
    </div>
    <button type="submit">Save</button>
</form>
</body>
</html>
