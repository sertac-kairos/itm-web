<!-- Topbar Start -->
<style>
.topbar-logo img {
    background-color: #1A4A9F;
    padding: 8px;
    border-radius: 4px;
    height: 40px !important;
    width: auto !important;
}
</style>
<header class="app-topbar" id="header">
	<div class="page-container topbar-menu">
		<div class="d-flex align-items-center gap-2">

			<!-- Brand Logo -->
			<a href="/admin" class="logo topbar-logo">
				<span class="logo-light">
					<span class="logo-lg"><img src="/images/logo.png" alt="logo"></span>
					<span class="logo-sm"><img src="/images/logo-sm.png" alt="small logo"></span>
				</span>

				<span class="logo-dark">
					<span class="logo-lg"><img src="/images/logo-dark.png" alt="dark logo"></span>
					<span class="logo-sm"><img src="/images/logo-sm.png" alt="small logo"></span>
				</span>
			</a>

			<!-- Sidebar Menu Toggle Button -->
			<button class="sidenav-toggle-button px-2">
				<i class="ri-menu-5-line fs-24"></i>
			</button>

			<!-- Horizontal Menu Toggle Button -->
			<button class="topnav-toggle-button px-2" data-bs-toggle="collapse" data-bs-target="#topnav-menu-content">
				<i class="ri-menu-5-line fs-24"></i>
			</button>

			<!-- Topbar Page Title -->

			<div class="topbar-item d-none d-md-flex px-2">
				@if(isset($topbarTitle))
				<div>
					<h4 class="page-title fs-20 fw-semibold mb-0">{{ $topbarTitle }}</h4>
				</div>
				@else
				<div>
					<h4 class="page-title fs-20 fw-semibold mb-0">Welcome!</h4>
				</div>
				@endif
			</div>


		</div>

		<div class="d-flex align-items-center gap-2">
			
		</div>
	</div>
</header>
<!-- Topbar End -->