<h1>Borrow Notification</h1>
<p>You have borrowed the book: {{ $borrow->book->title }}</p>
<p>Borrow Date: {{ $borrow->borrow_date }}</p>
<p>Please return by: {{ \Carbon\Carbon::parse($borrow->borrow_date)->addDays(14)->toDateString() }}</p>