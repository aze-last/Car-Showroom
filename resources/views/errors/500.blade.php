{{--
    Intentionally self-contained: a 500 can be caused by a DB outage, and the
    shared public layout queries the database (Setting::get, Livewire badges).
    Styled by hand to match the showroom theme instead.
--}}
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1.0" />
        <title>Something went wrong</title>
        <link rel="preconnect" href="https://fonts.googleapis.com" />
        <link crossorigin href="https://fonts.gstatic.com" rel="preconnect" />
        <link href="https://fonts.googleapis.com/css2?family=Hanken+Grotesk:wght@400;500;600;700&display=swap" rel="stylesheet" />
        <style>
            * { margin: 0; padding: 0; box-sizing: border-box; }
            body {
                min-height: 100vh;
                display: flex;
                align-items: center;
                justify-content: center;
                background-color: #fafafa;
                color: #18181b;
                font-family: 'Hanken Grotesk', ui-sans-serif, system-ui, sans-serif;
                -webkit-font-smoothing: antialiased;
                padding: 1rem;
            }
            .card { max-width: 36rem; width: 100%; text-align: center; padding: 6rem 0; }
            .eyebrow {
                font-size: 10px;
                font-weight: 900;
                text-transform: uppercase;
                letter-spacing: 0.2em;
                color: #a1a1aa;
                margin-bottom: 1.5rem;
            }
            h1 {
                font-size: 3.5rem;
                font-weight: 700;
                letter-spacing: -0.05em;
                color: #000;
                margin-bottom: 1.5rem;
            }
            p { font-size: 0.875rem; color: #71717a; line-height: 1.7; margin-bottom: 2.5rem; }
            a.btn {
                display: inline-flex;
                align-items: center;
                justify-content: center;
                font-size: 13px;
                font-weight: 700;
                text-transform: uppercase;
                letter-spacing: 0.2em;
                background: #000;
                color: #fff;
                border-radius: 0.75rem;
                padding: 0 2rem;
                height: 3rem;
                text-decoration: none;
                box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
                transition: background-color 0.3s;
            }
            a.btn:hover { background: #27272a; }
        </style>
    </head>
    <body>
        <div class="card">
            <p class="eyebrow">Error 500</p>
            <h1>Something went wrong</h1>
            <p>An unexpected problem occurred on our end. Our team has been notified — please try again in a moment.</p>
            <a class="btn" href="{{ url('/') }}">Back to the Showroom</a>
        </div>
    </body>
</html>
