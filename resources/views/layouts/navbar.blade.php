<nav class="navbar navbar-expand-md navbar-light bg-text-theme shadow-sm">
    <div class="container">
        <!-- Brand -->
        <a class="navbar-brand pe-5" href="{{ url('/') }}">
            <img src="{{ asset('images/logo.png') }}" alt="Logo" width="35">
        </a>

        <!-- Toggler -->
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent"
            aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>

        <!-- Navbar Links -->
        <div class="collapse navbar-collapse justify-content-between" id="navbarSupportedContent" style="visibility: visible !important;">

            <!-- Left Side -->
            <ul class="navbar-nav me-auto border-start border-white ps-3">
                 @guest
                     <li class="nav-item">
                         <a class="nav-link text-black {{ Route::currentRouteName() === 'landing' ? 'active fw-bolder' : 'fw-semibold' }}"
                             href="/">Home</a>
                     </li>
                     <li class="nav-item">
                         <a class="nav-link text-black {{ Request::is('services') ? 'active fw-bolder' : 'fw-semibold' }}"
                             href="{{ url('/services') }}">Services</a>
                     </li>
                     <li class="nav-item">
                         <a class="nav-link text-black {{ Request::is('about-us') ? 'active fw-bolder' : 'fw-semibold' }}"
                             href="{{ url('/about-us') }}">About</a>
                     </li>
                     <li class="nav-item">
                         <a class="nav-link text-black {{ Request::is('contact-us') ? 'active fw-bolder' : 'fw-semibold' }}"
                             href="{{ url('/contact-us') }}">Contact</a>
                     </li>
                 @else
                     <li class="nav-item">
                         <a class="nav-link text-black {{ Route::currentRouteName() === 'landing' ? 'active fw-bolder' : 'fw-semibold' }}"
                             href="/">Home</a>
                     </li>
                     <li class="nav-item">
                         <a class="nav-link text-black {{ Request::is('services') ? 'active fw-bolder' : 'fw-semibold' }}"
                             href="{{ url('/services') }}">Services</a>
                     </li>
                     <li class="nav-item">
                         <a class="nav-link text-black {{ Request::is('about-us') ? 'active fw-bolder' : 'fw-semibold' }}"
                             href="{{ url('/about-us') }}">About</a>
                     </li>
                     <li class="nav-item">
                         <a class="nav-link text-black {{ Request::is('contact-us') ? 'active fw-bolder' : 'fw-semibold' }}"
                             href="{{ url('/contact-us') }}">Contact</a>
                     </li>
                @endguest
            </ul>

            <!-- Right Side -->
            <ul class="navbar-nav ms-auto">
                @guest
                    <li class="nav-item">
                        <a class="nav-link text-black fw-semibold" href="{{ route('backpack.auth.login') }}">Login</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link text-black fw-semibold" href="{{ route('backpack.auth.register') }}">Sign Up</a>
                    </li>
                @else
                    <li class="nav-item dropdown dropstart">
                        <a class="nav-link dropdown-toggle text-black fw-semibold" href="#" id="userDropdown"
                            role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            {{ Auth::user()->name }}
                        </a>
                        <ul class="dropdown-menu" aria-labelledby="userDropdown">
                            @hasanyrole('Admin|Staff|Doctor', 'backpack')
                                <li>
                                    <a class="dropdown-item text-center btn btn-success fw-bolder mx-2"
                                        href="{{ route('dashboard') }}">
                                        Dashboard
                                    </a>
                                </li>
                            @endhasanyrole

                            @hasrole('Patient', 'backpack')
                                <li>
                                    <a class="dropdown-item text-center btn btn-success fw-bolder mx-2"
                                        href="{{ route('patient.dashboard') }}">
                                        Dashboard
                                    </a>
                                </li>
                            @endhasrole

                            @if (Auth::user()->isEmployee())
                                <li>
                                    <a class="dropdown-item text-center btn btn-success fw-bolder mx-2"
                                        href="{{ route('employee.dashboard') }}">
                                        Employee Dashboard
                                    </a>
                                </li>
                            @endif

                            <li>
                                <hr class="dropdown-divider">
                            </li>

                            <li>
                                <a class="dropdown-item" href="#"
                                    onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                                    <i class="bi bi-box-arrow-right"></i>&nbsp;Logout
                                </a>
                            </li>
                            <form id="logout-form" action="{{ url('admin/logout') }}" method="POST" class="d-none">
                                @csrf
                            </form>
                        </ul>
                    </li>
                @endguest
            </ul>
        </div>
    </div>
</nav>
