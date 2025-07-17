@extends('layout.app')

<link href="{{url('/')}}/assets/vendor/css/frontend.css" rel="stylesheet" />
@section('content')
<div class="custom-container">
    <div class="main-window">
        <nav>
            <div class="nav-container">
                <div class="logo">CakeBox</div>
                <input type="checkbox" id="nav-toggle" />
                <label for="nav-toggle" class="nav-toggle-label">&#9776;</label>
                <ul>
                    <li><a href="#">Home</a></li>
                    <li><a href="#">About</a></li>
                    <li><a href="#">Contact Us</a></li>
                    @if(Auth::check() && Auth::user()->isAdmin === 0)
                        <li><a href="{{route('order')}}">Place Order</a></li>
                        <li><a href="{{route('your_orders')}}">Your Orders</a></li>
                        <li><a href="{{route('profile')}}">Profile</a></li>
                        <!-- <li><p href="#">Last Login : <br />{{date("l, F j, Y g:i A", strtotime(Auth::user()->last_login))}}</p></li> -->
                        <li><a href="{{route('logout_user')}}">Logout</a></li>
                    @elseif(Auth::check() && Auth::user()->isAdmin === 1)
                        <li><a href="{{route('admin_dashboard')}}">Dashboard</a></li>
                        <!-- <li><p href="#">Last Login : <br />{{date("l, F j, Y g:i A", strtotime(Auth::user()->last_login))}}</p></li> -->
                        <li><button onclick="askNotificationPermission()">Enable Notifications</button></li>
                        <li><a href="{{route('logout_admin')}}">Logout</a></li>
                    @endif
                    @if(!Auth::check())
                        <li><button class="btn btn-success" onclick="window.location='{{ route('user-login') }}'">Sign In</button>
                    @endif
                </ul>
            </div>
        </nav>

        <div class="carousel">
            <div class="slides" id="slides">
                <div class="slide" style="background-image: url('{{ asset('assets/vendor/imgs/banner/cake1.jpg') }}');"></div>
                <div class="slide" style="background-image: url('{{ asset('assets/vendor/imgs/banner/cake2.jpg') }}');"></div>
                <div class="slide" style="background-image: url('{{ asset('assets/vendor/imgs/banner/cake3.jpg') }}');"></div>
                <div class="slide" style="background-image: url('{{ asset('assets/vendor/imgs/banner/cake4.jpg') }}');"></div>
                <div class="slide" style="background-image: url('{{ asset('assets/vendor/imgs/banner/cake5.jpg') }}');"></div>
                <div class="slide" style="background-image: url('{{ asset('assets/vendor/imgs/banner/cake6.jpg') }}');"></div>
                <div class="slide" style="background-image: url('{{ asset('assets/vendor/imgs/banner/cake7.jpg') }}');"></div>
                <div class="slide" style="background-image: url('{{ asset('assets/vendor/imgs/banner/cake8.jpg') }}');"></div>
                <div class="slide" style="background-image: url('{{ asset('assets/vendor/imgs/banner/cake9.jpg') }}');"></div>
                <div class="slide" style="background-image: url('{{ asset('assets/vendor/imgs/banner/cake10.jpg') }}');"></div>
            </div>
            <!-- <div class="controls">
                <button class="control" id="prev">&#10094;</button>
                <button class="control" id="next">&#10095;</button>
            </div> -->
        </div>

        <!-- <div class="dots" id="dots"></div> -->
    </div>
    <div class="intro-window">
        <section class="intro-section" id="intro">
            <div class="intro-container">
                <h3 class="intro-title">Welcome to CakeBox</h3>
                <p class="intro-text">
                    We are dedicated to providing you with the best experience possible. Explore our expertise in making a wide range of cakes, learn more about what we offer.
                </p>
                <div>
                    <button class="intro-btn">Learn More</button>
                </div>
                <div>
                    <img src="{{ asset('/assets/vendor/imgs/cake1.png') }}" class="intro-img">
                </div>
            </div>
        </section>
    </div>
    <div class="feature-window">
        <section class="features-section">
            <h2 class="section-title">Our Services</h2>
            <div class="features-container">
                <div class="feature-card">
                <div class="feature-icon">💡</div>
                <h3 class="feature-title">Creative Design</h3>
                <p class="feature-desc">Unique and modern design for any occassion.</p>
                </div>
                <div class="feature-card">
                <div class="feature-icon">⚙️</div>
                <h3 class="feature-title">Custom Solutions</h3>
                <p class="feature-desc">Tailored solutions built to your needs.</p>
                </div>
                <div class="feature-card">
                <div class="feature-icon">🚀</div>
                <h3 class="feature-title">Secure Delivery</h3>
                <p class="feature-desc">Fast and secure delivery.</p>
                </div>
            </div>
        </section>
    </div>
    <div class="testimonial-window">
        <div class="testimonials-section">
            <h2 class="testimonial-title">Happy Clients</h2>
            <div class="testimonial-container">
                <div class="testimonial" id="testimonial">
                <img src="https://i.pravatar.cc/100?img=1" alt="User" id="user-img" />
                <p class="quote" id="quote">"Amazing service! The cake was delicious."</p>
                <h3 id="name">John Doe</h3>
                <span id="role">Customer</span>
                </div>

                <div class="buttons">
                <button onclick="prevTestimonial()">&#8592;</button>
                <button onclick="nextTestimonial()">&#8594;</button>
                </div>
            </div>
        </div>
    </div>
    <div class="footer-window">
        <footer class="footer">
            <div class="footer-container">
                <div class="footer-column">
                <h3>About Us</h3>
                <p>We bake joy! Order cakes online for every occasion. Fresh, delicious, and delivered fast.</p>
                </div>

                <div class="footer-column">
                <h3>Quick Links</h3>
                <ul>
                    <li><a href="/">Home</a></li>
                    <li><a href="/cakes">Cakes</a></li>
                    <li><a href="/about">About</a></li>
                    <li><a href="/contact">Contact</a></li>
                </ul>
                </div>

                <div class="footer-column">
                <h3>Follow Us</h3>
                <div class="social-icons">
                    <a href="#"><img src="{{asset('/assets/vendor/imgs/meta.png')}}" alt="Meta"></a>
                    <a href="#"><img src="{{asset('/assets/vendor/imgs/insta.png')}}" alt="Instagram"></a>
                    <a href="#"><img src="{{asset('/assets/vendor/imgs/x.png')}}" alt="X"></a>
                </div>
                </div>
            </div>
            <div class="footer-bottom">
                <p>&copy; <span id="year"></span> CakeBox. All rights reserved.</p>
            </div>
        </footer>
    </div>
