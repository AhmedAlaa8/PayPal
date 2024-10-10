<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>
</head>

<body>


    <form action="{{ route('pay') }}" method="post">
        @csrf
        <input type="text" name="name" value="Ahmed">
        <input type="text" name="quan" value="2">
        <button type="submit">clic</button>
    </form>

</body>

</html>
