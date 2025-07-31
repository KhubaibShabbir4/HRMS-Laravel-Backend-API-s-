<!DOCTYPE html>
<html>
<head>
    <title>Performance Review</title>
</head>
<body>
<h2>Hello {{ $review->user->name }},</h2>
<p>Here is your performance review:</p>

<ul>
    <li><strong>Rating:</strong> {{ $review->rating }}</li>
    <li><strong>Feedback:</strong> {{ $review->feedback ?? 'No feedback provided' }}</li>
    <li><strong>Review Date:</strong> {{ \Carbon\Carbon::parse($review->review_date)->format('d M Y') }}</li>
</ul>

<p>Keep up the good work!</p>
</body>
</html>
