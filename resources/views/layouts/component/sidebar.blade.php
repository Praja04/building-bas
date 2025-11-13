 <div class="sidenav-menu">

     <!-- Brand Logo -->
     <a href="index.html" class="logo">
         <span class="logo logo-light">
             <span class="logo-lg"><img src="{{ asset('assets') }}/images/logos/bas.png" alt="logo"
                     style="height: 50px"></span>
             <span class="logo-sm"><img src="{{ asset('assets') }}/images/logos/bas.png" alt="small logo"></span>
         </span>

         <span class="logo logo-dark">
             <span class="logo-lg"><img src="{{ asset('assets') }}/images/logos/bas.png" alt="dark logo"
                     style="height: 50px"></span>
             <span class="logo-sm"><img src="{{ asset('assets') }}/images/logos/bas.png" alt="small logo"></span>
         </span>
     </a>

     <!-- Sidebar Hover Menu Toggle Button -->
     <button class="button-on-hover">
         <i class="ti ti-menu-4 fs-22 align-middle"></i>
     </button>

     <!-- Full Sidebar Menu Close Button -->
     <button class="button-close-offcanvas">
         <i class="ti ti-x align-middle"></i>
     </button>

     <div class="scrollbar" data-simplebar>
         <!--- Sidenav Menu -->
         <ul class="side-nav">
             <li class="side-nav-title mt-2" data-lang="menu-title">Navigasi</li>
             <li class="side-nav-item">
                 <a href="{{ route('attendance.index') }}"
                     class="side-nav-link {{ request()->routeIs('attendance.index') ? 'active' : '' }}">
                     <span class="menu-icon"><i data-lucide="person-standing"></i></span>
                     <span class="menu-text" data-lang="attendance">Kehadiran</span>
                 </a>
             </li>

             {{-- <li class="side-nav-item">
                 <a href="{{ route('penitipan-barang.index') }}"
                     class="side-nav-link {{ request()->routeIs('penitipan-barang.index') ? 'active' : '' }}">
                     <span class="menu-icon"><i data-lucide="package"></i></span>
                     <span class="menu-text" data-lang="penitipan-barang">Penitipan Barang</span>
                 </a>
             </li> --}}
             <li class="side-nav-item">
                 <a href="{{ route('pengambilan-makan.index') }}"
                     class="side-nav-link {{ request()->routeIs('pengambilan-makan.index') ? 'active' : '' }}">
                     <span class="menu-icon"><i data-lucide="utensils"></i></span>
                     <span class="menu-text" data-lang="pengambilan-makan">Pengambilan Makan</span>
                 </a>
             </li>
         </ul>
     </div>
 </div>
