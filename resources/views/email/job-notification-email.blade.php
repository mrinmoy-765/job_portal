<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Job Notification Email</title>
</head>

<body>
    <h1>New Applicant!!</h1>
    <p>Hello {{ $mailData['employer']->name }}.. New application for your {{ $mailData['job']->title }} job post</p>

    <h4>Employee Details:</h4>
    <p>Name: {{ $mailData['user']->name }}</p>
    <p>Email: {{ $mailData['email']->email }}</p>
    <p>Mobile: {{ $mailData['mobile']->mobile }}</p>
</body>

</html>