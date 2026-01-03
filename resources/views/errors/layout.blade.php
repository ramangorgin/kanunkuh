<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('code', 'خطا') | @yield('title', 'مشکلی پیش آمده است')</title>
    <style>
        :root {
            --bg: linear-gradient(135deg, #0f172a 0%, #1d4ed8 50%, #0ea5e9 100%);
            --card: #ffffff;
            --text: #0f172a;
            --muted: #6b7280;
            --primary: #2563eb;
            --primary-ghost: rgba(37, 99, 235, 0.1);
            --shadow: 0 25px 50px rgba(0, 0, 0, 0.2);
            --radius: 18px;
        }
        * { box-sizing: border-box; }
        body {
            margin: 0;
            min-height: 100vh;
            font-family: 'Peyda', 'Vazirmatn', 'IRANSans', sans-serif;
            background: var(--bg);
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 24px;
            color: var(--text);
        }
        .card {
            width: min(720px, 100%);
            background: var(--card);
            border-radius: var(--radius);
            padding: 32px 28px;
            box-shadow: var(--shadow);
            position: relative;
            overflow: hidden;
        }
        .blur {
            position: absolute;
            inset: 0;
            pointer-events: none;
        }
        .blur::before,
        .blur::after {
            content: '';
            position: absolute;
            border-radius: 999px;
            filter: blur(50px);
            opacity: 0.45;
        }
        .blur::before {
            width: 160px; height: 160px;
            top: -40px; right: -20px;
            background: #c7d2fe;
        }
        .blur::after {
            width: 180px; height: 180px;
            bottom: -60px; left: -40px;
            background: #a5f3fc;
        }
        .content { position: relative; z-index: 1; }
        .code {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 72px; height: 72px;
            border-radius: 18px;
            background: var(--primary-ghost);
            color: var(--primary);
            font-size: 26px;
            font-weight: 800;
            margin-bottom: 18px;
        }
        .title { font-size: 24px; font-weight: 800; margin: 0 0 8px; }
        .lead { font-size: 16px; color: var(--muted); margin: 0 0 18px; line-height: 1.7; }
        .actions { display: flex; flex-wrap: wrap; gap: 10px; }
        .btn {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 12px 16px;
            border-radius: 12px;
            border: 1px solid transparent;
            font-weight: 700;
            text-decoration: none;
            transition: transform 0.15s ease, box-shadow 0.15s ease, background 0.2s ease, border-color 0.2s ease;
        }
        .btn-primary { background: var(--primary); color: #fff; box-shadow: 0 10px 25px rgba(37, 99, 235, 0.2); }
        .btn-primary:hover { transform: translateY(-1px); box-shadow: 0 12px 28px rgba(37, 99, 235, 0.28); }
        .btn-ghost { background: var(--primary-ghost); color: var(--primary); border-color: transparent; }
        .btn-ghost:hover { background: rgba(37, 99, 235, 0.16); }
        .hint { margin-top: 14px; color: var(--muted); font-size: 14px; line-height: 1.7; }
        .icon {
            width: 58px; height: 58px;
            border-radius: 16px;
            background: rgba(37, 99, 235, 0.08);
            display: inline-flex;
            align-items: center; justify-content: center;
            color: var(--primary);
            margin-bottom: 12px;
        }
        @media (max-width: 640px) {
            .card { padding: 26px 22px; }
            .code { width: 64px; height: 64px; font-size: 22px; }
            .title { font-size: 21px; }
            .lead { font-size: 15px; }
            .btn { width: 100%; justify-content: center; }
        }
    </style>
</head>
<body>
    <div class="card">
        <div class="blur"></div>
        <div class="content">
            <div class="icon">
                @yield('icon')
            </div>
            <div class="code">@yield('code', 'خطا')</div>
            <h1 class="title">@yield('title')</h1>
            <p class="lead">@yield('message')</p>
            <div class="actions">
                @yield('actions')
                <a href="{{ url()->previous() }}" class="btn btn-ghost">بازگشت به صفحه قبل</a>
                <a href="{{ route('home') }}" class="btn btn-primary">بازگشت به صفحه اصلی</a>
            </div>
            <p class="hint">اگر مشکل برطرف نشد، لطفا دقایقی دیگر دوباره تلاش کنید یا با پشتیبانی تماس بگیرید.</p>
        </div>
    </div>
</body>
</html>
