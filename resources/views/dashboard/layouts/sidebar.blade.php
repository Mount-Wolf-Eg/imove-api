<!-- ========== App Menu ========== -->
<div class="app-menu navbar-menu">
    <!-- LOGO -->
    <div class="navbar-brand-box">
        <a href="{{route('dashboard')}}" class="logo logo-dark">
            <span class="logo-sm">
                <img src="{{ URL::asset('assets/images/virtualTouch.svg') }}" alt="" height="10">
             </span>
            <span class="logo-lg">
                <img src="{{ URL::asset('assets/images/virtualTouch.svg') }}" alt="" height="10">
             </span>
        </a>
        <a href="{{route('dashboard')}}" class="logo logo-light">
            <span class="logo-sm">
                <img src="{{ URL::asset('assets/images/virtualTouch.svg') }}" alt="" height="15">
             </span>
            <span class="logo-lg">
                 <img src="{{ URL::asset('assets/images/virtualTouch.svg') }}" alt="" height="40" class="my-2">
            </span>
        </a>
        <button type="button" class="btn btn-sm p-0 fs-20 header-item {{app()->getLocale() == 'ar' ? 'float-start' : 'float-end'}} btn-vertical-sm-hover"
            id="vertical-hover">
            <i class="ri-record-circle-line"></i>
        </button>
    </div>

    <div id="scrollbar">
        <div class="container-fluid">
            <div id="two-column-menu">
            </div>
            <ul class="navbar-nav" id="navbar-nav">
                <li class="menu-title"><span data-key="t-menu">{{ __('t-menu') }}</span></li>

                <li class="nav-item">
                    <a href="{{route('dashboard')}}" @class(['nav-link', 'menu-link' , 'active'=> request()->routeIs('dashboard')])>
                        <i class="bi bi-speedometer2"></i>
                        <span data-key="t-dashboard">{{ __('t-dashboard') }}</span>
                     </a>
                </li>
                @if(auth()->user()->can('read-general-settings') || auth()->user()->can('view-all-general-settings'))
                <li class="nav-item">
                    <a href="{{route('settings.edit')}}" @class(['nav-link', 'menu-link' , 'active'=> request()->routeIs('settings.edit')])>
                        <i class="bi bi-question-octagon"></i>
                        <span data-key="t-dashboard">{{ __('messages.settings') }}</span>
                    </a>
                </li>
                @endif

                @if(auth()->user()->can('read-consultation') || auth()->user()->can('view-all-consultation'))
                <li class="nav-item">
                    <a href="{{route('setting-consultations.index')}}" @class(['nav-link', 'menu-link' , 'active'=> request()->routeIs('setting-consultations.index', 'setting-consultations.show', 'setting-consultations.create', 'setting-consultations.edit')])>
                        <i class="bi bi-cash-stack"></i>
                        <span data-key="t-dashboard">{{ __('messages.setting-consultations') }}</span>
                    </a>
                </li>
                @endif

                @if(auth()->user()->can('read-banner') || auth()->user()->can('view-all-banner'))
                <li class="nav-item">
                    <a href="{{route('banners.index')}}" @class(['nav-link', 'menu-link' , 'active'=> request()->routeIs('banners.index', 'banners.show', 'banners.create', 'banners.edit')])>
                        <i class="bi bi-card-image"></i>
                        <span data-key="t-dashboard">{{ __('messages.banners') }}</span>
                    </a>
                </li>
                @endif

                @if(auth()->user()->can('read-general-settings') || auth()->user()->can('view-all-general-settings'))
                <li class="nav-item">
                    <a href="{{route('static-pages.index')}}" @class(['nav-link', 'menu-link' , 'active'=> request()->routeIs('static-pages.index', 'static-pages.show', 'static-pages.create', 'static-pages.edit')])>
                        <i class="bi bi-postcard-fill"></i>
                        <span data-key="t-dashboard">{{ __('messages.static-pages') }}</span>
                    </a>
                </li>
                @endif

                <hr class="menu-title mt-2"/>

                @if(auth()->user()->can('read-user') || auth()->user()->can('view-all-user'))
                <li class="nav-item">
                    <a href="{{route('users.index')}}" @class(['nav-link', 'menu-link' , 'active'=> request()->routeIs('users.index', 'users.show', 'users.create', 'users.edit')])>
                        <i class="bi bi-person"></i>
                        <span data-key="t-dashboard">{{ __('messages.supervisors') }}</span>
                    </a>
                </li>
                @endif

                @if(auth()->user()->can('read-role') || auth()->user()->can('view-all-role'))
                <li class="nav-item">
                    <a href="{{route('roles.index')}}" @class(['nav-link', 'menu-link' , 'active'=> request()->routeIs('roles.index', 'roles.show', 'roles.create', 'roles.edit')])>
                        <i class="bi bi-sign-stop"></i>
                        <span data-key="t-dashboard">{{ __('messages.supervisors_roles') }}</span>
                    </a>
                </li>
                @endif

                @if(auth()->user()->can('read-doctor') || auth()->user()->can('view-all-doctor'))
                <li class="nav-item">
                    <a href="{{route('doctors.index')}}" @class(['nav-link', 'menu-link' , 'active'=> request()->routeIs('doctors.index', 'doctors.show', 'doctors.create',
                        'doctors.edit')])>
                        <i class="bi bi-journal-plus"></i>
                        <span data-key="t-dashboard">{{ __('messages.doctors') }}</span>
                    </a>
                </li>
                @endif

                @if(auth()->user()->can('read-patient') || auth()->user()->can('view-all-patient'))
                <li class="nav-item">
                    <a href="{{route('patients.index')}}" @class(['nav-link', 'menu-link' , 'active'=> request()->routeIs('patients.index', 'patients.show', 'patients.create',
                        'patients.edit')])>
                        <i class="bi bi-person-badge"></i>
                        <span data-key="t-dashboard">{{ __('messages.patients') }}</span>
                    </a>
                </li>
                @endif

                <hr class="menu-title mt-2"/>

                @if(auth()->user()->can('read-academic-degree') || auth()->user()->can('view-all-academic-degree'))
                <li class="nav-item">
                    <a href="{{route('academic-degrees.index')}}" @class(['nav-link', 'menu-link' , 'active'=> request()->routeIs('academic-degrees.index', 'academic-degrees.show', 'academic-degrees.create',
                        'academic-degrees.edit')])>
                        <i class="bi bi-book"></i>
                        <span data-key="t-dashboard">{{ __('messages.academic_degrees') }}</span>
                    </a>
                </li>
                @endif

                @if(auth()->user()->can('read-medical-speciality') || auth()->user()->can('view-all-medical-speciality'))
                <li class="nav-item">
                    <a href="{{route('medical-specialities.index')}}" @class(['nav-link', 'menu-link' , 'active'=> request()->routeIs('medical-specialities.index', 'medical-specialities.show', 'medical-specialities.create',
                        'medical-specialities.edit')])>
                        <i class="bi bi-heart-pulse-fill"></i>
                        <span data-key="t-dashboard">{{ __('messages.medical_specialities') }}</span>
                    </a>
                </li>
                @endif

                @if(auth()->user()->can('read-university') || auth()->user()->can('view-all-university'))
                <li class="nav-item">
                    <a href="{{route('universities.index')}}" @class(['nav-link', 'menu-link' , 'active'=> request()->routeIs('universities.index', 'universities.show', 'universities.create', 'universities.edit')])>
                        <i class="bi bi-building"></i>
                        <span data-key="t-dashboard">{{ __('messages.universities') }}</span>
                    </a>
                </li>
                @endif

                @if(auth()->user()->can('read-seniority') || auth()->user()->can('view-all-seniority'))
                <li class="nav-item">
                    <a href="{{route('seniorities.index')}}" @class(['nav-link', 'menu-link' , 'active'=> request()->routeIs('seniorities.index', 'seniorities.show', 'seniorities.create',
                        'seniorities.edit')])>
                        <i class="bi bi-journal-plus"></i>
                        <span data-key="t-dashboard">{{ __('messages.seniorities') }}</span>
                    </a>
                </li>
                @endif

                <hr class="menu-title mt-2"/>

                {{-- @if(auth()->user()->can('read-vendor-service') || auth()->user()->can('view-all-vendor-service'))
                <li class="nav-item">
                    <a href="{{route('vendor-services.index')}}" @class(['nav-link', 'menu-link' , 'active'=> request()->routeIs('vendor-services.index', 'vendor-services.show', 'vendor-services.create',
                        'vendor-services.edit')])>
                        <i class="bi bi-box-seam-fill"></i>
                        <span data-key="t-dashboard">{{ __('messages.vendor_services') }}</span>
                    </a>
                </li>
                @endif

                @if(auth()->user()->can('read-vendor') || auth()->user()->can('view-all-vendor'))
                <li class="nav-item">
                    <a href="{{route('vendors.index')}}" @class(['nav-link', 'menu-link' , 'active'=> request()->routeIs('vendors.index', 'vendors.show', 'vendors.create',
                        'vendors.edit')])>
                        <i class="bi bi-houses"></i>
                        <span data-key="t-dashboard">{{ __('messages.vendors') }}</span>
                    </a>
                </li>
                @endif --}}

                @if(auth()->user()->can('read-consultation') || auth()->user()->can('view-all-consultation'))
                <li class="nav-item">
                    <a href="{{route('consultations.index')}}" @class(['nav-link', 'menu-link' , 'active'=> request()->routeIs('consultations.index', 'consultations.show')])>
                        <i class="bi bi-tv"></i>
                        <span data-key="t-dashboard">{{ __('messages.consultations') }}</span>
                    </a>
                </li>
                @endif

                @if(auth()->user()->can('read-consultation') || auth()->user()->can('view-all-consultation'))
                <li class="nav-item">
                    <a href="{{route('consultation-questions.index')}}" @class(['nav-link', 'menu-link' , 'active'=> request()->routeIs('consultation-questions.index', 'consultation-questions.show')])>
                        <i class="bi bi-tv"></i>
                        <span data-key="t-dashboard">{{ __('messages.consultation-questions') }}</span>
                    </a>
                </li>
                @endif

                @if(auth()->user()->can('read-consultation') || auth()->user()->can('view-all-consultation'))
                <li class="nav-item">
                    <a href="{{route('home-care-requests.index')}}" @class(['nav-link', 'menu-link' , 'active'=> request()->routeIs('home-care-requests.index', 'home-care-requests.show', 'home-care-requests.edit')])>
                        <i class="bi bi-postcard-fill"></i>
                        <span data-key="t-dashboard">{{ __('messages.home-care-requests') }}</span>
                    </a>
                </li>
                @endif

                <hr class="menu-title mt-2"/>

                @if(auth()->user()->can('read-article') || auth()->user()->can('view-all-article'))
                <li class="nav-item">
                    <a href="{{route('educational-contents.index')}}" @class(['nav-link', 'menu-link' , 'active'=> request()->routeIs('educational-contents.index', 'educational-contents.show', 'educational-contents.create', 'educational-contents.edit')])>
                        <i class="bi bi-postcard-fill"></i>
                        <span data-key="t-dashboard">{{ __('messages.educational-contents') }}</span>
                    </a>
                </li>
                @endif

                @if(auth()->user()->can('read-article') || auth()->user()->can('view-all-article'))
                <li class="nav-item">
                    <a href="{{route('exercises.index')}}" @class(['nav-link', 'menu-link' , 'active'=> request()->routeIs('exercises.index', 'exercises.show', 'exercises.create', 'exercises.edit')])>
                        <i class="bi bi-heart-pulse-fill"></i>
                        <span data-key="t-dashboard">{{ __('messages.exercises') }}</span>
                    </a>
                </li>
                @endif

                @if(auth()->user()->can('read-article') || auth()->user()->can('view-all-article'))
                <li class="nav-item">
                    <a href="{{route('articles.index')}}" @class(['nav-link', 'menu-link' , 'active'=> request()->routeIs('articles.index', 'articles.show', 'articles.create', 'articles.edit')])>
                        <i class="bi bi-postcard-fill"></i>
                        <span data-key="t-dashboard">{{ __('messages.articles') }}</span>
                    </a>
                </li>
                @endif

                <hr class="menu-title mt-2"/>

                @if(auth()->user()->can('read-faq-subject') || auth()->user()->can('view-all-faq-subject'))
                <li class="nav-item">
                    <a href="{{route('faq-subjects.index')}}" @class(['nav-link', 'menu-link' , 'active'=> request()->routeIs('faq-subjects.index', 'faq-subjects.show', 'faq-subjects.create',
                        'faq-subjects.edit')])>
                        <i class="bi bi-patch-question-fill"></i>
                        <span data-key="t-dashboard">{{ __('messages.faq_subjects') }}</span>
                    </a>
                </li>
                @endif

                @if(auth()->user()->can('read-faq') || auth()->user()->can('view-all-faq'))
                <li class="nav-item">
                    <a href="{{route('faqs.index')}}" @class(['nav-link', 'menu-link' , 'active'=> request()->routeIs('faqs.index', 'faqs.show', 'faqs.create', 'faqs.edit')])>
                        <i class="bi bi-question-octagon"></i>
                        <span data-key="t-dashboard">{{ __('messages.faqs') }}</span>
                    </a>
                </li>
                @endif

                @if(auth()->user()->can('read-contact') || auth()->user()->can('view-all-contact'))
                <li class="nav-item">
                    <a href="{{route('technical-support.index')}}" @class(['nav-link', 'menu-link' , 'active'=> request()->routeIs('technical-support.index', 'technical-support.show')])>
                        <i class="bi bi-telephone"></i>
                        <span data-key="t-dashboard">{{ __('messages.technical-support') }}</span>
                    </a>
                </li>
                @endif

                <hr class="menu-title mt-2"/>

                @if(auth()->user()->can('read-coupon') || auth()->user()->can('view-all-coupon'))
                <li class="nav-item">
                    <a href="{{route('coupons.index')}}" @class(['nav-link', 'menu-link' , 'active'=> request()->routeIs('coupons.index', 'coupons.show', 'coupons.create',
                        'coupons.edit')])>
                        <i class="bi bi-card-text"></i>
                        <span data-key="t-dashboard">{{ __('messages.coupons') }}</span>
                    </a>
                </li>
                @endif

                @if(auth()->user()->can('read-setting-package') || auth()->user()->can('view-all-setting-package'))
                <li class="nav-item">
                    <a href="{{route('setting-packages.index')}}" @class(['nav-link', 'menu-link' , 'active'=> request()->routeIs('setting-packages.index', 'setting-packages.show', 'setting-packages.create', 'setting-packages.edit')])>
                        <i class="bi bi-postcard-fill"></i>
                        <span data-key="t-dashboard">{{ __('messages.setting-packages') }}</span>
                    </a>
                </li>
                @endif

                @if(auth()->user()->can('read-package') || auth()->user()->can('view-all-package'))
                <li class="nav-item">
                    <a href="{{route('packages.index')}}" @class(['nav-link', 'menu-link' , 'active'=> request()->routeIs('packages.index', 'packages.show', 'packages.create', 'packages.edit')])>
                        <i class="bi bi-postcard-fill"></i>
                        <span data-key="t-dashboard">{{ __('messages.packages') }}</span>
                    </a>
                </li>
                @endif

                @if(auth()->user()->can('read-payment') || auth()->user()->can('view-all-payment'))
                <li class="nav-item">
                    <a href="{{route('payments.index')}}" @class(['nav-link', 'menu-link' , 'active'=> request()->routeIs('payments.index', 'payments.show')])>
                        <i class="bi bi-tv"></i>
                        <span data-key="t-dashboard">{{ __('messages.payments') }}</span>
                    </a>
                </li>
                @endif

                @if(auth()->user()->can('read-payment') || auth()->user()->can('view-all-payment'))
                <li class="nav-item">
                    <a href="{{route('refund-request')}}" @class(['nav-link', 'menu-link' , 'active'=> request()->routeIs('refunds.index', 'refunds.show')])>
                        <i class="bi bi-tv"></i>
                        <span data-key="t-dashboard">{{ __('messages.refunds') }}</span>
                    </a>
                </li>
                @endif

                <hr class="menu-title mt-2"/>

                @if(auth()->user()->can('read-academic-degree') || auth()->user()->can('view-all-academic-degree'))
                <li class="nav-item">
                    <a href="{{route('medical-equipments.index')}}" @class(['nav-link', 'menu-link' , 'active'=> request()->routeIs('medical-equipments.index', 'medical-equipments.show',
                        'medical-equipments.create', 'medical-equipments.edit')])>
                        <i class="bi bi-book"></i>
                        <span data-key="t-dashboard">{{ __('messages.medical-equipments') }}</span>
                    </a>
                </li>
                @endif

                @if(auth()->user()->can('read-academic-degree') || auth()->user()->can('view-all-academic-degree'))
                <li class="nav-item">
                    <a href="{{route('category-medical-equipments.index')}}" @class(['nav-link', 'menu-link' , 'active'=> request()->routeIs('category-medical-equipments.index', 'category-medical-equipments.show', 'category-medical-equipments.create',
                        'category-medical-equipments.edit')])>
                        <i class="bi bi-book"></i>
                        <span data-key="t-dashboard">{{ __('messages.equipment-categories') }}</span>
                    </a>
                </li>
                @endif

                <hr class="menu-title mt-2"/>

                @if(auth()->user()->can('read-region') || auth()->user()->can('view-all-region'))
                <li class="nav-item">
                    <a href="{{route('regions.index')}}" @class(['nav-link', 'menu-link' , 'active'=> request()->routeIs('regions.index', 'regions.show', 'regions.create', 'regions.edit')])>
                        <i class="bi bi-map"></i>
                        <span data-key="t-dashboard">{{ __('messages.regions') }}</span>
                    </a>
                </li>
                @endif

                @if(auth()->user()->can('read-city') || auth()->user()->can('view-all-city'))
                <li class="nav-item">
                    <a href="{{route('cities.index')}}" @class(['nav-link', 'menu-link' , 'active'=> request()->routeIs('cities.index', 'cities.show', 'cities.create', 'cities.edit')])>
                        <i class="bi bi-geo-alt-fill"></i>
                        <span data-key="t-dashboard">{{ __('messages.cities') }}</span>
                    </a>
                </li>
                @endif
            </ul>
        </div>
        <!-- Sidebar -->
    </div>

    <div class="sidebar-background"></div>
</div>
<!-- Left Sidebar End -->
<!-- Vertical Overlay-->
<div class="vertical-overlay"></div>