</div>
@endsection
<script src="{{url('/')}}/assets/vendor/js/jquery.min.js"></script>
<script>
    $(document).ready(function() {
        const slides = document.getElementById("slides");
        //const dotsContainer = document.getElementById("dots");
        const totalSlides = slides.children.length;
        let index = 0;

        // Generate dots
        // for (let i = 0; i < totalSlides; i++) {
        //     const dot = document.createElement("span");
        //     dot.classList.add("dot");
        //     if (i === 0) dot.classList.add("active");
        //     dot.addEventListener("click", () => showSlide(i));
        //     dotsContainer.appendChild(dot);
        // }

        //const dots = document.querySelectorAll(".dot");

        function showSlide(i) {
            index = (i + totalSlides) % totalSlides;
            slides.style.transform = `translateX(-${index * 100}%)`;
            //dots.forEach(dot => dot.classList.remove("active"));
            //dots[index].classList.add("active");
        }

        // document.getElementById("prev").onclick = () => showSlide(index - 1);
        // document.getElementById("next").onclick = () => showSlide(index + 1);

        // Auto play every 5 seconds
        setInterval(() => showSlide(index + 1), 3000);
    });
</script>
<script>
  window.addEventListener('scroll', () => {
    const intro = document.querySelector('.intro-container');
    const trigger = window.innerHeight / 1.2;

    const introTop = intro.getBoundingClientRect().top;

    if (introTop < trigger) {
      intro.classList.add('visible');
    }
  });
</script>
<script>
  window.addEventListener('scroll', () => {
    const features = document.querySelectorAll('.feature-card');
    features.forEach((card, index) => {
      const rect = card.getBoundingClientRect();
      if (rect.top < window.innerHeight - 50) {
        card.style.opacity = 1;
        card.style.transform = 'translateY(0)';
      }
    });
  });

  // Initial styles (add to CSS if using this JS)
  document.querySelectorAll('.feature-card').forEach(card => {
    card.style.opacity = 0;
    card.style.transform = 'translateY(20px)';
    card.style.transition = 'all 0.6s ease';
  });
</script>
<script>
    const testimonials = [
    {
        name: "John Doe",
        quote: "Amazing service! The cake was delicious.",
        img: "https://i.pravatar.cc/100?img=1",
        role: "Customer"
    },
    {
        name: "Jane Smith",
        quote: "Best online cake ordering experience I've had!",
        img: "https://i.pravatar.cc/100?img=2",
        role: "Regular Buyer"
    },
    {
        name: "Arun Kumar",
        quote: "Timely delivery and beautifully designed cake.",
        img: "https://i.pravatar.cc/100?img=3",
        role: "Customer"
    }
    ];

    let index = 0;

    function updateTestimonial() {
        const t = testimonials[index];
        document.getElementById("name").textContent = t.name;
        document.getElementById("quote").textContent = `"${t.quote}"`;
        document.getElementById("user-img").src = t.img;
        document.getElementById("role").textContent = t.role;
    }

    function nextTestimonial() {
        index = (index + 1) % testimonials.length;
        updateTestimonial();
    }

    function prevTestimonial() {
        index = (index - 1 + testimonials.length) % testimonials.length;
        updateTestimonial();
    }

    // Optional: auto-slide
    setInterval(nextTestimonial, 5000); // every 5 seconds

    updateTestimonial(); // initialize
</script>
<script>
  document.getElementById("year").textContent = new Date().getFullYear();
</script>
<script>
    async function askNotificationPermission() {
        if ('serviceWorker' in navigator && 'PushManager' in window) {
            navigator.serviceWorker.register('/service-worker.js', {
                scope: '/',
                type: 'classic',
            })
            .then(reg => {
                console.log('Service worker registered:', reg);
            })
            .catch(err => {
                console.error('Service worker registration failed:', err);
            });
        }
        const registration = await navigator.serviceWorker.register('/service-worker.js');
        const ready = await navigator.serviceWorker.ready;
        const subscription = await ready.pushManager.subscribe({
            userVisibleOnly: true,
            applicationServerKey: urlBase64ToUint8Array("BH9E2StT6Y1ZvCdLCiUOQkdK2Ig7NReQ7PTYna_MGaQ_wj9UB4JKOI2TnDihWnA8s9Fj9D243YC9VCR1OSafGUI")
        });
        await fetch('/api/v1/push/subscribe', {
            method: 'POST',
            credentials: 'include',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify(subscription)
        });

        alert('Notification subscription registered!');
    }


    function urlBase64ToUint8Array(base64String) {
        const padding = '='.repeat((4 - base64String.length % 4) % 4);
        const base64 = (base64String + padding).replace(/\-/g, '+').replace(/_/g, '/');
        const rawData = window.atob(base64);
        return Uint8Array.from([...rawData].map(char => char.charCodeAt(0)));
    }
</script>