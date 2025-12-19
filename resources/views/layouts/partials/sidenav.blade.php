<!-- Sidenav Menu Start -->
<style>
.app-logo {
    transition: filter 0.3s ease;
    object-fit: contain;
    background-color: #1A4A9F;
    padding: 8px;
    border-radius: 4px;
}

/* Ensure logo doesn't overflow */
.logo {
    padding: 100px 15px 10px 15px;
    text-align: center;
}

.logo .app-logo {
    max-height: 36px;
    width: auto;
}
</style>

<div class="sidenav-menu">

    <!-- Brand Logo -->
    <a href="/admin" class="logo d-flex align-items-center justify-content-center">
        <img src="{{ asset('applogo.png') }}" alt="İzmir Time Machine Logo" class="app-logo" style="height: 36px; width: auto; max-width: 100%;">
    </a>

    <!-- Sidebar Hover Menu Toggle Button -->
    <button class="button-sm-hover">
        <i class="ri-circle-line align-middle"></i>
    </button>

    <!-- Sidebar Menu Toggle Button -->
    <button class="sidenav-toggle-button">
        <i class="ri-menu-5-line fs-20"></i>
    </button>

    <!-- Full Sidebar Menu Close Button -->
    <button class="button-close-fullsidebar">
        <i class="ti ti-x align-middle"></i>
    </button>

    <div data-simplebar>

        <!-- User -->
        <div class="sidenav-user">
            <div class="dropdown-center text-center">
                <a class="topbar-link dropdown-toggle text-reset drop-arrow-none px-2" data-bs-toggle="dropdown"
                    type="button" aria-haspopup="false" aria-expanded="false">
                    <span class="d-flex gap-1 sidenav-user-name my-2">
                        <span>
                            <span class="mb-0 fw-semibold lh-base fs-15">{{ auth()->user()->name ?? "" }}</span>
                        </span>

                    </span>
                </a>
                
            </div>
        </div>

        <!--- Sidenav Menu -->
        <ul class="side-nav">
            <li class="side-nav-item">
                <a href="{{ route('admin.dashboard') }}" class="side-nav-link">
                    <span class="menu-icon"><i class="ti ti-dashboard"></i></span>
                    <span class="menu-text"> Dashboard </span>
                </a>
            </li>

            <li class="side-nav-item">
                <a href="{{ route('admin.statistics') }}" class="side-nav-link">
                    <span class="menu-icon"><i class="mdi mdi-chart-line"></i></span>
                    <span class="menu-text"> İstatistikler </span>
                </a>
            </li>

            <li class="side-nav-title mt-2">İÇERİK YÖNETİMİ</li>

            <!-- Bölgeler -->
            <li class="side-nav-item">
                <a data-bs-toggle="collapse" href="#sidebarRegions" aria-expanded="false" aria-controls="sidebarRegions"
                    class="side-nav-link">
                    <span class="menu-icon"><i class="mdi mdi-map-marker-multiple"></i></span>
                    <span class="menu-text"> Bölgeler </span>
                    <span class="menu-arrow"></span>
                </a>
                <div class="collapse" id="sidebarRegions">
                    <ul class="sub-menu">
                        <li class="side-nav-item">
                            <a href="{{ route('admin.regions.index') }}" class="side-nav-link">
                                <span class="menu-text">Tüm Bölgeler</span>
                            </a>
                        </li>
                        <li class="side-nav-item">
                            <a href="{{ route('admin.regions.create') }}" class="side-nav-link">
                                <span class="menu-text">Yeni Bölge Ekle</span>
                            </a>
                        </li>
                        <li class="side-nav-item">
                            <a href="{{ route('admin.sub-regions.index') }}" class="side-nav-link">
                                <span class="menu-text">Alt Bölgeler</span>
                            </a>
                        </li>
                        <li class="side-nav-item">
                            <a href="{{ route('admin.sub-regions.create') }}" class="side-nav-link">
                                <span class="menu-text">Yeni Alt Bölge</span>
                            </a>
                        </li>
                    </ul>
                </div>
            </li>

            <!-- Ören Yerleri -->
            <li class="side-nav-item">
                <a data-bs-toggle="collapse" href="#sidebarArchaeological" aria-expanded="false" aria-controls="sidebarArchaeological"
                    class="side-nav-link">
                    <span class="menu-icon"><i class="mdi mdi-castle"></i></span>
                    <span class="menu-text"> Ören Yerleri </span>
                    <span class="menu-arrow"></span>
                </a>
                <div class="collapse" id="sidebarArchaeological">
                    <ul class="sub-menu">
                        <li class="side-nav-item">
                            <a href="{{ route('admin.archaeological-sites.index') }}" class="side-nav-link">
                                <span class="menu-text">Tüm Ören Yerleri</span>
                            </a>
                        </li>
                        <li class="side-nav-item">
                            <a href="{{ route('admin.archaeological-sites.create') }}" class="side-nav-link">
                                <span class="menu-text">Yeni Ören Yeri Ekle</span>
                            </a>
                        </li>
                        <li class="side-nav-item">
                            <a href="{{ route('admin.models-3d.index') }}" class="side-nav-link">
                                <span class="menu-text">3D Modeller</span>
                            </a>
                        </li>
                        <li class="side-nav-item">
                            <a href="{{ route('admin.models-3d.create') }}" class="side-nav-link">
                                <span class="menu-text">Yeni 3D Model Ekle</span>
                            </a>
                        </li>
                    </ul>
                </div>
            </li>

            <!-- Onboarding -->
            <li class="side-nav-item">
                <a data-bs-toggle="collapse" href="#sidebarOnboarding" aria-expanded="false" aria-controls="sidebarOnboarding"
                    class="side-nav-link">
                    <span class="menu-icon"><i class="mdi mdi-walk"></i></span>
                    <span class="menu-text"> Onboarding </span>
                    <span class="menu-arrow"></span>
                </a>
                <div class="collapse" id="sidebarOnboarding">
                    <ul class="sub-menu">
                        <li class="side-nav-item">
                            <a href="{{ route('admin.onboarding-slides.index') }}" class="side-nav-link">
                                <span class="menu-text">Slaytlar</span>
                            </a>
                        </li>
                        <li class="side-nav-item">
                            <a href="{{ route('admin.onboarding-slides.create') }}" class="side-nav-link">
                                <span class="menu-text">Yeni Slayt Ekle</span>
                            </a>
                        </li>
                    </ul>
                </div>
            </li>

            <!-- Hikayeler -->
            <li class="side-nav-item">
                <a data-bs-toggle="collapse" href="#sidebarStories" aria-expanded="false" aria-controls="sidebarStories"
                    class="side-nav-link">
                    <span class="menu-icon"><i class="mdi mdi-book-open-page-variant"></i></span>
                    <span class="menu-text"> Hikayeler </span>
                    <span class="menu-arrow"></span>
                </a>
                <div class="collapse" id="sidebarStories">
                    <ul class="sub-menu">
                        <li class="side-nav-item">
                            <a href="{{ route('admin.stories.index') }}" class="side-nav-link">
                                <span class="menu-text">Tüm Hikayeler</span>
                            </a>
                        </li>
                        <li class="side-nav-item">
                            <a href="{{ route('admin.stories.create') }}" class="side-nav-link">
                                <span class="menu-text">Yeni Hikaye Ekle</span>
                            </a>
                        </li>
                    </ul>
                </div>
            </li>

            <!-- Yazılar -->
            <li class="side-nav-item">
                <a data-bs-toggle="collapse" href="#sidebarArticles" aria-expanded="false" aria-controls="sidebarArticles"
                    class="side-nav-link">
                    <span class="menu-icon"><i class="mdi mdi-file-document-edit"></i></span>
                    <span class="menu-text"> Yazılar </span>
                    <span class="menu-arrow"></span>
                </a>
                <div class="collapse" id="sidebarArticles">
                    <ul class="sub-menu">
                        <li class="side-nav-item">
                            <a href="{{ route('admin.articles.index') }}" class="side-nav-link">
                                <span class="menu-text">Tüm Yazılar</span>
                            </a>
                        </li>
                        <li class="side-nav-item">
                            <a href="{{ route('admin.articles.create') }}" class="side-nav-link">
                                <span class="menu-text">Yeni Yazı Ekle</span>
                            </a>
                        </li>
                    </ul>
                </div>
            </li>

            <!-- Haberler -->
            <li class="side-nav-item">
                <a data-bs-toggle="collapse" href="#sidebarNews" aria-expanded="false" aria-controls="sidebarNews"
                    class="side-nav-link">
                    <span class="menu-icon"><i class="mdi mdi-newspaper"></i></span>
                    <span class="menu-text"> Haberler ve Etkinlikler </span>
                    <span class="menu-arrow"></span>
                </a>
                <div class="collapse" id="sidebarNews">
                    <ul class="sub-menu">
                        <li class="side-nav-item">
                            <a href="{{ route('admin.news.index') }}" class="side-nav-link">
                                <span class="menu-text">Tüm Haberler ve Etkinlikler</span>
                            </a>
                        </li>
                        <li class="side-nav-item">
                            <a href="{{ route('admin.news.create') }}" class="side-nav-link">
                                <span class="menu-text">Yeni Haber/Etkinlik Ekle</span>
                            </a>
                        </li>
                    </ul>
                </div>
            </li>

            <!-- Hafıza İzmir -->
            <li class="side-nav-item">
                <a data-bs-toggle="collapse" href="#sidebarMemories" aria-expanded="false" aria-controls="sidebarMemories"
                    class="side-nav-link">
                    <span class="menu-icon"><i class="mdi mdi-brain"></i></span>
                    <span class="menu-text"> Hafıza İzmir </span>
                    <span class="menu-arrow"></span>
                </a>
                <div class="collapse" id="sidebarMemories">
                    <ul class="sub-menu">
                        <li class="side-nav-item">
                            <a href="{{ route('admin.memories.index') }}" class="side-nav-link">
                                <span class="menu-text">Tüm İçerikler</span>
                            </a>
                        </li>
                        <li class="side-nav-item">
                            <a href="{{ route('admin.memories.create') }}" class="side-nav-link">
                                <span class="menu-text">Yeni İçerik Ekle</span>
                            </a>
                        </li>
                    </ul>
                </div>
            </li>

            <!-- Cihazlar -->
            <li class="side-nav-item">
                <a data-bs-toggle="collapse" href="#sidebarDevices" aria-expanded="false" aria-controls="sidebarDevices"
                    class="side-nav-link">
                    <span class="menu-icon"><i class="mdi mdi-cellphone"></i></span>
                    <span class="menu-text"> Cihazlar </span>
                    <span class="menu-arrow"></span>
                </a>
                <div class="collapse" id="sidebarDevices">
                    <ul class="sub-menu">
                        <li class="side-nav-item">
                            <a href="{{ route('admin.devices.index') }}" class="side-nav-link">
                                <span class="menu-text">Tüm Cihazlar</span>
                            </a>
                        </li>
                        <li class="side-nav-item">
                            <a href="{{ route('admin.devices.notifications') }}" class="side-nav-link">
                                <span class="menu-text">Bildirim Gönder</span>
                            </a>
                        </li>
                    </ul>
                </div>
            </li>

            <!-- Destek Talepleri -->
            <li class="side-nav-item">
                <a href="{{ route('admin.support-requests.index') }}" class="side-nav-link">
                    <span class="menu-icon"><i class="mdi mdi-lifebuoy"></i></span>
                    <span class="menu-text"> Destek Talepleri </span>
                </a>
            </li>

            <li class="side-nav-title mt-2">Ayarlar</li>
            <li class="side-nav-item">
                <a href="{{ route('admin.settings.edit') }}" class="side-nav-link">
                    <span class="menu-icon"><i class="ti ti-settings"></i></span>
                    <span class="menu-text"> Site Ayarları </span>
                </a>
            </li>
            <li class="side-nav-item">
                <form action="{{ route('logout') }}" method="POST">
                    @csrf
                    <button type="submit" class="side-nav-link w-100 bg-transparent border-0 text-start">
                        <span class="menu-icon"><i class="ti ti-logout"></i></span>
                        <span class="menu-text"> Çıkış Yap </span>
                    </button>
                </form>
            </li>
        </ul>

        <div class="clearfix"></div>
    </div>
</div>
<!-- Sidenav Menu End -->
