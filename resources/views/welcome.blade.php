<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Al Falah Bakery</title>
    <link href="https://fonts.bunny.net/css2?family=Playfair+Display:wght@500;700&family=Poppins:wght@300;400;500&display=swap" rel="stylesheet">
    @vite('resources/css/app.css')
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            color: #eee;
            background: #111;
            overflow-x: hidden;
        }

        .hero {
            position: relative;
            height: 100vh;
            background: #0d0d0d;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            text-align: center;
        }

        .hero::before {
            content: '';
            position: absolute;
            inset: 0;
            background-image: url('https://images.unsplash.com/photo-1608198093002-ad4e005484ec?auto=format&fit=crop&w=1600&q=80');
            background-size: cover;
            background-position: center;
            filter: brightness(35%) blur(3px);
            z-index: 0;
        }

        .gold-text {
            background: linear-gradient(90deg, #d4af37, #f5d76e);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .btn-gold {
            background: linear-gradient(90deg, #d4af37, #f5d76e);
            color: #111;
            transition: all 0.3s ease;
        }

        .btn-gold:hover {
            background: linear-gradient(90deg, #f5d76e, #d4af37);
            transform: scale(1.05);
        }

        .section {
            background: #1b1b1b;
        }

        footer {
            background: #0d0d0d;
        }
    </style>
</head>
<body>

    <!-- Hero Section -->
    <section class="hero relative px-6">
        <div class="relative z-10">
            <h1 class="text-5xl md:text-7xl font-[Playfair_Display] mb-4 gold-text tracking-wide">
                Al Falah Bakery
            </h1>
            <p class="text-lg md:text-2xl font-light mb-8 max-w-2xl mx-auto text-gray-300">
                Cita rasa premium, sentuhan elegan, dan kehangatan di setiap gigitan.
            </p>

            <a href="{{ url('/admin/login') }}"
               class="btn-gold font-semibold px-8 py-3 rounded-full text-lg shadow-lg inline-block">
                Masuk ke Admin
            </a>
        </div>
    </section>

    <!-- About Section -->
    <section class="section py-20 text-center px-6">
        <h2 class="text-4xl font-[Playfair_Display] mb-6 gold-text">Kelezatan yang Tak Terlupakan</h2>
        <p class="max-w-3xl mx-auto text-gray-400 text-lg leading-relaxed">
            Al Falah Bakery menghadirkan roti, kue, dan pastry premium dengan bahan pilihan,
            dibuat dengan cinta oleh tangan profesional. Setiap gigitan adalah kisah kehangatan dan kemewahan.
        </p>
    </section>

    <!-- Footer -->
    <footer class="text-gray-500 py-6 text-center text-sm">
        &copy; {{ date('Y') }} <span class="gold-text font-semibold">Al Falah Bakery</span>. Semua hak dilindungi.
    </footer>

</body>
</html>
