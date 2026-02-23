<!DOCTYPE html>
<html>
<head>
    <style>
        body { font-family: sans-serif; background-color: #f4f7f6; padding: 20px; }
        .container { background: white; padding: 20px; border-radius: 8px; max-width: 600px; margin: auto; }
        h2 { color: #2c3e50; }
        .task { background: #ecf0f1; padding: 10px; margin-bottom: 10px; border-left: 5px solid #4a90e2; border-radius: 4px; }
        .title { font-weight: bold; font-size: 16px; }
        .date { font-size: 12px; color: #7f8c8d; }
    </style>
</head>
<body>
<div class="container">
    <h2>Günaydın Hasan! ☕</h2>
    <p>Bugün ({{ \Carbon\Carbon::now()->format('d.m.Y') }}) takviminde yer alan görevlerin aşağıda listelenmiştir. İyi çalışmalar!</p>

    @foreach($tasks as $task)
        <div class="task" style="border-left-color: {{ $task->color }}">
            <div class="title">{{ $task->title }}</div>
            <div class="date">
                {{ \Carbon\Carbon::parse($task->start_date)->format('d M') }} -
                {{ \Carbon\Carbon::parse($task->end_date)->format('d M') }}
            </div>
        </div>
    @endforeach

    <p style="margin-top: 30px; font-size: 12px; color: #bdc3c7; text-align: center;">
        Bu e-posta Kişisel Planlayıcı asistanın tarafından otomatik gönderilmiştir.
    </p>
</div>
</body>
</html>
