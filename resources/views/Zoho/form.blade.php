<!DOCTYPE html>
<html>
<head>
    <title>Create Deal and Account</title>
</head>
<body>


@if(session('success'))
    <div>{{ session('success') }}</div>
@endif

<form action="/form/submit" method="POST">
    @csrf
    <label for="deal_name">Deal Name:</label>
    <input type="text" id="deal_name" name="deal_name"><br>

    <label for="deal_stage">Deal Stage:</label>
    <input type="text" id="deal_stage" name="deal_stage"><br>

    <label for="account_name">Account Name:</label>
    <input type="text" id="account_name" name="account_name"><br>

    <label for="account_website">Account Website:</label>
    <input type="url" id="account_website" name="account_website"><br>

    <label for="account_phone">Account Phone:</label>
    <input type="text" id="account_phone" name="account_phone"><br>

    <button type="submit">Submit</button>
</form>
</body>
</html>
