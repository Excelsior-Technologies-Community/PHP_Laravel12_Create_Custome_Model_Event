<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <meta
        name="csrf-token"
        content="{{ csrf_token() }}"
    >

    <title>
        @yield('title', 'Product Event System')
    </title>

    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
        rel="stylesheet"
    >

    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css"
        rel="stylesheet"
    >

    <style>

        body {
            background: #f5f7fb;
        }

        .navbar-brand {
            font-weight: 700;
        }

        .notification-nav {
            position: relative;
        }

        #notificationBadge {
            position: absolute;
            top: -6px;
            right: -8px;
            font-size: 10px;
            min-width: 18px;
            height: 18px;
            padding: 2px 5px;
            border-radius: 50%;
            display: none;
        }

    </style>

    @stack('styles')

</head>


<body>

<nav class="navbar navbar-expand-lg bg-dark navbar-dark shadow-sm">

    <div class="container-fluid">

        <a
            class="navbar-brand"
            href="{{ route('products.index') }}"
        >
            <i class="bi bi-box-seam me-2"></i>
            Product Event System
        </a>


        <button
            class="navbar-toggler"
            type="button"
            data-bs-toggle="collapse"
            data-bs-target="#mainNavbar"
        >
            <span class="navbar-toggler-icon"></span>
        </button>


        <div
            class="collapse navbar-collapse"
            id="mainNavbar"
        >

            <ul class="navbar-nav me-auto">

                <li class="nav-item">

                    <a
                        class="nav-link"
                        href="{{ route('products.index') }}"
                    >
                        <i class="bi bi-box me-1"></i>
                        Products
                    </a>

                </li>


                <li class="nav-item">

                    <a
                        class="nav-link"
                        href="{{ route('products.dashboard') }}"
                    >
                        <i class="bi bi-bar-chart me-1"></i>
                        Event Dashboard
                    </a>

                </li>


                <li class="nav-item">

                    <a
                        class="nav-link notification-nav"
                        href="{{ route('notifications.index') }}"
                    >

                        <i class="bi bi-bell-fill me-1"></i>
                        Notifications

                        <span
                            id="notificationBadge"
                            class="badge bg-danger"
                        >
                            0
                        </span>

                    </a>

                </li>

            </ul>


            <span class="navbar-text text-light small">

                <i class="bi bi-activity me-1"></i>
                Custom Model Events

            </span>

        </div>

    </div>

</nav>


<main class="container-fluid">

    @if(session('success'))

        <div class="alert alert-success alert-dismissible fade show mt-3">

            <i class="bi bi-check-circle me-2"></i>

            {{ session('success') }}

            <button
                type="button"
                class="btn-close"
                data-bs-dismiss="alert"
            ></button>

        </div>

    @endif


    @if(session('error'))

        <div class="alert alert-danger alert-dismissible fade show mt-3">

            <i class="bi bi-exclamation-triangle me-2"></i>

            {{ session('error') }}

            <button
                type="button"
                class="btn-close"
                data-bs-dismiss="alert"
            ></button>

        </div>

    @endif


    @yield('content')

</main>


<script
    src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
></script>


<script>

document.addEventListener('DOMContentLoaded', function () {

    const badge =
        document.getElementById('notificationBadge');

    function refreshNotificationBadge() {

        fetch('{{ route('notifications.latest') }}?limit=1', {

            headers: {
                'Accept': 'application/json',
            }

        })
        .then(response => response.json())
        .then(data => {

            if (!data.success) {
                return;
            }

            if (!badge) {
                return;
            }

            const count = data.unread_count;

            badge.textContent = count;

            badge.style.display =
                count > 0
                    ? 'inline-block'
                    : 'none';

        })
        .catch(error => {

            console.error(
                'Notification refresh error:',
                error
            );

        });

    }


    /*
    |--------------------------------------------------------------------------
    | Initial Notification Count
    |--------------------------------------------------------------------------
    */

    refreshNotificationBadge();


    /*
    |--------------------------------------------------------------------------
    | Automatic Notification Polling
    |--------------------------------------------------------------------------
    |
    | Refresh notification count every 10 seconds.
    |
    */

    setInterval(
        refreshNotificationBadge,
        10000
    );

});

</script>


@stack('scripts')

</body>

</html>